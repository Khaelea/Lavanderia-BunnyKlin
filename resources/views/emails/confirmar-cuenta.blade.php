    <!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f9; padding: 40px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h2 style="color: #1E55AA; margin-bottom: 20px;">¡Hola, {{ $user->name }}!</h2>
        
        <p style="color: #334155; line-height: 1.6;">
            Se ha creado una cuenta para ti en el sistema de **BunnyKlin** con el rol de <strong>{{ $user->role }}</strong>.
        </p>

        <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px dashed #cbd5e1;">
            <p style="margin: 0; color: #334155;">Tu correo de acceso: <strong>{{ $user->email }}</strong></p>
            <p style="margin: 10px 0 0 0; color: #334155;">Tu contraseña temporal: <strong style="color: #1E55AA; font-size: 18px;">{{ $password }}</strong></p>
        </div>

        <p style="color: #334155; line-height: 1.6;">
            Para poder ingresar al sistema, primero debes confirmar y activar tu cuenta haciendo clic en el siguiente botón:
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('cuenta.confirmar', $token) }}" 
               style="background-color: #1E55AA; color: white; padding: 14px 28px; text-decoration: none; font-weight: bold; border-radius: 8px; display: inline-block;">
                Activar Cuenta e Iniciar Sesión
            </a>
        </div>
    </div>
</body>
</html>