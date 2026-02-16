<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PT Pindad</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo2.png') ?>">
    
    <!-- Bootstrap CSS -->
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-light: rgba(255, 255, 255, 0.9);
            --text-lighter: rgba(255, 255, 255, 0.7);
            --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 10px 30px rgba(0, 0, 0, 0.2);
            --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(45deg, #0f172a, #1e293b, #334155);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            position: relative;
            overflow-x: hidden;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Animated Background Shapes */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            filter: blur(40px);
        }
        
        .shape-1 {
            width: 500px;
            height: 500px;
            top: -200px;
            left: -200px;
            background: rgba(79, 70, 229, 0.1);
        }
        
        .shape-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: rgba(139, 92, 246, 0.1);
        }
        
        .shape-3 {
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(99, 102, 241, 0.05);
        }
        
        /* Main Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        /* Login Card */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: var(--shadow-lg);
            animation: cardAppear 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }
        
        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
        }
        
        .logo-container img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }
        
        /* Welcome Text */
        .welcome-text h1 {
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 8px;
            text-align: center;
            background: linear-gradient(135deg, #ffffff 0%, #d1d5db 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .welcome-text p {
            color: var(--text-lighter);
            font-size: 14px;
            text-align: center;
            font-weight: 400;
            margin-bottom: 40px;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        
        .form-label {
            display: block;
            color: var(--text-light);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .input-group {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 16px 48px 16px 16px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 15px;
            font-weight: 400;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-light);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
            font-size: 18px;
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: var(--primary-light);
        }
        
        /* Remember Me */
        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .checkbox-container input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-dark);
            cursor: pointer;
        }
        
        .checkbox-container label {
            color: var(--text-lighter);
            font-size: 14px;
            cursor: pointer;
        }
        
        .forgot-password {
            color: var(--primary-light);
            font-size: 14px;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: #a5b4fc;
            text-decoration: underline;
        }
        
        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .submit-btn i {
            font-size: 18px;
        }
        
        /* Alert */
        .alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Copyright */
        .copyright {
            text-align: center;
            margin-top: 32px;
            color: var(--text-lighter);
            font-size: 13px;
            padding-top: 20px;
            border-top: 1px solid var(--glass-border);
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 32px 24px;
                border-radius: 20px;
            }
            
            .welcome-text h1 {
                font-size: 28px;
            }
            
            .form-input {
                padding: 14px 44px 14px 14px;
            }
        }
        
        /* Loading Animation */
        .submit-btn.loading {
            position: relative;
            color: transparent;
        }
        
        .submit-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    
    <!-- Background Shapes -->
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <!-- Main Container -->
    <div class="login-container">
        
        <!-- Login Card -->
        <div class="login-card">
            
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-container">
                    <img src="<?= base_url('assets/img/logo2.png') ?>" alt="Pindad Logo">
                </div>
                <div class="welcome-text">
                    <h1>Pindad Inventory</h1>
                    <p>Sign in to access the management system</p>
                </div>
            </div>
            
            <!-- Error Message -->
            <?php if(session()->getFlashdata('error')): ?>
            <div class="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="post" action="<?= base_url('login') ?>" id="loginForm">
                <?= csrf_field() ?>
                
                <!-- Username Input -->
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="username" 
                            class="form-input" 
                            placeholder="Enter your username" 
                            required
                            autocomplete="username"
                            autofocus
                        >
                        <span class="input-icon">
                            <i class="bi bi-person-fill"></i>
                        </span>
                    </div>
                </div>
                
                <!-- Password Input -->
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-input" 
                            placeholder="Enter your password" 
                            required
                            minlength="8"
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>
            </form>
            
        </div>
        
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const submitBtn = document.getElementById('submitBtn');
            
            // Password toggle functionality
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? 
                    '<i class="bi bi-eye-fill"></i>' : 
                    '<i class="bi bi-eye-slash-fill"></i>';
            });
            
            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                // Basic validation
                const username = this.username.value.trim();
                const password = this.password.value.trim();
                
                if (!username || !password) {
                    e.preventDefault();
                    return;
                }
                
                // Show loading state
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });
            
            // Input validation styling
            const inputs = form.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.add('has-value');
                    } else {
                        this.classList.remove('has-value');
                    }
                });
                
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
            
            // Auto-hide error message after 5 seconds
            const errorAlert = document.querySelector('.alert');
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.style.opacity = '0';
                    errorAlert.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        errorAlert.style.display = 'none';
                    }, 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>