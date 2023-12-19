<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule; //untuk memakai fitur validasi Rule

use App\Models\User; //untuk memakai Model User

class UserController extends Controller
{
    public function create()
    {
        return view('users.register');
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required', 'min:3'], //'min:3' artinya rules-nya minimal harus ada 3 character
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'min:6'], //di Laravel, rule 'confirmed' bisa dipakai untuk field lain yg namanya sama lalu ada "_confirmation"
            /*
            'password' => 'required|confirmed|min:6', //ini sama saja dengan yg atas, bentuknya bisa dibuat seperti ini
            */
        ]);
        $formFields['password'] = bcrypt($formFields['password']); //lakukan hash untuk password-nya 
        $user = User::create($formFields); //INSERT data user-nya, tapi kali ini kita jadikan variable
        auth()->login($user); //langsung melakukan login, login() isi parameter-nya pakai variable bekas aksi INSERT data di atas
        return redirect('/')->with('message', 'User created and logged in.');
    }

    public function logout(Request $request)
    {
        auth()->logout(); //remove authentication information from Session
        //ini sebenarnya sudah logout, tapi disarankan untuk menambah step di bawah
        $request->session()->invalidate(); //yaitu meng-invalidate session milik user
        $request->session()->regenerateToken(); //dan melakukan re-generate (generate ulang) CSRF token (CSRF Protection) milik user
        return redirect('/')->with('message', 'You have been logged out!');
    }

    public function login()
    {
        return view('users.login');
    }

    public function authenticate(Request $request)
    {
        $formFields = $request->validate([
            'email' => ['required', 'email'], 
            'password' => 'required'
        ]);
        if(auth()->attempt($formFields)) { //kita meng-attempt to login (mencoba untuk login)
            $request->session()->regenerate(); //kalo true maka kita generate session id-nya
            return redirect('/')->with('message', 'You are now logged in!');
        }
        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }
}
