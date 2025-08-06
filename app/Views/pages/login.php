<?= view('templates/header_public', ['title' => 'Inicio de Sesión']) ?>

<div class="login-container">
    <div class="login-card">
        <div class="card-header">
            <div class="military-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="M9.5 9h5M12 12v6"></path>
                </svg>
            </div>
            <h2>Sistema de Entrevistas Psicológicas</h2>
            <p>Ejército Bolivariano de Venezuela</p>
        </div>
        <div class="card-body">
            <form id="login-form" class="login-form">
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
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-military btn-block">
                        <span class="btn-text">Acceder al Sistema</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
                
                <div class="form-footer">
                    <a href="/login" class="text-military">¿Olvidó su contraseña?</a>
                    <p class="mt-2">¿No tiene una cuenta? <a href="/register" class="text-military">Solicite acceso</a></p>
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
    document.getElementById('login-form').addEventListener('submit', async function (event) {
        event.preventDefault();
        
        const form = event.target;
        const button = form.querySelector('button[type="submit"]');
        const spinner = button.querySelector('.spinner-border');
        const buttonText = button.querySelector('.btn-text');
        
        // Mostrar spinner y deshabilitar botón
        button.disabled = true;
        buttonText.textContent = 'Autenticando...';
        spinner.classList.remove('d-none');
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const result = await response.json();

            if (response.ok) {
                // Limpiar datos de entrevista previos
                localStorage.removeItem('interview_answers');
                localStorage.removeItem('current_interview_page');
                
                // Almacenar token
                localStorage.setItem('jwt_token', result.token);
                document.cookie = `jwt_token=${result.token}; path=/; max-age=${60 * 60 * 24 * 30}; SameSite=Lax`;
                
                // Redirección
                const urlParams = new URLSearchParams(window.location.search);
                const redirectUrl = urlParams.get('redirect') || '/dashboard';
                
                Swal.fire({
                    icon: 'success',
                    title: 'Acceso autorizado',
                    text: 'Bienvenido al Sistema de Entrevistas Psicológicas',
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#f8f9fa',
                    willClose: () => {
                        window.location.href = redirectUrl;
                    }
                });

            } else {
                throw new Error(result.messages.error || 'Credenciales incorrectas o usuario no autorizado');
            }

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de autenticación',
                text: error.message,
                background: '#f8f9fa'
            });
        } finally {
            // Restaurar botón
            button.disabled = false;
            buttonText.textContent = 'Acceder al Sistema';
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
</script>

<?= view('templates/footer_public') ?>