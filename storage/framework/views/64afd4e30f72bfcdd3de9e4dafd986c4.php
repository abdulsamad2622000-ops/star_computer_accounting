<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Star Computer — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 55%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", Arial, sans-serif;
            padding: 20px;
        }
        .login-layout {
            width: 100%;
            max-width: 1000px;
            min-height: 620px;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(15, 23, 42, 0.18);
            background: #fff;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            transform: translateY(20px);
            opacity: 0;
            animation: enterFade 0.8s ease-out forwards;
        }
        .left-panel {
            position: relative;
            background: #0f172a;
            color: #f8fafc;
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }
        .hero-text {
            font-size: clamp(2.75rem, 4vw, 4.5rem);
            line-height: 1.02;
            font-weight: 800;
            letter-spacing: -0.05em;
            margin-bottom: 1rem;
            max-width: 14ch;
            text-shadow: 0 18px 24px rgba(15, 23, 42, 0.35);
            opacity: 0.98;
            position: relative;
            z-index: 2;
        }
        .hero-text span {
            display: inline-block;
            margin-left: 0.35rem;
            font-size: 1.05em;
            transform: translateY(-1px);
        }
        .hero-sub {
            font-size: 1rem;
            color: rgba(255,255,255,0.5);
            position: relative;
            z-index: 2;
            margin-top: 8px;
        }
        .hero-ring {
            position: absolute;
            border: 84px solid #b78b25;
            border-radius: 50%;
            opacity: 0.92;
            filter: drop-shadow(0 24px 40px rgba(183, 139, 37, 0.2));
        }
        .hero-ring.ring-1 {
            width: 560px; height: 560px;
            top: -180px; right: -260px;
            animation: floatRing 8s ease-in-out infinite;
        }
        .hero-ring.ring-2 {
            width: 420px; height: 420px;
            bottom: -180px; left: -200px;
            animation: floatRing 9s ease-in-out infinite reverse;
        }
        .right-panel {
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }
        .brand-logo {
            font-size: 1rem;
            font-weight: 700;
            color: #b78b25;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .form-title {
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.03em;
            color: #111827;
        }
        .form-title .accent { color: #b78b25; }
        .form-subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1.8rem;
        }
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-control {
            border-radius: 12px;
            border: 1px solid #d1d5db;
            padding: 0.82rem 1.1rem;
            font-size: 0.95rem;
            background: #fff;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control:hover {
            border-color: #c7ad5b;
            transform: translateY(-1px);
        }
        .form-control:focus {
            border-color: #b78b25;
            box-shadow: 0 0 0 3px rgba(183, 139, 37, 0.15);
            outline: none;
            transform: translateY(-1px);
        }
        .form-control.is-invalid {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }
        .invalid-feedback {
            font-size: 0.8rem;
            color: #dc2626;
            margin-top: 4px;
        }
        .password-group { position: relative; }
        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            transition: color 0.2s;
        }
        .toggle-password:hover { color: #111827; }
        .form-extra {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1rem 0 1.5rem;
            font-size: 0.88rem;
            color: #475569;
        }
        .btn-submit {
            border-radius: 12px;
            background: linear-gradient(135deg, #b78b25 0%, #d19f3d 100%);
            border: none;
            color: #fff;
            padding: 0.9rem 1.35rem;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 12px 24px rgba(183, 139, 37, 0.25);
            transition: all 0.25s ease;
            letter-spacing: 0.5px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #d19f3d 0%, #b78b25 100%);
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(183, 139, 37, 0.3);
            color: #fff;
        }
        .btn-submit:active { transform: translateY(0); }
        .error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.875rem;
            margin-bottom: 16px;
        }
        @keyframes enterFade {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes floatRing {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(12px); }
        }
        @media (max-width: 768px) {
            .login-layout {
                grid-template-columns: 1fr;
                min-height: auto;
            }
            .left-panel {
                min-height: 200px;
                padding: 2.5rem 2rem;
            }
            .right-panel { padding: 2rem 1.5rem; }
            .hero-text { font-size: 2rem; }
        }
    </style>
</head>
<body>

<div class="login-layout">

    <!-- Left Panel -->
    <div class="left-panel">
        <div class="hero-text">
            Hey Welcome Back <span>👋</span>
        </div>
        <div class="hero-sub">Star Computer Accounting System</div>
        <div class="hero-ring ring-1"></div>
        <div class="hero-ring ring-2"></div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
        <div class="brand-logo">⭐ Star Computer</div>
        <div class="form-title">Log<span class="accent">in</span></div>
        <div class="form-subtitle">Apni ID se login karein</div>

        
        <?php if($errors->any()): ?>
        <div class="error-alert">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div><?php echo e($error); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>

            
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text"
                       name="username"
                       class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="Username enter karein..."
                       value="<?php echo e(old('username')); ?>"
                       required autofocus>
                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="password-group">
                    <input type="password"
                           name="password"
                           id="passwordInput"
                           class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="Password enter karein..."
                           required>
                    <button type="button"
                            class="toggle-password"
                            onclick="togglePass()">👁️</button>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div class="form-extra">
                <label class="mb-0" style="cursor:pointer">
                    <input type="checkbox" name="remember"
                           class="form-check-input me-1"
                           style="vertical-align:middle">
                    Remember me
                </label>
            </div>

            
            <div class="d-grid">
                <button type="submit" class="btn btn-submit">
                    LOGIN →
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('passwordInput');
    const btn   = document.querySelector('.toggle-password');
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁️';
    }
}
</script>

</body>
</html><?php /**PATH C:\xampp\htdocs\star_computer_accounting\resources\views/auth/login.blade.php ENDPATH**/ ?>