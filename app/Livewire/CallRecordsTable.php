<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\CallRecord;

class CallRecordsTable extends Component
{
    public $showAddModal = false;
    // Yeni kayıt verileri için varsayılan değerler
    public $newRecord = [
        'call_date' => '',
        'student_tc' => '',
        'student_phone' => '',
        'reason' => '',
        'department_id' => '',
        'status' => 'pending',
    ];

    // Güncelleme modalı için değişkenler
    public $showEditModal = false;
    public $editRecord = [
        'id' => null,
        'student_tc' => '',
        'student_phone' => '',
        'reason' => '',
        'department_id' => '',
    ];

    // Edit modalını açar ve seçilen kaydın verilerini doldurur
    public function openEditModal($id)
    {
        $this->resetErrorBag(); // hata mesajlarını sıfırlar
        $record = \App\Models\CallRecord::find($id);
        if ($record) {
            $this->editRecord = [
                'id' => $record->id,
                'student_tc' => $record->student_tc,
                'student_phone' => $record->student_phone,
                'reason' => $record->reason,
                'department_id' => $record->department_id,
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
        if ($record) {
            $record->student_tc = $validated['editRecord']['student_tc'];
            $record->student_phone = $validated['editRecord']['student_phone'];
            $record->reason = $validated['editRecord']['reason'];
            $record->department_id = $validated['editRecord']['department_id'];
            $record->save();
        }
        $this->showEditModal = false;
    }

    // Yeni kayıt ekleme işlemi
    public function addRecord()
    {
        $this->resetErrorBag(); // hata mesajlarını sıfırla
        $validated = $this->validate([
            'newRecord.student_tc' => 'required|string|max:20',
            'newRecord.student_phone' => 'required|string|max:20',
            'newRecord.reason' => 'required|string',
            'newRecord.department_id' => 'required|integer|exists:departments,id',
        ], [
            'newRecord.student_tc.required' => 'Öğrenci T.C. Kimlik numarası zorunludur.',
            'newRecord.student_phone.required' => 'Telefon numarası zorunludur.',
            'newRecord.reason.required' => 'Arama gerekçesi zorunludur.',
            'newRecord.department_id.required' => 'Birim seçimi zorunludur.',
        ]);

        // Otomatik olarak eklenen alanlar
        $data = $validated['newRecord'];
        $data['call_date'] = now(); // Çağrı tarihi
        $data['status'] = 'pending'; // "bekliyor"
        $data['solution_date'] = null; // Çözüm tarihi başlangıçta null
        $data['user_id'] = Auth::id(); // Mevcut kullanıcı ID'si

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
            // Eğer durum 'resolved' ise çözüm tarihini güncellenir
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
    public function toggleReason($id)
    {
        $this->expandedReason[$id] = !($this->expandedReason[$id] ?? false);
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
        $callRecords = CallRecord::select([
            'id',           // No için
            'call_date',    // Çağrı Tarihi
            'student_tc',   // Öğrenci T.C.K.N
            'student_phone',// Telefon Numarası
            'reason',       // Arama Gerekçesi
            'department_id',// İlgili Birim
            'status',       // Durum
            'solution_date',  // Çözüm Tarihi
            'is_urgent'     // Acil durumu için
        ])
        ->with(['user', 'department'])
        ->orderBy('is_urgent', 'desc')
        ->orderBy('call_date', 'desc')
        ->get();

        $departments = \App\Models\Department::all();

        return view('livewire.call-records-table', [
            'records' => $callRecords,
            'departments' => $departments
        ]);
    }
}
