<?= view('templates/header', ['title' => 'Dashboard']) ?>

<div id="user-welcome" class="mb-4"></div>

<!-- Admin Dashboard -->
<div id="admin-dashboard" class="d-none">
    <div class="welcome-container p-5 rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: none; box-shadow: 0 10px 30px rgba(78, 115, 223, 0.15);">
        <div class="welcome-header text-center mb-5">
            <h3 class="text-dark font-weight-bold mb-3" style="font-size: 1.8rem; letter-spacing: -0.5px;">
                ¡Bienvenido/a al Sistema Psicológico!
            </h3>
            <p class="text-muted" style="font-size: 1.1rem;">Estimado/a Administrador/a</p>
        </div>

        <div class="welcome-content">
            <p class="mb-4 text-center" style="font-size: 1.1rem; line-height: 1.6; color: #4a5568;">
                Es un honor darle la bienvenida al <span class="text-primary font-weight-bold">Panel Especializado</span>, diseñado para optimizar su trabajo clínico y garantizar la mejor atención a nuestros beneficiarios.
            </p>
            
            <div class="features-list mb-5">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="feature-card p-4 h-100 rounded-3" style="background-color: #f8faff; border-left: 3px solid #4e73df;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle mr-3" style="width: 40px; height: 40px; background-color: #e6efff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-clipboard-list text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-dark">Gestión de entrevistas</h5>
                            </div>
                            <p class="mb-0" style="color: #4a5568;">Organice y adapte el banco de preguntas según las necesidades de cada evaluación psicológica.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="feature-card p-4 h-100 rounded-3" style="background-color: #f8faff; border-left: 3px solid #4e73df;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle mr-3" style="width: 40px; height: 40px; background-color: #e6efff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-chart-line text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-dark">Análisis de resultados</h5>
                            </div>
                            <p class="mb-0" style="color: #4a5568;">Acceda a informes detallados y seguimiento del historial psicológico de los pacientes.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="feature-card p-4 h-100 rounded-3" style="background-color: #f8faff; border-left: 3px solid #4e73df;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle mr-3" style="width: 40px; height: 40px; background-color: #e6efff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-lock text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-dark">Seguridad garantizada</h5>
                            </div>
                            <p class="mb-0" style="color: #4a5568;">Sistema protegido con autenticación avanzada y estrictos controles de acceso.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="feature-card p-4 h-100 rounded-3" style="background-color: #f8faff; border-left: 3px solid #4e73df;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle mr-3" style="width: 40px; height: 40px; background-color: #e6efff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-shield text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-dark">Confidencialidad</h5>
                            </div>
                            <p class="mb-0" style="color: #4a5568;">Protección absoluta de los datos sensibles de los pacientes.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="important-notes p-4 mb-5 rounded-3" style="background: linear-gradient(135deg, #fff8f0 0%, #fff3e8 100%); border-left: 4px solid #ff9f43;">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-exclamation-circle mr-3" style="font-size: 1.5rem; color: #ff9f43;"></i>
                    <h5 class="mb-0 text-dark">Aviso importante</h5>
                </div>
                <div class="pl-4">
                    <div class="d-flex mb-2">
                        <i class="fas fa-circle mr-2 mt-1" style="font-size: 0.5rem; color: #ff9f43;"></i>
                        <p class="mb-0" style="color: #4a5568;">Este sistema es de uso exclusivo para personal autorizado del <strong>Ejército Bolivariano de Venezuela</strong></p>
                    </div>
                    <div class="d-flex">
                        <i class="fas fa-circle mr-2 mt-1" style="font-size: 0.5rem; color: #ff9f43;"></i>
                        <p class="mb-0" style="color: #4a5568;">Mantenga sus credenciales en un lugar seguro y cambie su contraseña periódicamente</p>
                    </div>
                </div>
            </div>

            <div class="footer-message text-center p-4 rounded-3" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
                <p class="mb-3" style="font-size: 1.1rem;">¡Gracias por su compromiso con la salud psicológica de nuestra institución!</p>
                <div class="d-flex justify-content-center">
                    <div class="mr-4">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <span>Comandancia General del Ejército Bolivariano de Venezuela</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido adicional del panel del psicólogo -->
