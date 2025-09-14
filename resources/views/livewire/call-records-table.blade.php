<div class="mt-8 mx-8 p-6 rounded shadow">

    <!-- Yeni Kayıt Butonu -->
    <div class="flex justify-end mb-4">
        <button type="button" wire:click="$set('showAddModal', true)"
            class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700 transition">Yeni Kayıt</button>
    </div>

    <!-- Yeni Kayıt Modalı -->
    @if ($showAddModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4"
            style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(4px);">

            <div class="bg-white rounded-xl shadow-2xl w-full animate-fade-in flex flex-col overflow-hidden"
                style="max-width: 700px; margin: auto;">

                <div class="flex items-center justify-between p-5 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800">Yeni Kayıt Ekle</h3>
                    <button type="button" wire:click="$set('showAddModal', false)"
                        class="text-gray-400 rounded-full p-1 hover:bg-gray-100 hover:text-gray-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="addRecord">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                            <div class="md:col-span-2 flex flex-col gap-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Öğrenci T.C. Kimlik
                                        Numarası</label>
                                    <input type="text" wire:model.defer="newRecord.student_tc"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <p class="text-xs text-gray-500 mt-1">11 haneli sayısal bir değer olmalıdır.</p>
                                    @error('newRecord.student_tc')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Arama Gerekçesi</label>
                                    <textarea wire:model.defer="newRecord.reason" rows="8"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                                    @error('newRecord.reason')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="md:col-span-1 flex flex-col gap-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Telefon Numarası</label>
                                    <input type="text" wire:model.defer="newRecord.student_phone"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <p class="text-xs text-gray-500 mt-1">Örn: 5xx xxx xx xx</p>
                                    @error('newRecord.student_phone')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700 block mb-1">Yönlendirilen
                                        Birim</label>
                                    <select wire:model.defer="newRecord.department_id"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        <option value="">Birim Seçiniz</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newRecord.department_id')
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
                            <!-- Çağrı Tarihi -->
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-700">{{ $record->call_date->format('d/m/Y') }}</div>
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
                                <select
                                    class="rounded-full px-3 py-1 text-xs font-semibold shadow focus:outline-none transition-all cursor-pointer
                                        @if ($record->status == 'resolved') bg-green-500 text-white
                                        @elseif($record->status == 'in_progress') bg-blue-500 text-white
                                        @else bg-orange-500 text-white @endif"
                                    wire:change="updateStatus({{ $record->id }}, $event.target.value)"
                                    style="min-width: 100px;">
                                    <option value="pending" @if ($record->status == 'pending') selected @endif>Bekliyor
                                    </option>
                                    <option value="in_progress" @if ($record->status == 'in_progress') selected @endif>
                                        İşlemde</option>
                                    <option value="resolved" @if ($record->status == 'resolved') selected @endif>Çözüldü
                                    </option>
                                </select>
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
                                    <div class="relative">

                                        <!-- Düzenle butonu -->
                                        <button type="button" class="flex items-center justify-center w-8 h-8"
                                            style="background:rgb(230, 230, 235)"
                                            wire:click="openEditModal({{ $record->id }})"
                                            onmouseover="this.nextElementSibling.style.display='block'"
                                            onmouseout="this.nextElementSibling.style.display='none'">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <span
                                            style="display:none; position:absolute; left:50%; transform:translateX(-50%); bottom:110%; background:#1f2937; color:white; padding:4px 10px; border-radius:6px; font-size:12px; white-space:nowrap; z-index:50; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                                            Düzenle
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">Gösterilecek kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Düzenleme Modalı -->
            @if ($showEditModal)
                <div class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(4px);">
                    <div class="bg-white rounded-xl shadow-2xl w-full animate-fade-in flex flex-col overflow-hidden"
                        style="max-width: 700px; margin: auto;">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800">Kaydı Güncelle</h3>
                            <button type="button" wire:click="$set('showEditModal', false)"
                                class="text-gray-400 rounded-full p-1 hover:bg-gray-100 hover:text-gray-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="updateRecord">
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    <div class="md:col-span-2 flex flex-col gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Öğrenci T.C.
                                                Kimlik Numarası</label>
                                            <input type="text" wire:model.defer="editRecord.student_tc"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            <p class="text-xs text-gray-500 mt-1">11 haneli sayısal bir değer
                                                olmalıdır.</p>
                                            @error('editRecord.student_tc')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Arama
                                                Gerekçesi</label>
                                            <textarea wire:model.defer="editRecord.reason" rows="8"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                                            @error('editRecord.reason')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="md:col-span-1 flex flex-col gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Telefon
                                                Numarası</label>
                                            <input type="text" wire:model.defer="editRecord.student_phone"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            <p class="text-xs text-gray-500 mt-1">Örn: 5xx xxx xx xx</p>
                                            @error('editRecord.student_phone')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Yönlendirilen
                                                Birim</label>
                                            <select wire:model.defer="editRecord.department_id"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                                <option value="">Birim Seçiniz</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('editRecord.department_id')
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
                                    class="bg-blue-600 hover:bg-blue-700 transition text-white rounded-lg px-6 py-2 font-semibold shadow">Güncelle</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
