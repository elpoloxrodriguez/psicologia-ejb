<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PagesController extends BaseController
{
    public function login()
    {
        return view('pages/login', ['title' => 'Inicio de Sesión']);
    }

    public function register()
    {
        return view('pages/register', ['title' => 'Registro de Usuario']);
    }

    public function dashboard()
    {
        $data = ['title' => 'Dashboard'];
        
        // Check if user is logged in and is a patient
        $session = session();
        if ($session->has('user') && $session->get('user')['role'] === 'patient') {
            // Check if patient has already taken the interview
            $interviewModel = new \App\Models\InterviewModel();
            $data['hasTakenInterview'] = $interviewModel->where('patient_id', $session->get('user')['id'])->countAllResults() > 0;
        }
        
        return view('pages/dashboard', $data);
    }

    public function questions_management()
    {
        return view('pages/questions_management', ['title' => 'Gestión de Preguntas']);
    }

    public function interview()
    {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Check for JWT token in cookie or request
        $token = isset($_COOKIE['jwt_token']) ? $_COOKIE['jwt_token'] : null;
        
        // If no token in cookie, check if we're being redirected from login
        if (!$token && isset($_GET['token'])) {
            $token = $_GET['token'];
            // Set the cookie for future requests
            setcookie('jwt_token', $token, [
                'expires' => time() + (86400 * 30), // 30 days
                'path' => '/',
                'secure' => false, // Set to true in production with HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        
        // If still no token, redirect to login with redirect URL
        if (!$token) {
            $redirectUrl = urlencode(current_url());
            return redirect()->to('/login?redirect=' . $redirectUrl);
        }
        
        // Pass the token to the view
        return view('pages/interview', [
            'title' => 'Realizar Entrevista',
            'jwtToken' => $token
        ]);
    }

    public function interviews_list()
    {
        return view('pages/interviews_list', ['title' => 'Lista de Entrevistas']);
    }

    public function users_management()
    {
        return view('pages/users_management');
    }

    public function interview_details($id = null)
    {
        return view('pages/interview_details', ['title' => 'Detalles de Entrevista', 'interview_id' => $id]);
    }
}
