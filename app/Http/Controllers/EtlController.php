<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class EtlController extends Controller
{
    public function sync()
    {
        return back()->with('success', 'Test instant response');
    }
}
