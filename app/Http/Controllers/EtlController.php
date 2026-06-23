<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class EtlController extends Controller
{
    public function sync()
    {
        // Force synchronous execution temporarily just to read the error log
        Artisan::call('sync:dw');
        $output = Artisan::output();

        // If output is completely empty, the command is failing silently
        if (empty($output)) {
            $output = "The command ran but returned absolutely no output text. Check storage/logs/laravel.log";
        }

        return back()->with('success', 'Proses Selesai')->with('etl_output', $output);
    }
}
