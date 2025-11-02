<?php

namespace App\Livewire;

use App\Models\Personil;
use Livewire\Component;

class PersonilTable extends Component
{
    public $personil;

    public function mount()
    {
        $this->personil = Personil::select('id', 'nama', 'pangkat', 'jabatan', 'telp')->get();
    }

    public function render()
    {
        return view('livewire.personil-table');
    }
}
