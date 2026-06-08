<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TasksExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $userId;

    public function __construct($userId) {
        $this->userId = $userId; // simpen userId yang mau diexport
    }

    public function collection()
    {
        return Task::where('user_id', $this->userId)->latest()->get();
    }

    public function headings(): array
    {
        return ['ID', 'Judul', 'Deskripsi', 'Status', 'File', 'Dibuat'];
    }

    public function map($task): array 
    {
        return [
            $task->id,
            $task->judul,
            $task->deskripsi,
            $task->status,
            $task->file ?? '-',
            $task->created_at->format('d-m-Y H:i'),
        ];
    }
}
