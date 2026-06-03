<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f9; padding: 40px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h2 style="color: #1E55AA; margin-bottom: 20px;">Solicitud de Aprobación de Cuenta</h2>
        
        <p style="color: #334155; line-height: 1.6;">
            Se ha solicitado registrar a un nuevo usuario en el CRM. Hasta que no se apruebe, el usuario no podrá iniciar sesión y no recibirá sus credenciales.
        </p>

        <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px dashed #cbd5e1;">
            <p style="margin: 0 0 10px 0; color: #334155;"><strong>Detalles del nuevo registro:</strong></p>
            <p style="margin: 5px 0; color: #334155;">Nombre: <strong>{{ $user->name }}</strong></p>
            <p style="margin: 5px 0; color: #334155;">Email: <strong>{{ $user->email }}</strong></p>
            <p style="margin: 5px 0; color: #334155;">Rol: <strong>{{ $user->role }}</strong></p>
        </div>

        <p style="color: #334155; line-height: 1.6; margin-bottom: 25px;">
            ¿Deseas permitir la creación y acceso de esta cuenta?
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <!-- Botón de Permitir (Verde) -->
            <a href="{{ route('cuenta.aprobar', $token) }}" 
               style="background-color: #10b981; color: white; padding: 14px 24px; text-decoration: none; font-weight: bold; border-radius: 8px; display: inline-block; margin-right: 15px;">
                Permitir (Activar)
            </a>

            <!-- Botón de Cancelar (Rojo) -->
            <a href="{{ route('cuenta.rechazar', $token) }}" 
               style="background-color: #ef4444; color: white; padding: 14px 24px; text-decoration: none; font-weight: bold; border-radius: 8px; display: inline-block;">
                Cancelar (Eliminar)
            </a>
        </div>
    </div>
</body>
</html>