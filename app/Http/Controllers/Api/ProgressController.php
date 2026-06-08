<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Progress;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function store(Request $request, $target_id)
    {
        $target = Target::where('id', $target_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$target) {
            return response()->json([
                'message' => 'Target tidak ditemukan'
            ], 404);
        }

        if ($target->status === 'selesai') {
            return response()->json([
                'message' => 'Target sudah selesai'
            ], 422);
        }

        $request->validate([
            'jumlah' => 'required|numeric|min:1',
        ]);

        $user = $request->user();

        // Cek saldo cukup atau tidak
        if ($user->saldo < $request->jumlah) {
            return response()->json([
                'message' => 'Saldo tidak mencukupi'
            ], 422);
        }

        $progress = null;

        DB::transaction(function () use (
            $request,
            $target_id,
            $user,
            &$progress
        ) {

            $progress = Progress::create([
                'target_id' => $target_id,
                'jumlah'    => $request->jumlah,
            ]);

            // Kurangi saldo user
            $user->decrement('saldo', $request->jumlah);
        });

        $totalProgress = $target->progress()->sum('jumlah');

        $persentase = $target->target_nominal > 0
            ? round(($totalProgress / $target->target_nominal) * 100, 2)
            : 0;

        if ($totalProgress >= $target->target_nominal) {
            $target->update([
                'status' => 'selesai'
            ]);
        }

        return response()->json([
            'message'        => 'Progress berhasil ditambahkan',
            'progress'       => $progress,
            'saldo'          => $user->fresh()->saldo,
            'total_progress' => $totalProgress,
            'persentase'     => $persentase . '%',
            'status_target'  => $target->fresh()->status,
        ], 201);
    }

    public function index(Request $request, $target_id)
    {
        $target = Target::where('id', $target_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$target) {
            return response()->json([
                'message' => 'Target tidak ditemukan'
            ], 404);
        }

        $progress = $target->progress()->latest()->get();

        $totalProgress = $target->progress()->sum('jumlah');

        $persentase = $target->target_nominal > 0
            ? round(($totalProgress / $target->target_nominal) * 100, 2)
            : 0;

        return response()->json([
            'target'         => $target,
            'progress'       => $progress,
            'total_progress' => $totalProgress,
            'persentase'     => $persentase . '%',
        ]);
    }
}
