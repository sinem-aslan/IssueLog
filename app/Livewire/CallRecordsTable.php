<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\CallRecord;

class CallRecordsTable extends Component
{
    // Not ekleme modalı için değişkenler
    public $showNoteModal = false;
    public $noteText = '';
    public $noteRecordId = null;

    // Not ekleme modalını açar
    public function openNoteModal($id)
    {
        $record = CallRecord::find($id);
        if ($record) {
            $this->noteRecordId = $id;
            $this->noteText = $record->description ?? '';
            $this->showNoteModal = true;
        }
    }

    // Notu kaydeder
    public function saveNote()
    {
        if ($this->noteRecordId) {
            $record = CallRecord::find($this->noteRecordId);
            if ($record) {
                $record->description = $this->noteText;
                $record->save();
            }
        }
        $this->showNoteModal = false;
        $this->noteText = '';
        $this->noteRecordId = null;
    }
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
    public $showAddModal = false;

    // Yeni kayıt verileri için varsayılan değerler
    public $newRecord = [
        'call_date' => '',
        'student_tc' => '',
        'student_phone' => '',
        'reason' => '',
        'department_id' => '',
        'status' => 'pending',
        'is_urgent' => 0,
    ];

    public $showEditModal = false;
    // Düzenleme için kayıt verileri
    public $editRecord = [
        'id' => null,
        'student_tc' => '',
        'student_phone' => '',
        'reason' => '',
        'department_id' => '',
        'is_urgent' => 0,
    ];

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
            ];
            $this->showEditModal = true;
        }
    }

    // Kaydı günceller
    public function updateRecord()
    {
        $validated = $this->validate([
            'editRecord.student_tc' => 'required|string|max:20',
            'editRecord.student_phone' => 'required|string|max:20',
            'editRecord.reason' => 'required|string',
            'editRecord.department_id' => 'required|integer|exists:departments,id',
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
            $record->save();
        }
        $this->showEditModal = false;
    }

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

        CallRecord::create($data);
        $this->reset('newRecord');
        $this->showAddModal = false;
    }

    // Kayıt durumu güncelleme işlemi
    public function updateStatus($recordId, $status)
    {
        $record = CallRecord::find($recordId);
        if ($record) {
            $record->status = $status;
            // Eğer durum 'çözüldü' ise çözüm tarihini güncellenir
            if ($status === 'resolved') {
                $record->solution_date = now();
            } else {
                $record->solution_date = null;
            }
            $record->save();
        }
    }
    // Arama gerekçesini genişletme/gizleme işlemi
    public $expandedReason = [];
    public $expandedDescription = [];
    public function toggleReason($id)
    {
        $this->expandedReason[$id] = !($this->expandedReason[$id] ?? false);
    }
    public function toggleDescription($id)
    {
        $this->expandedDescription[$id] = !($this->expandedDescription[$id] ?? false);
    }

    /**
     * Bileşenin görünümünü render eder.
     * Veritabanından çağrı kayıtlarını çeker ve görünüme gönderir.
     */
    public function updateDepartment($recordId, $departmentId)
    {
        $record = CallRecord::find($recordId);
        if ($record) {
            $record->department_id = $departmentId;
            $record->save();
        }
    }

    // Görünümü render etme
    public function render()
    {
        $user = Auth::user();
        $query = CallRecord::query();

        if ($user->is_admin) {
            // Tüm kayıtlar
        } elseif (!empty($user->department_id)) {
            $department = $user->department;
            if ($department && $department->name === 'Çalışan') {
                // Çalışan departmanına bağlıysa sadece kendi kayıtlarını görebilir
                $query->where('user_id', $user->id);
            } else {
                // Diğer departmanlara bağlı olanlar kendi birimlerinin kayıtlarını görebilir
                $query->where('department_id', $user->department_id);
            }
        } else {
            // Hiçbir departmana bağlı değilse sadece kendi kayıtlarını görebilir
            $query->where('user_id', $user->id);
        }

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
                'user_id'
            ])
            ->with(['user', 'department'])
            // Çözüldü olan ve çözüm tarihi üzerinden 7 gün geçenler hariç
            // çözüldü olarak işaretlenen kayıtlar 7 gün sonra tablodan silinir
            ->where(function ($q) {
                $q->where('status', '!=', 'resolved')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'resolved')
                           ->where('solution_date', '>', now()->subDays(7));
                  });
            })
            ->orderByRaw('(is_urgent = 1 AND status != "resolved") DESC')
            ->orderByRaw('(status = "resolved") ASC')
            ->orderBy('is_urgent', 'desc')
            ->orderBy('call_date', 'desc')
            ->get();

        // Sayıların hesaplanması
        $totalCount = $callRecords->count();
        // Sadece çözümlenmemiş acil kayıtlar
        $urgentCount = $callRecords->where('is_urgent', 1)->where('status', '!=', 'resolved')->count();
        $resolvedCount = $callRecords->where('status', 'resolved')->count();
        $pendingCount = $callRecords->where('status', 'pending')->count();

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
        ]);
    }
}
