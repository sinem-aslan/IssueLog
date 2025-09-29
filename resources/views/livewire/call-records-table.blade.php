<div class="mt-8 mx-8 p-6 rounded shadow">
    <style>
        .urgent-row {
            border: 2px solid #b91c1c !important;
            /* acil kayıt çerçevesi */
        }
    </style>

    <!-- Kayıt İstatistikleri -->
    <div class="mb-4 flex flex-row gap-4 items-center">
        <div class="bg-gray-100 rounded-lg px-4 py-2 shadow text-gray-800 text-sm font-semibold cursor-pointer hover:ring hover:ring-blue-300"
            wire:click="filterStatus('all')">
            Toplam: <span class="font-bold text-blue-700">{{ $totalCount }}</span>
        </div>
        <div class="bg-red-100 rounded-lg px-4 py-2 shadow text-red-700 text-sm font-semibold cursor-pointer hover:ring hover:ring-red-300"
            wire:click="filterStatus('urgent')">
            Acil: <span class="font-bold">{{ $urgentCount }}</span>
        </div>
        <div class="bg-yellow-100 rounded-lg px-4 py-2 shadow text-yellow-700 text-sm font-semibold cursor-pointer hover:ring hover:ring-yellow-300"
            wire:click="filterStatus('pending')">
            Bekliyor: <span class="font-bold">{{ $pendingCount }}</span>
        </div>
        <div class="bg-blue-100 rounded-lg px-4 py-2 shadow text-blue-700 text-sm font-semibold cursor-pointer hover:ring hover:ring-blue-300"
            wire:click="filterStatus('in_progress')">
            İşlemde: <span class="font-bold">{{ $inProgressCount }}</span>
        </div>
        <div class="bg-green-100 rounded-lg px-4 py-2 shadow text-green-700 text-sm font-semibold cursor-pointer hover:ring hover:ring-green-300"
            wire:click="filterStatus('resolved')">
            Çözüldü: <span class="font-bold">{{ $resolvedCount }}</span>
        </div>
    </div>

    <!-- Kullanıcı Yetkileri -->
    @php
        $currentUser = Auth::user();
        $showActions =
            $currentUser->is_admin || ($currentUser->department && $currentUser->department->name === 'Çalışan');
    @endphp
    @if ($showActions)
        <!-- Yeni Kayıt Butonu -->
        <div class="flex justify-end mb-4">
            <button type="button" wire:click="$set('showAddModal', true)"
                class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700 transition">Yeni Kayıt</button>
        </div>
    @endif

    <!-- Tabloyu çevreleyen ve kaydırma özelliğini sağlayan ana konteyner -->
    <div class="overflow-hidden rounded-lg border border-gray-200 shadow-md">
        <div class="relative max-h-[70vh] overflow-y-auto  overflow-x-auto">
            <!-- Maksimum yükseklik ve dikey kaydırma -->
            <table class="border-collapse bg-white text-left text-sm text-gray-500"
                style="table-layout: fixed; min-width: 1800px;">
                <thead class="sticky top-0" style="background-color: #ccdbe6; z-index: 20;">
                    <!-- Tablo başlıkları -->
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium text-gray-900" style="width: 3%;">No</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 8%;">Çağrı Tarihi
                        </th>
                        @if ($currentUser->is_admin)
                            <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 10%;">Temsilci
                            </th>
                        @endif
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 10%;">Öğrenci
                            T.C.K.N</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 9%;">Telefon
                            Numarası</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 10%;">Arama
                            Gerekçesi</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 8%;">İlgili Birim
                        </th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap text-center font-medium text-gray-900"
                            style="width: 7%;">Durum</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap text-center font-medium text-gray-900"
                            style="width: 8%;">Çözüm Tarihi</th>
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 10%;">Çağrı Notu
                        </th>
                        @if ($currentUser->is_admin)
                            <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 10%;">Açıklama
                            </th>
                        @endif
                        <th scope="col" class="px-6 py-3 font-medium text-gray-900" style="width: 7%;">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-s border-t border-gray-100">
                    @forelse ($records as $record)
                        <!-- Acil olan kayıtlar için arkaplan renginin değiştirilmesi -->
                        <tr @php
