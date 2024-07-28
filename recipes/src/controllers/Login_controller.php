<?php
    // require('models/CRUD.php');
    require('controllers/Recipe_controller.php');
    require_once 'helpers/helpers.php';

    class LoginController {
        public function validateLoginCredentials() {
            $email = $_POST['email'];
            $password = $_POST['password'];

            if (!empty($email) && !empty($password)) {
                $CRUD = new CRUD();
                $res = $CRUD->get_user_data($email, $password);
                return $res;
                
            } else {
                return Create_Response(true,"Username or Password not found!",[]);
            }
        }
    }

?>