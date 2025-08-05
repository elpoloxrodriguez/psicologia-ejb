<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function usersManagement()
    {
        return view('pages/users_management');
    }

    public function patientsManagement()
    {
        return view('pages/patients_management');
    }
}
