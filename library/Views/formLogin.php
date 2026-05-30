<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #1a3a52 0%, #2d5a7b 50%, #1d4d6f 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 400px;
        padding: 50px 40px;
        text-align: center;
    }

    .login-icon {
        width: 50px;
        height: 50px;
        background-color: #1a3a52;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: white;
        font-size: 28px;
    }

    .login-title {
        color: #1a3a52;
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 5px;
        line-height: 1.3;
    }

    .login-subtitle {
        color: #c97a3c;
        font-size: 13px;
        margin-bottom: 30px;
        font-weight: 500;
    }

    .tabs {
        display: flex;
        gap: 0;
        margin-bottom: 30px;
        border-bottom: 1px solid #e5e7eb;
    }

    .tab-btn {
        flex: 1;
        padding: 12px;
        background: none;
        border: none;
        color: #9ca3af;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
        text-decoration: none;
    }

    .tab-btn.active {
        color: #1a3a52;
        border-bottom-color: #1a3a52;
    }

    .tab-btn:hover {
        color: #1a3a52;
    }

    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }

    .form-group label {
        display: block;
        color: #374151;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        color: #374151;
        background-color: #f9fafb;
        transition: all 0.3s ease;
    }

    .form-group input::placeholder {
        color: #9ca3af;
    }

    .form-group input:focus {
        outline: none;
        background-color: white;
        border-color: #1a3a52;
        box-shadow: 0 0 0 3px rgba(26, 58, 82, 0.1);
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        background-color: #1a3a52;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background-color: #0f2438;
        box-shadow: 0 4px 12px rgba(26, 58, 82, 0.3);
        transform: translateY(-2px);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .error-message {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        display: none;
    }

    .error-message.show {
        display: block;
    }

    .loading {
        display: none;
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 40px 25px;
            max-width: 100%;
            margin: 20px;
        }

        .login-title {
            font-size: 20px;
        }
    }
</style>

<div class="login-container">
    <div class="login-icon">
        <i class="fas fa-book"></i>
    </div>
    
    <div class="login-title">Libraria e Universitetit "Eqrem Cabej"</div>
    <div class="login-subtitle">Mësonjëvni në Biblioteken Digjitale</div>

    <div class="tabs">
        <button class="tab-btn active">Sign In</button>
        <a href="/library/register" class="tab-btn">Register</a>
    </div>

    <div id="errorMessage" class="error-message"></div>

    <form id="loginForm">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">Sign In</button>
        <div class="loading" id="loading">
            <i class="fas fa-spinner fa-spin"></i> Signing in...
        </div>
    </form>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorDiv = document.getElementById('errorMessage');
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.getElementById('loading');

    // Limpiar error anterior
    errorDiv.classList.remove('show');
    errorDiv.textContent = '';

    // Deshabilitar botón
    submitBtn.disabled = true;
    loading.style.display = 'block';

    try {
        const response = await fetch('/library/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Login failed');
        }

        // El token se guarda automáticamente en cookies (HttpOnly)
        // Redirigir a books
        window.location.href = '/library/books';
    } catch (error) {
        errorDiv.textContent = error.message;
        errorDiv.classList.add('show');
        submitBtn.disabled = false;
        loading.style.display = 'none';
    }
});
</script>
