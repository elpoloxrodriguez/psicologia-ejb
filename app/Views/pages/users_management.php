<?= view('templates/header', ['title' => 'Gestión de Usuarios']) ?>

<div class="container-fluid">
    <h2 class="mb-4">Gestión de Usuarios</h2>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Usuarios del Sistema</h5>
            <button class="btn btn-primary" id="add-user-btn">
                <i class="bi bi-plus-circle me-1"></i> Añadir Usuario
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="users-table" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th class="no-export">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables cargará los datos aquí -->
                    </tbody>
                </table>
            </div>
            <div id="loading-message" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 mb-0">Cargando usuarios...</p>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="user-modal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Añadir Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="user-form">
                    <input type="hidden" id="user-id" name="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Dejar en blanco para no cambiar la contraseña al editar.</small>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select" id="role" name="role_id" required>
                            <option value="" disabled selected>Seleccione un rol...</option>
                            <option value="1">Admin</option>
                            <option value="2">Psychologist</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" form="user-form" id="save-user-btn">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('jwt_token');
    const loadingMessage = document.getElementById('loading-message');
    const addUserBtn = document.getElementById('add-user-btn');
    const userModal = new bootstrap.Modal(document.getElementById('user-modal'));
    const userForm = document.getElementById('user-form');
    const userModalLabel = document.getElementById('userModalLabel');
    const tableBody = document.querySelector('#users-table tbody');
    
    // Inicializar DataTable
    const usersTable = $('#users-table').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>" +
             "<'row'<'col-sm-12'B>>",
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-earmark-excel me-1"></i> Exportar a Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Columnas a exportar (excluye acciones)
                },
                title: 'Usuarios del Sistema',
                filename: 'usuarios_' + new Date().toISOString().split('T')[0]
            }
        ],
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: '/api/users',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            dataSrc: function(json) {
                loadingMessage.classList.add('d-none');
                return json;
            },
            error: function(xhr, error, thrown) {
                loadingMessage.classList.add('d-none');
                Swal.fire('Error', 'No se pudieron cargar los usuarios. Intente nuevamente.', 'error');
                console.error('Error al cargar usuarios:', error);
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'cedula' },
            { data: 'email' },
            { 
                data: 'role',
                render: function(data, type, row) {
                    return data === 'admin' ? 'Administrador' : 'Psicólogo';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning edit-btn me-1" data-id="${row.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        initComplete: function() {
            $('.dt-buttons button').removeClass('btn-secondary').addClass('btn-sm');
        }
    });

    async function fetchUsers() {
        loadingMessage.classList.remove('d-none');
        tableBody.innerHTML = '';

        try {
            const response = await fetch('/api/users', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    Swal.fire('Acceso Denegado', 'No tienes permiso para ver esta información.', 'error');
                } else {
                    throw new Error('Error al cargar los usuarios.');
                }
                return;
            }

            const users = await response.json();

            if (users.length === 0) {
                emptyMessage.classList.remove('d-none');
            } else {
                users.forEach(user => {
                    const row = `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.cedula}</td>
                            <td>${user.email}</td>
                            <td><span class="badge bg-secondary">${user.role}</span></td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="${user.id}" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }
        } catch (error) {
            console.error('Error fetching users:', error);
            Swal.fire('Error', 'No se pudo conectar con el servidor para obtener los usuarios.', 'error');
        } finally {
            loadingMessage.classList.add('d-none');
        }
    }

    addUserBtn.addEventListener('click', function() {
        userForm.reset();
        document.getElementById('user-id').value = '';
        userModalLabel.textContent = 'Añadir Usuario';
        document.getElementById('password').setAttribute('required', 'required');
        userModal.show();
    });

    userForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(userForm);
        const userData = Object.fromEntries(formData.entries());
        const userId = userData.id;

        // Si no hay contraseña, no la enviamos para que no se actualice
        if (!userData.password) {
            delete userData.password;
        }

        const url = userId ? `/api/users/${userId}` : '/api/users';
        const method = userId ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(userData)
            });

            const result = await response.json();

            if (!response.ok) {
                let errorText = 'Ocurrió un error.';
                if (result.messages) {
                    errorText = Object.values(result.messages).join('\n');
                }
                Swal.fire('Error de Validación', errorText, 'error');
                return;
            }

            Swal.fire('Éxito', `Usuario ${userId ? 'actualizado' : 'creado'} correctamente.`, 'success');
            userModal.hide();
            reloadUsersTable(); // Recargar la lista de usuarios

        } catch (error) {
            console.error('Error saving user:', error);
            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
        }
    });

    // Manejar la edición de un usuario
    $(document).on('click', '.edit-btn', function() {
        const userId = $(this).data('id');
        // Resto del código de edición...
        
        // Código existente para cargar datos del usuario en el modal
        (async () => {
            try {
                const response = await fetch(`/api/users/${userId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('No se pudo cargar el usuario');
                }

                const user = await response.json();
                
                userForm.reset();
                userModalLabel.textContent = 'Editar Usuario';
                document.getElementById('user-id').value = user.id;
                document.getElementById('name').value = user.name;
                document.getElementById('cedula').value = user.cedula;
                document.getElementById('email').value = user.email;
                document.getElementById('role').value = user.role_id;
                document.getElementById('password').removeAttribute('required');

                userModal.show();
            } catch (error) {
                console.error('Error al cargar el usuario:', error);
                Swal.fire('Error', 'No se pudieron cargar los datos del usuario.', 'error');
            }
        })();
    });

    // Manejar la eliminación de un usuario
    $(document).on('click', '.delete-btn', function() {
        const userId = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/api/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al eliminar el usuario');
                    }
                    return response.json();
                })
                .then(() => {
                    Swal.fire(
                        '¡Eliminado!',
                        'El usuario ha sido eliminado correctamente.',
                        'success'
                    );
                    reloadUsersTable();
                })
                .catch(error => {
                    console.error('Error al eliminar el usuario:', error);
                    Swal.fire('Error', 'No se pudo eliminar el usuario.', 'error');
                });
            }
        });
    });

    // Función para hashear la contraseña (usando SHA-256)
    async function hashPassword(password) {
        const msgBuffer = new TextEncoder().encode(password);
        const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }

    // Estilos adicionales para DataTables
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            .dataTables_wrapper .dataTables_filter input {
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
                padding: 0.375rem 0.75rem;
            }
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
                padding: 0.25rem;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.25rem 0.5rem;
                margin: 0 2px;
                border-radius: 0.25rem;
                border: 1px solid #dee2e6;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
            .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                background: #0d6efd;
                color: white !important;
                border-color: #0d6efd;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #e9ecef;
                color: #0d6efd !important;
            }
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                line-height: 1.5;
                border-radius: 0.2rem;
            }
            .dt-buttons .btn {
                margin-bottom: 0.5rem;
            }
            .no-export {
                border: none !important;
                background: none !important;
            }
        </style>
    `);

    fetchUsers();
});
</script>

<?= view('templates/footer') ?>
