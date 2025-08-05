<!-- app/Views/templates/header_public.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Psico-Test') ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
    :root {
        --military-dark: #1a3a1a;
        --military-medium: #2a5a2a;
        --military-light: #3a7a3a;
        --military-accent: #c9a66b;
    }
    
    body {
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .login-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem;
        background: linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.9)), 
                    /* url('/assets/images/military-pattern.png'); */
        background-size: cover;
    }
    
    .login-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 450px;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .card-header {
        background: var(--military-dark);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }
    
    .card-header h2 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .card-header p {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0;
    }
    
    .military-icon {
        width: 60px;
        height: 60px;
        background: var(--military-accent);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }
    
    .military-icon svg {
        width: 30px;
        height: 30px;
        color: var(--military-dark);
    }
    
    .card-body {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--military-dark);
    }
    
    .input-group-text {
        background: var(--military-light);
        color: white;
        border: none;
    }
    
    .form-control {
        border-left: none;
        height: 45px;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(42, 90, 42, 0.25);
        border-color: var(--military-medium);
    }
    
    .btn-military {
        background: var(--military-medium);
        color: white;
        border: none;
        padding: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-military:hover {
        background: var(--military-dark);
        color: white;
    }
    
    .btn-military:active {
        background: var(--military-dark);
    }
    
    .btn-block {
        width: 100%;
    }
    
    .form-footer {
        text-align: center;
        margin-top: 2rem;
        font-size: 0.9rem;
    }
    
    .text-military {
        color: var(--military-medium);
        font-weight: 500;
        text-decoration: none;
    }
    
    .text-military:hover {
        color: var(--military-dark);
        text-decoration: underline;
    }
    
    .login-branding {
        text-align: center;
        margin-top: 2rem;
    }
    
    .brand-logo {
        height: 80px;
        margin-bottom: 1rem;
    }
    
    .brand-text {
        color: var(--military-dark);
        font-size: 0.8rem;
        line-height: 1.4;
    }
    
    .toggle-password {
        cursor: pointer;
    }
    
    @media (max-width: 576px) {
        .login-container {
            padding: 1rem;
        }
        
        .card-header, .card-body {
            padding: 1.5rem;
        }
    }
</style>
</head>
<body>
<div class="container">
