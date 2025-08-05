<?php

namespace App\Controllers\API;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function index()
    {
        $users = $this->model
            ->select('users.id, users.name, users.cedula, users.email, roles.name as role')
            ->join('roles', 'roles.id = users.role_id')
            ->whereIn('roles.name', ['admin', 'psychologist'])
            ->orderBy('users.id', 'DESC')
            ->findAll();

        if (!$users) {
            return $this->respond([]);
        }

        return $this->respond($users);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        $validationRules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'cedula'   => 'required|is_unique[users.cedula]|min_length[5]|max_length[20]',
            'email'    => 'required|valid_email|is_unique[users.email]|max_length[255]',
            'password' => 'required|min_length[8]',
            'role_id'  => 'required|in_list[1,2]',
        ];

        if (!$this->validate($validationRules)) {
            return $this->fail($this->validator->getErrors());
        }

        // Hash password after validation
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $userId = $this->model->insert($data);

        if ($userId === false) {
            return $this->fail($this->model->errors()); 
        }

        $user = $this->model->find($userId);
        return $this->respondCreated($user, 'User created successfully');
    }

    public function show($id = null)
    {
        $user = $this->model->find($id);
        if ($user === null) {
            return $this->failNotFound('User not found');
        }
        return $this->respond($user);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $validationRules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'cedula'   => "required|is_unique[users.cedula,id,{$id}]|min_length[5]|max_length[20]",
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]|max_length[255]",
            'role_id'  => 'required|in_list[1,2]',
        ];

        // Only validate password if it's provided
        if (!empty($data['password'])) {
            $validationRules['password'] = 'required|min_length[8]';
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']); // Do not update password if empty
        }

        if (!$this->validate($validationRules)) {
            return $this->fail($this->validator->getErrors());
        }

        if ($this->model->update($id, $data) === false) {
            return $this->fail($this->model->errors());
        }

        return $this->respondUpdated($data, 'User updated successfully');
    }

    public function delete($id = null)
    {
        $user = $this->model->find($id);

        if ($user === null) {
            return $this->failNotFound('User not found');
        }

        if ($this->model->delete($id) === false) {
            return $this->fail($this->model->errors());
        }

        return $this->respondDeleted(['id' => $id], 'User deleted successfully');
    }
}
