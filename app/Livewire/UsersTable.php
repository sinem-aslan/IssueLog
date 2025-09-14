<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    public $expandedDescription = [];

    public function toggleDescription($id): void
    {
        $this->expandedDescription[$id] = !($this->expandedDescription[$id] ?? false);
    }

    public function render()
    {
        $users = User::select([
            'id',         // Kullanıcı No
            'name',       // Ad
            'surname',    // Soyad
            'email',      // E-posta
            'department_id', // Birim
            'is_active',  // Durum
            'is_admin',   // Yetki
            'description' // Açıklama
        ])
        ->with('department') // ilişkili birim bilgisi
        ->orderBy('name') // Ad'a göre sıralama
        ->paginate(10);

        return view('livewire.users-table', [
            'users' => $users
        ]);
    }
}
