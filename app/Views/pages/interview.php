<?= view('templates/header', ['title' => 'Realizar Entrevista']) ?>

<div class="container mt-4" id="interview-container">
    <!-- Content will be loaded dynamically via JavaScript -->
    <div class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2">Cargando la entrevista...</p>
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
        if (userData.role !== 'patient') {
            Swal.fire('Acceso Denegado', 'Solo los pacientes pueden realizar entrevistas.', 'error');
            window.location.href = '/dashboard';
            return;
        }

        const questionsContainer = document.getElementById('questions-container');
        const interviewForm = document.getElementById('interview-form');

        // Function to load questions from the API
        async function loadQuestions() {
            const container = document.getElementById('questions-container');
            if (!container) return;
            
            try {
                // Show loading state
                container.innerHTML = `
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando preguntas...</span>
                        </div>
                        <p class="mt-2">Cargando preguntas...</p>
                    </div>`;
                
                // Get the token from localStorage or PHP variable
                let authToken = localStorage.getItem('jwt_token') || token;
                if (!authToken) {
                    throw new Error('No se encontró el token de autenticación');
                }
                
                // Clean up the token (remove any quotes or whitespace)
                authToken = authToken.trim().replace(/^"|"$/g, '');
                
                const headers = new Headers();
                headers.append('Authorization', `Bearer ${authToken}`);
                headers.append('Accept', 'application/json');
                
                // Check if we need to add CSRF token (if using CodeIgniter's CSRF protection)
                const csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.content;
                if (csrfToken) {
                    headers.append('X-CSRF-TOKEN', csrfToken);
                }
                
                const response = await fetch('/api/interviews/questions', { 
                    method: 'GET',
                    headers: headers,
                    credentials: 'include' // Important for sending cookies
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error(`Error al cargar las preguntas (${response.status})`);
                }
                
                const result = await response.json().catch(error => {
                    console.error('Error parsing JSON response:', error);
                    throw new Error('Error al procesar las preguntas del servidor');
                });

                if (!result.data || !Array.isArray(result.data) || result.data.length === 0) {
                    throw new Error('No hay preguntas disponibles para la entrevista');
                }
                
                allQuestions = Array.isArray(result.data) ? result.data : [];
                
                if (allQuestions.length === 0) {
                    throw new Error('No se encontraron preguntas para la entrevista');
                }
                
                // Clear the container before rendering
                container.innerHTML = '';
                
                // Render all questions (pagination will handle the rest)
                renderCurrentPage();
                
                // Initialize pagination after questions are loaded
                setupPagination();
                
            } catch (error) {
                console.error('Error loading questions:', error);
                const container = document.getElementById('questions-container');
                if (container) {
                    container.innerHTML = `
                        <div class="alert alert-danger">
                            <h5>Error al cargar las preguntas</h5>
                            <p>${error.message || 'Por favor, recarga la página o inténtalo más tarde.'}</p>
                            <button class="btn btn-primary" onclick="window.location.reload()">Recargar</button>
                        </div>`;
                }
            }
        }

        function collectFormData() {
            const answers = [];
            const processedQuestions = new Set();
            const savedAnswers = JSON.parse(localStorage.getItem('interview_answers') || '{}');
            
            // Add saved answers from localStorage
            Object.entries(savedAnswers).forEach(([questionId, answer]) => {
                if (questionId && answer && !processedQuestions.has(questionId)) {
                    answers.push({
                        question_id: questionId,
                        answer: answer.value === 'true',
                        comments: answer.comment || ''
                    });
                    processedQuestions.add(questionId);
                }
            });
            
            // Add answers from current page
            document.querySelectorAll('input[type="radio"]:checked, textarea').forEach(element => {
                const match = element.name.match(/answers\[(\d+)\]\[(value|comments)\]/);
                if (match) {
                    const questionId = match[1];
                    const field = match[2];
                    
                    if (!processedQuestions.has(questionId)) {
                        answers.push({
                            question_id: questionId,
                            answer: false,
                            comments: ''
                        });
                        processedQuestions.add(questionId);
                    }
                    
                    const answerIndex = answers.findIndex(a => a.question_id === questionId);
                    if (answerIndex !== -1) {
                        if (field === 'value') {
                            answers[answerIndex].answer = element.value === 'true';
                        } else if (field === 'comments') {
                            answers[answerIndex].comments = element.value;
                        }
                    }
                }
            });
            
            // Ensure all questions have an answer (default to false if not answered)
            allQuestions.forEach(question => {
                if (!processedQuestions.has(question.id.toString())) {
                    answers.push({
                        question_id: question.id.toString(),
                        answer: false,
                        comments: ''
                    });
                }
            });
            
            // Sort answers by question ID for consistency
            return answers.sort((a, b) => parseInt(a.question_id) - parseInt(b.question_id));
        }

        // Function to show the summary view
        function showSummaryView(answers) {
            const summaryContainer = document.getElementById('summary-view');
            const formContainer = document.getElementById('form-actions');
            const summaryContent = document.getElementById('answers-summary');
            const questionsContainer = document.getElementById('questions-container');
            
            if (!summaryContainer || !formContainer || !summaryContent || !questionsContainer) {
                console.error('Required elements not found for summary view');
                return;
            }
            
            // Hide form and show summary
            questionsContainer.style.display = 'none';
            formContainer.classList.add('d-none');
            summaryContainer.classList.remove('d-none');
            
            // Build summary HTML
            let summaryHTML = '';
            answers.forEach((answer, index) => {
                const question = allQuestions.find(q => q.id == answer.question_id);
                if (!question) return;
                
                const answerText = answer.answer ? 'Verdadero' : 'Falso';
                const comment = answer.comments || 'Sin comentario';
                
                summaryHTML += `
                    <div class="mb-4">
                        <h5>${index + 1}. ${question.text}</h5>
                        <div class="ms-3">
                            <p class="mb-1"><strong>Respuesta:</strong> ${answerText}</p>
                            ${comment && comment !== 'Sin comentario' ? 
                                `<p class="mb-0 text-muted"><em>${comment}</em></p>` : ''}
                        </div>
                        <hr>
                    </div>`;
            });
            
            summaryContent.innerHTML = summaryHTML || '<p>No hay respuestas para mostrar.</p>';
            
            // Scroll to top to show the summary
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Manejador de envío del formulario se ha movido más abajo para evitar duplicados

        loadQuestions();
    });
