<?= view('templates/header', ['title' => 'Gestión de Pacientes']) ?>

<div class="container-fluid">
    <h1 class="my-4">Gestión de Pacientes</h1>
    <button id="add-patient-btn" class="btn btn-primary mb-3">Añadir Paciente</button>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="patients-table-body">
                <!-- Los pacientes se cargarán aquí dinámicamente -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Añadir/Editar Paciente -->
<div class="modal fade" id="patient-modal" tabindex="-1" aria-labelledby="patientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="patient-modal-label">Añadir Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="patient-form">
                    <input type="hidden" id="patient-id" name="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Dejar en blanco para no cambiar la contraseña al editar.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('jwt_token');
    const tableBody = document.getElementById('patients-table-body');
    const addPatientBtn = document.getElementById('add-patient-btn');
    const patientModal = new bootstrap.Modal(document.getElementById('patient-modal'));
    const patientForm = document.getElementById('patient-form');
    const patientModalLabel = document.getElementById('patient-modal-label');

    // Cargar pacientes
    async function fetchPatients() {
        try {
            const response = await fetch('/api/patients', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (!response.ok) throw new Error('Failed to fetch patients');
            const patients = await response.json();

            tableBody.innerHTML = '';
            patients.forEach(patient => {
                const row = `
                    <tr>
                        <td>${patient.id}</td>
                        <td>${patient.name}</td>
                        <td>${patient.cedula}</td>
                        <td>${patient.email}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" data-id="${patient.id}">Editar</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${patient.id}">Eliminar</button>
                        </td>
                    </tr>`;
                tableBody.innerHTML += row;
            });
        } catch (error) {
            console.error('Error fetching patients:', error);
            Swal.fire('Error', 'No se pudieron cargar los pacientes.', 'error');
        }
    }

    // Abrir modal para añadir
    addPatientBtn.addEventListener('click', () => {
        patientForm.reset();
        document.getElementById('patient-id').value = '';
        patientModalLabel.textContent = 'Añadir Paciente';
        document.getElementById('password').setAttribute('required', 'true');
        patientModal.show();
    });

    // Guardar o actualizar paciente
    patientForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const patientId = document.getElementById('patient-id').value;
        const formData = new FormData(patientForm);
        const data = Object.fromEntries(formData.entries());

        // No enviar contraseña si está vacía al editar
        if (patientId && !data.password) {
            delete data.password;
        }

        const url = patientId ? `/api/patients/${patientId}` : '/api/patients';
        const method = patientId ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                const errors = Object.values(result.messages || { error: 'Ocurrió un error.' }).join('\n');
                throw new Error(errors);
            }

            Swal.fire('Éxito', `Paciente ${patientId ? 'actualizado' : 'creado'} correctamente.`, 'success');
            patientModal.hide();
            fetchPatients();
        } catch (error) {
            console.error('Error saving patient:', error);
            Swal.fire('Error', error.message, 'error');
        }
    });

    // Abrir modal para editar o eliminar
    tableBody.addEventListener('click', async function(e) {
        const editButton = e.target.closest('.edit-btn');
        const deleteButton = e.target.closest('.delete-btn');

        if (editButton) {
            const patientId = editButton.dataset.id;
            try {
                const response = await fetch(`/api/patients/${patientId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (!response.ok) throw new Error('Could not fetch patient data.');
                const patient = await response.json();

                patientForm.reset();
                patientModalLabel.textContent = 'Editar Paciente';
                document.getElementById('patient-id').value = patient.id;
                document.getElementById('name').value = patient.name;
                document.getElementById('cedula').value = patient.cedula;
                document.getElementById('email').value = patient.email;
                document.getElementById('password').removeAttribute('required');
                patientModal.show();
            } catch (error) {
                console.error('Error fetching patient for edit:', error);
                Swal.fire('Error', 'No se pudieron cargar los datos del paciente.', 'error');
            }
        } else if (deleteButton) {
            const patientId = deleteButton.dataset.id;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, ¡eliminar!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/api/patients/${patientId}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        if (!response.ok) throw new Error('Error al eliminar el paciente.');
                        Swal.fire('¡Eliminado!', 'El paciente ha sido eliminado.', 'success');
                        fetchPatients();
                    } catch (error) {
                        console.error('Error deleting patient:', error);
                        Swal.fire('Error', 'No se pudo eliminar el paciente.', 'error');
                    }
                }
            });
        }
    });

    fetchPatients();
});
</script>

<?= view('templates/footer') ?>