if ($record->is_urgent && $record->status !== 'resolved') {
                                $rowClass = 'bg-red-100 urgent-row';
                            } elseif ($record->status === 'resolved') {
                                $rowClass = 'bg-green-100';
                            } elseif ($record->status === 'pending') {
                                $rowClass = 'bg-yellow-100';
                            } elseif ($record->status === 'in_progress') {
                                $rowClass = 'bg-blue-100';
                            } else {
                                $rowClass = '';
                            } @endphp
                            class="hover:bg-gray-50 {{ $rowClass }}">
                            <!-- No -->
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            <!-- Çağrı Tarihi -->
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-700">{{ $record->call_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $record->call_date->format('H:i') }}</div>
                            </td>
                            {{-- @php
                                $currentUser = Auth::user();
                                $showActions =
                                    $currentUser->is_admin ||
                                    ($currentUser->department && $currentUser->department->name === 'Çalışan');
                            @endphp --}}
                            <!-- Temsilci -->
                            @if ($currentUser->is_admin)
                                <td class="px-6 py-3">
                                    {{ $record->user ? $record->user->name . ' ' . ($record->user->surname ?? '') : 'Bilinmiyor' }}
                                </td>
                            @endif
                            <!-- Öğrenci T.C.K.N -->
                            <td class="px-6 py-3">{{ $record->student_tc ?: 'Bilinmiyor' }}</td>
                            <!-- Telefon Numarası -->
                            <td class="px-6 py-3">{{ $record->student_phone ?: 'Bilinmiyor' }}</td>
                            <!-- Arama Gerekçesi -->
                            <td class="px-6 py-3">
                                @php
                                    $words = explode(' ', $record->reason);
                                    $shortReason = implode(' ', array_slice($words, 0, 10));
                                    $isLong = count($words) > 10;
                                @endphp
                                <div style="min-height: 40px;">
                                    @if ($isLong && !($expandedReason[$record->id] ?? false))
                                        <span>{{ $shortReason }}...</span>
                                        <a href="#" wire:click.prevent="toggleReason({{ $record->id }})"
                                            class="ml-2 text-blue-600 hover:underline">devamı</a>
                                    @elseif ($isLong && ($expandedReason[$record->id] ?? false))
                                        <span class="whitespace-pre-line">{{ $record->reason }}</span>
                                        <a href="#" wire:click.prevent="toggleReason({{ $record->id }})"
                                            class="ml-2 text-blue-600 hover:underline">daha az</a>
                                    @else
                                        <span>{{ $record->reason }}</span>
                                    @endif
                                </div>
                            </td>
                            <!-- İlgili Birim -->
                            <td class="px-6 py-3">{{ $record->department?->name ?? 'Bilinmiyor' }}</td>
                            <!-- Durum -->
                            <td class="px-6 py-3 whitespace-nowrap text-center">
                                @if ($record->status == 'resolved' && !$currentUser->is_admin)
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold bg-green-500 text-white shadow">
                                        Çözüldü
                                    </span>
                                @else
                                    <select
                                        class="rounded-full px-3 py-1 text-xs font-semibold shadow focus:outline-none transition-all cursor-pointer
                                            @if ($record->status == 'resolved') bg-green-500 text-white
                                            @elseif($record->status == 'in_progress') bg-blue-500 text-white
                                            @else bg-yellow-500 text-white @endif"
                                        wire:change="updateStatus({{ $record->id }}, $event.target.value)"
                                        style="min-width: 100px;">
                                        <option value="pending" @if ($record->status == 'pending') selected @endif>
                                            Bekliyor</option>
                                        <option value="in_progress" @if ($record->status == 'in_progress') selected @endif>
                                            İşlemde</option>
                                        <option value="resolved" @if ($record->status == 'resolved') selected @endif>
                                            Çözüldü</option>
                                    </select>
                                @endif
                                @if ($currentUser->is_admin)
                                    @if ($record->status_changed_by)
                                        <!-- Son değişiklik yapan kişi bilgisi -->
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ optional($record->statusChanger)->name }}
                                            {{ optional($record->statusChanger)->surname }}
                                            <br>
                                            @if ($record->status_changed_at)
                                                ({{ \Carbon\Carbon::parse($record->status_changed_at)->format('d/m/Y H:i') }})
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <!-- Çözüm Tarihi -->
                            <td class="px-6 py-3 whitespace-nowrap text-center">
                                @if ($record->solution_date)
                                    <div class="font-medium text-gray-700">
                                        {{ \Carbon\Carbon::parse($record->solution_date)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($record->solution_date)->format('H:i') }}</div>
                                @endif
                            </td>
                            <!-- Çağrı Notu -->
                            <td class="px-6 py-3">
                                @php
                                    // Not metni boş ise boş bir stringe çevirilir
                                    $noteText = trim((string) ($record->description ?? ''));
                                    // Metin kelimelere ayrılır
                                    $noteWords = $noteText === '' ? [] : explode(' ', $noteText);
                                    // İlk 10 kelimeyi alınır
                                    $shortNote = implode(' ', array_slice($noteWords, 0, 10));
                                    // Metnin 10 kelimeden uzun olup olmadığını kontrol edilir
                                    $isNoteLong = count($noteWords) > 10;
                                @endphp
                                <div style="min-height: 40px;">
                                    @if ($noteText === '')
                                    @elseif ($isNoteLong && !($expandedDescription[$record->id] ?? false))
                                        <span>{{ $shortNote }}...</span>
                                        <a href="#" wire:click.prevent="toggleDescription({{ $record->id }})"
                                            class="ml-2 text-blue-600 hover:underline">devamı</a>
                                    @elseif ($isNoteLong && ($expandedDescription[$record->id] ?? false))
                                        <span class="whitespace-pre-line">{{ $noteText }}</span>
                                        <a href="#" wire:click.prevent="toggleDescription({{ $record->id }})"
                                            class="ml-2 text-blue-600 hover:underline">daha az</a>
                                    @else
                                        <span>{{ $noteText }}</span>
                                    @endif
                                </div>
                            </td>
                            @if ($currentUser->is_admin)
                                <!-- Açıklama -->
                                <td class="px-6 py-3">
                                    @php
                                        $adminText = trim((string) ($record->admin_description ?? ''));
                                        $adminWords = $adminText === '' ? [] : explode(' ', $adminText);
                                        $shortAdmin = implode(' ', array_slice($adminWords, 0, 10));
                                        $isAdminLong = count($adminWords) > 10;
                                    @endphp
                                    <div style="min-height: 40px;">
                                        @if ($adminText === '')
                                        @elseif ($isAdminLong && !($expandedAdminDescription[$record->id] ?? false))
                                            <span>{{ $shortAdmin }}...</span>
                                            <a href="#"
                                                wire:click.prevent="toggleAdminDescription({{ $record->id }})"
                                                class="ml-2 text-blue-600 hover:underline">devamı</a>
                                        @elseif ($isAdminLong && ($expandedAdminDescription[$record->id] ?? false))
                                            <span class="whitespace-pre-line">{{ $adminText }}</span>
                                            <a href="#"
                                                wire:click.prevent="toggleAdminDescription({{ $record->id }})"
                                                class="ml-2 text-blue-600 hover:underline">daha az</a>
                                        @else
                                            <span>{{ $adminText }}</span>
                                        @endif
                                    </div>
                            @endif
                            <!-- İşlemler -->
                            <td class="px-6 py-3" style="overflow: visible;">
                                <div class="flex justify-start items-center gap-4 h-full" style="overflow: visible;">
                                    @php
                                        $currentUser = Auth::user();
                                        $isAdmin = $currentUser->is_admin;
                                        $isResolved = $record->status === 'resolved';
                                        $isCalisan =
                                            $currentUser->department && $currentUser->department->name === 'Çalışan';
                                    @endphp

                                    @if ($isAdmin || !$isResolved)
                                        <div class="flex justify-start items-center gap-4 h-full"
                                            style="overflow: visible;">
                                            @if ($isAdmin)
                                                <!-- Yönetici: Düzenle, Not Ekle, Açıklama Ekle -->
                                                <div class="relative group">
                                                    <button type="button"
                                                        class="bg-blue-600 text-white rounded flex items-center justify-center w-6 h-6 hover:bg-blue-700 transition"
                                                        wire:click="openEditModal({{ $record->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <span
                                                        class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-blue-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">Düzenle</span>
                                                </div>
                                                <div class="relative group">
                                                    <button type="button"
                                                        class="bg-green-600 text-white rounded flex items-center justify-center w-6 h-6 hover:bg-green-700 transition"
                                                        wire:click="openNoteModal({{ $record->id }})">
                                                        <i class="fas fa-sticky-note"></i>
                                                    </button>
                                                    <span
                                                        class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-green-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">Not
                                                        Ekle</span>
                                                </div>
                                                <div class="relative group">
                                                    <button type="button"
                                                        class="bg-cyan-600 text-white rounded flex items-center justify-center w-6 h-6 hover:bg-cyan-700 transition"
                                                        wire:click="openAdminDescriptionModal({{ $record->id }})">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                    <span
                                                        class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-cyan-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">Açıklama
                                                        Ekle</span>
                                                </div>
                                            @elseif($isCalisan)
                                                <!-- Çalışan: Sadece Düzenle -->
                                                <div class="relative group">
                                                    <button type="button"
                                                        class="bg-blue-600 text-white rounded flex items-center justify-center w-6 h-6 hover:bg-blue-700 transition"
                                                        wire:click="openEditModal({{ $record->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <span
                                                        class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-blue-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">Düzenle</span>
                                                </div>
                                            @else
                                                <!-- Diğer birimler: Sadece Not Ekle -->
                                                <div class="relative group">
                                                    <button type="button"
                                                        class="bg-green-600 text-white rounded flex items-center justify-center w-6 h-6 hover:bg-green-700 transition"
                                                        wire:click="openNoteModal({{ $record->id }})">
                                                        <i class="fas fa-sticky-note"></i>
                                                    </button>
                                                    <span
                                                        class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 bg-green-600 text-white px-2 py-1 rounded text-xs whitespace-nowrap z-50 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">Not
                                                        Ekle</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">Gösterilecek kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
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
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Öğrenci T.C.
                                                Kimlik
                                                Numarası</label>
                                            <input type="text" wire:model.defer="newRecord.student_tc"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            <p class="text-xs text-gray-500 mt-1">11 haneli sayısal bir değer
                                                olmalıdır.</p>
                                            @error('newRecord.student_tc')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Arama
                                                Gerekçesi</label>
                                            <textarea wire:model.defer="newRecord.reason" rows="8"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                                            @error('newRecord.reason')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="md:col-span-1 flex flex-col gap-6">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700 block mb-1">Telefon
                                                Numarası</label>
                                            <input type="tel" wire:model.defer="newRecord.student_phone"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                                placeholder="5xx xxx xx xx">
                                            <p class="text-xs text-gray-500 mt-1">10 haneli olmalı, 0 ile başlamamalı.
                                            </p>
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
                                                    <option value="{{ $department->id }}">{{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('newRecord.department_id')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Acil Checkbox -->
                                        <div>
                                            <label class="inline-flex items-center mt-2">
                                                <input type="checkbox" wire:model="newRecord.is_urgent"
                                                    class="form-checkbox h-5 w-5 text-red-600">
                                                <span class="ml-2 text-sm text-gray-700"
                                                    style="color: red">ACİL</span>
                                            </label>
                                            <p class="text-xs text-gray-500 mt-1">Acil olan çağrılar için bu seçenek
                                                işaretlenmelidir.</p>
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

            <!-- Düzenleme Modalı (admin ve çalışanlar) -->
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
                                        <!-- Acil Checkbox -->
                                        <div>
                                            <label class="inline-flex items-center mt-2">
                                                <input type="checkbox" wire:model.defer="editRecord.is_urgent"
                                                    value="1" class="form-checkbox h-5 w-5 text-red-600">
                                                <span class="ml-2 text-sm text-gray-700"
                                                    style="color: red">ACİL</span>
                                            </label>
                                            <p class="text-xs text-gray-500 mt-1">Acil olan çağrılar için bu seçenek
                                                işaretlenmelidir.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Son Düzenleyen Bilgisi -->
                                @if (!empty($editRecord['edited_by']))
                                    <div class="text-xs text-gray-500 mt-2 text-left pr-4">
                                        Son değişiklik:
                                        {{ optional($editRecord['editor'])->name }}
                                        {{ optional($editRecord['editor'])->surname }}
                                        @if (!empty($editRecord['edited_at']))
                                            ({{ \Carbon\Carbon::parse($editRecord['edited_at'])->format('d/m/Y H:i') }})
                                        @endif
                                    </div>
                                @endif
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

            <!-- Çağrı Notu Ekleme Modalı (admin ve birimler) -->
            @if ($showNoteModal)
                <div class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(4px);">
                    <div class="bg-white rounded-xl shadow-2xl w-full animate-fade-in flex flex-col overflow-hidden"
                        style="max-width: 700px; margin: auto;">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800">Çağrıya Not Ekle</h3>
                            <button type="button" wire:click="$set('showNoteModal', false)"
                                class="text-gray-400 rounded-full p-1 hover:bg-gray-100 hover:text-gray-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="saveNote">
                            <div class="p-6">
                                <textarea wire:model.defer="noteText" rows="5"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Notunuzu yazınız..."></textarea>
                                <!-- Notu son düzenleyen bilgisi -->
                                @if (!empty($noteRecord['description_changed_by']))
                                    <div class="text-xs text-gray-500 mt-2 text-left pr-4">
                                        Son değişiklik:
                                        {{ optional($noteRecord['descriptionChanger'])->name }}
                                        {{ optional($noteRecord['descriptionChanger'])->surname }}
                                        @if (!empty($noteRecord['description_changed_at']))
                                            ({{ \Carbon\Carbon::parse($noteRecord['description_changed_at'])->format('d/m/Y H:i') }})
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="flex justify-end gap-3 bg-gray-50 p-4 border-t border-gray-200">
                                <button type="button" wire:click="$set('showNoteModal', false)"
                                    class="bg-white text-gray-800 border border-gray-300 rounded-lg px-6 py-2 font-semibold hover:bg-gray-100 transition">Kapat</button>
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 transition text-white rounded-lg px-6 py-2 font-semibold shadow">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Admin Açıklama Ekleme Modalı -->
            @if ($showAdminDescriptionModal)
                <div class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(4px);">
                    <div class="bg-white rounded-xl shadow-2xl w-full animate-fade-in flex flex-col overflow-hidden"
                        style="max-width: 700px; margin: auto;">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800">Açıklama Ekle</h3>
                            <button type="button" wire:click="$set('showAdminDescriptionModal', false)"
                                class="text-gray-400 rounded-full p-1 hover:bg-gray-100 hover:text-gray-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="saveAdminDescription">
                            <div class="p-6">
                                <textarea wire:model.defer="adminDescriptionText" rows="5"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Açıklamanızı yazınız..."></textarea>
                            </div>
                            <div class="flex justify-end gap-3 bg-gray-50 p-4 border-t border-gray-200">
                                <button type="button" wire:click="$set('showAdminDescriptionModal', false)"
                                    class="bg-white text-gray-800 border border-gray-300 rounded-lg px-6 py-2 font-semibold hover:bg-gray-100 transition">Kapat</button>
                                <button type="submit"
                                    class="bg-cyan-600 hover:bg-cyan-700 transition text-white rounded-lg px-6 py-2 font-semibold shadow">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
