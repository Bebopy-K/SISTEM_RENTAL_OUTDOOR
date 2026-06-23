<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessEtl;

class EtlController extends Controller
{
    public function sync()
    {
        // Dispatch job to the queue
        ProcessEtl::dispatch();

        // Note: You can't get Artisan::output() here anymore because it runs later.
        return back()->with('success', 'Proses ETL telah dimulai di latar belakang! Silakan cek beberapa saat lagi.');
    }
}
