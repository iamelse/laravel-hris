<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('pages.employee.dashboard.index', [
            'title' => 'Dashboard'
        ]);
    }
}
