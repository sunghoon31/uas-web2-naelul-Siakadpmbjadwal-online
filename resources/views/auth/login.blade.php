<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aetheris Academic</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #05070f;
            --bg-secondary: #070a18;
            --fg: #e5e7eb;
            --muted: #9ca3af;
            --accent: #7c7cff;
            --accent-glow: rgba(124, 124, 255, 0.3);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--fg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .bg-animation {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: radial-gradient(circle at 20% 50%, rgba(124, 124, 255, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(124, 124, 255, 0.06) 0%, transparent 50%);
            animation: bgPulse 8s ease-in-out infinite;
        }

        @keyframes bgPulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            text-decoration: none;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .back-btn:hover {
            color: var(--fg);
            transform: translateX(-4px);
        }

        .back-btn svg {
            width: 20px;
            height: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(124, 124, 255, 0.15);
            border-radius: 24px;
            padding: clamp(32px, 5vw, 48px);
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--accent), #a855f7);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 32px;
            margin-bottom: 16px;
            box-shadow: 0 8px 32px var(--accent-glow);
        }

        .logo-section h2 {
            font-size: clamp(24px, 4vw, 32px);
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--fg);
        }

        .logo-section p {
            color: var(--muted);
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            color: var(--fg);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--muted);
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(124, 124, 255, 0.2);
            border-radius: 12px;
            color: var(--fg);
            font-size: 15px;
            transition: all 0.3s;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(124, 124, 255, 0.1);
        }

        input::placeholder {
            color: var(--muted);
        }

        .error-message {
            color: #ff6b6b;
            font-size: 13px;
            margin-top: 6px;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--accent);
        }

        .remember-me label {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            cursor: pointer;
            font-weight: 400;
        }

        .forgot-link {
            color: var(--accent);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .forgot-link:hover {
            color: #9c9cff;
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: var(--accent);
            color: var(--bg);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 24px var(--accent-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #9c9cff;
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(124, 124, 255, 0.5);
        }

        .btn-primary svg {
            width: 20px;
            height: 20px;
        }

        .register-link {
            text-align: center;
            margin-top: 24px;
            color: var(--muted);
            font-size: 14px;
        }

        .register-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #9c9cff;
        }

        .footer-text {
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            margin-top: 32px;
        }

        .status-message {
            background: rgba(124, 124, 255, 0.1);
            border: 1px solid var(--accent);
            color: var(--fg);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        @media (max-width: 640px) {
            .login-card {
                padding: 24px;
            }

            .form-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>

    <div class="login-container">
        <!-- Back Button -->
        <a href="/" class="back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Home
        </a>

        <!-- Login Card -->
        <div class="login-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo">Æ</div>
                <h2>Welcome Back</h2>
                <p>Sign in to your Aetheris account</p>
            </div>

            <!-- Status Message (if any) -->
            <!-- Uncomment if using Laravel Breeze status messages -->
            <!-- <div class="status-message">{{ session('status') }}</div> -->

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="your@email.com">
                    </div>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-footer">
                    <div class="remember-me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <label for="remember_me">Remember me</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-primary">
                    <span>Log in</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>

                <!-- Register Link -->
                <div class="register-link">
                    Don't have an account? 
                    <a href="{{ route('register') }}">Register here</a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="footer-text">
            © 2026 Aetheris Academic — Digital Academic Civilization
        </div>
    </div>
</body>
</html>