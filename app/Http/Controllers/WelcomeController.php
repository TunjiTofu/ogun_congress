<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;

class WelcomeController extends Controller
{
    public function index()
    {
        $regOpen    = setting('registration_open', '1') === '1';
        $closesAt   = setting('registration_closes_at');
        $autoClose  = $closesAt && now()->gt(Carbon::parse($closesAt));
        $regOpen    = $regOpen && ! $autoClose;

        return view('welcome', compact('regOpen'));
    }
}
