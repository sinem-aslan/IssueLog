<?php

namespace App\Livewire;

use App\Models\CallRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CallRecordsTable extends Component
{
    // Filtreleme durumu
    public $statusFilter = 'all';

    // Modal görünürlük durumları
    public $showAddModal = false;
    public $showEditModal = false;
    public $showNoteModal = false;
    public $showAdminDescriptionModal = false;

    // "gerekçe", "açıklama" ve "admin açıklaması" alanlarının genişletilip daraltılmasını kontrol etmek için
    public $expandedReason = [];
    public $expandedDescription = [];
    public $expandedAdminDescription = [];

    // Yeni kayıt modalı için veri modeli
    public $newRecord = [
        'call_date' => '',
        'student_tc' => '',
        'student_phone' => '',
        'reason' => '',
        'department_id' => '',
        'status' => 'pending',
        'is_urgent' => 0,
        'admin_description' => '',
    ];

    // Kayıt düzenleme modalı için veri modeli
    public $editRecord = [
        'id' => null,
        'student_tc' => '',
        'student_phone' => '',
        'reason' => '',
        'department_id' => '',
        'is_urgent' => 0,
        'admin_description' => '',
    ];

    // Not ekleme modalı için değişkenler
    public $noteText = '';
    public $noteRecordId = null;

    // Admin açıklama ekleme modalı için değişkenler
    public $adminDescriptionText = '';
    public $adminDescriptionRecordId = null;

    // --- Filtreleme ve Görünüm Metotları ---

    // filtreleme metodu
    public function filterStatus($status)
    {
        $this->statusFilter = $status;
    }

    // Arama gerekçesini devamı/daha işlemi
    public function toggleReason($id)
    {
        $this->expandedReason[$id] = !($this->expandedReason[$id] ?? false);
    }
    // Açıklama devamı/daha az işlemi
    public function toggleDescription($id)
    {
        $this->expandedDescription[$id] = !($this->expandedDescription[$id] ?? false);
    }

    // Admin açıklama devamı/daha az işlemi
    public function toggleAdminDescription($id)
    {
        $this->expandedAdminDescription[$id] = !($this->expandedAdminDescription[$id] ?? false);
    }

    // --- Modal Yönetim Metotları ---

    // Yeni kayıt modalını açarken formu sıfırlamak için
    public function openAddModal()
    {
        $this->resetErrorBag();
        $this->newRecord = [
            'call_date' => '',
            'student_tc' => '',
            'student_phone' => '',
            'reason' => '',
            'department_id' => '',
            'status' => 'pending',
            'is_urgent' => 0,
        ];
        $this->showAddModal = true;
    }

    // Edit modalını açar ve seçilen kaydın verilerini doldurur
    public function openEditModal($id)
    {
        $this->resetErrorBag(); // hata mesajlarını sıfırlar
        $record = \App\Models\CallRecord::find($id);
        $user = Auth::user();
        // Kullanıcı sadece kendi kaydını açabilir (admin hariç)
        if ($record && ($user->is_admin || $record->user_id === $user->id)) {
            $this->editRecord = [
                'id' => $record->id,
                'student_tc' => $record->student_tc,
                'student_phone' => $record->student_phone,
                'reason' => $record->reason,
                'department_id' => $record->department_id,
                'is_urgent' => $record->is_urgent ?? 0,
                'admin_description' => $record->admin_description ?? '',
                'edited_by' => $record->edited_by,
                'edited_at' => $record->edited_at,
                'editor' => $record->editor,
            ];
            $this->showEditModal = true;
        }
    }

    public $noteRecord = [];
    // Not ekleme modalını açar
    public function openNoteModal($id)
    {
        $record = CallRecord::find($id);
        if ($record) {
            $this->noteRecordId = $id;
            $this->noteText = $record->description ?? '';
            $this->noteRecord = [
            'description_changed_by' => $record->description_changed_by,
            'description_changed_at' => $record->description_changed_at,
            'descriptionChanger' => $record->descriptionChanger,
        ];
            $this->showNoteModal = true;
        }
    }

    // Admin açıklama modalını açar
    public function openAdminDescriptionModal($id)
    {
        $record = CallRecord::find($id);
        if ($record) {
            $this->adminDescriptionRecordId = $id;
            $this->adminDescriptionText = $record->admin_description ?? '';
            $this->showAdminDescriptionModal = true;
        }
    }

    // --- Veri İşleme Metotları ---

    // Yeni kayıt ekleme işlemi
    public function addRecord()
    {
        $this->resetErrorBag(); // hata mesajlarını sıfırla
        $validated = $this->validate([
            'newRecord.student_tc' => [
                'required',
                'digits:11',
                function ($attribute, $value, $fail) {
                    // TC algoritması
                    if (!preg_match('/^[1-9][0-9]{10}$/', $value)) {
                        $fail('TC Kimlik numarası 11 haneli ve ilk hanesi 0 olamaz.');
                        return;
                    }
                    $digits = str_split($value);
                    $odd = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
                    $even = $digits[1] + $digits[3] + $digits[5] + $digits[7];
                    $digit10 = (($odd * 7) - $even) % 10;
                    if ($digit10 != $digits[9]) {
                        $fail('TC Kimlik numarası geçersiz.');
                        return;
                    }
                    $sum = array_sum(array_slice($digits, 0, 10));
                    if ($sum % 10 != $digits[10]) {
                        $fail('TC Kimlik numarası geçersiz.');
                    }
                }
            ],
            // Telefon numarası doğrulaması
            'newRecord.student_phone' => [
                'required',
                'digits:10',
                function ($attribute, $value, $fail) {
                    if (substr($value, 0, 1) == '0') {
                        $fail('Telefon numarası 0 ile başlamamalı.');
                    }
                }
            ],
            'newRecord.reason' => 'required|string',
            'newRecord.department_id' => 'required|integer|exists:departments,id',
            'newRecord.is_urgent' => 'nullable|boolean',
            'newRecord.admin_description' => 'nullable|string',
        ], [
            'newRecord.student_tc.required' => 'Öğrenci T.C. Kimlik numarası zorunludur.',
            'newRecord.student_tc.digits' => 'TC Kimlik numarası 11 haneli olmalıdır.',
            'newRecord.student_phone.required' => 'Telefon numarası zorunludur.',
            'newRecord.student_phone.digits' => 'Telefon numarası 10 haneli olmalıdır.',
            'newRecord.reason.required' => 'Arama gerekçesi zorunludur.',
            'newRecord.department_id.required' => 'Birim seçimi zorunludur.',
        ]);

        $data = $validated['newRecord'];
        $data['call_date'] = now();
        $data['status'] = 'pending';
        $data['solution_date'] = null;
        $data['user_id'] = Auth::id();
        $data['is_urgent'] = !empty($this->newRecord['is_urgent']) ? 1 : 0;
        $data['admin_description'] = $this->newRecord['admin_description'];

        CallRecord::create($data);
        $this->reset('newRecord');
        $this->showAddModal = false;
    }

    // Kaydı günceller
    public function updateRecord()
    {
        $validated = $this->validate([
            'editRecord.student_tc' => 'required|string|max:20',
            'editRecord.student_phone' => 'required|string|max:20',
            'editRecord.reason' => 'required|string',
            'editRecord.department_id' => 'required|integer|exists:departments,id',
            'editRecord.admin_description' => 'nullable|string',
        ], [
            // hata mesajları
            'editRecord.student_tc.required' => 'Öğrenci T.C. Kimlik numarası zorunludur.',
            'editRecord.student_phone.required' => 'Telefon numarası zorunludur.',
            'editRecord.reason.required' => 'Arama gerekçesi zorunludur.',
            'editRecord.department_id.required' => 'Birim seçimi zorunludur.',
        ]);

        $record = \App\Models\CallRecord::find($this->editRecord['id']);
        $user = Auth::user();
        // Kullanıcı sadece kendi kaydını güncelleyebilir (admin hariç)
        if ($record && ($user->is_admin || $record->user_id === $user->id)) {
            $record->student_tc = $validated['editRecord']['student_tc'];
            $record->student_phone = $validated['editRecord']['student_phone'];
            $record->reason = $validated['editRecord']['reason'];
            $record->department_id = $validated['editRecord']['department_id'];
            $record->is_urgent = !empty($this->editRecord['is_urgent']) ? 1 : 0;
            $record->admin_description = $validated['editRecord']['admin_description'];
            // Düzenleme yapan kullanıcı ve zaman bilgisi
            $record->edited_by = $user->id;
            $record->edited_at = now();
            $record->save();
        }
        $this->showEditModal = false;
    }

    // Notu kaydeder
    public function saveNote()
    {
        if ($this->noteRecordId) {
            $record = CallRecord::find($this->noteRecordId);
            if ($record) {
                $record->description = $this->noteText;
                // Notu değiştiren kullanıcıyı ve zamanını kaydeder
                $record->description_changed_by = Auth::id();
                $record->description_changed_at = now();
                $record->save();
            }
        }
        $this->showNoteModal = false;
        $this->noteText = '';
        $this->noteRecordId = null;
    }

    // Admin açıklamasını kaydeder
    public function saveAdminDescription()
    {
        if ($this->adminDescriptionRecordId) {
            $record = CallRecord::find($this->adminDescriptionRecordId);
            if ($record) {
                $record->admin_description = $this->adminDescriptionText;
                $record->save();
            }
        }
        $this->showAdminDescriptionModal = false;
        $this->adminDescriptionText = '';
        $this->adminDescriptionRecordId = null;
    }

    // Kayıt durumu güncelleme işlemi
    public function updateStatus($recordId, $status)
    {
        $record = CallRecord::find($recordId);
        $user = Auth::user();
        if ($record) {
            // Eğer kayıt çözüldü ise ve kullanıcı admin değilse artık işlem yapamaz
            // sadece admin değişiklik yapabilir
            if ($record->status === 'resolved' && !$user->is_admin) {
                return;
            }
            $record->status = $status;
            $record->status_changed_by = $user->id;
            $record->status_changed_at = now();
            // Eğer durum 'çözüldü' ise çözüm tarihini güncellenir
            if ($status === 'resolved') {
                $record->solution_date = now();
            } else {
                $record->solution_date = null;
            }
            $record->save();
        }
    }

    public function updateDepartment($recordId, $departmentId)
    {
        $record = CallRecord::find($recordId);
        if ($record) {
            $record->department_id = $departmentId;
            $record->save();
        }
    }

    /**
     * Bileşenin görünümünü render eder.
     * Veritabanından çağrı kayıtlarını çeker ve görünüme gönderir.
     */
    // Görünümü render etme
    public function render()
    {
        $user = Auth::user();
        $query = CallRecord::query();

        // Kullanıcı rolüne göre temel sorguyu oluşturma
        if ($user->is_admin) {
            // Admin ise tüm kayıtları görebilir
        } elseif (!empty($user->department_id)) {
            $department = $user->department;
            if ($department && $department->name === 'Çalışan') {
                // 'Çalışan' departmanındaysa sadece kendi kayıtlarını görür
                $query->where('user_id', $user->id);
            } else {
                // Diğer departmanlardaysa kendi biriminin kayıtlarını görür
                $query->where('department_id', $user->department_id);
            }
        } else {
            // Bir departmana bağlı değilse sadece kendi kayıtlarını görür
            $query->where('user_id', $user->id);
        }

        // çözüldü olarak işaretlenen kayıtlar 7 gün sonra tablodan silinir
        $query->where(function ($q) {
            $q->where('status', '!=', 'resolved')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'resolved')
                       ->where('solution_date', '>', now()->subDays(7));
              });
        });

        // - İstatistiksel Sayımlar -

        // Toplam kayıt sayısı
        $totalCount = (clone $query)->count();
        $urgentCount = (clone $query)->where('is_urgent', 1)->where('status', '!=', 'resolved')->count();
        $resolvedCount = (clone $query)->where('status', 'resolved')->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $inProgressCount = (clone $query)->where('status', 'in_progress')->count();

        // Seçilen filtreye göre sorgu uygulama
        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'urgent') {
                // 'Acil' filtresi seçiliyse, is_urgent=1 olan ve henüz çözülmemiş kayıtları getir.
                $query->where('is_urgent', 1)->where('status', '!=', 'resolved');
            } else {
                // Diğer durumlar için doğrudan status filtresinin uygulanması
                $query->where('status', $this->statusFilter);
            }
        }

        // Sonuçları sıralama ve getirme
        $callRecords = $query
            ->select([
                'id',
                'call_date',
                'student_tc',
                'student_phone',
                'reason',
                'department_id',
                'status',
                'solution_date',
                'is_urgent',
                'description',
                'user_id',
                'admin_description',
                'status_changed_by',
                'status_changed_at'
            ])
            ->with(['user', 'department', 'statusChanger'])
            ->orderByRaw('(is_urgent = 1 AND status != "resolved") DESC')
            ->orderByRaw('(status = "resolved") ASC')
            ->orderBy('is_urgent', 'desc')
            ->orderBy('call_date', 'desc')
            ->get();


        // Çalışan rolündeki kullanıcılar yeni kayıt alanında 'Çalışan' departmanını göremez
        if (!$user->is_admin) {
            $departments = \App\Models\Department::where('name', '!=', 'Çalışan')->get();
        } else {
            $departments = \App\Models\Department::all();
        }

        return view('livewire.call-records-table', [
            'records' => $callRecords,
            'departments' => $departments,
            'totalCount' => $totalCount,
            'urgentCount' => $urgentCount,
            'resolvedCount' => $resolvedCount,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
        ]);
    }
}
