<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboardSuperAdmin()
    {
        $data = [ 
            "title"                 => "Dashboard Super Admin",
            "menuDashboard"         => "active",
        ];
         return view('super-admin/dashboard', $data);
    }

    public function dashboardAdmin()
    {
        $data = [
            "title"                 => "Dashboard Admin",
            "menuDashboard"         => "active",
        ];
         return view('admin/dashboard', $data);
    }
}
