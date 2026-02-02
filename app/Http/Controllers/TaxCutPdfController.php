<?php

namespace App\Http\Controllers;

use App\Models\TaxCut;
use Barryvdh\DomPDF\Facade\Pdf;

class TaxCutPdfController extends Controller
{
    public function generate(TaxCut $taxCut)
    {
        $taxCut->load(['company', 'recipient']);
        
        $calc = $taxCut->calculate();

         $pdf = Pdf::loadView('pdf.tax-cut', [
            'taxCut' => $taxCut,
            'calc' => $calc, // ⬅️ KIRIM KE BLADE
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Bukti-Potong-' . preg_replace('/[^A-Za-z0-9\-_.]/', '-', $taxCut->memo_number) . '.pdf';
        
        return $pdf->stream($filename);
    }
    
    public function download(TaxCut $taxCut)
    {
        $taxCut->load(['company', 'recipient']);
        
        $calc = $taxCut->calculate();
        
        $pdf = Pdf::loadView('pdf.tax-cut', [
            'taxCut' => $taxCut,
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Bukti-Potong-' . preg_replace('/[^A-Za-z0-9\-_.]/', '-', $taxCut->memo_number) . '.pdf';
        
        return $pdf->download($filename);
    }
}
