<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'PagesController::login');
$routes->get('/login', 'PagesController::login');
$routes->get('/register', 'PagesController::register');
$routes->get('/dashboard', 'PagesController::dashboard');
$routes->get('/questions-management', 'PagesController::questions_management');
$routes->get('/interview', 'PagesController::interview');
$routes->get('/users-management', 'Home::usersManagement');
$routes->get('/patients-management', 'Home::patientsManagement');
$routes->get('/interviews', 'PagesController::interviews_list');
$routes->get('/interview/details/(:num)', 'PagesController::interview_details/$1');

// API Routes
$routes->group('api', function ($routes) {
    $routes->group('auth', function ($routes) {
        $routes->post('register', 'API\\AuthController::register');
        $routes->post('login', 'API\\AuthController::login');
    });

    $routes->resource('questions', ['controller' => 'API\\QuestionController', 'filter' => 'auth']);

    $routes->get('interviews/check', 'API\\InterviewController::check', ['filter' => 'auth']);
    $routes->get('interviews/questions', 'API\\InterviewController::questions', ['filter' => 'auth']);
    $routes->resource('interviews', ['controller' => 'API\InterviewController', 'only' => ['index', 'show', 'create', 'delete'], 'filter' => 'auth']);
    $routes->get('debug/questions', 'API\InterviewController::debug'); // Temporary debug route
    $routes->resource('users', ['controller' => 'API\UserController', 'only' => ['index', 'create', 'update', 'delete', 'show'], 'filter' => 'role:admin']);
    $routes->resource('patients', ['controller' => 'API\PatientController', 'filter' => 'role:admin,psychologist']);
});
