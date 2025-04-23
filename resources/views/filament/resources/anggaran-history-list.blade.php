<x-filament::card>
    <div class="text-lg font-semibold mb-4">Riwayat Perubahan Anggaran</div>

    <div class="space-y-2">
        @foreach ($histories as $history)
            <div class="border p-4 rounded-lg shadow-sm bg-white">
                <div class="text-sm text-gray-600">
                    Tanggal: {{ $history->changed_at->format('d M Y H:i') }}
                </div>
                <div class="mt-1">
                    <span class="font-semibold text-gray-800">Sebelumnya:</span>
                    Rp{{ number_format($history->previous_amount, 0, ',', '.') }}
                </div>
                <div>
                    <span class="font-semibold text-gray-800">Sekarang:</span>
                    Rp{{ number_format($history->current_amount, 0, ',', '.') }}
                </div>
            </div>
        @endforeach
    </div>
</x-filament::card>
