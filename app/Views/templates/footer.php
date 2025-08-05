    </div> <!-- Cierre del container -->

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div> <!-- Cierra .content-wrapper -->
</div> <!-- Cierra .main-wrapper -->

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    // Función centralizada para manejar el cierre de sesión
    function handleLogout() {
        localStorage.removeItem('jwt_token');
        Swal.fire({
            icon: 'success',
            title: 'Sesión Cerrada',
            text: 'Has cerrado sesión exitosamente.',
            timer: 1500,
            showConfirmButton: false,
            willClose: () => {
                window.location.href = '/login';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('jwt_token');

        function parseJwt(token) {
            try {
                const base64Url = token.split('.')[1];
                const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));
                return JSON.parse(jsonPayload);
            } catch (e) {
                return null;
            }
        }

        const decodedToken = parseJwt(token);

        // Si no hay token o rol, y estamos en una página protegida, redirigir
        const protectedPaths = ['/dashboard', '/questions-management', '/interviews-list', '/interview', '/interview_details'];
        if (!decodedToken || !decodedToken.role) {
            if (protectedPaths.includes(window.location.pathname)) {
                localStorage.removeItem('jwt_token');
                window.location.href = '/login';
            }
            return; // Detener la ejecución si no está autenticado
        }

        // --- A partir de aquí, el usuario está autenticado ---
        const userRole = decodedToken.role;

        // Actualizar el rol en el sidebar
        const roleDisplay = document.getElementById('user-role-display');
        if (roleDisplay) {
            roleDisplay.textContent = userRole.charAt(0).toUpperCase() + userRole.slice(1);
        }

        // Definir qué roles ven qué enlaces
        const navLinksConfig = {
            'nav-users': ['admin'],
            'nav-patients': ['admin', 'psychologist'],
            'nav-questions': ['admin', 'psychologist'],
            'nav-interviews-list': ['admin', 'psychologist'],
            'nav-interview': ['patient'],
            'nav-my-interviews': ['patient']
        };

        // Mostrar enlaces según el rol
        for (const navId in navLinksConfig) {
            const element = document.getElementById(navId);
            if (element && navLinksConfig[navId].includes(userRole)) {
                element.classList.remove('d-none');
            }
        }

        // Marcar el enlace activo
        const currentPage = window.location.pathname;
        const navAnchors = document.querySelectorAll('.sidebar .nav-link');
        navAnchors.forEach(anchor => {
            if (anchor.getAttribute('href') === currentPage) {
                anchor.classList.add('active');
            }
        });

        // Lógica específica para el Dashboard
        if (window.location.pathname === '/dashboard') {
            const welcomeEl = document.getElementById('user-welcome');
            if (welcomeEl) {
                welcomeEl.innerHTML = `<h4>Bienvenido, ${decodedToken.email} </h4>`;
            }
            const roleDashboardId = `${userRole}-dashboard`;
            const dashboardToShow = document.getElementById(roleDashboardId);
            if (dashboardToShow) {
                dashboardToShow.classList.remove('d-none');
            }
        }

        // Activar el botón de logout del sidebar
        const logoutButton = document.getElementById('logout-button');
        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                handleLogout();
            });
        }
    });
    </script>
</body>
</html>
