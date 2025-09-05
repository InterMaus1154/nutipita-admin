<?php

namespace App\Livewire\FinRecord;

use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use function Laravel\Prompts\alert;

class FileImport extends Component
{
    use WithFileUploads;

    public $file;

    public function updatedFile(): void
    {
        $extension = $this->file->getClientOriginalExtension();
        if($extension !== "csv"){
            session()->flash('error', 'File needs to be .csv only!');
            $this->redirectRoute('financial-records.create');
        }

    }

    public function render(): View
    {
        return view('livewire.fin-record.file-import');
    }
}
