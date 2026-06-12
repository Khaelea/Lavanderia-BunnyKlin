<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Mail\AprobacionCuentaMailable;

class EmpleadoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'rol' => 'required',
            'password' => 'required|min:6'
        ]);

        try {
            DB::beginTransaction();

            $token = Str::random(60);

            $empleado = User::create([
                'name' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->rol,
                'status' => 'pendiente', 
                'confirmation_token' => $token
            ]);

            Mail::to('soporte@inteligreen.com.mx')->send(new AprobacionCuentaMailable($empleado, $token));

            DB::commit();

            return redirect()->back()->with('success', '¡Solicitud enviada! La cuenta está pendiente de aprobación.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No se pudo procesar la solicitud. Revisa la conexión al correo.')->withInput();
        }
    }

    public function eliminarPorId($id)
    {
        $usuario = User::findOrFail($id);
        $nombre = $usuario->name;
        $usuario->delete();
        
        return redirect()->back()->with('success', 'Has eliminado la cuenta de '.$nombre.' exitosamente.');
    }

    public function aprobar($token)
    {
        $tokenLimpio = trim($token);
        
        $usuario = User::where('confirmation_token', $tokenLimpio)->first();

        if (!$usuario) {
            return redirect('/personal')->with('error', 'El enlace no es válido o esta solicitud ya fue procesada.');
        }

        $usuario->update([
            'status' => 'activo',
            'confirmation_token' => null, 
            'email_verified_at' => now()
        ]);

        return redirect('/personal')->with('success', '¡Has aprobado la cuenta de '.$usuario->name.' con éxito!');
    }

    public function rechazar($token)
    {
        $tokenLimpio = trim($token);
        
        $usuario = User::where('confirmation_token', $tokenLimpio)->first();

        if (!$usuario) {
            return redirect('/personal')->with('error', 'El enlace no es válido o esta solicitud ya fue procesada.');
        }

        $usuario->delete();
        
        return redirect('/personal')->with('success', 'Has rechazado la solicitud.');
    }
}