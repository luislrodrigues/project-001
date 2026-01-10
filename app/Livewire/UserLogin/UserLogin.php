<?php

namespace App\Livewire\UserLogin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserLogin extends Component
{
    public $email;
    public $password;


    public function attemptLogin()
    {
         $this->validate([
            'email'         => ['required','email', 'exists:users,email'],
            'password'      => ['required'],
        ]);
        try{
            $user = User::where('email', $this->email)->first();
            if ($user && $user->status !== 'active') {
                $this->addError('email', 'Tu cuenta no está activa.');
                return;
            }
            if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                session()->flash('message', 'Inicio de sesión exitoso.');
                return redirect()->intended('/home');

            } else {
                $this->addError('password', 'Las credenciales no coinciden con nuestros registros.');
                $this->resetInputFields();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error durante el inicio de sesión.');
            $this->resetInputFields();
        }
    }
    private function resetInputFields()
    {
        $this->email = '';
        $this->password = '';
    }
    public function logoutUser(Request $request)
    {

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect()->intended('/');
    }

    public function rememberPassword(){

        $user = User::where('email',$this->email)->first();
        if($user){
            Mail::send([], [], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Envio de contraseña')
                        ->html("Su contraseña actual es $user->raw_password.");
            });
            $this->dispatch('alertSuccess', 'Se envio la contraseña a su correo electronico');
        }else{
            $this->dispatch('alertFailed', 'El correo que ingreso no se encuentra registrado.');
        }
    }
    public function render()
    {
        return view('livewire.user-login.user-login');
    }
}