</script>

</div>

<script>
// Global error handler for uncaught exceptions
window.onerror = function(message, source, lineno, colno, error) {
    // console.error('Global error:', { message, source, lineno, colno, error });
    const container = document.getElementById('interview-container') || document.body;
    container.innerHTML = `
        <div class="alert alert-danger">
            <h4>Error inesperado</h4>
            <p>Ha ocurrido un error inesperado. Por favor, recarga la página o inténtalo más tarde.</p>
            <button class="btn btn-primary" onclick="window.location.reload()">Recargar página</button>
        </div>`;
    return true; // Prevent default error handling
};

document.addEventListener('DOMContentLoaded', function() {
    // Function to get cookie by name
    function getCookie(name) {
        try {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        } catch (e) {
            console.error('Error reading cookie:', e);
            return null;
        }
    }
    
    // Function to safely decode JWT
    function decodeJwt(token) {
        try {
            if (!token) return null;
            const base64Url = token.split('.')[1];
            if (!base64Url) return null;
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            return JSON.parse(atob(base64));
        } catch (e) {
            console.error('Error decoding JWT:', e);
            return null;
        }
    }
    
    // Get token from localStorage or cookie
    let token = localStorage.getItem('jwt_token') || getCookie('jwt_token');
    const container = document.getElementById('interview-container');
    
    if (!token) {
        console.error('No JWT token found in localStorage or cookies');
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
        return;
    }
    
    // Clean and validate token
    token = token.trim().replace(/^"|"$/g, '');
    const tokenData = decodeJwt(token);
    
    if (!tokenData || !tokenData.userId || !tokenData.role) {
        console.error('Invalid or malformed JWT token');
        localStorage.removeItem('jwt_token');
        document.cookie = 'jwt_token=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT';
        window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
        return;
    }
    
    // Make token available globally for API calls
    window.authToken = token;

    // Function to check interview status
    async function checkInterviewStatus() {
        const container = document.getElementById('interview-container');
        if (!container) {
            console.error('Interview container not found');
            return;
        }
        
        // Show loading state
        container.innerHTML = `
            <div class="text-center my-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Verificando estado de la entrevista...</p>
            </div>`;
        
        try {
            // Use the global auth token
            const authToken = window.authToken;
            if (!authToken) {
                throw new Error('No se encontró el token de autenticación. Por favor, inicia sesión nuevamente.');
            }
            
            const response = await fetch('/api/interviews/check', { 
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                credentials: 'include'
            });
            
            let responseData;
            try {
                responseData = await response.json();
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                throw new Error('Error al procesar la respuesta del servidor');
            }
            
            // Handle unauthorized or token expired
            if (response.status === 401) {
                console.error('Authentication failed - token might be invalid or expired');
                
                // Clear invalid token
                localStorage.removeItem('jwt_token');
                document.cookie = 'jwt_token=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT';
                
                // Show error and redirect to login
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <h4>Sesión expirada</h4>
                        <p>Tu sesión ha expirado o no es válida. Por favor, inicia sesión nuevamente.</p>
                        <button class="btn btn-primary" onclick="window.location.href='/login?redirect=' + encodeURIComponent(window.location.pathname)">
                            Ir a Iniciar Sesión
                        </button>
                    </div>`;
                return;
            }
            
            if (!response.ok) {
                const errorMsg = responseData.message || `Error del servidor (${response.status})`;
                console.error('API Error:', { status: response.status, message: errorMsg });
                throw new Error(errorMsg);
            }
            
            if (responseData.status === 'error') {
                console.error('API returned error:', responseData);
                throw new Error(responseData.message || 'Error al verificar el estado de la entrevista');
            }

            if (responseData.hasInterview) {
                // Show already taken interview message
                container.innerHTML = `
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="text-warning">¡Entrevista Ya Realizada!</h3>
                            <p class="lead">Ya has completado la entrevista psicológica previamente.</p>
                            <p>Solo se permite una entrevista por paciente. Si crees que esto es un error, por favor contacta al administrador del sistema.</p>
                            <div class="mt-4 d-flex justify-content-center gap-3">
                                <a href="/dashboard" class="btn btn-primary">
                                    <i class="bi bi-house-door"></i> Volver al Inicio
                                </a>
                                <button class="btn btn-outline-primary" id="view-summary-btn">
                                    <i class="bi bi-card-checklist"></i> Ver Resumen
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Modal for interview summary -->
                    <div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="summaryModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="summaryModalLabel">Resumen de la Entrevista</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="interview-summary-content">
                                    <div class="text-center my-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2">Cargando resumen de la entrevista...</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                
                // Add event listener for the view summary button
                document.getElementById('view-summary-btn').addEventListener('click', loadInterviewSummary);
            } else {
                // Show interview form
                container.innerHTML = `
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h3 class="card-title mb-0">Entrevista Psicológica</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill"></i> 
                                Por favor, responde a las siguientes preguntas de la manera más honesta y completa posible.
                            </div>
                            <form id="interview-form">
                                <div id="questions-container" class="mb-4">
                                <div class="text-center my-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando preguntas...</span>
                                    </div>
                                    <p class="mt-2">Cargando preguntas de la entrevista...</p>
                                </div>
                            </div>
                            <nav aria-label="Navegación de preguntas" class="mt-4">
                                <ul class="pagination justify-content-center" id="pagination-controls">
                                    <li class="page-item disabled" id="prev-page">
                                        <button class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</button>
                                    </li>
                                    <li class="page-item">
                                        <span class="page-text mx-2 d-inline-block my-auto" id="page-info">Página 1</span>
                                    </li>
                                    <li class="page-item" id="next-page">
                                        <button class="page-link" href="#">Siguiente</button>
                                    </li>
                                </ul>
                            </nav>
                            <!-- Interview Form Actions -->
                            <div id="form-actions">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg" id="submit-interview">
                                        <i class="bi bi-send-check"></i> Enviar Entrevista
                                    </button>
                                </div>
                                <div id="incomplete-message" class="alert alert-warning mt-3 d-none">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Por favor responde todas las preguntas en todas las páginas antes de enviar la entrevista.
                                </div>
                            </div>
                            
                            <!-- Summary View (initially hidden) -->
                            <div id="summary-view" class="d-none">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h4 class="mb-0"><i class="bi bi-card-checklist"></i> Resumen de tus respuestas</h4>
                                    </div>
                                    <div class="card-body" id="answers-summary">
                                        <!-- Summary will be inserted here -->
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-outline-secondary me-md-2" id="back-to-interview">
                                        <i class="bi bi-arrow-left"></i> Volver a la entrevista
                                    </button>
                                    <button type="button" class="btn btn-primary" id="confirm-interview">
                                        <i class="bi bi-check-circle"></i> Confirmar y enviar
                                    </button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>`;
                
                // Load questions after showing the form
                try {
                    await loadQuestions();
                    // Initialize form handlers after questions are loaded
                    initializeInterviewForm();
                } catch (error) {
                    console.error('Error loading questions:', error);
                    const questionsContainer = document.getElementById('questions-container');
                    if (questionsContainer) {
                        questionsContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                No se pudieron cargar las preguntas. Por favor, recarga la página.
                            </div>`;
                    }
                }
            }
        } catch (error) {
            console.error('Error:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <h4>Error</h4>
                    <p>${error.message || 'Ocurrió un error al cargar la entrevista. Por favor, inténtalo de nuevo más tarde.'}</p>
                    <a href="/dashboard" class="btn btn-primary">Volver al Inicio</a>
                </div>
            `;
        }
    }

    // Pagination variables
    let currentPage = 1;
    const questionsPerPage = 5;
    let allQuestions = [];
    
    // Function to check if all questions are answered across all pages
    function checkAllQuestionsAnswered() {
        const submitButton = document.querySelector('#interview-form button[type="submit"]');
        if (!submitButton) return false;
        
        // Get all required radio buttons from all pages
        const allRadioGroups = new Set();
        const allCheckedRadios = new Set();
        
        // Check all questions in memory, not just current page
        allQuestions.forEach(question => {
            const radioName = `answers[${question.id}][value]`;
            allRadioGroups.add(radioName);
            
            // Check if this question has a saved answer
            const savedAnswer = getSavedAnswer(question.id, 'value');
            if (savedAnswer) {
                allCheckedRadios.add(radioName);
            }
        });
        
        // Enable submit button only if all questions across all pages are answered
        const allAnswered = allRadioGroups.size === 0 || allCheckedRadios.size === allRadioGroups.size;
        submitButton.disabled = !allAnswered;
        
        // Show/hide warning message
        const warningMessage = document.getElementById('incomplete-message');
        if (warningMessage) {
            if (!allAnswered) {
                const remaining = allRadioGroups.size - allCheckedRadios.size;
                warningMessage.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Por favor responde las ${remaining} preguntas restantes en las otras páginas antes de enviar.`;
                warningMessage.classList.remove('d-none');
            } else {
                warningMessage.classList.add('d-none');
            }
        }
        
        return allAnswered;
    }
    
    // Function to render questions for the current page
    function renderCurrentPage() {
        const container = document.getElementById('questions-container');
        if (!container) return;
        
        container.innerHTML = '';
        
        // Always check all questions when rendering a new page
        checkAllQuestionsAnswered();
        
        const startIndex = (currentPage - 1) * questionsPerPage;
        const endIndex = Math.min(startIndex + questionsPerPage, allQuestions.length);
        const currentQuestions = allQuestions.slice(startIndex, endIndex);
        
        // Update pagination controls
        document.getElementById('page-info').textContent = `Pregunta ${startIndex + 1} a ${endIndex} de ${allQuestions.length}`;
        document.getElementById('prev-page').classList.toggle('disabled', currentPage === 1);
        document.getElementById('next-page').classList.toggle('disabled', endIndex >= allQuestions.length);
        
        // Render current questions
        currentQuestions.forEach((question, index) => {
            const questionIndex = startIndex + index;
            const questionDiv = document.createElement('div');
            questionDiv.className = 'question-container mb-4';
            questionDiv.dataset.questionIndex = questionIndex;
            
            const radioButtons = question.options ? 
                question.options.map(option => `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" 
                               name="answers[${question.id}][value]" 
                               id="q${question.id}_${option.value}" 
                               value="${option.value}" 
                               ${getSavedAnswer(question.id, 'value') === String(option.value) ? 'checked' : ''}
                               required>
                        <label class="form-check-label" for="q${question.id}_${option.value}">
                            ${option.label}
                        </label>
                    </div>`).join('') : 
                `
                <div class="form-check">
                    <input class="form-check-input" type="radio" 
                           name="answers[${question.id}][value]" 
                           id="q${question.id}_true" 
                           value="true" 
                           ${getSavedAnswer(question.id, 'value') === 'true' ? 'checked' : ''}
                           required>
                    <label class="form-check-label" for="q${question.id}_true">Verdadero</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" 
                           name="answers[${question.id}][value]" 
                           id="q${question.id}_false" 
                           value="false"
                           ${getSavedAnswer(question.id, 'value') === 'false' ? 'checked' : ''}
                           required>
                    <label class="form-check-label" for="q${question.id}_false">Falso</label>
                </div>`;
            
            // Add textarea if areatext is enabled for this question
            const textarea = question.areatext ? 
                `<div class="mt-2">
                    <label for="comment-${question.id}" class="form-label">Comentario (opcional):</label>
                    <textarea class="form-control" id="comment-${question.id}" 
                        name="answers[${question.id}][comment]" 
                        rows="2">${getSavedAnswer(question.id, 'comment') || ''}</textarea>
                </div>` : '';
            
            questionDiv.innerHTML = `
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="form-label fw-bold">${questionIndex + 1}. ${question.text}</label>
                        ${radioButtons}
                        ${textarea}
                    </div>
                </div>`;
                
                // Add event listeners to radio buttons to check completion
                questionDiv.querySelectorAll('input[type="radio"]').forEach(radio => {
                    radio.addEventListener('change', () => {
                        saveFormData();
                        checkAllQuestionsAnswered();
                    });
                });
            
            container.appendChild(questionDiv);
        });
    }
    
    // Function to save answers to localStorage
    function saveAnswer(questionId, field, value) {
        const answers = JSON.parse(localStorage.getItem('interview_answers') || '{}');
        if (!answers[questionId]) answers[questionId] = {};
        answers[questionId][field] = value;
        localStorage.setItem('interview_answers', JSON.stringify(answers));
    }
    
    // Function to get saved answer from localStorage
    function getSavedAnswer(questionId, field) {
        const answers = JSON.parse(localStorage.getItem('interview_answers') || '{}');
        return answers[questionId]?.[field] || '';
    }
    
    // Function to load questions from the API
    async function loadQuestions() {
        const container = document.getElementById('questions-container');
        if (!container) return;
        
        try {
            
            // Get the token from localStorage or PHP variable
            let authToken = localStorage.getItem('jwt_token') || token;
            if (!authToken) {
                throw new Error('No authentication token found');
            }
            
            // Clean up the token (remove any quotes or whitespace)
            authToken = authToken.trim().replace(/^"|"$/g, '');
            
            const headers = new Headers();
            headers.append('Authorization', `Bearer ${authToken}`);
            headers.append('Accept', 'application/json');
            
            // Check if we need to add CSRF token (if using CodeIgniter's CSRF protection)
            const csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.content;
            if (csrfToken) {
                headers.append('X-CSRF-TOKEN', csrfToken);
            }
            
            const response = await fetch('/api/interviews/questions', { 
                method: 'GET',
                headers: headers,
                credentials: 'include' // Important for sending cookies
            });
            
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error(`Error al cargar las preguntas (${response.status})`);
            }
            
            const result = await response.json().catch(error => {
                console.error('Error parsing JSON response:', error);
                throw new Error('Error al procesar las preguntas del servidor');
            });
            
            
            // Check if we have questions in the response
            if (!result.data || !Array.isArray(result.data) || result.data.length === 0) {
                throw new Error('No hay preguntas disponibles para la entrevista');
            }
            
            allQuestions = Array.isArray(result.data) ? result.data : [];
            
            if (allQuestions.length === 0) {
                throw new Error('No se encontraron preguntas para la entrevista');
            }
            
            // Initialize pagination
            setupPagination();
            renderCurrentPage();
            
        } catch (error) {
            console.error('Error loading questions:', error);
            const container = document.getElementById('questions-container');
            if (container) {
                container.innerHTML = `
                    <div class="alert alert-danger">
                        Error al cargar las preguntas. Por favor, recarga la página.
                    </div>
                `;
            }
        }
    }

    // Set up pagination event listeners
    function setupPagination() {
        // Previous page button
        document.getElementById('prev-page').addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                saveCurrentPage();
                renderCurrentPage();
            }
        });
        
        // Next page button
        document.getElementById('next-page').addEventListener('click', (e) => {
            e.preventDefault();
            const totalPages = Math.ceil(allQuestions.length / questionsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                saveCurrentPage();
                renderCurrentPage();
            }
        });
        
        // Save answers when leaving the page
        window.addEventListener('beforeunload', () => {
            saveFormData();
        });
    }
    
    // Save current page to localStorage
    function saveCurrentPage() {
        localStorage.setItem('current_interview_page', currentPage);
    }
    
    // Load current page from localStorage
    function loadCurrentPage() {
        const savedPage = parseInt(localStorage.getItem('current_interview_page')) || 1;
        currentPage = Math.max(1, Math.min(savedPage, Math.ceil(allQuestions.length / questionsPerPage)));
    }
    
    // Save form data to localStorage
    function saveFormData() {
        const form = document.getElementById('interview-form');
        if (!form) return;
        
        // Get existing answers from localStorage
        const existingAnswers = JSON.parse(localStorage.getItem('interview_answers') || '{}');
        const newAnswers = {};
        
        // Save radio button answers from current page
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            const match = radio.name.match(/answers\[(\d+)\]\[value\]/);
            if (match && radio.value) {  // Only save if there's a value
                const questionId = match[1];
                if (!newAnswers[questionId]) newAnswers[questionId] = {};
                newAnswers[questionId].value = radio.value;
            }
        });
        
        // Save textarea comments from current page
        document.querySelectorAll('textarea').forEach(textarea => {
            const match = textarea.name.match(/answers\[(\d+)\]\[comment\]/);
            if (match && textarea.value.trim()) {  // Only save non-empty comments
                const questionId = match[1];
                if (!newAnswers[questionId]) newAnswers[questionId] = {};
                newAnswers[questionId].comment = textarea.value.trim();
            }
        });
        
        // Merge with existing answers, only keeping non-empty values
        const mergedAnswers = { ...existingAnswers };
        Object.entries(newAnswers).forEach(([questionId, answerData]) => {
            if (!mergedAnswers[questionId]) {
                mergedAnswers[questionId] = {};
            }
            Object.entries(answerData).forEach(([key, value]) => {
                if (value !== undefined && value !== '') {
                    mergedAnswers[questionId][key] = value;
                }
            });
        });
        
        // Save to localStorage
        localStorage.setItem('interview_answers', JSON.stringify(mergedAnswers));
    }
    
    // Obtener preguntas de la página actual
    function getCurrentPageQuestions() {
        const startIndex = (currentPage - 1) * questionsPerPage;
        const endIndex = Math.min(startIndex + questionsPerPage, allQuestions.length);
        return allQuestions.slice(startIndex, endIndex);
    }
    
    // Function to show the summary view
    function showSummaryView() {
        const summaryContainer = document.getElementById('summary-view');
        const formContainer = document.getElementById('form-actions');
        const summaryContent = document.getElementById('answers-summary');
        const questionsContainer = document.getElementById('questions-container');
        
        if (!summaryContainer || !formContainer || !summaryContent || !questionsContainer) return;
        
        // Hide form and show summary
        questionsContainer.style.display = 'none';
        formContainer.classList.add('d-none');
        summaryContainer.classList.remove('d-none');
        
        // Build summary HTML
        let summaryHTML = '';
        allQuestions.forEach((question, index) => {
            const answer = getSavedAnswer(question.id, 'value');
            const comment = getSavedAnswer(question.id, 'comment') || 'Sin comentario';
            
            // Format the answer text
            let answerText = 'No respondida';
            if (answer) {
                if (question.options) {
                    const selectedOption = question.options.find(opt => String(opt.value) === answer);
                    answerText = selectedOption ? selectedOption.label : answer;
                } else {
                    answerText = answer === 'true' ? 'Verdadero' : 'Falso';
                }
            }
            
            summaryHTML += `
                <div class="mb-4">
                    <h5>${index + 1}. ${question.text}</h5>
                    <div class="ms-3">
                        <p class="mb-1"><strong>Respuesta:</strong> ${answerText}</p>
                        ${comment && comment !== 'Sin comentario' ? 
                            `<p class="mb-0 text-muted"><em>${comment}</em></p>` : ''}
                    </div>
                    <hr>
                </div>`;
        });
        
        summaryContent.innerHTML = summaryHTML;
        
        // Scroll to top to show the summary
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Function to hide the summary view
    function hideSummaryView() {
        const summaryContainer = document.getElementById('summary-view');
        const formContainer = document.getElementById('form-actions');
        const questionsContainer = document.getElementById('questions-container');
        
        if (summaryContainer && formContainer && questionsContainer) {
            summaryContainer.classList.add('d-none');
            questionsContainer.style.display = 'block';
            formContainer.classList.remove('d-none');
            
            // Scroll to top when going back to the form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
    
    // Initialize the interview form
    function initializeInterviewForm() {
        const interviewForm = document.getElementById('interview-form');
        if (!interviewForm) return;
        
        // Disable submit button by default
        const submitButton = interviewForm.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }
        
        // Add click handler for back to interview button
        const backToInterviewBtn = document.getElementById('back-to-interview');
        if (backToInterviewBtn) {
            backToInterviewBtn.addEventListener('click', hideSummaryView);
        }
        
        // Add click handler for confirm interview button
        const confirmInterviewBtn = document.getElementById('confirm-interview');
        if (confirmInterviewBtn) {
            confirmInterviewBtn.addEventListener('click', async function() {
                // Submit the form when confirming
                const form = document.getElementById('interview-form');
                if (form) {
                    const submitEvent = new Event('submit', { cancelable: true });
                    form.dispatchEvent(submitEvent);
                }
            });
        }
        
        // Load saved page
        loadCurrentPage();
        
        // Save form data when inputs change
        interviewForm.addEventListener('change', (e) => {
            if (e.target.matches('input[type="radio"], textarea')) {
                saveFormData();
            }
        });

        // Function to collect form data from all pages
        function collectFormData() {
            const answers = [];
            const processedQuestions = new Set();
            const savedAnswers = JSON.parse(localStorage.getItem('interview_answers') || '{}');
            
            // Add saved answers from localStorage
            Object.entries(savedAnswers).forEach(([questionId, answer]) => {
                if (questionId && answer && !processedQuestions.has(questionId)) {
                    answers.push({
                        question_id: questionId,
                        answer: answer.value === 'true',
                        comments: answer.comment || ''
                    });
                    processedQuestions.add(questionId);
                }
            });
            
            // Add answers from current page
            document.querySelectorAll('input[type="radio"]:checked, textarea').forEach(element => {
                const match = element.name.match(/answers\[(\d+)\]\[(value|comments)\]/);
                if (match) {
                    const questionId = match[1];
                    const field = match[2];
                    
                    if (!processedQuestions.has(questionId)) {
                        answers.push({
                            question_id: questionId,
                            answer: false,
                            comments: ''
                        });
                        processedQuestions.add(questionId);
                    }
                    
                    const answerIndex = answers.findIndex(a => a.question_id === questionId);
                    if (answerIndex !== -1) {
                        if (field === 'value') {
                            answers[answerIndex].answer = element.value === 'true';
                        } else if (field === 'comments') {
                            answers[answerIndex].comments = element.value;
                        }
                    }
                }
            });
            
            // Ensure all questions have an answer (default to false if not answered)
            allQuestions.forEach(question => {
                if (!processedQuestions.has(question.id.toString())) {
                    answers.push({
                        question_id: question.id.toString(),
                        answer: false,
                        comments: ''
                    });
                }
            });
            
            // Sort answers by question ID for consistency
            return answers.sort((a, b) => parseInt(a.question_id) - parseInt(b.question_id));
        }
        
        // Add submit event listener to the form
        interviewForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const button = event.target.querySelector('button[type="submit"]');
            const originalBtnText = button.innerHTML; // Guardar el texto original del botón
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';

            try {
                // Guardar las respuestas de la página actual antes de enviar
                saveFormData();
                
                // Usar la función collectFormData que ya hemos mejorado
                const answers = collectFormData();
                
                
                // Submit answers
                const headers = {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };
                
                // Add CSRF token if available
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                                document.querySelector('meta[name="X-CSRF-TOKEN"]')?.content ||
                                document.querySelector('input[name="csrf_token"]')?.value;
                                
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }
                
                const response = await fetch('/api/interviews', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({ answers }),
                    credentials: 'same-origin' // Include cookies if needed
                });
                
                const result = await response.json().catch(e => ({
                    status: 'error',
                    message: 'Error parsing response: ' + e.message
                }));

                
                if (response.ok && result.status === 'success') {
                    // Show success message and redirect
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: result.message || 'Tu entrevista ha sido enviada correctamente.',
                        willClose: () => {
                            // Clear interview-related localStorage items
                            localStorage.removeItem('interview_answers');
                            localStorage.removeItem('current_interview_page');
                            window.location.href = '/dashboard';
                        }
                    });
                } else {
                    // Show error message with details from server
                    const errorMessage = result.message || 'Error al enviar la entrevista';
                    const errorDetails = result.debug ? `\n\nDetalles: ${JSON.stringify(result.debug, null, 2)}` : '';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `${errorMessage}${errorDetails}`,
                        confirmButtonText: 'Entendido'
                    });
                    throw new Error(result.message || 'Error al enviar la entrevista');
                }
            } catch (error) {
                console.error('Error submitting interview:', error);
                
                // Reset button state
                if (confirmInterviewBtn) {
                    confirmInterviewBtn.disabled = false;
                    confirmInterviewBtn.innerHTML = originalBtnText;
                }
                
                await Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al enviar la entrevista. Por favor, inténtalo de nuevo.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });

        // Load questions
        loadQuestions();
    }

    // Function to decode JWT token
    function decodeJwt(token) {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            return JSON.parse(atob(base64));
        } catch (e) {
            console.error('Error decoding JWT:', e);
            return null;
        }
    }

    // Function to load interview summary
    async function loadInterviewSummary() {
        const modal = new bootstrap.Modal(document.getElementById('summaryModal'));
        const summaryContent = document.getElementById('interview-summary-content');
        
        try {
            // Show loading state
            summaryContent.innerHTML = `
                <div class="text-center my-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando resumen de la entrevista...</p>
                </div>`;
            
            // Show the modal
            modal.show();
            
            // Get user ID from JWT token
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                throw new Error('No se encontró el token de autenticación');
            }
            
            const decodedToken = decodeJwt(token);
            if (!decodedToken || !decodedToken.userId) {
                throw new Error('No se pudo obtener la información del usuario');
            }
            
            const userId = decodedToken.userId;
            
            // Fetch the interview details
            const response = await fetch(`/api/interviews?patient_id=${userId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('No se pudo cargar el resumen de la entrevista.');
            }
            
            const interviews = await response.json();
            if (!interviews || interviews.length === 0) {
                throw new Error('No se encontró la entrevista.');
            }
            
            // Get the most recent interview
            const interview = interviews[0];
            
            // Fetch the interview details
            const detailResponse = await fetch(`/api/interviews/${interview.id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!detailResponse.ok) {
                throw new Error('No se pudieron cargar los detalles de la entrevista.');
            }
            
            const details = await detailResponse.json();
            
            // Generate the summary HTML
            let answersHtml = '';
            if (details.answers && Array.isArray(details.answers)) {
                details.answers.forEach((answer, index) => {
                    const answerValue = answer.answer === true || answer.answer === 'true' ? 'Verdadero' : 'Falso';
                    const badgeClass = answer.answer === true || answer.answer === 'true' ? 'bg-success' : 'bg-danger';
                    
                    answersHtml += `
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>Pregunta ${index + 1}:</strong> ${answer.question_text || 'Pregunta sin texto'}
                            </div>
                            <div class="card-body">
                                <p class="card-text"><strong>Respuesta:</strong> <span class="badge ${badgeClass}">${answerValue}</span></p>
                                ${answer.comments ? `<p class="card-text mb-0"><strong>Comentarios:</strong></p><p class="text-muted fst-italic">${answer.comments}</p>` : '<p class="text-muted mb-0">Sin comentarios.</p>'}
                            </div>
                        </div>`;
                });
            } else {
                answersHtml = '<div class="alert alert-warning">No se encontraron respuestas para esta entrevista.</div>';
            }
            
            // Update the modal content
            summaryContent.innerHTML = `
                <div class="mb-4">
                    <h5 class="mb-3">Detalles de la Entrevista</h5>
                    <p><strong>Fecha de realización:</strong> ${new Date(interview.interview_date).toLocaleString()}</p>
                </div>
                <div class="mb-4">
                    <h5 class="mb-3">Tus respuestas</h5>
                    ${answersHtml}
                </div>`;
                
        } catch (error) {
            console.error('Error loading interview summary:', error);
            summaryContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    ${error.message || 'Ocurrió un error al cargar el resumen de la entrevista.'}
                </div>`;
        }
    }
    
    // Clear all answers when the page loads
    function clearAllAnswers() {
        localStorage.removeItem('interview_answers');
        localStorage.removeItem('current_interview_page');
        // Clear all radio buttons and textareas
        document.querySelectorAll('input[type="radio"]').forEach(radio => radio.checked = false);
        document.querySelectorAll('textarea').forEach(ta => ta.value = '');
    }

    // Clear answers and then check interview status
    clearAllAnswers();
    checkInterviewStatus();
});
</script>

<?= view('templates/footer') ?>
