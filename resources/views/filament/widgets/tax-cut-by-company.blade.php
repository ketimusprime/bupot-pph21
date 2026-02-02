<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">Bukti Potong per Perusahaan</h2>

        <ul class="space-y-2">
            @foreach ($data as $row)
                <li class="flex justify-between">
                    <span>{{ $row->company }}</span>
                    <span class="font-semibold">{{ $row->total }}</span>
                </li>
            @endforeach
        </ul>
    </x-filament::card>
</x-filament::widget>
