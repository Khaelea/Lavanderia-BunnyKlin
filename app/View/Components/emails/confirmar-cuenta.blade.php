<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmar Cuenta - BunnyKlin</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 40px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; rounded-radius: 8px; border: 1px solid #e2e8f0;">
        <h2 style="color: #1e40af; margin-bottom: 20px;">¡Hola, {{ $user->name }}!</h2>
        <p style="color: #334155; line-height: 1.6;">
            Se ha creado una cuenta para ti en el sistema de **BunnyKlin** con el rol de <strong>{{ $user->role }}</strong>.
        </p>
        <p style="color: #334155; line-height: 1.6;">
            Tu cuenta actualmente se encuentra en estado <strong>pendiente</strong>. Para poder ingresar al sistema, es necesario que confirmes tu correo haciendo clic en el siguiente botón:
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('cuenta.confirmar', $token) }}" 
               style="background-color: #1e40af; color: white; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; display: inline-block;">
                Confirmar y Activar Cuenta
            </a>
        </div>

        <p style="color: #64748b; font-size: 12px; line-height: 1.5;">
            Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:<br>
            <a href="{{ route('cuenta.confirmar', $token) }}" style="color: #1e40af;">{{ route('cuenta.confirmar', $token) }}</a>
        </p>
    </div>
</body>
</html>