<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Agenda de Acuerdos OS</title>
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
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
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
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 2rem;">
                <div
                    style="border: 3px solid var(--primary); padding: 12px 18px; display: flex; align-items: center; gap: 10px; border-radius: 8px; position: relative; background: rgba(99, 102, 241, 0.05);">
                    <span style="font-size: 2.2rem; font-weight: 800; color: white;">M</span>
                    <i class="fas fa-sparkles" style="font-size: 1rem; color: var(--primary);"></i>
                    <span style="font-size: 2.2rem; font-weight: 800; color: white;">T</span>
                    <span
                        style="position: absolute; top: -12px; right: -12px; font-size: 0.7rem; border: 1px solid white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; background: var(--dark);">R</span>
                </div>
                <div style="margin-top: 15px; font-size: 1.1rem; font-weight: 700; color: white; letter-spacing: 2px;">
                    MAPE+TZIN<br>
                    <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 400;">GOBIERNO</span>
                </div>
            </div>
            <h2 style="margin-top: 1rem;">Bienvenido</h2>
            <p>Ingresa tus credenciales para continuar</p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="error-message">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php echo e($error); ?><br>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form action="<?php echo e(route('login.post')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="ejemplo@agenda.com" required
                            value="<?php echo e(old('email')); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">Ingresar al Sistema</button>
            </form>

            <div class="footer-links" style="margin-top: 2rem;">
                © <?php echo e(date('Y')); ?> Mape+Tzin Gobierno
            </div>
        </div>
    </div>
</body>

</html><?php /**PATH D:\Sistemas\repos\agenda-acuerdos-lideres\resources\views/auth/login.blade.php ENDPATH**/ ?>