<?php

namespace App\Livewire\UserLogin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
                return redirect()->intended('/report'); 
                
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
    public function render()
    {
        return view('livewire.user-login.user-login');
    }
}
