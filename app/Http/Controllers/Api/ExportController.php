<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Exports\TasksExport;
use App\Exports\TransactionsExport;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportTasks(Request $request)
    {
        $filename = 'tasks_' . $request->user()->id . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new TasksExport($request->user()->id), $filename);
    }


    public function exportTransactions(Request $request)
    {
        $filename = 'transactions_' . $request->user()->id . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new TransactionsExport($request->user()->id), $filename);
    }

    // Export semua users (admin only)
    public function exportUsers(Request $request)
    {
        if ($request->user()->role !== 'admin') { //klo bukan admin g boleh
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $filename = 'users_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new UserExport(), $filename);
    }
}
