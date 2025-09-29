<div class="mt-8 mx-8 p-6 rounded shadow">
    <!-- Tablo üstü istatistikler ve Yeni Kayıt Butonu -->
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-gray-700">
            <span class="font-semibold">Toplam Birim:</span> <span class="text-blue-600">{{ $departmentCount }}</span><br>
            <span class="font-semibold">Birimlerdeki Toplam Kişi:</span> <span
                class="text-green-600">{{ $userCount }}</span>
        </div>
        <button type="button" wire:click="$set('showAddModal', true)"
            class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700 transition">Yeni Kayıt</button>
    </div>

    <!-- Tabloyu çevreleyen ve kaydırma özelliğini sağlayan ana konteyner -->
    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md">
        <div class="relative max-h-[70vh] overflow-y-auto"> <!-- Maksimum yükseklik ve dikey kaydırma -->
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="sticky top-0" style="background-color: #ccdbe6; z-index: 20;">
                    <!-- Başlıkların kaydırma sırasında sabit kalması için -->
                    <tr>
                        <th scope="col" class="px-4 py-4 font-medium text-gray-900" style="width: 5%;">No</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900" style="width: 15%;">Eklenme
                            Tarihi</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900" style="width: 15%;">İsim</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900" style="width: 10%;">Kişi Sayısı
                        </th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900" style="width: 10%;">Aktif/Pasif
                        </th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900" style="width: 25%;">Açıklama</th>
                        <th scope="col" class="px-2 py-4 font-medium text-gray-900" style="width: 15%;">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse ($departments as $department)
                        <tr class="hover:bg-gray-50">
                            <!-- No -->
                            <td class="px-4 py-4">{{ $loop->iteration }}</td>
                            <!-- Eklenme Tarihi -->
                            <td class="px-6 py-4">
                                @if ($department->created_at)
                                    <div class="font-medium text-gray-700">
                                        {{ $department->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $department->created_at->format('H:i') }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <!-- İsim -->
                            <td class="px-6 py-4">{{ $department->name }}</td>
                            <!-- Kişi Sayısı -->
                            <td class="px-6 py-4">{{ $userCounts[$department->id] ?? 0 }}</td>
                            <!-- Aktif/Pasif -->
                            <td class="px-6 py-4">
                                <span class="text-green-700 font-semibold"
                                    style="color: green;">{{ $activeCounts[$department->id] ?? 0 }}</span>
                                /
                                <span class="text-red-700 font-semibold"
                                    style="color: red;">{{ $passiveCounts[$department->id] ?? 0 }}</span>
                            </td>
                            <!-- Açıklama -->
                            <td class="px-6 py-4">
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
                            <td class="px-2 py-4">
                                <div class="flex justify-start items-center gap-4 h-full">
                                    <div class="relative group">
                                        <button type="button"
                                            class="bg-blue-600 text-white rounded flex items-center justify-center w-6 h-6 hover:bg-blue-700 transition"
                                            wire:click="openEditModal({{ $department->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <span
                                            class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-blue-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                            Düzenle
                                        </span>
                                    </div>

                                    <!-- Sil butonu -->
                                    <div class="relative group">
                                        <button type="button" wire:click="deleteDepartment({{ $department->id }})"
                                            class="flex items-center justify-center w-7 h-7 rounded transition duration-150"
                                            style="background-color: #ef4444; color: white;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <span
                                            class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-red-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                            Sil
                                        </span>
                                    </div>
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


            <!-- Yeni birim Ekle Modalı -->
            @if ($showAddModal)
                <div class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(4px);">
                    <div class="bg-white rounded-xl shadow-2xl w-full animate-fade-in flex flex-col overflow-hidden"
                        style="max-width: 700px; margin: auto;">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800">Yeni Birim Ekle</h3>
                            <button type="button" wire:click="$set('showAddModal', false)"
                                class="text-gray-400 rounded-full p-1 hover:bg-gray-100 hover:text-gray-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="addDepartment">
                            <div class="p-6 flex flex-col gap-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Birim Adı</label>
                                    <input type="text" wire:model.defer="newDepartment.name"
                                        class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
                                    @error('newDepartment.name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Açıklama</label>
                                    <input type="text" wire:model.defer="newDepartment.description"
                                        class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
                                    @error('newDepartment.description')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex justify-end mt-6">
                                    <button type="button" wire:click="$set('showAddModal', false)"
                                        class="inline-flex items-center justify-center bg-gray-100 text-gray-700 border border-gray-300 rounded px-4 py-1 text-sm font-semibold hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 transition mr-2">Kapat</button>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center bg-blue-600 text-white rounded px-4 py-1 text-sm font-semibold shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">Kaydet</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- birim Düzenle Modalı -->
            @if ($showEditModal)
                <div class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(4px);">
                    <div class="bg-white rounded-xl shadow-2xl w-full animate-fade-in flex flex-col overflow-hidden"
                        style="max-width: 700px; margin: auto;">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800">Birim Adını Düzenle</h3>
                            <button type="button" wire:click="$set('showEditModal', false)"
                                class="text-gray-400 rounded-full p-1 hover:bg-gray-100 hover:text-gray-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="updateDepartment">
                            <div class="p-6 flex flex-col gap-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Birim Adı</label>
                                    <input type="text" wire:model.defer="editDepartment.name"
                                        class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
                                    @error('editDepartment.name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Açıklama</label>
                                    <input type="text" wire:model.defer="editDepartment.description"
                                        class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
                                    @error('editDepartment.description')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                    <!-- Son Düzenleyen Bilgisi -->
                                    @if (!empty($editDepartment['editor_name']) || !empty($editDepartment['updated_at']))
                                        <div class="text-xs text-gray-500 mt-2 text-left pr-4">
                                            Son değişiklik:
                                            {{ $editDepartment['editor_name'] ?? '-' }}
                                            @if (!empty($editDepartment['updated_at']))
                                                ({{ \Carbon\Carbon::parse($editDepartment['updated_at'])->format('d/m/Y H:i') }})
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="button" wire:click="$set('showEditModal', false)"
                                        class="inline-flex items-center justify-center bg-gray-100 text-gray-700 border border-gray-300 rounded px-4 py-1 text-sm font-semibold hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 transition mr-2">Kapat</button>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center bg-blue-600 text-white rounded px-4 py-1 text-sm font-semibold shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">Kaydet</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