</div>

<!-- Psychologist Dashboard -->
<div id="psychologist-dashboard" class="d-none">
    <div class="welcome-container p-5 rounded-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: none; box-shadow: 0 10px 30px rgba(78, 115, 223, 0.15);">
        <div class="welcome-header text-center mb-5">
            <h3 class="text-dark font-weight-bold mb-3" style="font-size: 1.8rem; letter-spacing: -0.5px;">
                ¡Bienvenido/a al Sistema Psicológico!
            </h3>
            <p class="text-muted" style="font-size: 1.1rem;">Estimado/a profesional de la psicología</p>
        </div>

        <div class="welcome-content">
            <p class="mb-4 text-center" style="font-size: 1.1rem; line-height: 1.6; color: #4a5568;">
                Es un honor darle la bienvenida al <span class="text-primary font-weight-bold">Panel Especializado</span>, diseñado para optimizar su trabajo clínico y garantizar la mejor atención a nuestros beneficiarios.
            </p>
            
            <div class="features-list mb-5">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="feature-card p-4 h-100 rounded-3" style="background-color: #f8faff; border-left: 3px solid #4e73df;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle mr-3" style="width: 40px; height: 40px; background-color: #e6efff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-clipboard-list text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-dark">Gestión de entrevistas</h5>
                            </div>
                            <p class="mb-0" style="color: #4a5568;">Organice y adapte el banco de preguntas según las necesidades de cada evaluación psicológica.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="feature-card p-4 h-100 rounded-3" style="background-color: #f8faff; border-left: 3px solid #4e73df;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle mr-3" style="width: 40px; height: 40px; background-color: #e6efff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-chart-line text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-dark">Análisis de resultados</h5>
                            </div>
                            <p class="mb-0" style="color: #4a5568;">Acceda a informes detallados y seguimiento del historial psicológico de los pacientes.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="feature-card p-4 h-100 rounded-3" style="background-color: #f8faff; border-left: 3px solid #4e73df;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle mr-3" style="width: 40px; height: 40px; background-color: #e6efff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-lock text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-dark">Seguridad garantizada</h5>
                            </div>
                            <p class="mb-0" style="color: #4a5568;">Sistema protegido con autenticación avanzada y estrictos controles de acceso.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="feature-card p-4 h-100 rounded-3" style="background-color: #f8faff; border-left: 3px solid #4e73df;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle mr-3" style="width: 40px; height: 40px; background-color: #e6efff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-shield text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-dark">Confidencialidad</h5>
                            </div>
                            <p class="mb-0" style="color: #4a5568;">Protección absoluta de los datos sensibles de los pacientes.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="important-notes p-4 mb-5 rounded-3" style="background: linear-gradient(135deg, #fff8f0 0%, #fff3e8 100%); border-left: 4px solid #ff9f43;">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-exclamation-circle mr-3" style="font-size: 1.5rem; color: #ff9f43;"></i>
                    <h5 class="mb-0 text-dark">Aviso importante</h5>
                </div>
                <div class="pl-4">
                    <div class="d-flex mb-2">
                        <i class="fas fa-circle mr-2 mt-1" style="font-size: 0.5rem; color: #ff9f43;"></i>
                        <p class="mb-0" style="color: #4a5568;">Este sistema es de uso exclusivo para personal autorizado del <strong>Ejército Bolivariano de Venezuela</strong></p>
                    </div>
                    <div class="d-flex">
                        <i class="fas fa-circle mr-2 mt-1" style="font-size: 0.5rem; color: #ff9f43;"></i>
                        <p class="mb-0" style="color: #4a5568;">Mantenga sus credenciales en un lugar seguro y cambie su contraseña periódicamente</p>
                    </div>
                </div>
            </div>

            <div class="footer-message text-center p-4 rounded-3" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
                <p class="mb-3" style="font-size: 1.1rem;">¡Gracias por su compromiso con la salud psicológica de nuestra institución!</p>
                <div class="d-flex justify-content-center">
                    <div class="mr-4">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <span>Comandancia General del Ejército Bolivariano de Venezuela</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido adicional del panel del psicólogo -->
</div>

