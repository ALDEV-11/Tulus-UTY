<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    public function showRegistrationForm()
    {
        return view('222.register');
    }

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'no_hp' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->normalizePhone($value) === null) {
                        $fail('Nomor HP harus berformat 08xx, 62xx, atau +62xx.');
                    }
                },
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'no_hp' => $this->normalizePhone($data['no_hp']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'pelanggan',
            'aktif' => true,
        ]);
    }

    protected function normalizePhone(mixed $value): ?string
    {
        $phone = preg_replace('/[\s-]+/', '', trim((string) $value));

        if ($phone === null || str_starts_with($phone, '+62')) {
            $phone = $phone === null ? '' : '62'.substr($phone, 3);
        } elseif (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return preg_match('/^62[0-9]{9,13}$/', $phone) === 1 ? $phone : null;
    }
}
