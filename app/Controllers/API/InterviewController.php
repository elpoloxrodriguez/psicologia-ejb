<?php

namespace App\Controllers\API;

use App\Models\InterviewModel;
use App\Models\InterviewAnswerModel;
use App\Models\QuestionModel;
use CodeIgniter\RESTful\ResourceController;

class InterviewController extends ResourceController
{
    protected $format = 'json';

    protected function _get_user_data()
    {
        // Get token using our helper function
        $token = get_jwt_from_request($this->request);
        
        if (empty($token)) {
            // log_message('error', 'No JWT token found in request');
            return (object)[
                'status' => 'error', 
                'message' => 'No authentication token provided',
                'code' => 'no_token'
            ];
        }
        
        // log_message('debug', 'Validating JWT token: ' . substr($token, 0, 20) . '...');
        $validation = validate_jwt($token);
        
        if (!$validation['status']) {
            // log_message('error', 'JWT validation failed: ' . ($validation['message'] ?? 'Unknown error'));
            return (object)[
                'status' => 'error',
                'message' => $validation['message'] ?? 'Invalid token',
                'code' => $validation['code'] ?? 'invalid_token'
            ];
        }
        
        $userData = (object)$validation['data'];
        // log_message('debug', 'JWT validated for user ID: ' . ($userData->userId ?? 'unknown'));
        
        if (empty($userData->userId) || empty($userData->role)) {
            // log_message('error', 'Invalid user data in token');
            return (object)[
                'status' => 'error',
                'message' => 'Invalid user data in token',
                'code' => 'invalid_user_data'
            ];
        }
        
        return $userData;
    }
    
    /**
     * Check if the current user has already taken the interview
     */
    public function check()
    {
        // log_message('debug', 'Interview check endpoint called');
        
        // Get user data from JWT token
        $userData = $this->_get_user_data();
        
        // Log basic info (without sensitive data)
        // log_message('debug', 'User check - ID: ' . ($userData->userId ?? 'none') . 
        //           ', Role: ' . ($userData->role ?? 'none') . 
        //           ', Status: ' . ($userData->status ?? 'valid'));
        
        // Check for authentication errors
        if (is_object($userData) && property_exists($userData, 'status') && $userData->status === 'error') {
            $errorMsg = $userData->message ?? 'Authentication failed';
            $errorCode = $userData->code ?? 'authentication_error';
            
            // log_message('error', 'Authentication failed in check endpoint: ' . $errorMsg . ' (code: ' . $errorCode . ')');
            
            return $this->respond([
                'status' => 'error',
                'code' => $errorCode,
                'message' => $errorMsg,
                'requiresLogin' => in_array($errorCode, ['no_token', 'token_expired', 'invalid_token'])
            ], 401);
        }
        
        // Validate required user data
        if (empty($userData->userId) || empty($userData->role)) {
            // log_message('error', 'Invalid user data in token - missing userId or role');
            return $this->respond([
                'status' => 'error',
                'code' => 'invalid_user_data',
                'message' => 'Invalid user data in token'
            ], 400);
        }
        
        // Check user role
        if ($userData->role !== 'patient') {
            // log_message('warning', 'Access denied - User role is not patient (user ID: ' . $userData->userId . ', role: ' . $userData->role . ')');
            return $this->respond([
                'status' => 'error',
                'code' => 'invalid_role',
                'message' => 'Only patients can check interview status'
            ], 403);
        }
        
        try {
            // Check if patient has already taken the interview
            $interviewModel = new \App\Models\InterviewModel();
            $hasInterview = $interviewModel->where('patient_id', $userData->userId)->countAllResults() > 0;
            
            // log_message('info', 'Interview check - User ID: ' . $userData->userId . ', Has Interview: ' . ($hasInterview ? 'Yes' : 'No'));
            
            return $this->respond([
                'status' => 'success',
                'hasInterview' => $hasInterview,
                'message' => $hasInterview ? 'Interview already completed' : 'No interview found',
                'userId' => $userData->userId
            ]);
            
        } catch (\Exception $e) {
            $errorId = uniqid('ERR-');
            // log_message('error', '[' . $errorId . '] Error in check endpoint: ' . $e->getMessage());
            // log_message('error', '[' . $errorId . '] Stack trace: ' . $e->getTraceAsString());
            
            return $this->respond([
                'status' => 'error',
                'code' => 'server_error',
                'message' => 'An error occurred while checking interview status',
                'errorId' => $errorId
            ], 500);
        }
    }

