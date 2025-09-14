<div class="mt-8 mx-8 p-6 rounded shadow">


    <!-- Tabloyu çevreleyen ve kaydırma özelliğini sağlayan ana konteyner -->
    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md max-w-3xl mx-auto px-4 p-4">
        <div class="relative max-h-[70vh] overflow-y-auto"> <!-- Maksimum yükseklik ve dikey kaydırma -->
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-50 sticky top-0"> <!-- Başlıkların kaydırma sırasında sabit kalması için -->
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium text-gray-900 w-32">No</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900 w-64">İsim</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900 w-72">Açıklama</th>
                        <th scope="col" class="px-2 py-3 font-medium text-gray-900 w-32">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse ($departments as $department)
                        <tr class="hover:bg-gray-50">
                            <!-- No -->
                            <td class="px-4 py-4 w-32">{{ $loop->iteration }}</td>
                            <!-- İsim -->
                            <td class="px-6 py-4 w-64">{{ $department->name }}</td>
                            <!-- Açıklama -->
                            <td class="px-6 py-3 w-72 max-w-xs align-top">
                                @php
                                    $text = trim((string) ($department->description ?? ''));
                                    $words = $text === '' ? [] : explode(' ', $text);
                                    $shortText = implode(' ', array_slice($words, 0, 10));
                                    $isLong = count($words) > 10;
                                @endphp
                                @if ($isLong && !($expandedDescription[$department->id] ?? false))
                                    <div>{{ $shortText }}...</div>
                                    <a href="#" wire:click.prevent="toggleDescription({{ $department->id }})"
                                        class="block mt-2 text-blue-600 hover:underline">devamı</a>
                                @elseif ($isLong && ($expandedDescription[$department->id] ?? false))
                                    <div class="whitespace-pre-line">{{ $text }}</div>
                                    <a href="#" wire:click.prevent="toggleDescription({{ $department->id }})"
                                        class="block mt-2 text-blue-600 hover:underline">daha az</a>
                                @else
                                    <div>{{ $text }}</div>
                                @endif
                            </td>
                            <!-- İşlemler -->
                            <td class="px-2 py-4 w-32">
                                <div class="flex justify-start items-center gap-4 h-full">
                                    <button type="button" class="flex items-center justify-center w-10 h-10">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="flex items-center justify-center w-10 h-10">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Gösterilecek kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
