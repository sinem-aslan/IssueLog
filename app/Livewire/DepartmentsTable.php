<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Department;
use Livewire\WithPagination;

class DepartmentsTable extends Component
{
    use WithPagination;

    public $newDepartment = [
        'name' => '',
        'description' => '',
    ];

    public $editDepartment = [
        'id' => null,
        'name' => '',
        'description' => '',
    ];

    public $expandedDescription = [];

    public function addDepartment()
    {
        $validated = $this->validate([
            'newDepartment.name' => ['required', 'string', 'max:50', 'regex:/^[^0-9]+$/'],
        ], [
            'newDepartment.name.required' => 'Departman adı zorunludur.',
            'newDepartment.name.regex' => 'Departman adında sayı olamaz.',
        ]);
        $data = $this->newDepartment;
        $data['name'] = ucfirst(mb_strtolower($data['name'], 'UTF-8'));
        Department::create($data);
        $this->reset('newDepartment');
    }

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
        $data['name'] = ucfirst(mb_strtolower($data['name'], 'UTF-8'));
        $department->update($data);
    }

    public function toggleDescription($id): void
    {
        $this->expandedDescription[$id] = !($this->expandedDescription[$id] ?? false);
    }

    public function render()
    {
        $departments = Department::select([
            'id',
            'name',
            'description',
        ])->orderBy('name')->paginate(10);

        return view('livewire.departments-table', [
            'departments' => $departments
        ]);
    }
}
