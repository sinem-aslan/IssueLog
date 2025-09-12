<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CallRecord;

class CallRecordsTable extends Component
{
    public $expandedReason = [];

    public function toggleReason($id)
    {
        $this->expandedReason[$id] = !($this->expandedReason[$id] ?? false);
    }
    /**
     * Bileşenin görünümünü render eder.
     * Veritabanından çağrı kayıtlarını çeker ve görünüme gönderir.
     */
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

        return view('livewire.call-records-table', [
            'records' => $callRecords
        ]);
    }
}
