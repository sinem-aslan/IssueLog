<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Tablo adı
    protected $table = 'departments';

    // Mass assignment için izin verilen kolonlar
    protected $fillable = [
        'name',
        'description',
        'updated_by',
    ];

    // Tarih kolonlarını otomatik olarak Carbon instance'larına dönüştür
    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    ];


    // Bir departman birden fazla çağrı kaydına sahip olabilir
    public function callRecords()
    {
        return $this->hasMany(CallRecord::class);
    }

    // Departmanı oluşturan kullanıcı
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
