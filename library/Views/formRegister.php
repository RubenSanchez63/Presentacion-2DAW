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
        <a href="/library/login" class="tab-btn">Sign In</a>
        <button class="tab-btn active">Register</button>
    </div>

    <form action="" method="post">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Enter name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>

        <button type="submit" class="btn-submit">Register</button>
    </form>
</div>
