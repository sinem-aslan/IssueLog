<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallRecord extends Model
{
    use HasFactory;

    // Tablo adı
    protected $table = 'call_records';

    // Mass assignment için izin verilen kolonlar
    protected $fillable = [
        'call_date',
        'user_id',
        'student_tc',
        'student_phone',
        'reason',
        'description',
        'department_id',
        'status',
        'solution_date',
        'is_urgent',
        'admin_description',
        'status_changed_by',
        'status_changed_at',
    ];

    // Tip dönüşümleri
    protected $casts = [
        'call_date' => 'datetime',
        'solution_date' => 'datetime',
        'is_urgent' => 'boolean',
    ];

    // İlgili birim
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Durumu değiştiren kullanıcı
    public function statusChanger()
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }

    // Çağrıyı düzenleyen kullanıcı
    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    // Notu değiştiren kullanıcı
    public function descriptionChanger()
    {
        return $this->belongsTo(User::class, 'description_changed_by');
    }

    // Çağrıyı kaydeden kullanıcı
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
