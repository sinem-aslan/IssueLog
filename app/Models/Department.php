<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    // Tablo adı
    protected $table = 'departments';

    // Mass assignment için izin verilen kolonlar
    protected $fillable = [
        'name',
        'description',
    ];

    // Bir departman birden fazla çağrı kaydına sahip olabilir
    public function callRecords()
    {
        return $this->hasMany(CallRecord::class);
    }
}
