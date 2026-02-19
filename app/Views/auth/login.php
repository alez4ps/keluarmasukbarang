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
            --primary-blue: #0066b3;      /* Biru utama Pindad */
            --secondary-blue: #004b82;     /* Biru lebih gelap */
            --light-blue: #e6f0fa;         /* Biru sangat muda untuk background */
            --accent-blue: #4a90e2;         /* Biru aksen */
            --white: #ffffff;
            --off-white: #f8fafc;
            --gray-light: #e2e8f0;
            --gray: #64748b;
            --text-dark: #1e293b;
            --text-soft: #334155;
            --shadow-soft: 0 10px 30px rgba(0, 102, 179, 0.1);
            --shadow-strong: 0 20px 40px rgba(0, 75, 130, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(145deg, var(--off-white) 0%, #ffffff 50%, var(--light-blue) 100%);
            position: relative;
            overflow-x: hidden;
        }
        
        /* Pattern Background */
        .bg-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 102, 179, 0.03) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(0, 102, 179, 0.03) 0%, transparent 20%),
                linear-gradient(45deg, rgba(0, 102, 179, 0.02) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(0, 102, 179, 0.02) 25%, transparent 25%);
            background-size: 60px 60px;
        }
        
        /* Geometric Shapes */
        .shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        .shape-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(145deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
            opacity: 0.05;
            filter: blur(50px);
        }
        
        .shape-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
        }
        
        .shape-2 {
            width: 300px;
            height: 300px;
            bottom: -50px;
            right: -50px;
            background: linear-gradient(145deg, var(--accent-blue) 0%, var(--primary-blue) 100%);
        }
        
        .shape-3 {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 80%;
            transform: translate(-50%, -50%);
            background: var(--primary-blue);
            opacity: 0.03;
            filter: blur(60px);
        }
        
        /* Main Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
        }
        
        /* Login Card */
        .login-card {
            width: 100%;
            max-width: 460px;
            background: var(--white);
            border-radius: 32px;
            padding: 50px 48px;
            box-shadow: var(--shadow-strong);
            animation: slideUp 0.7s ease-out;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 102, 179, 0.1);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Card Decoration */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
        }
        
        .login-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle at bottom right, rgba(0, 102, 179, 0.03) 0%, transparent 70%);
            pointer-events: none;
        }
        
        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .logo-container {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            background: linear-gradient(145deg, var(--off-white) 0%, var(--white) 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0, 102, 179, 0.1);
            padding: 15px;
        }
        
        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        /* Welcome Text */
        .welcome-text h1 {
            color: var(--text-dark);
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .welcome-text p {
            color: var(--gray);
            font-size: 15px;
            font-weight: 400;
            margin-bottom: 0;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        
        .form-label {
            display: block;
            color: var(--text-soft);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .input-group {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 16px 48px 16px 20px;
            background: var(--off-white);
            border: 2px solid transparent;
            border-radius: 16px;
            color: var(--text-dark);
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            background: var(--white);
            box-shadow: 0 4px 12px rgba(0, 102, 179, 0.1);
        }
        
        .form-input::placeholder {
            color: var(--gray);
            font-weight: 400;
            opacity: 0.6;
        }
        
        .input-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-blue);
            font-size: 18px;
            opacity: 0.7;
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary-blue);
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            transition: all 0.3s ease;
            opacity: 0.7;
        }
        
        .password-toggle:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
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
            accent-color: var(--primary-blue);
            cursor: pointer;
        }
        
        .checkbox-container label {
            color: var(--text-soft);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }
        
        .forgot-password {
            color: var(--primary-blue);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .forgot-password:hover {
            color: var(--secondary-blue);
            text-decoration: underline;
        }
        
        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(145deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            border: none;
            border-radius: 16px;
            color: var(--white);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(0, 102, 179, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 102, 179, 0.4);
        }
        
        .submit-btn:hover::before {
            left: 100%;
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .submit-btn i {
            font-size: 20px;
        }
        
        /* Alert */
        .alert {
            background: linear-gradient(145deg, #fee2e2 0%, #fef2f2 100%);
            border-left: 4px solid #ef4444;
            color: #b91c1c;
            padding: 16px 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            animation: slideAlert 0.4s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert i {
            color: #ef4444;
            font-size: 18px;
        }
        
        @keyframes slideAlert {
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
            margin-top: 40px;
            color: var(--gray);
            font-size: 13px;
            font-weight: 500;
            padding-top: 20px;
            border-top: 1px solid var(--gray-light);
            position: relative;
        }
        
        /* Additional Info */
        .additional-info {
            text-align: center;
            margin-top: 20px;
        }
        
        .additional-info a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .additional-info a:hover {
            color: var(--secondary-blue);
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 40px 24px;
                border-radius: 28px;
            }
            
            .welcome-text h1 {
                font-size: 28px;
            }
            
            .logo-container {
                width: 80px;
                height: 80px;
            }
            
            .form-input {
                padding: 14px 44px 14px 16px;
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
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Input Focus Effects */
        .input-group.focused .input-icon {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }
        
        .form-input.has-value {
            border-color: rgba(0, 102, 179, 0.3);
        }
        
        /* Company Name Highlight */
        .company-highlight {
            color: var(--primary-blue);
            font-weight: 700;
        }
        
        /* Blue Dot Decoration */
        .blue-dots {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .blue-dot {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--primary-blue);
            border-radius: 50%;
            opacity: 0.1;
        }
        
        .dot-1 {
            top: 20px;
            right: 40px;
        }
        
        .dot-2 {
            bottom: 40px;
            left: 30px;
        }
        
        .dot-3 {
            top: 60%;
            right: 20px;
        }
    </style>
</head>
<body>
    
    <div class="bg-pattern"></div>
    
    <div class="shapes">
        <div class="shape-circle shape-1"></div>
        <div class="shape-circle shape-2"></div>
        <div class="shape-circle shape-3"></div>
    </div>
    
    <div class="blue-dots">
        <div class="blue-dot dot-1"></div>
        <div class="blue-dot dot-2"></div>
        <div class="blue-dot dot-3"></div>
    </div>
    
    <div class="login-container">
        
        <div class="login-card">
            
            <div class="logo-section">
                <div class="logo-container">
                    <img src="<?= base_url('assets/img/logo2.png') ?>" alt="Pindad Logo">
                </div>
                <div class="welcome-text">
                    <h1>Welcome Back!</h1>
                    <p>Sign in to <span class="company-highlight">Pindad Inventory</span> system</p>
                </div>
            </div>
            
            <?php if(session()->getFlashdata('error')): ?>
            <div class="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="<?= base_url('login') ?>" id="loginForm">
                <?= csrf_field() ?>
                
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
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>
            
            </form>
            
            <div class="copyright">
                <i class="bi bi-shield-check me-1"></i>
                © 2024 PT Pindad. All rights reserved.
            </div>
            
        </div>
        
    </div>
    
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const submitBtn = document.getElementById('submitBtn');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? 
                    '<i class="bi bi-eye-fill"></i>' : 
                    '<i class="bi bi-eye-slash-fill"></i>';
            });
            
            form.addEventListener('submit', function(e) {
                const username = this.username.value.trim();
                const password = this.password.value.trim();
                
                if (!username || !password) {
                    e.preventDefault();
                    
                    if (!username) {
                        this.username.style.borderColor = '#ef4444';
                    }
                    if (!password) {
                        this.password.style.borderColor = '#ef4444';
                    }
                    
                    return;
                }
                
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });
            
            const inputs = form.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.add('has-value');
                        this.style.borderColor = '';
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
            
            const loginCard = document.querySelector('.login-card');
            loginCard.addEventListener('mousemove', (e) => {
                const rect = loginCard.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                loginCard.style.setProperty('--mouse-x', `${x}px`);
                loginCard.style.setProperty('--mouse-y', `${y}px`);
            });
        });
    </script>
</body>
</html>