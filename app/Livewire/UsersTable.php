<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;
    protected $listeners = [
        'deleteUser' => 'deleteUser',
    ];

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
    public function openEditModal($id)
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
            'editUser.email' => 'required|email|max:100|unique:users,email,' . $this->editUser['id'],
            'editUser.department_id' => 'required|integer|exists:departments,id',
            'editUser.password' => 'nullable|string|min:6',
            'editUser.is_active' => 'required|boolean',
        ], [
            // hata mesajları
            'editUser.name.required' => 'İsim zorunludur.',
            'editUser.surname.required' => 'Soyisim zorunludur.',
            'editUser.email.required' => 'E-posta zorunludur.',
            'editUser.email.email' => 'Lütfen geçerli bir e-posta adresi girin (ör: kullanici@site.com).',
            'editUser.email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'editUser.department_id.required' => 'Birim seçimi zorunludur.',
            'editUser.password.min' => 'Şifre en az 6 karakter olmalı.',
        ]);

        // Veritabanında güncelleme
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

    // Aç/Kapa kullanıcı açıklaması
    public function toggleDescription($id): void
    {
        $this->expandedDescription[$id] = !($this->expandedDescription[$id] ?? false);
    }

    // Yeni kullanıcı ekler
    public function addUser()
    {
        $validated = $this->validate([
            'newUser.name' => ['required', 'string', 'max:50', 'regex:/^[^0-9]+$/'],
            'newUser.surname' => ['required', 'string', 'max:50', 'regex:/^[^0-9]+$/'],
            'newUser.email' => 'required|email|max:100|unique:users,email',
            'newUser.password' => 'required|string|min:6',
            'newUser.department_id' => 'required|integer|exists:departments,id',
        ], [
            'newUser.name.required' => 'İsim zorunludur.',
            'newUser.name.regex' => 'İsim alanında sayı olamaz.',
            'newUser.surname.required' => 'Soyisim zorunludur.',
            'newUser.surname.regex' => 'Soyisim alanında sayı olamaz.',
            'newUser.email.required' => 'E-posta zorunludur.',
            'newUser.email.email' => 'Lütfen geçerli bir e-posta adresi girin (ör: kullanici@site.com).',
            'newUser.email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'newUser.password.required' => 'Şifre zorunludur.',
            'newUser.password.min' => 'Şifre en az 6 karakter olmalı.',
            'newUser.department_id.required' => 'Birim seçimi zorunludur.',
        ]);

        $data = $this->newUser;
        // Kullanıcı adının baş harfi büyük, diğerleri küçük
        $data['name'] = ucfirst(mb_strtolower($data['name'], 'UTF-8'));
        // Soyisim tamamen büyük harf
        $data['surname'] = mb_strtoupper($data['surname'], 'UTF-8');
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
        // Aktif kullanıcılar önce gelir, en eski eklenen en üstte olacak şekilde sıralanır
        $activeUsers = User::select([
            'id', 'name', 'surname', 'email', 'department_id', 'is_active', 'is_admin', 'description', 'created_at'
        ])
            ->with('department')
            ->where('is_active', 1)
            ->orderBy('created_at', 'asc')
            ->get();

        // Pasif kullanıcılar en son eklenen en üstte olacak şekilde sıralanır
        $passiveUsers = User::select([
            'id', 'name', 'surname', 'email', 'department_id', 'is_active', 'is_admin', 'description', 'created_at'
        ])
            ->with('department')
            ->where('is_active', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $allUsers = $activeUsers->concat($passiveUsers);

        $totalCount = $allUsers->count();
        $activeCount = $activeUsers->count();
        $passiveCount = $passiveUsers->count();

        $departments = \App\Models\Department::orderBy('name')->get();
        return view('livewire.users-table', [
            'users' => $allUsers,
            'departments' => $departments,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'passiveCount' => $passiveCount
        ]);
    }

    // Kullanıcı aktif/pasif durumunu değiştirir
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = $user->is_active == 1 ? 0 : 1;
        $user->save();
    }

    // Kullanıcıyı soft delete ile siler
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
