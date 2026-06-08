<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->with('category')
            ->latest()
            ->get();

        return response()->json($transactions);
    }

 public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'jumlah'      => 'required|numeric|min:1',
        'tipe'        => 'required|in:pemasukan,pengeluaran',
        'deskripsi'   => 'nullable|string',
        'bukti_file'  => 'nullable|file|max:2048',
    ]);

    $user = User::find($request->user()->id);

    // Cegah saldo minus
    if (
        $request->tipe === 'pengeluaran' &&
        $user->saldo < $request->jumlah //kalo pengeluaran, pastiin saldo e cukup
    ) {
        return response()->json([
            'message' => 'Saldo tidak mencukupi'
        ], 422);
    }

    $transaction = null;

    DB::transaction(function () use ($request, $user, &$transaction) {

        $transaction = Transaction::create([
            'user_id'     => $request->user()->id,
            'category_id' => $request->category_id,
            'jumlah'      => $request->jumlah,
            'tipe'        => $request->tipe,
            'deskripsi'   => $request->deskripsi,
        ]);

        if ($request->tipe === 'pemasukan') {
            $user->increment('saldo', $request->jumlah);
        } else {
            $user->decrement('saldo', $request->jumlah);
        }
    });

    return response()->json([
        'message'     => 'Transaksi berhasil dicatat',
        'saldo'       => $user->fresh()->saldo, //fresh=dapetin data terbaru
        'transaction' => $transaction,
    ], 201);
}


    public function show(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('category')
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaction);
    }


    public function destroy(Request $request, $id)
{
    $transaction = Transaction::where('id', $id)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$transaction) {
        return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
    }

    DB::transaction(function () use ($request, $transaction) {
        $user = $request->user();

        // Kembalikan saldo 
        if ($transaction->tipe === 'pemasukan') {
            $user->decrement('saldo', $transaction->jumlah);
        } else {
            $user->increment('saldo', $transaction->jumlah);
        }

        $transaction->delete();
    });

    return response()->json([
        'message' => 'Transaksi berhasil dihapus',
        'saldo'   => $request->user()->fresh()->saldo,
    ]);
}
}
