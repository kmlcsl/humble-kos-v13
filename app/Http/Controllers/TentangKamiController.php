<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class TentangKamiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        return view('users.tentang-kami.index');
    }
}
