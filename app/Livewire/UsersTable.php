<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    // Durum ve Kontrol Özellikleri
    public $showAddModal = false;
    public $showEditModal = false;
    public $expandedDescription = [];
    public $tcResetError = '';
    public $selectedDepartment = '';

    // Dinleyiciler
    protected $listeners = [
        // 'deleteUser' => 'deleteUser', // kullanıcı silme işlemi
    ];

    // Yeni kullanıcı verileri için değerler
    public $newUser = [
        'name' => '',
        'surname' => '',
        'tc' => '',
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

    // --- Kullanıcı Ekleme/Güncelleme ---

    // Yeni kullanıcı ekler
    public function addUser()
    {
        $isAdmin = !empty($this->newUser['is_admin']);
        $rules = [
            'newUser.name' => ['required', 'string', 'max:50', 'regex:/^[^0-9]+$/'],
            'newUser.surname' => ['required', 'string', 'max:50', 'regex:/^[^0-9]+$/'],
            'newUser.tc' => [
            'nullable',
            'digits:11',
            function ($attribute, $value, $fail) {
                if ($value === null || $value === '') {
                    return;
                }
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
            'newUser.email' => 'required|email|max:100|unique:users,email',
            'newUser.password' => 'required|string|min:6',
            'newUser.department_id' => $isAdmin ? 'nullable' : 'required|integer|exists:departments,id',
        ];
        $messages = [
            'newUser.name.required' => 'İsim zorunludur.',
            'newUser.name.regex' => 'İsim alanında sayı olamaz.',
            'newUser.surname.required' => 'Soyisim zorunludur.',
            'newUser.surname.regex' => 'Soyisim alanında sayı olamaz.',
            'newUser.tc.digits' => 'TC Kimlik numarası 11 haneli olmalıdır.',
            'newUser.email.required' => 'E-posta zorunludur.',
            'newUser.email.email' => 'Lütfen geçerli bir e-posta adresi girin (ör: kullanici@site.com).',
            'newUser.email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'newUser.password.required' => 'Şifre zorunludur.',
            'newUser.password.min' => 'Şifre en az 6 karakter olmalı.',
            'newUser.department_id.required' => 'Birim seçimi zorunludur.',
        ];
        $validated = $this->validate($rules, $messages);

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

    // Kullanıcıyı günceller
    public function updateUser()
    {
        $isAdmin = !empty($this->editUser['is_admin']);
        $rules = [
            'editUser.name' => 'required|string|max:50',
            'editUser.surname' => 'required|string|max:50',
            'editUser.tc' => [
            'nullable',
            'digits:11',
            function ($attribute, $value, $fail) {
                if ($value === null || $value === '') {
                    return;
                }
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
            'editUser.email' => 'required|email|max:100|unique:users,email,' . $this->editUser['id'],
            'editUser.department_id' => $isAdmin ? 'nullable' : 'required|integer|exists:departments,id',
            'editUser.password' => 'nullable|string|min:6',
            'editUser.is_active' => 'required|boolean',
        ];
        $messages = [
            'editUser.name.required' => 'İsim zorunludur.',
            'editUser.surname.required' => 'Soyisim zorunludur.',
            'editUser.tc.digits' => 'TC Kimlik numarası 11 haneli olmalıdır.',
            'editUser.tc.required' => 'TC Kimlik numarası zorunludur.',
            'editUser.email.required' => 'E-posta zorunludur.',
            'editUser.email.email' => 'Lütfen geçerli bir e-posta adresi girin (ör: kullanici@site.com).',
            'editUser.email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'editUser.department_id.required' => 'Birim seçimi zorunludur.',
            'editUser.password.min' => 'Şifre en az 6 karakter olmalı.',
        ];
        $validated = $this->validate($rules, $messages);

        // Veritabanında güncelleme
        $user = User::findOrFail($this->editUser['id']);
        $user->name = $this->editUser['name'];
        $user->surname = $this->editUser['surname'];
        $user->tc = $this->editUser['tc'];
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

    // Kullanıcıyı soft delete ile siler
    // public function deleteUser($id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->delete();
    // }

    // --- Yardımcı Yöntemler ---

    // Edit modalını açar ve seçilen kullanıcının verilerini doldurur
    public function openEditModal($id)
    {
        $user = User::findOrFail($id);
        $this->editUser = [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'tc' => $user->tc,
            'email' => $user->email,
            'password' => '',
            'department_id' => $user->department_id,
            'is_admin' => $user->is_admin,
            'description' => $user->description,
            'is_active' => $user->is_active,
        ];
        $this->showEditModal = true;
    }

    // Aç/Kapa kullanıcı açıklaması
    public function toggleDescription($id): void
    {
        $this->expandedDescription[$id] = !($this->expandedDescription[$id] ?? false);
    }

    // Kullanıcı aktif/pasif durumunu değiştirir
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        if ($user->is_active) {
            // Pasif yap
            $user->is_active = 0;
            $user->deactivated_at = now();
        } else {
            // Aktif yap
            $user->is_active = 1;
            $user->activated_at = now();
            $user->deactivated_at = null; // Aktif olunca pasif tarihi sıfırlanır
        }
        $user->save();
    }

    public function resetPasswordWithTC()
    {
        $user = $this->editUser;
        $tc = $user['tc'] ?? '';
        if ($tc && strlen($tc) >= 7) {
            $this->editUser['password'] = substr($tc, 0, 7);
            $this->tcResetError = '';
        } else {
            $this->tcResetError = "Kullanıcının TC'si yok. Lütfen şifreyi manuel olarak girip kaydedin.";
        }
    }

    // --- Render Yöntemi ---

    // Kullanıcıları listeler
    public function render()
    {
        // Aktif kullanıcılar önce gelir, en eski eklenen en üstte olacak şekilde sıralanır
        $activeUsers = User::select([
            'id', 'name', 'surname', 'tc', 'email', 'department_id', 'is_active', 'is_admin', 'description', 'created_at', 'updated_at',
            'activated_at', 'deactivated_at'
        ])
            ->with('department')
            ->where('is_active', 1)
            ->orderBy('created_at', 'asc')
            ->get();

        // Pasif kullanıcılar: en son pasif yapılan en üstte olacak şekilde sıralanır
        $passiveUsers = User::select([
            'id', 'name', 'surname', 'tc', 'email', 'department_id', 'is_active', 'is_admin', 'description', 'created_at', 'updated_at',
            'activated_at', 'deactivated_at'
        ])
            ->with('department')
            ->where('is_active', 0)
            ->orderBy('updated_at', 'desc')
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
}
