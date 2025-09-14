<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    public $expandedDescription = [];
    public $showAddModal = false;
    public $showEditModal = false;

    // Yeni kullanıcı verileri için değerler
    public $newUser = [
        'name' => '',
        'surname' => '',
        'email' => '',
        'password' => '',
        'department_id' => '',
        'is_admin' => false,
        'description' => '',
        'is_active' => 0,
    ];

    // Güncelleme modalı için değişkenler
    public $editUser = [
        'id' => null,
        'name' => '',
        'surname' => '',
        'email' => '',
        'password' => '',
        'department_id' => '',
        'is_admin' => false,
        'description' => '',
        'is_active' => 0,
    ];

    // Edit modalını açar ve seçilen kullanıcının verilerini doldurur
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->editUser = [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'password' => '',
            'department_id' => $user->department_id,
            'is_admin' => $user->is_admin,
            'description' => $user->description,
            'is_active' => $user->is_active,
        ];
        $this->showEditModal = true;
    }

    // Kullanıcıyı günceller
    public function updateUser()
    {
        $validated = $this->validate([
            'editUser.name' => 'required|string|max:50',
            'editUser.surname' => 'required|string|max:50',
            'editUser.email' => 'required|email|max:100',
            'editUser.department_id' => 'required|integer|exists:departments,id',
            'editUser.password' => 'nullable|string|min:6',
        ], [
            'editUser.name.required' => 'İsim zorunludur.',
            'editUser.surname.required' => 'Soyisim zorunludur.',
            'editUser.email.required' => 'E-posta zorunludur.',
            'editUser.department_id.required' => 'Birim seçimi zorunludur.',
            'editUser.password.min' => 'Şifre en az 6 karakter olmalı.',
        ]);

        $user = User::findOrFail($this->editUser['id']);
        $user->name = $this->editUser['name'];
        $user->surname = $this->editUser['surname'];
        $user->email = $this->editUser['email'];
        $user->department_id = $this->editUser['department_id'];
        $user->is_admin = !empty($this->editUser['is_admin']);
        $user->description = $this->editUser['description'];
        $user->is_active = $this->editUser['is_active'];
        if (!empty($this->editUser['password'])) {
            $user->password = bcrypt($this->editUser['password']);
        }
        $user->save();
        $this->showEditModal = false;
    }

    public function toggleDescription($id): void
    {
        $this->expandedDescription[$id] = !($this->expandedDescription[$id] ?? false);
    }

    // Yeni kullanıcı ekler
    public function addUser()
    {
        $validated = $this->validate([
            'newUser.name' => 'required|string|max:50',
            'newUser.surname' => 'required|string|max:50',
            'newUser.email' => 'required|email|max:100',
            'newUser.password' => 'required|string|min:6',
            'newUser.department_id' => 'required|integer|exists:departments,id',
        ], [
            'newUser.name.required' => 'İsim zorunludur.',
            'newUser.surname.required' => 'Soyisim zorunludur.',
            'newUser.email.required' => 'E-posta zorunludur.',
            'newUser.password.required' => 'Şifre zorunludur.',
            'newUser.password.min' => 'Şifre en az 6 karakter olmalı.',
            'newUser.department_id.required' => 'Birim seçimi zorunludur.',
        ]);

        $data = $this->newUser;
        $data['is_active'] = 0; // otomatik pasif
        $data['is_admin'] = !empty($data['is_admin']);
        $data['password'] = bcrypt($data['password']);
        User::create($data);
        $this->reset('newUser');
        $this->showAddModal = false;
    }

    // Kullanıcıları listeler
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

        $departments = \App\Models\Department::orderBy('name')->get();
        return view('livewire.users-table', [
            'users' => $users,
            'departments' => $departments
        ]);
    }
}
