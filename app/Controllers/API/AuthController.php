<?php

namespace App\Controllers\API;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    public function __construct()
    {
        $this->model = new UserModel();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        //
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        //
    }

    public function register()
    {
        $rules = [
            'name' => 'required',
            'cedula' => 'required|is_unique[users.cedula]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'role_id'  => 3, // Default role is 'patient'
            'name'     => $this->request->getVar('name'),
            'cedula'   => $this->request->getVar('cedula'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];

        $userId = $this->model->insert($data);

        if (!$userId) {
            return $this->failServerError('Could not create user.');
        }

        return $this->respondCreated(['status' => 'success', 'message' => 'User registered successfully']);
    }

    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $user = $this->model->where('email', $this->request->getVar('email'))->first();

        if (!$user) {
            return $this->failNotFound('User not found.');
        }

        if (!password_verify($this->request->getVar('password'), $user['password'])) {
            return $this->failUnauthorized('Invalid credentials.');
        }

        // Get user role name
        $db = \Config\Database::connect();
        $role = $db->table('roles')->where('id', $user['role_id'])->get()->getRow()->name;

        $token = generate_jwt($user['email'], $user['id'], $role);

        return $this->respond(['status' => 'success', 'token' => $token]);
    }
}
