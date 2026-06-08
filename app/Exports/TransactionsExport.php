<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

       protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }



    public function collection()
    {
        return Transaction::where('user_id', $this->userId)
            ->with('category')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Kategori', 'Jumlah', 'Tipe', 'Deskripsi', 'Bukti File', 'Dibuat'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->category->nama ?? '-',
            'Rp ' . number_format($transaction->jumlah, 0, ',', '.'),
            $transaction->tipe,
            $transaction->deskripsi ?? '-',
            $transaction->bukti_file ?? '-',
            $transaction->created_at->format('d-m-Y H:i'),
        ];
    }
}
