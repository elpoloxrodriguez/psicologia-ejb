<?= view('templates/header', ['title' => 'Dashboard']) ?>

<div id="user-welcome" class="mb-4"></div>

<!-- Admin Dashboard -->
<div id="admin-dashboard" class="d-none">
    <h3>Panel de Administrador</h3>
    <p>Aquí puedes gestionar usuarios, psicólogos y ver todas las entrevistas.</p>
    <a href="/users-management" class="btn btn-primary me-2">Gestionar Usuarios</a>
    <a href="/interviews" class="btn btn-secondary">Ver Entrevistas Realizadas</a>
    <!-- Contenido del admin -->
</div>

<!-- Psychologist Dashboard -->
<div id="psychologist-dashboard" class="d-none">
    <h3>Panel de Psicólogo</h3>
    <p>Aquí puedes gestionar las preguntas de las entrevistas y ver los resultados de los pacientes.</p>
    <a href="/patients-management" class="btn btn-info">Gestionar Pacientes</a>
    <a href="/questions-management" class="btn btn-primary me-2">Gestionar Preguntas</a>
    <a href="/interviews" class="btn btn-secondary">Ver Entrevistas Realizadas</a>
    <!-- Contenido del psicólogo -->
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
