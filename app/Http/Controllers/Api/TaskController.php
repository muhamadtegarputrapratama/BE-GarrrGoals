<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use App\Models\tasks;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = tasks::where('user_id', $request->user()->id)
                     ->latest()
                     ->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string',
            'deskripsi' => 'nullable|string',
            'status'    => 'in:pending,in_progress,selesai',
            'file'      => 'nullable|file|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('tasks', 'public');
        }

        $task = tasks::create([
            'user_id'   => $request->user()->id,
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status'    => $request->status ?? 'pending',
            'file'      => $filePath,
        ]);

        return response()->json([
            'message' => 'Task berhasil dibuat',
            'task'    => $task,
        ], 201);
    }


    public function show(Request $request, $id)
    {
        $task = tasks::where('id', $id)
                    ->where('user_id', $request->user()->id)
                    ->first();

        if (!$task) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        return response()->json($task);
    }


   public function update(Request $request, $id)
{
    $task = Tasks::where('id', $id)
        ->where('user_id', $request->user()->id)
        ->first();

    if (!$task) {
        return response()->json([
            'message' => 'Task tidak ditemukan'
        ], 404);
    }

    $request->validate([
        'judul'     => 'sometimes|string',
        'deskripsi' => 'nullable|string',
        'status'    => 'sometimes|in:pending,in_progress,selesai',
        'file'      => 'nullable|file|max:2048',
    ]);

    $data = $request->only([
        'judul',
        'deskripsi',
        'status'
    ]);

    if ($request->hasFile('file')) {

        if ($task->file) {
            Storage::disk('public')->delete($task->file);
        }

        $data['file'] = $request->file('file')
            ->store('tasks', 'public');
    }

    $task->update($data);

    return response()->json([
        'message' => 'Task berhasil diupdate',
        'task' => $task->fresh(),
    ]);
}


    public function destroy(Request $request, $id)
    {
        $task = tasks::where('id', $id)
                    ->where('user_id', $request->user()->id)
                    ->first();

        if (!$task) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        if ($task->file) {
         Storage::disk('public')->delete($task->file);
            }

        $task->delete();

        return response()->json(['message' => 'Task berhasil dihapus']);
    }
}
