<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request)/*: Response*/
    {
        //dd("Entro a olvidar contraseña");
        return Inertia::render('Auth/ResetPassword'/*, [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]*/);
        
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Buscar al usuario por correo electrónico
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return redirect()->route('password.reset')->with('notFound', '404 - Usuario inexistente');
        }
        
        // Actualizar la contraseña del usuario
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Contraseña Actualizada, por favor inicie sesión');
    }
}
