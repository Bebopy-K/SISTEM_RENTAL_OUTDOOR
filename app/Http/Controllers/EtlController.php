<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EtlController extends Controller
{
    public function sync()
    {
        // Jalankan command sync:dw
        Artisan::call('sync:dw');

        // Ambil output command
        $output = Artisan::output();

        return back()->with('success', 'ETL selesai! Data Warehouse telah diperbarui.')
                     ->with('etl_output', $output);
    }
}