<?= view('templates/header', ['title' => 'Gestión de Preguntas']) ?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestión de Preguntas</h3>
    <button id="add-question-btn" class="btn btn-primary">Añadir Nueva Pregunta</button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Texto de la Pregunta</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="questions-table-body">
                    <!-- Las preguntas se cargarán aquí dinámicamente -->
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
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            };
        }

        // Función para decodificar el payload del JWT
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

        const tableBody = document.getElementById('questions-table-body');

        async function loadQuestions() {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center fst-italic">Cargando preguntas...</td></tr>';
            try {
                const response = await fetch('/api/questions', { headers: getAuthHeaders() });
                if (!response.ok) throw new Error('No se pudo conectar con el servidor.');
                
                const questions = await response.json();
                tableBody.innerHTML = ''; // Limpiar tabla antes de llenar

                if (questions.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="3" class="text-center fst-italic">No hay preguntas definidas. Agrega una para comenzar.</td></tr>';
                    return;
                }

                questions.forEach(q => {
                    const row = `
                        <tr>
                            <td>${q.id}</td>
                            <td>${q.question_text}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="${q.id}" data-text="${q.question_text}">Editar</button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${q.id}">Eliminar</button>
                            </td>
                        </tr>`;
                    tableBody.innerHTML += row;
                });
            } catch (error) {
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${error.message}</td></tr>`;
            }
        }

        const questionModal = new bootstrap.Modal(document.getElementById('questionModal'));
        const questionForm = document.getElementById('question-form');
        const questionModalLabel = document.getElementById('questionModalLabel');
        const questionIdInput = document.getElementById('question-id');
        const questionTextInput = document.getElementById('question-text');

        // --- MANEJO DEL MODAL ---

        // Abrir modal para AGREGAR una pregunta nueva
        document.getElementById('add-question-btn').addEventListener('click', () => {
            questionForm.reset();
            questionIdInput.value = ''; // Asegurarse de que no hay ID
            questionModalLabel.textContent = 'Agregar Nueva Pregunta';
            questionModal.show();
        });

        // Guardar cambios (ya sea para crear o actualizar)
        document.getElementById('save-question-button').addEventListener('click', async () => {
            const id = questionIdInput.value;
            const text = questionTextInput.value.trim();

            if (!text) {
                Swal.fire('Inválido', 'El texto de la pregunta no puede estar vacío.', 'warning');
                return;
            }

            const isUpdating = !!id;
            const url = isUpdating ? `/api/questions/${id}` : '/api/questions';
            const method = isUpdating ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: getAuthHeaders(),
                    body: JSON.stringify({ question_text: text, question_type: 'true_false' })
                });

                if (!response.ok) {
                    const errorResult = await response.json();
                    throw new Error(errorResult.messages?.error || 'La operación falló.');
                }

                questionModal.hide();
                await Swal.fire(
                    isUpdating ? '¡Actualizada!' : '¡Guardada!',
                    'La pregunta se ha guardado correctamente.',
                    'success'
                );
                loadQuestions();
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        });

        // --- DELEGACIÓN DE EVENTOS PARA LA TABLA ---
        document.getElementById('questions-table-body').addEventListener('click', async (e) => {
            const button = e.target.closest('button');
            if (!button) return; // Si no se hizo clic en un botón, no hacer nada

            const questionId = button.dataset.id;

            // Acción para EDITAR
            if (button.classList.contains('edit-btn')) {
                const questionText = button.dataset.text; // La forma correcta y robusta
                questionIdInput.value = questionId;
                questionTextInput.value = questionText;
                questionModalLabel.textContent = 'Editar Pregunta';
                questionModal.show();
            }

            // Acción para ELIMINAR
            if (button.classList.contains('delete-btn')) {
                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, ¡eliminar!',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/api/questions/${questionId}`, {
                            method: 'DELETE',
                            headers: getAuthHeaders()
                        });

                        if (!response.ok) {
                            const errorResult = await response.json();
                            throw new Error(errorResult.messages?.error || 'No se pudo eliminar.');
                        }

                        await Swal.fire('¡Eliminada!', 'La pregunta ha sido eliminada.', 'success');
                        loadQuestions();
                    } catch (error) {
                        Swal.fire('Error', error.message, 'error');
                    }
                }
            }
        });



        // Carga inicial
        loadQuestions();
    });
</script>

<!-- Modal para Agregar/Editar Pregunta -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">Agregar Pregunta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="question-form">
                    <input type="hidden" id="question-id">
                    <div class="mb-3">
                        <label for="question-text" class="form-label">Texto de la Pregunta</label>
                        <textarea class="form-control" id="question-text" rows="5" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="save-question-button">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<?= view('templates/footer') ?>
