<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Department;
use Livewire\WithPagination;

class DepartmentsTable extends Component
{
    use WithPagination;

    // Durum ve Kontrol Özellikleri
    public $showAddModal = false;
    public $showEditModal = false;
    public $expandedDescription = [];

    // Veri Özellikleri
    public $newDepartment = [
        'name' => '',
        'description' => '',
    ];

    public $editDepartment = [
        'id' => null,
        'name' => '',
        'description' => '',
    ];

    // Yaşam Döngüsü ve Dinleyiciler
    protected $listeners = [
      'deleteDepartment' => 'deleteDepartment', // departman silme işlemi
    ];

    // --- CRUD Yöntemleri ---
    // Yeni birim ekleme fonksiyonu
    public function addDepartment()
    {
        $validated = $this->validate([
            'newDepartment.name' => ['required', 'string', 'max:50', 'regex:/^[^0-9]+$/'],
        ], [
            'newDepartment.name.required' => 'Departman adı zorunludur.',
            'newDepartment.name.regex' => 'Departman adında sayı olamaz.',
        ]);

        $data = $this->newDepartment;
        $data['name'] = mb_convert_case($data['name'], MB_CASE_TITLE, "UTF-8");
        Department::create($data);

        $this->reset('newDepartment');
        $this->showAddModal = false;
    }

    // Güncelleme işlemi
    public function updateDepartment()
    {
        $validated = $this->validate([
            'editDepartment.name' => ['required', 'string', 'max:50', 'regex:/^[^0-9]+$/'],
        ], [
            'editDepartment.name.required' => 'Departman adı zorunludur.',
            'editDepartment.name.regex' => 'Departman adında sayı olamaz.',
        ]);

        $department = Department::findOrFail($this->editDepartment['id']);
        $data = $this->editDepartment;
        $data['name'] = mb_convert_case($data['name'], MB_CASE_TITLE, "UTF-8");

        unset($data['id']);
        $data['updated_by'] = auth()->id();

        $department->update($data);
        $this->showEditModal = false;
    }

    // Departmanı soft delete ile siler
    public function deleteDepartment($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();
    }

    // --- Yardımcı Yöntemler ---

    // Edit modalını açar ve seçilen kullanıcının verilerini doldurur
    public function openEditModal($id)
    {
        $department = Department::findOrFail($id);
        $editorName = null;

        if ($department->updated_by) {
            $user = \App\Models\User::find($department->updated_by);
            $editorName = $user ? $user->name . ' ' . $user->surname : null;
        }

        $this->editDepartment = [
            'id' => $department->id,
            'name' => $department->name,
            'description' => $department->description,
            'updated_at' => $department->updated_at,
            'editor_name' => $editorName,
        ];

        $this->showEditModal = true;
    }

    // metni açma/kapatma işlemi
    public function toggleDescription($id): void
    {
        $this->expandedDescription[$id] = !($this->expandedDescription[$id] ?? false);
    }

    // --- Render Yöntemi ---

    // Render fonksiyonu
    public function render()
    {
        $departments = Department::select([
            'id',
            'name',
            'description',
            'created_at',
            'updated_at',
            'updated_by',
        ])->orderBy('name')->get();

        $departmentCount = Department::count();
        $userCount = \App\Models\User::whereNotNull('department_id')->count();

        // Her birimdeki kişi sayısı ve aktif/pasif sayısı
        $userCounts = [];
        $activeCounts = [];
        $passiveCounts = [];

        foreach ($departments as $department) {
            $userCounts[$department->id] = \App\Models\User::where('department_id', $department->id)->count();
            $activeCounts[$department->id] = \App\Models\User::where('department_id', $department->id)->where('is_active', true)->count();
            $passiveCounts[$department->id] = \App\Models\User::where('department_id', $department->id)->where('is_active', false)->count();
        }

        return view('livewire.departments-table', [
            'departments' => $departments,
            'departmentCount' => $departmentCount,
            'userCount' => $userCount,
            'userCounts' => $userCounts,
            'activeCounts' => $activeCounts,
            'passiveCounts' => $passiveCounts,
        ]);
    }
}