<!-- Patient Dashboard -->
<div id="patient-dashboard" class="d-none">
    <!-- <h3>Panel de Paciente</h3> -->
    <?php if (isset($hasTakenInterview) && $hasTakenInterview): ?>
        <div class="alert alert-info">
            <h4>¡Entrevista Completada!</h4>
            <p>Ya has completado la entrevista. Gracias por tu participación.</p>
            <p>Si necesitas ayuda o tienes alguna pregunta, por favor contacta al administrador del sistema.</p>
        </div>
    <?php else: ?>

        <div style="max-width: auto; margin: 30px auto; font-family: 'Arial', sans-serif;">
    <div style="background: white; border-radius: 16px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1); overflow: hidden; border: 1px solid #e0e6ed;">
        <!-- Card Header -->
        <div style="background: linear-gradient(135deg, #4a89dc 0%, #3b7dd8 100%); padding: 25px; text-align: center; color: white;">
            <h2 style="margin: 0; font-size: 28px; font-weight: 600;">¡Hola y bienvenido/a! 💙</h2>
            <p style="margin: 10px 0 0; opacity: 0.9; font-size: 16px;">Tu opinión es invaluable para nosotros</p>
        </div>
        
        <!-- Card Body -->
        <div style="padding: 30px;">
            <p style="margin-bottom: 20px; color: #555; line-height: 1.6; text-align: center; font-size: 16px;">
                Este es un <strong style="color: #4a89dc;">espacio seguro</strong> y completamente <strong style="color: #4a89dc;">confidencial</strong> donde puedes compartir tus experiencias con total libertad.
            </p>
            
            <!-- Info Box -->
            <div style="background: #f8faff; border-left: 4px solid #4a89dc; padding: 15px; border-radius: 0 8px 8px 0; margin: 25px 0;">
                <p style="margin: 0 0 10px 0; font-weight: 600; color: #3b7dd8; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 20px;">📝</span> Así funciona:
                </p>
                <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #555;">
                    <li style="margin-bottom: 8px;">Responde con <strong>"Verdadero"</strong> o <strong>"Falso"</strong></li>
                    <li style="margin-bottom: 8px;">Añade comentarios si lo deseas</li>
                    <li>No hay respuestas correctas o incorrectas</li>
                </ul>
            </div>
            
            <!-- Comfort Tips -->
            <div style="background: #fff9f2; border-radius: 12px; padding: 20px; margin: 25px 0; text-align: center; border: 1px dashed #ffb347;">
                <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 15px;">
                    <div style="text-align: center;">
                        <div style="font-size: 24px;">🌿</div>
                        <div style="font-size: 13px; color: #666;">Tómate tu tiempo</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 24px;">💆</div>
                        <div style="font-size: 13px; color: #666;">Respira profundo</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 24px;">🔄</div>
                        <div style="font-size: 13px; color: #666;">Pausa cuando necesites</div>
                    </div>
                </div>
                <p style="margin: 0; font-style: italic; color: #e67e22; font-size: 15px;">
                    Tu comodidad es nuestra prioridad
                </p>
            </div>
            
            <!-- CTA -->
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #777; margin-bottom: 20px; font-size: 15px;">
                    Cuando te sientas listo/a para comenzar...
                </p>
                <a href="/interview" style="background: linear-gradient(135deg, #4a89dc 0%, #3b7dd8 100%); color: white; padding: 14px 30px; border-radius: 50px; text-decoration: none; font-weight: 600; display: inline-block; box-shadow: 0 4px 15px rgba(74, 137, 220, 0.3); transition: transform 0.3s, box-shadow 0.3s;" 
                   onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(74, 137, 220, 0.4)'" 
                   onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(74, 137, 220, 0.3)'">
                    Comenzar Entrevista
                </a>
            </div>
        </div>
        
        <!-- Card Footer -->
        <div style="background: #f9fbfd; padding: 15px; text-align: center; border-top: 1px solid #e0e6ed;">
            <p style="margin: 0; color: #8898aa; font-size: 13px;">
                Tus respuestas son completamente anónimas y seguras
            </p>
        </div>
    </div>
</div>

    <?php endif; ?>
</div>



<?= view('templates/footer') ?>
