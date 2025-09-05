<?php

namespace App\Livewire\FinRecord;

use App\Enums\FinancialRecordType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use function Laravel\Prompts\alert;

class FileImport extends Component
{
    use WithFileUploads;

    public $file;

    public bool $isEditMode = false;
    public bool $isLoading = false;

    public array $csvData = [];
    public array $csvHeaders = [];

    public $categories;

    public function mount($categories): void
    {
        $this->categories = $categories;
    }

    public function updatedFile(): void
    {
        $this->isEditMode = true;
        $this->isLoading = true;

        // only csv allowed
        $extension = $this->file->getClientOriginalExtension();
        if ($extension !== "csv") {
            session()->flash('error', 'File needs to be .csv only!');
            $this->redirectRoute('financial-records.create');
        }

        // open file
        try {
            $filePath = $this->file->getRealPath();
            $handle = fopen($filePath, 'rb');
            $this->readCSVRows($handle);
            fclose($handle);
        } catch (\Throwable $e) {
            Log::error("Error opening CSV file:");
            Log::error($e->getMessage());
            $this->resetExcept('categories');
            session()->flash('error', 'Error at opening csv file.');
            $this->redirectRoute('financial-records.create');
        }
    }

    public function readCSVRows($handle): void
    {
        // assuming first line are headers
        $this->csvHeaders = fgetcsv($handle);
        $csvHeaderSlugs = array_map(function ($header) {
            // transform to snake case -> for better key - value pairs
            return Str::snake($header);
        }, $this->csvHeaders);

        $this->csvHeaders = array_combine($csvHeaderSlugs, $this->csvHeaders);

        // add extra fields and change labels for some
        $this->csvHeaders['category_id'] = 'Category';
        $this->csvHeaders['type'] = 'Type';
        $this->csvHeaders['transaction_date'] = 'Date';
        $this->csvHeaders['transaction_description'] = 'Item Name';
        $this->csvHeaders['debit_amount'] = 'Expense Amount';
        $this->csvHeaders['credit_amount'] = 'Income Amount';

        try {
            // loop through row by row
            while (($row = fgetcsv($handle)) !== false) {
                $combined = array_combine($csvHeaderSlugs, $row);
                $combined['category_id'] = '';

                if (!empty(trim($combined['debit_amount']))) {
                    $combined['type'] = FinancialRecordType::EXPENSE->name;
                } else if (!empty(trim($combined['credit_amount']))) {
                    $combined['type'] = FinancialRecordType::INCOME->name;
                }

                try {
                    $combined['transaction_date'] = Carbon::createFromFormat('d/m/Y', $combined['transaction_date'])->format('Y-m-d');
                } catch (\Throwable $e) {
                    // do nothing, leave date as it is
                }

                $this->csvData[] = $combined;
            }
            $this->isLoading = false;
        } catch (\Throwable $e) {
            Log::error("Error reading CSV file:");
            Log::error($e->getMessage());
            $this->resetExcept('categories');
            session()->flash('error', 'Error at reading through csv file.');
            $this->redirectRoute('financial-records.create');
        }

    }

    public function removeColumn(string $column): void
    {
        unset($this->csvHeaders[$column]);

        $this->csvData = array_map(function ($row) {
            $newRow = [];
            foreach ($this->csvHeaders as $key => $header) {
                $newRow[$key] = $row[$key] ?? null;
            }
            return $newRow;
        }, $this->csvData);
    }

    public function closeEditor(): void
    {
        $this->resetExcept('categories');
    }

    public function save(): void
    {

    }

    public function render(): View
    {
        return view('livewire.fin-record.file-import');
    }
}
