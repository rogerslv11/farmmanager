<?php

namespace App\Http\Controllers;

use App\Support\CurrentOrganization;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            CurrentOrganization::set(auth()->user()->organization_id);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
    }

    public function demoLogin(Request $request)
    {
        abort_unless(config('farmmanager.demo_login', true), 404);

        $demo = \App\Models\User::where('email', 'admin@fazenda.com')->where('active', true)->first();

        abort_if(! $demo, 404, 'Conta demo indisponível. Rode php artisan db:seed.');

        auth()->login($demo, true);
        $request->session()->regenerate();
        CurrentOrganization::set($demo->organization_id);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showProfile() { return view('profile.show'); }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
        $request->user()->update($data);
        return back()->with('status', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $request->user()->update(['password' => $data['password']]);
        return back()->with('status', 'Senha alterada com sucesso.');
    }
}
