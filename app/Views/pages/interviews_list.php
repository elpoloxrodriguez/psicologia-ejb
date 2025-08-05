<?= view('templates/header', ['title' => 'Lista de Entrevistas']) ?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Entrevistas Realizadas</h3>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID Entrevista</th>
                        <th scope="col">Paciente</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="interviews-table-body">
                    <!-- Las entrevistas se cargarán aquí dinámicamente -->
                    <tr><td colspan="4" class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const token = localStorage.getItem('jwt_token');

        if (!token) {
            window.location.href = '/login';
            return;
        }

        function getAuthHeaders() {
            return {
                'Authorization': `Bearer ${token}`
            };
        }

        function decodeJwt(token) {
            try {
                return JSON.parse(atob(token.split('.')[1]));
            } catch (e) {
                return null;
            }
        }

        const payload = decodeJwt(token);
        if (!payload || !payload.role) { // Verificamos una propiedad clave como 'role'
            localStorage.removeItem('jwt_token');
            window.location.href = '/login';
            return;
        }
        const userData = payload; // Usamos el payload directamente
        if (!['admin', 'psychologist'].includes(userData.role)) {
            Swal.fire('Acceso Denegado', 'No tienes permiso para ver esta página.', 'error');
            window.location.href = '/dashboard';
            return;
        }

        const tableBody = document.getElementById('interviews-table-body');

        async function loadInterviews() {
            try {
                const response = await fetch('/api/interviews', { headers: getAuthHeaders() });
                if (!response.ok) {
                    const errorResult = await response.json();
                    throw new Error(errorResult.messages.error || 'Error al cargar las entrevistas.');
                }

                const interviews = await response.json();
                tableBody.innerHTML = ''; // Limpiar spinner

                if (interviews.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No hay entrevistas realizadas todavía.</td></tr>';
                    return;
                }

                interviews.forEach(interview => {
                    const row = `
                        <tr>
                            <td>${interview.id}</td>
                            <td>${interview.patient_name}</td>
                            <td>${new Date(interview.interview_date).toLocaleString()}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="/interview/details/${interview.id}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <button data-interview-id="${interview.id}" data-patient-name="${interview.patient_name}" class="btn btn-sm btn-danger btn-delete-interview">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    tableBody.innerHTML += row;
                });

            } catch (error) {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${error.message}</td></tr>`;
            }
        }



        loadInterviews();
        
        // Add event delegation for delete buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete-interview')) {
                const button = e.target.closest('.btn-delete-interview');
                const interviewId = button.dataset.interviewId;
                const patientName = button.dataset.patientName;
                deleteInterview(interviewId, patientName);
            }
        });

        // Función para eliminar una entrevista
        async function deleteInterview(interviewId, patientName) {
            try {
                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: `Vas a eliminar la entrevista de ${patientName}. Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    const response = await fetch(`/api/interviews/${interviewId}`, {
                        method: 'DELETE',
                        headers: getAuthHeaders()
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Error al eliminar la entrevista');
                    }

                    await Swal.fire(
                        '¡Eliminada!',
                        'La entrevista ha sido eliminada correctamente.',
                        'success'
                    );
                    
                    // Recargar la lista de entrevistas
                    loadInterviews();
                }
            } catch (error) {
                console.error('Error al eliminar la entrevista:', error);
                Swal.fire(
                    'Error',
                    error.message || 'Ocurrió un error al intentar eliminar la entrevista',
                    'error'
                );
            }
        }
    });
</script>

<!-- Incluir SweetAlert2 para los diálogos de confirmación -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?= view('templates/footer') ?>
