<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\tasks;
use App\Models\Transaction;
use App\Models\Target;
use App\Models\Category;

class AdminController
{
      public function statistics()
    {
        return response()->json([
            'users' => User::count(),
            'tasks' => tasks::count(),
            'transactions' => Transaction::count(),
            'targets' => Target::count(),
        ]);
    }

    public function users()
    {
        return User::latest()->get();
    }

    public function chart()
{
    $days = [];

    for ($i = 0; $i < 7; $i++) {
        $date = now()->startOfWeek()->addDays($i); //ngambil data dri minggun ini

        $days[] = [
            'day' => $date->format('D'),
            'total' => Transaction::whereDate(
                'created_at',
                $date->toDateString()
            )->count()
        ];
    }

    return response()->json($days);
}

public function tasks()
{
    return tasks::with('user')
        ->latest()
        ->get();
}

public function transactions()
{
    return Transaction::with('user', 'category')
        ->latest()
        ->get();
}

public function targets()
{
    return Target::with('user')
        ->latest()
        ->get();
}

public function categories()
    {
        return Category::latest()->get();
    }
}
