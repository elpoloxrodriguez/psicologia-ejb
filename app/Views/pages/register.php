<?= view('templates/header_public', ['title' => 'Registro de Usuario']) ?>

<div class="login-container">
    <div class="login-card">
        <div class="card-header">
            <div class="military-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="M9.5 9h5M12 12v6"></path>
                </svg>
            </div>
            <h2>Registro de Personal</h2>
            <p>Sistema de Entrevistas Psicológicas - Ejército Bolivariano</p>
        </div>
        <div class="card-body">
            <form id="register-form" class="login-form">
                <div class="form-group">
                    <label for="name">Nombre Completo</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                            </svg>
                        </span>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Ej: Juan Pérez" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="cedula">Cédula de Identidad</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5 8c0-1.657 2.343-3 4-3V4a4 4 0 0 0-4 4z"/>
                                <path d="M12.318 3h2.015C15.253 3 16 3.746 16 4.667v6.666c0 .92-.746 1.667-1.667 1.667h-2.015A5.97 5.97 0 0 1 9 14a5.972 5.972 0 0 1-3.318-1H1.667C.747 13 0 12.254 0 11.333V4.667C0 3.747.746 3 1.667 3H5.68A5.97 5.97 0 0 1 9 2c1.227 0 2.367.368 3.318 1zM2 4.5a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0zm14 7.5a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v1z"/>
                            </svg>
                        </span>
                        <input type="text" id="cedula" name="cedula" class="form-control" placeholder="Ej: V-12345678" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Correo Institucional</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" class="form-control" placeholder="usuario@ejercito.mil.ve" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </button>
                    </div>
                    <small class="form-text text-muted">Debe contener al menos 8 caracteres, una mayúscula y un número</small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                            </svg>
                        </span>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Repita su contraseña" required>
                    </div>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">Acepto los <a href="#" class="text-military">términos y condiciones</a> del servicio</label>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-military btn-block">
                        <span class="btn-text">Registrarse</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
                
                <div class="form-footer">
                    <p class="mb-0">¿Ya tiene una cuenta? <a href="/login" class="text-military">Inicie sesión aquí</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <!-- <div class="login-branding">
        <img src="/assets/images/ejercito-logo.png" alt="Escudo del Ejército Bolivariano" class="brand-logo">
        <p class="brand-text">República Bolivariana de Venezuela<br>Ministerio del Poder Popular para la Defensa<br>Ejército Bolivariano</p>
    </div> -->
</div>

<script>
    document.getElementById('register-form').addEventListener('submit', async function (event) {
        event.preventDefault();
        
        const form = event.target;
        const button = form.querySelector('button[type="submit"]');
        const spinner = button.querySelector('.spinner-border');
        const buttonText = button.querySelector('.btn-text');
        
        // Validación de contraseñas
        const password = document.getElementById('password').value;
        const password_confirm = document.getElementById('password_confirm').value;
        
        if (password !== password_confirm) {
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'Las contraseñas no coinciden. Por favor verifique.',
                background: '#f8f9fa'
            });
            return;
        }
        
        // Validación de términos
        if (!document.getElementById('terms').checked) {
            Swal.fire({
                icon: 'error',
                title: 'Acepte los términos',
                text: 'Debe aceptar los términos y condiciones para registrarse.',
                background: '#f8f9fa'
            });
            return;
        }
        
        // Mostrar spinner y deshabilitar botón
        button.disabled = true;
        buttonText.textContent = 'Registrando...';
        spinner.classList.remove('d-none');
        
        const name = document.getElementById('name').value;
        const cedula = document.getElementById('cedula').value;
        const email = document.getElementById('email').value;
        
        try {
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    name, 
                    cedula, 
                    email, 
                    password, 
                    password_confirm 
                })
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Registro exitoso',
                    html: '<p>Su cuenta ha sido creada exitosamente.</p><p class="mb-0">Será redirigido al inicio de sesión.</p>',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#f8f9fa',
                    willClose: () => {
                        window.location.href = '/login';
                    }
                });
            } else {
                // Construir mensaje de error detallado
                let errorMessage = 'Por favor corrija los siguientes errores:<br><ul class="text-start">';
                
                if (result.messages) {
                    for (const key in result.messages) {
                        errorMessage += `<li>${result.messages[key]}</li>`;
                    }
                } else {
                    errorMessage = result.message || 'Ocurrió un error durante el registro.';
                }
                
                errorMessage += '</ul>';
                
                throw new Error(errorMessage);
            }

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error en el registro',
                html: error.message,
                background: '#f8f9fa'
            });
        } finally {
            button.disabled = false;
            buttonText.textContent = 'Registrarse';
            spinner.classList.add('d-none');
        }
    });
    
    // Toggle para mostrar/ocultar contraseña
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('svg');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
            }
        });
    });
    
    // Validación de contraseña en tiempo real
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const feedback = this.nextElementSibling;
        
        if (password.length > 0 && password.length < 8) {
            feedback.textContent = 'La contraseña debe tener al menos 8 caracteres';
            feedback.style.color = 'red';
        } else if (!/[A-Z]/.test(password) || !/\d/.test(password)) {
            feedback.textContent = 'Debe contener al menos una mayúscula y un número';
            feedback.style.color = 'red';
        } else {
            feedback.textContent = 'Contraseña válida';
            feedback.style.color = 'green';
        }
    });
</script>

<?= view('templates/footer_public') ?>