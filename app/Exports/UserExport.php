<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::latest()->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'Email', 'Role', 'Saldo', 'Dibuat'];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->nama,
            $user->email,
            $user->role,
            'Rp ' . number_format($user->saldo, 0, ',', '.'),
            $user->created_at->format('d-m-Y H:i'),
        ];
    }
}
