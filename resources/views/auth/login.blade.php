<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Agenda de Acuerdos OS</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg viewBox='0 0 2325 2290' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3ClinearGradient x1='1175.85' y1='16.8703' x2='3225.15' y2='2459.13' gradientUnits='userSpaceOnUse' id='f'%3E%3Cstop offset='0' stop-color='%23460046'/%3E%3Cstop offset='0.5' stop-color='%23CC3D77'/%3E%3Cstop offset='1' stop-color='%23FF558A'/%3E%3C/linearGradient%3E%3C/defs%3E%3Cg transform='translate(-1038 -93)'%3E%3Cpath d='M1059.5 391.488C1059.5 238.511 1183.5 114.5 1336.46 114.5L3064.54 114.5C3217.5 114.5 3341.5 238.511 3341.5 391.488L3341.5 2084.51C3341.5 2237.49 3217.5 2361.5 3064.54 2361.5L1336.46 2361.5C1183.5 2361.5 1059.5 2237.49 1059.5 2084.51Z' stroke='%23FFFFFF' stroke-width='42.8365' fill='url(%23f)'/%3E%3Cpath d='M1648.77 701.295 1825.48 712.005 1820.9 787.491C1819.07 817.811 1842.16 843.88 1872.48 845.718 1902.8 847.555 1928.87 824.466 1930.7 794.146L1935.28 718.659 2525.45 754.428 2520.87 829.915C2519.03 860.235 2542.12 886.304 2572.44 888.141 2602.76 889.979 2628.83 866.889 2630.67 836.569L2635.24 761.083 2811.95 771.793C2876.38 775.698 2925.45 831.094 2921.54 895.524L2867.06 1794.5C2863.15 1858.93 2807.75 1907.99 2743.32 1904.09L1580.15 1833.59C1515.72 1829.69 1466.65 1774.29 1470.56 1709.86L1525.04 810.885C1528.95 746.455 1584.34 697.39 1648.77 701.295Z' stroke='%23FFFFFF' stroke-width='45.8333' fill='%23FFFFFF' fill-opacity='0.2'/%3E%3Cpath d='M1615.47 1179.69C1616.57 1161.68 1632.04 1147.98 1650.04 1149.07L2756.61 1216.13C2774.61 1217.22 2788.32 1232.7 2787.23 1250.7L2757.28 1744.8C2756.19 1762.8 2740.71 1776.51 2722.71 1775.42L1616.15 1708.35C1598.15 1707.26 1584.44 1691.78 1585.53 1673.78Z' stroke='%23FFFFFF' stroke-width='45.8333' fill='%23000000' fill-opacity='0.25'/%3E%3C/g%3E%3C/svg%3E">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Righteous&display=swap" rel="stylesheet">
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

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.75rem;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        p {
            color: #94a3b8;
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
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
            border-radius: 1rem;
            padding: 0.85rem 1.25rem 0.85rem 3rem;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-login {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 1rem;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
        }

        .footer-links {
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 0.75rem;
            border-radius: 0.75rem;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            text-align: left;
        }

        /* Attempts & Lockout Styles */
        .attempts-card {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: 0.85rem;
            padding: 0.85rem 1rem;
            margin-bottom: 1.25rem;
            text-align: left;
            animation: fadeIn 0.3s ease-out;
        }

        .attempts-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .attempts-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #fbbf24;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .attempts-tag {
            font-size: 0.7rem;
            font-weight: 700;
            background: rgba(245, 158, 11, 0.2);
            color: #fde68a;
            padding: 0.15rem 0.5rem;
            border-radius: 1rem;
        }

        .attempts-bars {
            display: flex;
            gap: 0.35rem;
            margin-bottom: 0.45rem;
        }

        .attempt-pip {
            flex: 1;
            height: 5px;
            border-radius: 3px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .attempt-pip.used {
            background: #f59e0b;
            box-shadow: 0 0 6px rgba(245, 158, 11, 0.6);
        }

        .attempts-sub {
            font-size: 0.75rem;
            color: #cbd5e1;
            margin: 0;
        }

        /* Lockout Timer Card */
        .lockout-card {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(185, 28, 28, 0.1) 100%);
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 16px -4px rgba(239, 68, 68, 0.2);
            animation: shakeAlert 0.4s ease-in-out;
        }

        @keyframes shakeAlert {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .lockout-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.25rem 0.65rem;
            border-radius: 1rem;
            margin-bottom: 0.5rem;
        }

        .timer-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            font-family: monospace;
            letter-spacing: 2px;
            margin: 0.4rem 0;
            text-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
        }

        .timer-display i {
            font-size: 1.5rem;
            color: #f87171;
            animation: pulseClock 1s infinite alternate;
        }

        @keyframes pulseClock {
            from { transform: scale(1); opacity: 0.8; }
            to { transform: scale(1.1); opacity: 1; }
        }

        .timer-progress {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
            margin: 0.6rem 0;
        }

        .timer-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ef4444 0%, #f59e0b 100%);
            width: 100%;
            transition: width 1s linear;
        }

        .lockout-sub {
            font-size: 0.78rem;
            color: #cbd5e1;
            margin: 0;
        }

        .btn-login.btn-disabled {
            background: #475569 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
            opacity: 0.7;
        }

        .form-control:disabled {
            background: rgba(255, 255, 255, 0.02) !important;
            color: #64748b !important;
            cursor: not-allowed !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
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

    <div class="login-container">
        <div class="login-card">
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 1.75rem;">
                <x-icons.agenda-logo style="width: 68px; height: 68px; filter: drop-shadow(0 10px 24px rgba(255, 85, 138, 0.35));" />
                <div style="margin-top: 0.9rem; display: flex; align-items: center; gap: 0.5rem; font-family: 'Righteous', sans-serif; color: white;">
                    <span style="font-size: 1.25rem; letter-spacing: 0.04em;">Agenda</span>
                    <span style="font-size: 0.9rem; opacity: 0.7;">|</span>
                    <span style="font-size: 1.05rem; letter-spacing: 0.06em;">Acuerdos</span>
                </div>
            </div>
            <h2 style="margin-top: 1rem;">Bienvenido</h2>
            <p>Ingresa tus credenciales para continuar</p>

            @if (session('status'))
                <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 0.75rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.85rem; text-align: left;">
                    <i class="fas fa-check-circle" style="margin-right: 6px;"></i> {{ session('status') }}
                </div>
            @endif

            @if (session('lockout_seconds'))
                <div class="lockout-card" id="lockoutCard">
                    <div class="lockout-badge">
                        <i class="fas fa-shield-virus"></i> Bloqueo de Seguridad
                    </div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: #fecaca; margin-bottom: 0.25rem;">
                        Acceso temporalmente restringido
                    </div>
                    <div class="timer-display">
                        <i class="fas fa-stopwatch"></i>
                        <span id="timerCountdown">00:00</span>
                    </div>
                    <div class="timer-progress">
                        <div class="timer-progress-fill" id="timerFill"></div>
                    </div>
                    <p class="lockout-sub" id="lockoutInstruction">
                        Podrá intentar ingresar nuevamente en cuanto el temporizador llegue a cero.
                    </p>
                </div>
            @endif

            @if (session('attempts') && !session('lockout_seconds'))
                <div class="attempts-card">
                    <div class="attempts-header">
                        <div class="attempts-title">
                            <i class="fas fa-exclamation-triangle"></i> Control de Intentos
                        </div>
                        <span class="attempts-tag">
                            Intento {{ session('attempts') }} de {{ session('max_attempts', 5) }}
                        </span>
                    </div>
                    <div class="attempts-bars">
                        @for ($i = 1; $i <= session('max_attempts', 5); $i++)
                            <div class="attempt-pip {{ $i <= session('attempts') ? 'used' : '' }}"></div>
                        @endfor
                    </div>
                    <p class="attempts-sub">
                        Te quedan <strong>{{ session('retries_left', 0) }}</strong> intento(s) antes de bloquear el acceso temporalmente.
                    </p>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <div style="display: flex; align-items: flex-start; gap: 0.4rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-info-circle" style="margin-top: 2px;"></i>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="emailInput" class="form-control" placeholder="ejemplo@mapetzin.com" required
                            value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="margin-bottom: 0; padding-left: 0.5rem;">Contraseña</label>
                        <a href="{{ route('password.request') }}" style="color: #818cf8; font-size: 0.8rem; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#a5b4fc'" onmouseout="this.style.color='#818cf8'">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">Ingresar al Sistema</button>
            </form>

            <div class="footer-links" style="margin-top: 2rem;">
                © {{ date('Y') }} Mape+Tzin Gobierno
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let secondsRemaining = {{ (int) session('lockout_seconds', 0) }};
            const totalLockoutSeconds = secondsRemaining;

            const timerElement = document.getElementById('timerCountdown');
            const timerFill = document.getElementById('timerFill');
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('passwordInput');
            const lockoutInstruction = document.getElementById('lockoutInstruction');

            if (secondsRemaining > 0) {
                // Deshabilitar botón e inputs mientras dure el bloqueo
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('btn-disabled');
                    submitBtn.innerHTML = '<i class="fas fa-lock"></i> Acceso Bloqueado';
                }
                if (emailInput) emailInput.disabled = true;
                if (passwordInput) passwordInput.disabled = true;

                function formatTime(secs) {
                    const m = Math.floor(secs / 60);
                    const s = secs % 60;
                    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                }

                function tick() {
                    if (timerElement) {
                        timerElement.textContent = formatTime(secondsRemaining);
                    }

                    if (timerFill && totalLockoutSeconds > 0) {
                        const pct = (secondsRemaining / totalLockoutSeconds) * 100;
                        timerFill.style.width = pct + '%';
                    }

                    if (secondsRemaining <= 0) {
                        clearInterval(intervalId);

                        // Reactivar interfaz
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('btn-disabled');
                            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Ingresar al Sistema';
                        }
                        if (emailInput) emailInput.disabled = false;
                        if (passwordInput) {
                            passwordInput.disabled = false;
                            passwordInput.focus();
                        }

                        if (timerElement) timerElement.textContent = '00:00';
                        if (timerFill) timerFill.style.width = '0%';

                        if (lockoutInstruction) {
                            lockoutInstruction.innerHTML = '<span style="color: #34d399; font-weight: 700;"><i class="fas fa-check-circle"></i> Bloqueo finalizado. Ya puede intentar ingresar de nuevo.</span>';
                        }
                    } else {
                        secondsRemaining--;
                    }
                }

                tick();
                const intervalId = setInterval(tick, 1000);
            }
        });
    </script>
</body>

</html>