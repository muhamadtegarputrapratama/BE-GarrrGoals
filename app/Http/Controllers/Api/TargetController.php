<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Target;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index(Request $request)
    {
        $targets = Target::where('user_id', $request->user()->id)
            ->withSum('progress', 'jumlah')
            ->latest()
            ->get()
            ->map(function ($target) {
                $target->persentase = $target->target_nominal > 0
                    ? round(($target->progress_sum_jumlah / $target->target_nominal) * 100, 2)
                    : 0;
                return $target;
            });

        return response()->json($targets);
    }


    public function store(Request $request)
    {
        $request->validate([
            'judul'          => 'required|string',
            'target_nominal' => 'required|numeric|min:1',
        ]);

        $target = Target::create([
            'user_id'        => $request->user()->id,
            'judul'          => $request->judul,
            'target_nominal' => $request->target_nominal,
            'status'         => 'aktif',
        ]);

        return response()->json([
            'message' => 'Target berhasil dibuat',
            'target'  => $target,
        ], 201);
    }


    public function show(Request $request, $id)
    {
        $target = Target::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->withSum('progress', 'jumlah') 
            ->first();

        if (!$target) {
            return response()->json(['message' => 'Target tidak ditemukan'], 404);
        }

        $target->persentase = $target->target_nominal > 0
            ? round(($target->progress_sum_jumlah / $target->target_nominal) * 100, 2)
            : 0;

        return response()->json($target);
    }


    public function destroy(Request $request, $id)
    {
        $target = Target::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$target) {
            return response()->json(['message' => 'Target tidak ditemukan'], 404);
        }

        $target->delete();

        return response()->json(['message' => 'Target berhasil dihapus']);
    }
}
