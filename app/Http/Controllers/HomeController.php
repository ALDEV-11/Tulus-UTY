<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Send each user to the dashboard that matches their role.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return auth()->user()?->role === UserRole::Admin
            ? redirect()->route('admin')
            : redirect()->route('booking-saya');
    }
}
