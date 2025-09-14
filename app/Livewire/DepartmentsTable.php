<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Department;
use Livewire\WithPagination;

class DepartmentsTable extends Component
{
    use WithPagination;

    public $expandedDescription = [];

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
