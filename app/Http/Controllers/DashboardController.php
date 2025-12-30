<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use App\Models\User;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
    }
    public function index(Request $request)
    {
        return Inertia::render('Dashboard', [
            'data' => [
                'employees' => User::count(),
                'clients' => Client::count(),
            ]
        ]);
    }
}
