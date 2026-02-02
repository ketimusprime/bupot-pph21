<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">Format File Excel</h3>
            <p class="text-sm text-blue-700 mb-3">File Excel harus memiliki kolom berikut (dimulai dari baris pertama sebagai header):</p>
            <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                <li><strong>Nama</strong> - Nama penerima (wajib)</li>
                <li><strong>NPWP</strong> - Nomor NPWP (opsional)</li>
                <li><strong>Komisi Freelance</strong> - Jumlah komisi dalam angka</li>
                <li><strong>DPP Pemotongan</strong> - Dasar Pengenaan Pajak (biasanya 50% dari komisi)</li>
                <li><strong>Tarif Pajak (%)</strong> - Persentase tarif pajak (default 5)</li>
            </ol>
            <div class="mt-3">
                <a href="{{ asset('template-import-pph21.xlsx') }}" 
                   class="text-sm text-blue-600 hover:text-blue-800 underline"
                   download>
                    Download Template Excel
                </a>
            </div>
        </div>

        <form wire:submit="import">
            {{ $this->form }}
            
            <div class="flex justify-end gap-3 mt-6">
                <x-filament::button
                    color="gray"
                    tag="a"
                    :href="$indexUrl">
                    Batal
                </x-filament::button>
                
                <x-filament::button type="submit">
                    Import Data
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
