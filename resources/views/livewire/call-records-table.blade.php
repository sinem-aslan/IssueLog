<div class="mt-8 mx-8 p-6 rounded shadow">
    <!-- Tabloyu çevreleyen ve kaydırma özelliğini sağlayan ana konteyner -->
    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md">
        <div class="relative max-h-[70vh] overflow-y-auto"> <!-- Maksimum yükseklik ve dikey kaydırma -->
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-50 sticky top-0"> <!-- Başlıkların kaydırma sırasında sabit kalması için -->
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium text-gray-900">No</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900">Çağrı Tarihi</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900">Öğrenci T.C.K.N</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900">Telefon Numarası</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900">Arama Gerekçesi</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900">İlgili Birim</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900">Durum</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900">Çözüm Tarihi</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse ($records as $record)
                        <!-- Acil olan kayıtlar için arkaplan renginin değiştirilmesi -->
                        <tr
                            class="hover:bg-gray-50 {{ $record->is_urgent && empty($record->solution_date) ? 'bg-yellow-50' : '' }}">
                            <!-- No -->
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            <!-- Çağrı tarihi -->
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-700">{{ $record->call_date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $record->call_date->format('H:i') }}</div>
                            </td>
                            <!-- Öğrenci T.C.K.N -->
                            <td class="px-6 py-3">{{ $record->student_tc ?: 'Bilinmiyor' }}</td>
                            <!-- Telefon Numarası -->
                            <td class="px-6 py-3">{{ $record->student_phone ?: 'Bilinmiyor' }}</td>
                            <!-- Arama Gerekçesi -->
                            <td class="px-6 py-3 max-w-xs align-top">
                                @php
                                    $words = explode(' ', $record->reason);
                                    $shortReason = implode(' ', array_slice($words, 0, 10));
                                    $isLong = count($words) > 10;
                                @endphp
                                @if ($isLong && !($expandedReason[$record->id] ?? false))
                                    <div>{{ $shortReason }}...</div>
                                    <a href="#" wire:click.prevent="toggleReason({{ $record->id }})"
                                        class="block mt-2 text-blue-600 hover:underline">devamı</a>
                                @elseif ($isLong && ($expandedReason[$record->id] ?? false))
                                    <div class="whitespace-pre-line">{{ $record->reason }}</div>
                                    <a href="#" wire:click.prevent="toggleReason({{ $record->id }})"
                                        class="block mt-2 text-blue-600 hover:underline">daha az</a>
                                @else
                                    <div>{{ $record->reason }}</div>
                                @endif
                            </td>
                            <!-- İlgili Birim -->
                            <td class="px-6 py-3">{{ $record->department?->name ?? 'Bilinmiyor' }}</td>
                            <!-- Durum -->
                            <td class="px-6 py-3">
                                <!-- Duruma göre farklı renklerde etiketler gösterilir -->
                                @if ($record->status == 'resolved')
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                        Çözüldü
                                    </span>
                                @elseif ($record->status == 'in_progress')
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                                        İşlemde
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-2 py-1 text-xs font-semibold text-orange-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-orange-600"></span>
                                        Bekliyor
                                    </span>
                                @endif
                            </td>
                            <!-- Çözüm Tarihi -->
                            <td class="px-6 py-3">
                                @if ($record->solution_date)
                                    <div class="font-medium text-gray-700">
                                        {{ \Carbon\Carbon::parse($record->solution_date)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($record->solution_date)->format('H:i') }}</div>
                                @endif
                            </td>
                            <!-- İşlemler -->
                            <td class="px-6 py-3">
                                <div class="flex justify-start items-center gap-4 h-full">
                                    <!-- düzenleme, silme detayı görme butonları -->
                                    <button type="button" class="flex items-center justify-center w-8 h-8">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="flex items-center justify-center w-8 h-8">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Veritabanında hiç kayıt yoksa -->
                        <tr>
                            <td colspan="8" class="text-center py-4">Gösterilecek kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