    /**
     * Get all questions for the interview
     */
    public function questions()
    {
        // log_message('debug', 'Questions endpoint called');
        
        try {
            // Get user data from JWT token
            $userData = $this->_get_user_data();
            
            // Log basic user info (without sensitive data)
            // log_message('debug', 'User data: ' . json_encode([
            //     'userId' => $userData->userId ?? null,
            //     'role' => $userData->role ?? null,
            //     'status' => $userData->status ?? 'valid'
            // ]));
            
            // Get database config
            $dbConfig = config('Database');
            // log_message('debug', 'Database config: ' . json_encode([
            //     'hostname' => $dbConfig->default['hostname'] ?? null,
            //     'database' => $dbConfig->default['database'] ?? null,
            //     'username' => $dbConfig->default['username'] ? '***' : null,
            //     'password' => $dbConfig->default['password'] ? '***' : null,
            //     'DBDriver' => $dbConfig->default['DBDriver'] ?? null,
            //     'port' => $dbConfig->default['port'] ?? null
            // ]));
            
            // Check database connection
            $db = db_connect();
            if (!$db->connect()) {
                throw new \Exception('Failed to connect to database: ' . $db->error()['message'] ?? 'Unknown error');
            }
            
            // Get list of all tables
            $tables = $db->listTables();
            // log_message('debug', 'Available tables: ' . json_encode($tables));
            
            // Check if questions table exists
            $questionsTableExists = in_array('questions', $tables);
            if (!$questionsTableExists) {
                throw new \Exception('Questions table does not exist in the database');
            }
            
            // Get table structure
            $fields = $db->getFieldData('questions');
            // log_message('debug', 'Questions table structure: ' . json_encode($fields));
            
            // Try direct query first
            $query = $db->query('SELECT * FROM questions');
            if ($query === false) {
                throw new \Exception('Query failed: ' . $db->error()['message'] ?? 'Unknown error');
            }
            
            $questions = $query->getResultArray();
            // log_message('debug', 'Direct query found ' . count($questions) . ' questions');
            
            if (empty($questions)) {
                // Try using model
                $questionModel = new \App\Models\QuestionModel();
                $questions = $questionModel->findAll();
                log_message('debug', 'Model found ' . count($questions) . ' questions');
                
                if (empty($questions)) {
                    // Try one more time with raw query
                    $query = $db->query("SHOW TABLES LIKE 'questions'");
                    $tableExists = $query->getRowArray() !== null;
                    
                    if (!$tableExists) {
                        throw new \Exception('Questions table does not exist');
                    }
                    
                    // Try to get count
                    $query = $db->query('SELECT COUNT(*) as count FROM questions');
                    $count = $query->getRow()->count;
                    
                    throw new \Exception('No questions found in the database. Table exists but is empty.');
                }
            }
            
            // Format the response with answer options and areatext handling
            $formattedQuestions = [];
            foreach ($questions as $question) {
                $formattedQuestion = [
                    'id' => $question['id'],
                    'text' => $question['question_text'],
                    'type' => 'boolean',
                    'required' => true,
                    'options' => [
                        ['value' => true, 'label' => 'Verdadero'],
                        ['value' => false, 'label' => 'Falso']
                    ],
                    'areatext' => false  // Default to false, can be overridden below
                ];
                
                // Enable text area for all questions
                $formattedQuestion['areatext'] = true;
                log_message('debug', 'Question ' . $question['id'] . ' has areatext enabled');
                
                $formattedQuestions[] = $formattedQuestion;
            }
            
            log_message('debug', 'Returning ' . count($formattedQuestions) . ' questions');
            
            // Ensure we're returning an array in the data field
            return $this->respond([
                'status' => 'success',
                'data' => $formattedQuestions
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in questions endpoint: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
            
            return $this->respond([
                'status' => 'error',
                'message' => 'An error occurred while fetching questions',
                'error' => $e->getMessage(),
                'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function create()
    {
        // Set a unique ID for this request to track logs
        $requestId = uniqid('REQ-');
        
        try {
            log_message('debug', '[' . $requestId . '] Starting interview submission');
            
            // Get database connection
            $db = db_connect();
            
            // Get user data
            $userData = $this->_get_user_data();
            log_message('debug', '[' . $requestId . '] User data: ' . print_r($userData, true));
            
            if ($userData->role !== 'patient') {
                log_message('error', '[' . $requestId . '] Unauthorized role: ' . $userData->role);
                return $this->failUnauthorized('Only patients can submit interviews.');
            }

            // Get and log raw input for debugging
            $rawInput = $this->request->getJSON(true);
            log_message('debug', '[' . $requestId . '] Raw input received: ' . print_r($rawInput, true));
            
            // Log the raw post data
            $rawPostData = file_get_contents('php://input');
            log_message('debug', '[' . $requestId . '] Raw POST data: ' . $rawPostData);
            
            // Log database connection info
            $dbConnectionStatus = $db->connect() ? 'Connected' : 'Not connected';
            log_message('debug', '[' . $requestId . '] Database connection status: ' . $dbConnectionStatus);
            log_message('debug', '[' . $requestId . '] Database name: ' . $db->database);
            
            // Log database configuration
            $dbConfig = config('Database');
            log_message('debug', '[' . $requestId . '] Database config: ' . json_encode([
                'hostname' => $dbConfig->default['hostname'],
                'database' => $dbConfig->default['database'],
                'username' => $dbConfig->default['username'] ? '***' : null,
                'DBDriver' => $dbConfig->default['DBDriver'],
                'port' => $dbConfig->default['port']
            ]));

            if (empty($rawInput) || !isset($rawInput['answers'])) {
                $errorMsg = 'Invalid request data. ' . 
                           'Raw input: ' . print_r($rawInput, true) . 
                           'Raw POST: ' . $rawPostData;
                log_message('error', $errorMsg);
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Invalid request data. Answers are required.',
                    'debug' => [
                        'raw_input' => $rawInput,
                        'raw_post' => $rawPostData
                    ]
                ]);
            }

            $answers = $rawInput['answers'];
            log_message('debug', 'Answers to process: ' . print_r($answers, true));
            
            if (!is_array($answers)) {
                log_message('error', 'Answers is not an array: ' . gettype($answers));
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Answers must be an array',
                    'type' => gettype($answers)
                ]);
            }

            log_message('debug', 'Processing ' . count($answers) . ' answers');

            // Get database connection and start transaction
            $db = \Config\Database::connect();
            $db->transStart();
            log_message('debug', '[' . $requestId . '] Starting database transaction');

            try {
                // Create interview record
                $interviewModel = new InterviewModel();
                $interviewData = [
                    'patient_id' => $userData->userId,
                    'interview_date' => date('Y-m-d H:i:s'),
                ];
                
                log_message('debug', '[' . $requestId . '] Inserting interview record: ' . json_encode($interviewData));
                $interviewId = $interviewModel->insert($interviewData);
                
                if (!$interviewId) {
                    throw new \RuntimeException('Failed to create interview record: ' . print_r($interviewModel->errors(), true));
                }

                // Save answers
                $interviewAnswerModel = new InterviewAnswerModel();
                $savedAnswers = 0;
                $answerErrors = [];

            // Process each answer
            foreach ($answers as $index => $answer) {
                $answerId = 'A' . ($index + 1);
                try {
                    log_message('debug', "[Answer $index] Processing answer: " . print_r($answer, true));
                    
                    // Validate answer structure
                    if (!is_array($answer)) {
                        throw new \Exception('Answer is not an array: ' . gettype($answer));
                    }
                    
                    // Check required fields
                    if (!isset($answer['question_id'])) {
                        throw new \Exception('Missing question_id in answer');
                    }
                    if (!isset($answer['answer'])) {
                        throw new \Exception('Missing answer value');
                    }

                    // Ensure valid question ID and interview ID
                    $questionId = (int)$answer['question_id'];
                    if ($questionId <= 0) {
                        throw new \Exception('Invalid question_id: ' . ($answer['question_id'] ?? 'NULL'));
                    }
                    
                    if ($interviewId <= 0) {
                        throw new \Exception('Invalid interview_id: ' . $interviewId);
                    }

                    // Process answer value (convert string 'true'/'false' to boolean if needed)
                    $answerValue = $answer['answer'];
                    $isBoolean = is_bool($answerValue) || 
                                (is_string($answerValue) && in_array(strtolower($answerValue), ['true', 'false']));
                    
                    if (is_string($answerValue)) {
                        $answerValue = strtolower($answerValue) === 'true' ? true : 
                                     (strtolower($answerValue) === 'false' ? false : $answerValue);
                    }
                    
                    // Prepare answer data for storage
                    $answerData = [
                        'answer' => $answerValue,
                        'comments' => $answer['comments'] ?? ''
                    ];

                    // Convert to JSON string for storage
                    $answerToStore = json_encode($answerData, JSON_UNESCAPED_UNICODE);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Failed to encode answer data: ' . json_last_error_msg());
                    }

                    // Prepare data for insertion
                    $insertData = [
                        'interview_id' => (int)$interviewId,
                        'question_id' => $questionId,
                        'answer_text' => $answerToStore,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    log_message('debug', "[Answer $index] Attempting to insert: " . print_r($insertData, true));
                    
                        // Try to insert the answer
                        $result = $interviewAnswerModel->insert($insertData);
                        $error = $db->error();
                        
                        if ($result === false || !empty($error['message'])) {
                            $errorMsg = $error['message'] ?? 'Unknown database error';
                            $errorCode = $error['code'] ?? 0;
                            
                            log_message('error', '[' . $requestId . '] [' . $answerId . '] Database error: ' . $errorMsg);
                            log_message('error', '[' . $requestId . '] [' . $answerId . '] Last query: ' . $db->getLastQuery());
                            
                            // Try direct SQL as fallback
                            $sql = "INSERT INTO interview_answers (interview_id, question_id, answer_text, created_at) " .
                                  "VALUES (?, ?, ?, ?)";
                            $db->query($sql, [
                                $insertData['interview_id'],
                                $insertData['question_id'],
                                $insertData['answer_text'],
                                $insertData['created_at']
                            ]);
                            
                            if ($db->affectedRows() > 0) {
                                $result = $db->insertID();
                                log_message('debug', '[' . $requestId . '] [' . $answerId . '] Successfully inserted using direct SQL with ID: ' . $result);
                            } else {
                                $error = $db->error();
                                throw new \Exception('Database error (direct): ' . ($error['message'] ?? 'Unknown error'));
                            }
                        }
                        $savedAnswers++;
                        $lastInsertId = $db->insertID();
                        log_message('debug', '[' . $requestId . '] [' . $answerId . '] Successfully saved with ID: ' . $lastInsertId);
                        
                    } catch (\Exception $e) {
                        $errorMsg = '[' . $requestId . '] [' . $answerId . '] Error: ' . $e->getMessage();
                        log_message('error', $errorMsg);
                        log_message('error', '[' . $requestId . '] [' . $answerId . '] Stack trace: ' . $e->getTraceAsString());
                        
                        $answerErrors[] = [
                            'answer_index' => $index,
                            'question_id' => $questionId ?? 'unknown',
                            'error' => $e->getMessage(),
                            'answer_data' => $answer ?? 'No answer data'
                        ];
                        
                        continue; // Skip this answer but continue with others
                    }
            }

                // If we have any answer errors, rollback the transaction
                if (!empty($answerErrors)) {
                    throw new \RuntimeException('Failed to save some answers: ' . print_r($answerErrors, true));
                }
                
                // If no answers were saved but we have answers to save, consider it an error
                if ($savedAnswers === 0 && !empty($answers)) {
                    throw new \RuntimeException('No answers were saved despite having ' . count($answers) . ' answers to save');
                }
                
                // Commit the transaction if we get here
                $db->transCommit();
                log_message('debug', '[' . $requestId . '] Transaction committed successfully');
                
            } catch (\Exception $e) {
                // Rollback the transaction on error
                $db->transRollback();
                log_message('error', '[' . $requestId . '] Transaction rolled back: ' . $e->getMessage());
                
                // Re-throw the exception to be caught by the outer try-catch
                throw $e;
            }

            log_message('debug', '[' . $requestId . '] Successfully saved ' . $savedAnswers . ' out of ' . 
                count($answers) . ' answers for interview ID: ' . $interviewId);
            
            $response = [
                'status' => 'success',
                'request_id' => $requestId,
                'message' => 'Answer(s) submitted successfully.',
                'data' => [
                    'interview_id' => $interviewId,
                    'total_questions' => count($answers),
                    'answers_saved' => $savedAnswers,
                    'answers_failed' => count($answers) - $savedAnswers
                ]
            ];
            
            // Add debug info if there were any errors
            if (!empty($answerErrors)) {
                $response['debug'] = [
                    'answer_errors' => $answerErrors
                ];
            }
            
            log_message('debug', '[' . $requestId . '] Interview submission completed successfully');
            return $this->respondCreated($response);

            localStorage.removeItem('interview_answers');
            
            
        } catch (\Exception $e) {
            // Rollback transaction if it's still active
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }
            
            $errorId = uniqid('ERR-');
            $errorMsg = '[' . $requestId . '] [' . $errorId . '] Error in create interview: ' . $e->getMessage();
            
            log_message('error', $errorMsg);
            log_message('error', '[' . $requestId . '] [' . $errorId . '] Stack trace: ' . $e->getTraceAsString());
            
            return $this->failServerError([
                'status' => 'error',
                'request_id' => $requestId,
                'error_id' => $errorId,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function index()
    {
        $userData = $this->_get_user_data();
        $interviewModel = new InterviewModel();
        $query = $interviewModel->select('interviews.id, interviews.interview_date, users.name as patient_name, interviews.patient_id')
            ->join('users', 'users.id = interviews.patient_id');
            
        // If user is a patient, only show their own interviews
        if ($userData->role === 'patient') {
            $query->where('interviews.patient_id', $userData->userId);
        } 
        // Admin and psychologist can see all interviews
        elseif (!in_array($userData->role, ['admin', 'psychologist'])) {
            return $this->failUnauthorized('No tienes permiso para ver este recurso.');
        }

        $interviews = $query->orderBy('interviews.interview_date', 'DESC')
            ->findAll();

        return $this->respond($interviews);
    }

    public function show($id = null)
    {
        $userData = $this->_get_user_data();
        $interviewModel = new InterviewModel();
        
        // Build the base query
        $query = $interviewModel->select('interviews.id, interviews.interview_date, users.name as patient_name, users.email as patient_email, interviews.patient_id')
            ->join('users', 'users.id = interviews.patient_id')
            ->where('interviews.id', $id);
            
        // If user is a patient, only allow viewing their own interview
        if ($userData->role === 'patient') {
            $query->where('interviews.patient_id', $userData->userId);
        } 
        // Admin and psychologist can see any interview
        elseif (!in_array($userData->role, ['admin', 'psychologist'])) {
            return $this->failUnauthorized('No tienes permiso para ver este recurso.');
        }

        // Forzar el resultado a ser un objeto para mantener la consistencia
        $interview = (object) $query->first();

        if (!$interview) {
            return $this->failNotFound('Entrevista no encontrada o no tienes permiso para verla.');
        }

        $interviewAnswerModel = new InterviewAnswerModel();
        $rawAnswers = $interviewAnswerModel
            ->select('interview_answers.id, questions.question_text, interview_answers.answer_text')
            ->join('questions', 'questions.id = interview_answers.question_id')
            ->where('interview_answers.interview_id', $id)
            ->findAll();

        // Procesar las respuestas
        $decodedAnswers = [];
        foreach ($rawAnswers as $rawAnswer) {
            $answerText = $rawAnswer['answer_text'];
            
            // Verificar si la respuesta es un JSON
            if (is_string($answerText) && (strpos($answerText, '{') === 0 || strpos($answerText, '[') === 0)) {
                $answerData = json_decode($answerText, true);
                
                // Manejar diferentes formatos de respuesta
                if (is_array($answerData)) {
                    // Formato nuevo: {"answer":true,"comments":""}
                    if (isset($answerData['answer'])) {
                        $answer = $answerData['answer'];
                        $comments = $answerData['comments'] ?? '';
                    } 
                    // Formato alternativo: {"value":"true","comment":""}
                    elseif (isset($answerData['value'])) {
                        $answer = $answerData['value'] === 'true';
                        $comments = $answerData['comment'] ?? '';
                    } 
                    // Formato antiguo: solo el valor booleano
                    else {
                        $answer = (bool)$answerData;
                        $comments = '';
                    }
                } else {
                    // Si no es un array, usar el valor directamente
                    $answer = (bool)$answerData;
                    $comments = '';
                }
            } else {
                // Si no es JSON, asumir que es un valor booleano directo
                $answer = filter_var($answerText, FILTER_VALIDATE_BOOLEAN);
                $comments = '';
            }
            
            $decodedAnswers[] = [
                'question_text' => $rawAnswer['question_text'],
                'answer'        => $answer,
                'comments'      => $comments
            ];
        }

        $interview->answers = $decodedAnswers;

        return $this->respond($interview);
    }

    public function new()
    {
        //
    }

    public function edit($id = null)
    {
        //
    }

    public function update($id = null)
    {
        //
    }

    public function delete($id = null)
    {
        try {
            // Get user data from JWT token
            $userData = $this->_get_user_data();
            
            // Check if user is authorized (only admin or psychologist can delete)
            if (!in_array($userData->role, ['admin', 'psychologist'])) {
                return $this->failUnauthorized('No tienes permiso para eliminar entrevistas.');
            }

            // Validate interview ID
            if (empty($id)) {
                return $this->fail('Se requiere un ID de entrevista válido.');
            }

            $db = db_connect();
            $db->transStart();

            // Load models
            $interviewModel = new InterviewModel();
            $interviewAnswerModel = new InterviewAnswerModel();

            // Check if interview exists
            $interview = $interviewModel->find($id);
            if (!$interview) {
                $db->transRollback();
                return $this->failNotFound('La entrevista no existe o ya ha sido eliminada.');
            }

            // Delete all answers associated with this interview
            $interviewAnswerModel->where('interview_id', $id)->delete();
            
            // Delete the interview
            $deleted = $interviewModel->delete($id);
            
            if (!$deleted) {
                $db->transRollback();
                return $this->failServerError('No se pudo eliminar la entrevista. Por favor, inténtalo de nuevo.');
            }

            $db->transCommit();

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Entrevista y sus respuestas eliminadas correctamente.',
                'deleted_id' => $id
            ]);

        } catch (\Exception $e) {
            // Rollback transaction if it's still active
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }
            
            log_message('error', 'Error deleting interview: ' . $e->getMessage());
            log_message('error', $e->getTraceAsString());
            
            return $this->failServerError('Ocurrió un error al intentar eliminar la entrevista. Por favor, inténtalo de nuevo.');
        }
    }
    
}
