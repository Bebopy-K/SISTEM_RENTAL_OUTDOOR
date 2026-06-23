<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class EtlController extends Controller
{
    public function sync()
    {
        // Bypass session writing delays and close the session immediately
        session()->writeClose();

        // Run via background OS command to ensure zero waiting time
        $artisanPath = base_path('artisan');

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start /B php $artisanPath sync:dw", "r"));
        } else {
            exec("php $artisanPath sync:dw > /dev/null 2>&1 &");
        }

        // Instantly redirect back with no overhead
        return redirect()->back()->with('success', 'ETL processing started in the background.');
    }
}
