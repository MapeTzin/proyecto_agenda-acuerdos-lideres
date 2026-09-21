<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña | Agenda de Acuerdos OS</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #64748b;
            --dark: #0f172a;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .forgot-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 2rem;
        }

        .forgot-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
        }

        .icon-badge {
            width: 56px;
            height: 56px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.5rem;
            color: #818cf8;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        p {
            color: #94a3b8;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #cbd5e1;
            padding-left: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.85rem 1rem 0.85rem 3rem;
            border-radius: 1rem;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 0.95rem;
            border-radius: 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 20px -3px rgba(99, 102, 241, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .status-message {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 0.85rem 1rem;
            border-radius: 0.85rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            text-align: left;
            line-height: 1.4;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 0.75rem 1rem;
            border-radius: 0.85rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            text-align: left;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #94a3b8;
            font-size: 0.85rem;
            text-decoration: none;
            margin-top: 1.75rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: white;
        }

        /* Decorative Background */
        .decoration {
            position: absolute;
            z-index: 1;
            border-radius: 50%;
            filter: blur(80px);
        }

        .decoration-1 {
            width: 300px;
            height: 300px;
            background: rgba(99, 102, 241, 0.15);
            top: -100px;
            right: -100px;
        }

        .decoration-2 {
            width: 250px;
            height: 250px;
            background: rgba(16, 185, 129, 0.1);
            bottom: -50px;
            left: -50px;
        }
    </style>
</head>

<body>
    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>

    <div class="forgot-container">
        <div class="forgot-card">
            <div class="icon-badge">
                <i class="fas fa-key"></i>
            </div>

            <h2>Recuperar Contraseña</h2>
            <p>Ingresa tu correo institucional registrado y te enviaremos un enlace seguro para restablecer tu contraseña.</p>

            @if (session('status'))
                <div class="status-message">
                    <i class="fas fa-check-circle" style="margin-right: 6px;"></i> {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i> {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="usuario@mapetzin.com" required
                            value="{{ old('email') }}" autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Enviar Enlace de Recuperación
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al Inicio de Sesión
            </a>
        </div>
    </div>
</body>

</html>
