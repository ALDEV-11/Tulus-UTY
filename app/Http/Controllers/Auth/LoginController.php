<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $user = User::where($this->username(), $credentials[$this->username()])->first();

        if ($user?->aktif === false) {
            $this->incrementLoginAttempts($request);

            throw ValidationException::withMessages([
                $this->username() => 'Akun Anda sedang tidak aktif. Hubungi administrator.',
            ]);
        }

        return $this->guard()->attempt(
            $credentials,
            $request->boolean('remember')
        );
    }

    public function showLoginForm()
    {
        return view('222.login');
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function redirectPath()
    {
        return Auth::user()?->role === UserRole::Admin
            ? '/admin'
            : '/booking-saya';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
