<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

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
    public function showAdminLoginForm()
    {
        return view('admin.admin-login'); // Custom admin login view
    }

    public function showEmployeeLoginForm()
    {
        return view('auth.employee-login'); // Custom employee login view
    }
    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }
    
        return redirect('/home');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        // Attempt to log in as admin
        if (Auth::attempt($credentials) && Auth::user()->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }
        
        // If login fails or user is not an admin
        return redirect()->back()->withErrors(['email' => 'Invalid credentials or not an admin.']);
    }

    public function employeeLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        // Attempt to log in as employee
        if (Auth::attempt($credentials) && Auth::user()->role === 'employee') {
            return redirect()->intended('/employee/dashboard');
        }
        
        // If login fails or user is not an employee
        return redirect()->back()->withErrors(['email' => 'Invalid credentials or not an employee.']);
    }
    public function adminLogout(Request $request)
    {
        dd('d,fnkdn');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function employeeLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('employee.login');
    }
}
