<div class="mt-8 mx-8 p-6 rounded shadow">

    <!-- Yeni Kayıt Butonu -->
    <div class="flex justify-end mb-4">
        <div class="flex justify-between items-center w-full mb-4">
            <div class="flex flex-col items-start">
                <span class="text-sm font-semibold text-gray-700">Toplam Kullanıcı: <span
                        class="text-blue-600">{{ $totalCount }}</span></span>
                <span class="text-xs mt-1"><span class="text-green-600 font-semibold">Aktif:</span> {{ $activeCount }}
                    &nbsp; <span class="text-red-600 font-semibold">Pasif:</span> {{ $passiveCount }}</span>
            </div>
            <button type="button" wire:click="$set('showAddModal', true)"
                class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700 transition">Yeni Kayıt</button>
        </div>
    </div>

    <!-- Tabloyu çevreleyen ve kaydırma özelliğini sağlayan ana konteyner -->
    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md">
        <div class="relative max-h-[70vh] overflow-y-auto"> <!-- Maksimum yükseklik ve dikey kaydırma -->
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="sticky top-0" style="background-color: #ccdbe6; z-index: 20;">
                    <!-- Başlıkların kaydırma sırasında sabit kalması için -->
                    <tr>
                        <th scope="col" class="px-4 py-4 font-medium text-gray-900 w-16">No </th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900 w-40">İsim</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900 w-40">Soyisim</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900 w-48">E-posta</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900 w-40">Birim</th>
                        <th scope="col" class="px-6 py-4 font-medium text-gray-900 w-56">Açıklama</th>
                        <th scope="col" class="px-4 py-4 font-medium text-gray-900 w-24">Durum</th>
                        <th scope="col" class="px-2 py-4 font-medium text-gray-900 w-16">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <!-- No -->
                            <td class="px-4 py-4 w-16">{{ $loop->iteration }}</td>
                            <!-- İsim -->
                            <td class="px-6 py-4 w-40">
                                <div class="font-medium text-gray-700">{{ $user->name }}</div>
                                @if ($user->is_admin)
                                    <span class="block text-xs text-gray-500 mt-1 font-normal">Yönetici</span>
                                @else
                                    <span class="block text-xs text-gray-500 mt-1 font-normal">Kullanıcı</span>
                                @endif
                            </td>
                            <!-- Soyisim -->
                            <td class="px-6 py-4 w-40">{{ $user->surname }}</td>
                            <!-- E-posta -->
                            <td class="px-6 py-4 w-48">{{ $user->email }}</td>
                            <!-- Birim -->
                            <td class="px-6 py-4 w-40">{{ $user->department?->name ?? 'Bilinmiyor' }}</td>
                            <!-- Açıklama -->
                            <td class="px-6 py-4 w-56 max-w-xs align-top">
                                @php
                                    $text = trim((string) ($user->description ?? ''));
                                    $words = $text === '' ? [] : explode(' ', $text);
                                    $shortText = implode(' ', array_slice($words, 0, 10));
                                    $isLong = count($words) > 10;
                                @endphp
                                @if ($isLong && !($expandedDescription[$user->id] ?? false))
                                    <div>{{ $shortText }}...</div>
                                    <a href="#" wire:click.prevent="toggleDescription({{ $user->id }})"
                                        class="block mt-2 text-blue-600 hover:underline">devamı</a>
                                @elseif ($isLong && ($expandedDescription[$user->id] ?? false))
                                    <div class="whitespace-pre-line">{{ $text }}</div>
                                    <a href="#" wire:click.prevent="toggleDescription({{ $user->id }})"
                                        class="block mt-2 text-blue-600 hover:underline">daha az</a>
                                @else
                                    <div>{{ $text }}</div>
                                @endif
                            </td>
                            <!-- Durum -->
                            <td class="px-4 py-4 w-24">{{ $user->is_active == 1 ? 'Aktif' : 'Pasif' }}</td>
                            <!-- İşlemler -->
                            <td class="px-2 py-4 w-16">
                                <div class="flex justify-start items-center gap-4 h-full" style="position:relative;">
                                    <div class="relative group">
                                        <button type="button"
                                            class="bg-blue-600 text-white rounded flex items-center justify-center w-6 h-6 hover:bg-blue-700 transition"
                                            wire:click="openEditModal({{ $user->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <span
                                            class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-blue-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                            Düzenle
                                        </span>
                                    </div>
                                    <div class="relative group">
                                        <button type="button" wire:click="toggleActive({{ $user->id }})"
                                            class="flex items-center justify-center w-7 h-7 rounded transition duration-150"
                                            style="background-color: {{ $user->is_active == 1 ? '#22c55e' : '#ef4444' }}; color: white;">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                        <span
                                            class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-gray-800 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                            {{ $user->is_active == 1 ? 'Pasif yap' : 'Aktif yap' }}
                                        </span>
                                    </div>
                                    <!-- Sil butonu -->
                                    {{-- <button type="button" wire:click="deleteUser({{ $user->id }})"
                                        class="flex items-center justify-center w-7 h-7 rounded transition duration-150"
                                        style="background-color: #ef4444; color: white;"
                                        onmouseover="this.nextElementSibling.style.display='block'"
                                        onmouseout="this.nextElementSibling.style.display='none'">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <span
                                        style="display:none; position:absolute; left:50%; transform:translateX(-50%); bottom:110%; background:#dc2626; color:white; padding:4px 10px; border-radius:6px; font-size:12px; white-space:nowrap; z-index:50; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                                        Sil
                                    </span> --}}
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

            <!-- Yeni Kullanıcı Ekle Modalı -->
            @if ($showAddModal)
                <div class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(4px);">
                    <div class="bg-white rounded-xl shadow-2xl w-full animate-fade-in flex flex-col overflow-hidden"
                        style="max-width: 700px; margin: auto;">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800">Yeni Kullanıcı Ekle</h3>
                            <button type="button" wire:click="$set('showAddModal', false)"
                                class="text-gray-400 rounded-full p-1 hover:bg-gray-100 hover:text-gray-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="addUser">
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="flex flex-col gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">İsim</label>
                                            <input type="text" wire:model.defer="newUser.name"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            @error('newUser.name')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Soyisim</label>
                                            <input type="text" wire:model.defer="newUser.surname"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            @error('newUser.surname')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">E-posta</label>
                                            <input type="email" wire:model.defer="newUser.email"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            @error('newUser.email')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Şifre</label>
                                            <input type="password" wire:model.defer="newUser.password"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            @error('newUser.password')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Birim</label>
                                            <select wire:model.defer="newUser.department_id"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                                <option value="">Birim Seçiniz</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('newUser.department_id')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Yönetici
                                                mi?</label>
                                            <input type="checkbox" wire:model.defer="newUser.is_admin"
                                                class="mr-2 align-middle">
                                            <span class="text-xs text-gray-500">Kullanıcı yönetici olarak eklensin
                                                mi?</span>
                                            @error('newUser.is_admin')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="text-sm font-medium text-gray-700 block mb-1">Açıklama</label>
                                            <textarea wire:model.defer="newUser.description" rows="4"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                                            @error('newUser.description')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 bg-gray-50 p-4 border-t border-gray-200">
                                <button type="button" wire:click="$set('showAddModal', false)"
                                    class="bg-white text-gray-800 border border-gray-300 rounded-lg px-6 py-2 font-semibold hover:bg-gray-100 transition">Kapat</button>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 transition text-white rounded-lg px-6 py-2 font-semibold shadow">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif


            <!-- Kullanıcı Düzenle Modalı -->
            @if ($showEditModal)
                <div class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(4px);">
                    <div class="bg-white rounded-xl shadow-2xl w-full animate-fade-in flex flex-col overflow-hidden"
                        style="max-width: 700px; margin: auto;">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800">Kullanıcıyı Düzenle</h3>
                            <button type="button" wire:click="$set('showEditModal', false)"
                                class="text-gray-400 rounded-full p-1 hover:bg-gray-100 hover:text-gray-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="updateUser">
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="flex flex-col gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">İsim</label>
                                            <input type="text" wire:model.defer="editUser.name"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            @error('editUser.name')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Soyisim</label>
                                            <input type="text" wire:model.defer="editUser.surname"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            @error('editUser.surname')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">E-posta</label>
                                            <input type="email" wire:model.defer="editUser.email"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            @error('editUser.email')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Şifre
                                                (değiştirmek için doldurun)</label>
                                            <input type="password" wire:model.defer="editUser.password"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            @error('editUser.password')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Birim</label>
                                            <select wire:model.defer="editUser.department_id"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                                <option value="">Birim Seçiniz</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('editUser.department_id')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Yönetici
                                                mi?</label>
                                            <input type="checkbox" wire:model.defer="editUser.is_admin"
                                                class="mr-2 align-middle">
                                            <span class="text-xs text-gray-500">Kullanıcı yönetici olarak eklensin
                                                mi?</span>
                                            @error('editUser.is_admin')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="text-sm font-medium text-gray-700 block mb-1">Açıklama</label>
                                            <textarea wire:model.defer="editUser.description" rows="4"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                                            @error('editUser.description')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 bg-gray-50 p-4 border-t border-gray-200">
                                <button type="button" wire:click="$set('showEditModal', false)"
                                    class="bg-white text-gray-800 border border-gray-300 rounded-lg px-6 py-2 font-semibold hover:bg-gray-100 transition">Kapat</button>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 transition text-white rounded-lg px-6 py-2 font-semibold shadow">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
