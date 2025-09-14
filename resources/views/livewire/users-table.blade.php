<div class="mt-8 mx-8 p-6 rounded shadow">
    <!-- Tabloyu çevreleyen ve kaydırma özelliğini sağlayan ana konteyner -->
    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md">
        <div class="relative max-h-[70vh] overflow-y-auto"> <!-- Maksimum yükseklik ve dikey kaydırma -->
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-50 sticky top-0"> <!-- Başlıkların kaydırma sırasında sabit kalması için -->
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium text-gray-900 w-16">No</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900 w-48">İsim</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900 w-40">Soyisim</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900 w-56">E-posta</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900 w-40">Birim</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900 w-40">Açıklama</th>
                        <th scope="col" class="px-4 py-3 font-medium text-gray-900 w-24">Durum</th>
                        <th scope="col" class="px-2 py-3 font-medium text-gray-900 w-16">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <!-- No -->
                            <td class="px-4 py-3 w-16">{{ $loop->iteration }}</td>
                            <!-- İsim -->
                            <td class="px-6 py-3 w-48">
                                <div class="font-medium text-gray-700">{{ $user->name }}</div>
                                @if ($user->is_admin)
                                    <span class="block text-xs text-gray-500 mt-1 font-normal">Yönetici</span>
                                @else
                                    <span class="block text-xs text-gray-500 mt-1 font-normal">Kullanıcı</span>
                                @endif
                            </td>
                            <!-- Soyisim -->
                            <td class="px-6 py-3 w-40">{{ $user->surname }}</td>
                            <!-- E-posta -->
                            <td class="px-6 py-3 w-56">{{ $user->email }}</td>
                            <!-- Birim -->
                            <td class="px-6 py-3 w-40">{{ $user->department?->name ?? 'Bilinmiyor' }}</td>
                            <!-- Açıklama -->
                            <td class="px-6 py-3 max-w-xs align-top">
                                @php
                                    $text = trim((string)($user->description ?? ''));
                                    $words = $text === '' ? [] : explode(' ', $text);
                                    $shortText = implode(' ', array_slice($words, 0, 10));
                                    $isLong = count($words) > 10;
                                @endphp
                                @if ($isLong && !($expandedDescription[$user->id] ?? false))
                                    <div>{{ $shortText }}...</div>
                                    <a href="#" wire:click.prevent="toggleDescription({{ $user->id }})" class="block mt-2 text-blue-600 hover:underline">devamı</a>
                                @elseif ($isLong && ($expandedDescription[$user->id] ?? false))
                                    <div class="whitespace-pre-line">{{ $text }}</div>
                                    <a href="#" wire:click.prevent="toggleDescription({{ $user->id }})" class="block mt-2 text-blue-600 hover:underline">daha az</a>
                                @else
                                    <div>{{ $text }}</div>
                                @endif
                            </td>
                            <!-- Durum -->
                            <td class="px-4 py-3 w-24">{{ $user->is_active == 1 ? 'Aktif' : 'Pasif' }}</td>
                            <!-- İşlemler -->
                            <td class="px-2 py-3 w-16">
                                <div class="flex justify-start items-center gap-4 h-full">
                                    <button type="button" class="flex items-center justify-center w-7 h-7">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="flex items-center justify-center w-7 h-7">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                    <button type="button" class="flex items-center justify-center w-7 h-7">
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
