<?php

namespace App\Filament\Resources\TaxCutResource\Pages;

use App\Filament\Resources\TaxCutResource;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\PartTimer;
use App\Models\TaxCut;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportTaxCut extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TaxCutResource::class;

    protected static string $view = 'filament.resources.tax-cut-resource.pages.import-tax-cut';
    
    public ?array $data = [];
    public $file;
    public $company_id;
    public $recipient_type;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('company_id')
                    ->label('Pilih Perusahaan')
                    ->options(Company::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                
                Select::make('recipient_type')
                    ->label('Tipe Penerima')
                    ->options([
                        'supplier' => 'Supplier',
                        'part_timer' => 'Part Timer / Freelancer',
                    ])
                    ->required(),
                
                FileUpload::make('file')
                    ->label('File Excel')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                    ->required()
                    ->helperText('Format: Nama, NPWP (optional), Komisi Freelance, DPP Pemotongan, Tarif Pajak (%), PPh 21'),
            ])
            ->statePath('data');
    }

    public function import(): void
    {
        $data = $this->form->getState();
        
        if (!$data['file']) {
            Notification::make()
                ->title('Error')
                ->body('File tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        try {
            $filePath = storage_path('app/public/' . $data['file']);
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $imported = 0;
            $errors = [];
            
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header
                
                if (empty($row[0])) continue; // Skip empty rows
                
                try {
                    $recipientName = $row[0];
                    $npwp = $row[1] ?? null;
                    $commissionAmount = $row[2] ?? 0;
                    $dppAmount = $row[3] ?? 0;
                    $taxRate = $row[4] ?? 5;
                    
                    // Find or create recipient
                    if ($data['recipient_type'] === 'supplier') {
                        $recipient = Supplier::firstOrCreate(
                            ['name' => $recipientName],
                            ['npwp' => $npwp, 'is_active' => true]
                        );
                        $recipientType = 'App\\Models\\Supplier';
                    } else {
                        $recipient = PartTimer::firstOrCreate(
                            ['name' => $recipientName],
                            ['npwp' => $npwp, 'is_active' => true]
                        );
                        $recipientType = 'App\\Models\\PartTimer';
                    }
                    
                    // Generate memo number
                    $lastMemo = TaxCut::latest('id')->first();
                    $number = $lastMemo ? (int)substr($lastMemo->memo_number, -4) + 1 : 1;
                    $memoNumber = 'PPH21-' . date('Ym') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
                    
                    // Calculate tax
                    $taxAmount = ($dppAmount * $taxRate) / 100;
                    $netPayment = $commissionAmount - $taxAmount;
                    
                    TaxCut::create([
                        'company_id' => $data['company_id'],
                        'recipient_type' => $recipientType,
                        'recipient_id' => $recipient->id,
                        'memo_number' => $memoNumber,
                        'commission_amount' => $commissionAmount,
                        'dpp_amount' => $dppAmount,
                        'tax_rate' => $taxRate,
                        'tax_amount' => $taxAmount,
                        'net_payment' => $netPayment,
                        'cut_date' => now(),
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                }
            }
            
            if ($imported > 0) {
                Notification::make()
                    ->title('Berhasil!')
                    ->body("$imported data berhasil diimport")
                    ->success()
                    ->send();
            }
            
            if (count($errors) > 0) {
                Notification::make()
                    ->title('Beberapa data gagal diimport')
                    ->body(implode("\n", array_slice($errors, 0, 5)))
                    ->warning()
                    ->send();
            }
            
            if ($imported > 0) {
                $this->redirect(TaxCutResource::getUrl('index'));
            }
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal membaca file: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getViewData(): array
    {
        return [
            'indexUrl' => TaxCutResource::getUrl('index'),
        ];
    }
}
