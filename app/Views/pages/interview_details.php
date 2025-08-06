<?= view('templates/header', ['title' => 'Detalles de Entrevista']) ?>



<div id="details-container">
    <div class="text-center">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="/interviews" class="btn btn-secondary">&laquo; Volver a la lista</a>
</div>

<?php
// Inyectar el ID de la entrevista en una variable JS para que el script pueda usarlo
echo "<script>const INTERVIEW_ID = {$interview_id};</script>";
?>

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

        const detailsContainer = document.getElementById('details-container');

        async function loadInterviewDetails() {
            if (!INTERVIEW_ID) {
                detailsContainer.innerHTML = '<div class="alert alert-danger">No se especificó un ID de entrevista.</div>';
                return;
            }

            try {
                const response = await fetch(`/api/interviews/${INTERVIEW_ID}`, { headers: getAuthHeaders() });
                if (!response.ok) {
                    let errorMessage = `Error ${response.status}: ${response.statusText}`;
                    try {
                        const errorResult = await response.json();
                        // Intentamos obtener un mensaje de error más específico si existe
                        errorMessage = errorResult.messages?.error || errorResult.message || JSON.stringify(errorResult);
                    } catch (e) {
                        // Si la respuesta de error no es JSON, usamos el texto plano
                        errorMessage = await response.text();
                    }
                    throw new Error(errorMessage);
                }

                const details = await response.json();
                
                let answersHtml = '';
                details.answers.forEach((answer, index) => {
                    const answerValue = answer.answer === true ? 'Verdadero' : 'Falso';
                    const badgeClass = answer.answer === true ? 'bg-success' : 'bg-danger';

                    answersHtml += `
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>Pregunta ${index + 1}:</strong> ${answer.question_text}
                            </div>
                            <div class="card-body">
                                <p class="card-text"><strong>Respuesta:</strong> <span class="badge ${badgeClass}">${answerValue}</span></p>
                                ${answer.comments ? `<p class="card-text mb-0"><strong>Comentarios:</strong></p><p class="text-muted fst-italic">${answer.comments}</p>` : '<p class="text-muted mb-0">Sin comentarios.</p>'}
                            </div>
                        </div>
                    `;
                });

                const detailsHtml = `
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4 class="mb-0">Detalles de la Entrevista #${details.id}</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Paciente:</strong> ${details.patient_name}</p>
                            <p><strong>Email:</strong> ${details.patient_email}</p>
                            <p><strong>Fecha de Realización:</strong> ${new Date(details.interview_date).toLocaleString()}</p>
                            <hr>
                            <h5 class="mt-4">Respuestas</h5>
                            ${answersHtml}
                        </div>
                    </div>`;

                detailsContainer.innerHTML = detailsHtml;
            } catch (error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
            }
        }



        loadInterviewDetails();
    });
</script>

<?= view('templates/footer') ?>
