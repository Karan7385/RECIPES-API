<?php
    require('models/CRUD.php');
    // require('Tokens_controller.php');

    class RegisterController {

        public function insertRegisterCredentials() {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $created_at = new DateTime();
            $created_at_formatted = $created_at->format('H:i:s');
            $expires_at = (clone $created_at)->add(new DateInterval('PT1H'));
            $expires_at_formatted = $expires_at->format('H:i:s');

            if ($username != '' && $email != '' && $password != '') {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $CRUD = new CRUD();
                if ($CRUD) {
                    $token = new Tokens_controller();
                    $user_tokens = $token -> generateSecureToken();
                    
                    $last_id = $CRUD -> insert_data($username, $email, $hashedPassword, $created_at_formatted);

                    if ($last_id) {
                        $val = $CRUD -> insert_token($last_id, $user_tokens, $created_at_formatted, $expires_at_formatted);
                        if ($val) {
                            $data = [
                                'last_id' => $last_id,
                                'user_tokens' => $user_tokens,
                                'created_at' => $created_at_formatted,
                                'expires_at' => $expires_at_formatted,
                            ];

                            $res = Create_response(false, "Your data is registered properly", $data);
                            return $res;
                        } else {
                            $res = Create_response(true, "Token data is not inserted", []);
                            return $res;
                        }
                    } else {
                        $res = Create_response(true, "Last ID is not fetched", []);
                        return $res;
                    }
                } else {
                    $res = Create_response(true, "Login data is not inserted", []);
                    return $res;
                }
            } else {
                $res = Create_response(true, "Some of the login credentials are missing", []);
                return $res;
            }
        }
    }

?>