<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Example extends RestController {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->database();
    }

    public function users_get()
    {
        // Example to get users, assuming you have a `users` table.
        // Adjust the query based on your actual database scheme.
        // $users = $this->db->get('users')->result();
        $users = [
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
        ];

        if ($users)
        {
            $this->response($users, 200); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No users were found'
            ], 404); // NOT_FOUND (404) being the HTTP response code
        }
    }
}
