<!DOCTYPE html>
<html>
<body>
    <h2>Nueva solicitud: {{ $employee->full_name }}</h2>
    <p>Email: {{ $employee->email }}</p>
    
    <div style="margin-top: 20px;">
        <a href="{{ route('personal.approve', $employee->id) }}" style="padding: 10px; background: green; color: white;">APROBAR</a>
        <a href="{{ route('personal.reject', $employee->id) }}" style="padding: 10px; background: red; color: white; margin-left: 10px;">RECHAZAR</a>
    </div>
</body>
</html> 