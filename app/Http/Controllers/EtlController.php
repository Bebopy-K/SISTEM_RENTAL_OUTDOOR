<?php

namespace App\Http\Controllers;

class EtlController extends Controller
{
    public function sync()
    {
        // Path to your artisan file
        $artisanPath = base_path('artisan');

        // This command runs the artisan command and redirects output to "nothing" in the background
        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B php $artisanPath sync:dw", "r"));
        } else {
            exec("php $artisanPath sync:dw > /dev/null 2>&1 &");
        }

        return back()->with('success', 'Proses ETL sedang berjalan di latar belakang.');
    }
}
