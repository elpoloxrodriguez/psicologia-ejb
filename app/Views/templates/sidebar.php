<!-- app/Views/templates/sidebar.php -->
<aside class="sidebar vh-100 border-end bg-light shadow-sm d-flex flex-column">
    <div>
        <div class="sidebar-header p-3 border-bottom">
            <h5 class="fw-bold mb-0">Psico-Test</h5>
            <small id="user-role-display" class="text-muted">Cargando rol...</small>
        </div>
        <ul class="nav flex-column p-3" id="main-nav-links">
            <!-- Dashboard (Visible para todos) -->
            <li class="nav-item" id="nav-dashboard">
                <a class="nav-link" href="/dashboard">
                    <i class="bi bi-house-door me-2"></i>Dashboard
                </a>
            </li>
    
            <!-- Psicólogo y Admin -->
            <li class="nav-item d-none" id="nav-users">
                <a class="nav-link" href="/users-management">
                    <i class="bi bi-people-fill me-2"></i>
                    <span>Gestionar Usuarios</span>
                </a>
            </li>
            <li class="nav-item d-none" id="nav-questions">
                <a class="nav-link" href="/questions-management">
                    <i class="bi bi-patch-question me-2"></i>Gestión Preguntas
                </a>
            </li>
            <li class="nav-item d-none" id="nav-interviews-list">
                <a class="nav-link" href="/interviews">
                    <i class="bi bi-list-ul me-2"></i>Lista de Entrevistas
                </a>
            </li>
            <li class="nav-item d-none" id="nav-patients">
                <a class="nav-link" href="/patients-management">
                    <i class="bi bi-person-lines-fill me-2"></i>Gestionar Pacientes
                </a>
            </li>
    
            <!-- Paciente -->
            <li class="nav-item d-none" id="nav-interview">
                <a class="nav-link" href="/interview">
                    <i class="bi bi-keyboard me-2"></i>Realizar Entrevista
                </a>
            </li>
            <!-- <li class="nav-item d-none" id="nav-my-interviews">
                <a class="nav-link" href="/my-interviews">
                    <i class="bi bi-file-earmark-text me-2"></i>Mis Entrevistas
                </a>
            </li> -->
        </ul>
    </div>

    <div class="mt-auto p-3 border-top">
        <a href="#" class="nav-link" id="logout-button">
            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
        </a>
    </div>
</aside>
