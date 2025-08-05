<?php

namespace App\Controllers\API;

use App\Models\QuestionModel;
use CodeIgniter\RESTful\ResourceController;

class QuestionController extends ResourceController
{
    protected $modelName = 'App\Models\QuestionModel';
    protected $format    = 'json';

    private function _get_user_role()
    {
        $header = $this->request->getHeaderLine('Authorization');
        $token = null;
        
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }
        
        if (empty($token)) {
            log_message('error', 'No JWT token provided in the request');
            return null;
        }
        
        $decoded = validate_jwt($token);
        
        if (!$decoded['status']) {
            log_message('error', 'JWT validation failed: ' . ($decoded['message'] ?? 'Unknown error'));
            return null;
        }
        
        return $decoded['data']['role'] ?? null;
    }

    private function _is_authorized()
    {
        $role = $this->_get_user_role();
        return !empty($role) && in_array($role, ['admin', 'psychologist']);
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        return $this->respond($this->model->findAll());
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
        $question = $this->model->find($id);
        if ($question) {
            return $this->respond($question);
        }
        return $this->failNotFound('No question found with id ' . $id);
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
        if (!$this->_is_authorized()) {
            return $this->failUnauthorized('You are not authorized to perform this action.');
        }

        $rules = [
            'question_text' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'question_text' => $this->request->getVar('question_text'),
        ];

        $questionId = $this->model->insert($data);

        if (!$questionId) {
            return $this->failServerError('Could not create question.');
        }

        return $this->respondCreated(['status' => 'success', 'message' => 'Question created successfully', 'id' => $questionId]);
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
        if (!$this->_is_authorized()) {
            return $this->failUnauthorized('You are not authorized to perform this action.');
        }

        $rules = [
            'question_text' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'question_text' => $this->request->getVar('question_text'),
        ];

        if (!$this->model->find($id)) {
            return $this->failNotFound('No question found with id ' . $id);
        }

        if ($this->model->update($id, $data) === false) {
            return $this->fail($this->model->errors());
        }

        return $this->respondUpdated(['status' => 'success', 'message' => 'Question updated successfully']);
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
        if (!$this->_is_authorized()) {
            return $this->failUnauthorized('You are not authorized to perform this action.');
        }

        if (!$this->model->find($id)) {
            return $this->failNotFound('No question found with id ' . $id);
        }

        if ($this->model->delete($id) === false) {
            return $this->fail($this->model->errors());
        }

        return $this->respondDeleted(['status' => 'success', 'message' => 'Question deleted successfully']);
    }
}
