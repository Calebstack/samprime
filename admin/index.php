<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['adminonline'])) {
    header("location:admindashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Samprime Autos Global</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background-color: #050d1a;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Full-page car background image with dark overlay */
        .bg-scene {
            position: fixed;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1920&q=85');
            background-size: cover;
            background-position: center 60%;
            z-index: 0;
        }

        .bg-scene::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(2, 10, 30, 0.88) 0%,
                rgba(10, 20, 55, 0.82) 50%,
                rgba(2, 8, 20, 0.92) 100%
            );
        }

        /* Animated glow orbs */
        .glow-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.22;
            pointer-events: none;
            z-index: 1;
            animation: floatOrb 8s ease-in-out infinite;
        }

        .glow-orb.orb-1 {
            width: 480px; height: 480px;
            background: #2563eb;
            top: -120px; left: -100px;
            animation-delay: 0s;
        }

        .glow-orb.orb-2 {
            width: 360px; height: 360px;
            background: #0284c7;
            bottom: -80px; right: -60px;
            animation-delay: 3s;
        }

        .glow-orb.orb-3 {
            width: 240px; height: 240px;
            background: #6366f1;
            top: 50%; left: 60%;
            animation-delay: 5s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        /* Login card */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 1.5rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 24px;
            padding: 3rem 2.8rem;
            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(37, 99, 235, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: cardIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Brand */
        .admin-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            font-family: 'Outfit', sans-serif;
            font-size: 1.65rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 0.4rem;
            text-decoration: none;
        }

        .admin-brand span { color: #60a5fa; }

        .admin-brand i {
            color: #60a5fa;
            font-size: 1.5rem;
        }

        /* Shield icon area */
        .shield-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1.4rem 0 1.8rem;
        }

        .shield-icon {
            width: 68px; height: 68px;
            border-radius: 20px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            color: #fff;
            box-shadow:
                0 0 0 8px rgba(37, 99, 235, 0.18),
                0 0 30px rgba(37, 99, 235, 0.45),
                0 8px 24px rgba(37, 99, 235, 0.35);
            animation: pulseGlow 2.5s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 8px rgba(37,99,235,0.18), 0 0 30px rgba(37,99,235,0.45), 0 8px 24px rgba(37,99,235,0.35); }
            50%       { box-shadow: 0 0 0 14px rgba(37,99,235,0.12), 0 0 50px rgba(37,99,235,0.6), 0 8px 32px rgba(37,99,235,0.5); }
        }

        .portal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
            text-align: center;
            margin-bottom: 0.35rem;
        }

        .portal-sub {
            text-align: center;
            color: rgba(255,255,255,0.55);
            font-size: 0.9rem;
            margin-bottom: 2.2rem;
        }

        /* Alerts */
        .alert {
            padding: 0.9rem 1.2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(22, 163, 74, 0.15);
            border: 1px solid rgba(22, 163, 74, 0.3);
            color: #86efac;
        }

        /* Form */
        .form-group {
            margin-bottom: 1.3rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.55rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.35);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #ffffff;
            padding: 0.88rem 1.1rem 0.88rem 2.8rem;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.97rem;
            outline: none;
            transition: all 0.25s ease;
        }

        .form-control::placeholder { color: rgba(255,255,255,0.3); }

        .form-control:focus {
            border-color: rgba(37, 99, 235, 0.7);
            background: rgba(255,255,255,0.1);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2), 0 0 20px rgba(37, 99, 235, 0.15);
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 1rem;
            margin-top: 0.5rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.6);
        }

        .btn-login:active { transform: translateY(0); }

        /* Divider line */
        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 2rem 0 0;
        }

        .security-note {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }
    </style>
</head>
<body>

<!-- Background scene -->
<div class="bg-scene"></div>

<!-- Glowing orbs -->
<div class="glow-orb orb-1"></div>
<div class="glow-orb orb-2"></div>
<div class="glow-orb orb-3"></div>

<div class="login-wrapper">
    <div class="login-card">

        <!-- Brand -->
        <div>
            <a class="admin-brand">
                <i class="fa-solid fa-car-side"></i> SAMPRIME <span>AUTOS</span>
            </a>
        </div>

        <!-- Shield icon -->
        <div class="shield-wrap">
            <div class="shield-icon">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>

        <h1 class="portal-title">Admin Portal</h1>
        <p class="portal-sub">Sign in to manage your vehicle inventory</p>

        <?php if (isset($_SESSION['errormsg'])): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?php echo htmlspecialchars($_SESSION['errormsg']); unset($_SESSION['errormsg']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['feedback'])): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?php echo htmlspecialchars($_SESSION['feedback']); unset($_SESSION['feedback']); ?>
            </div>
        <?php endif; ?>

        <form action="action/login.php" method="POST" novalidate>
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your admin email" autocomplete="email">
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1.8rem;">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" autocomplete="current-password">
                </div>
            </div>
            <button type="submit" name="login" class="btn-login">
                <i class="fa-solid fa-right-to-bracket"></i> Login to Dashboard
            </button>
        </form>

        <hr class="divider">
        <p class="security-note">
            <i class="fa-solid fa-shield-halved"></i> Restricted access — authorized personnel only
        </p>

    </div>
</div>

</body>
</html>
