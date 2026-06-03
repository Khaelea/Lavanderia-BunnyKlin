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
    // Guarda al empleado como pendiente y dispara el correo
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'rol' => 'required',
            'password' => 'required|min:6'
        ]);

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

        return redirect()->back()->with('success', '¡Solicitud enviada! La cuenta está pendiente de aprobación por el administrador.');
    }

    // ¡NUEVA! Función para eliminar cuenta directamente desde la tabla de la página
    public function eliminarPorId($id)
    {
        $usuario = User::findOrFail($id);
        $nombre = $usuario->name;
        
        DB::table('users')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Has eliminado la cuenta de '.$nombre.' exitosamente.');
    }

    // Funciones que se activan desde el correo
    public function aprobar($token)
    {
        $usuario = User::where('confirmation_token', $token)->first();

        if (!$usuario) {
            return redirect('/personal')->with('error', 'El enlace no es válido o esta solicitud ya fue procesada.');
        }

        $usuario->status = 'activo';
        $usuario->confirmation_token = null; 
        $usuario->email_verified_at = now();
        $usuario->save();

        return redirect('/personal')->with('success', '¡Has aprobado la cuenta de '.$usuario->name.' con éxito!');
    }

    public function rechazar($token)
    {
        $usuario = User::where('confirmation_token', $token)->first();

        if (!$usuario) {
            return redirect('/personal')->with('error', 'El enlace no es válido o esta solicitud ya fue procesada.');
        }

        $nombre = $usuario->name;
        DB::table('users')->where('id', $usuario->id)->delete();

        return redirect('/personal')->with('success', 'Has rechazado y eliminado la solicitud de cuenta de '.$nombre.'.');
    }
}