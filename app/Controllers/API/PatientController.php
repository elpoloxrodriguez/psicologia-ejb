<?php

namespace App\Controllers\API;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class PatientController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function index()
    {
        $users = $this->model
            ->where('role_id', 3) // Role ID 3 for 'patient'
            ->orderBy('id', 'DESC')
            ->findAll();

        return $this->respond($users);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $data['role_id'] = 3; // Force role to patient

        $validationRules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'cedula'   => 'required|is_unique[users.cedula]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($validationRules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $userId = $this->model->insert($data);
        if ($userId === false) {
            return $this->fail($this->model->errors());
        }

        $user = $this->model->find($userId);
        return $this->respondCreated($user, 'Patient created successfully');
    }

    public function show($id = null)
    {
        $user = $this->model->where('role_id', 3)->find($id);
        if ($user === null) {
            return $this->failNotFound('Patient not found');
        }
        return $this->respond($user);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        unset($data['role_id']); // Prevent role change

        $validationRules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'cedula'   => "required|is_unique[users.cedula,id,{$id}]",
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
        ];

        if (!empty($data['password'])) {
            $validationRules['password'] = 'min_length[8]';
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        if (!$this->validate($validationRules)) {
            return $this->fail($this->validator->getErrors());
        }

        if ($this->model->update($id, $data) === false) {
            return $this->fail($this->model->errors());
        }

        return $this->respondUpdated($data, 'Patient updated successfully');
    }

    public function delete($id = null)
    {
        $user = $this->model->where('role_id', 3)->find($id);
        if ($user === null) {
            return $this->failNotFound('Patient not found');
        }

        if ($this->model->delete($id) === false) {
            return $this->fail($this->model->errors());
        }

        return $this->respondDeleted(['id' => $id], 'Patient deleted successfully');
    }
}
