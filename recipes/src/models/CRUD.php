<?php

    require_once 'helpers/helpers.php';
    require_once 'config/database.php';
    require_once 'controllers/Tokens_controller.php';

    class CRUD {
        public function check_expiry($token) {
            $connect = new Database();
            $mysqli = $connect->con;
        
            $stmt = $mysqli->prepare("SELECT token_created_at, expires_at FROM tokens WHERE token = ?");
            
            if ($stmt) {
                $stmt->bind_param("s", $token);
                
                if ($stmt->execute()) {
                    $stmt->bind_result($token_created_at, $expires_at);
                    
                    if ($stmt->fetch()) {
                        $current_time = new DateTime();
                        $expires_at_time = new DateTime($expires_at);
        
                        $stmt->close();
                        $mysqli->close();
                        
                        if ($current_time < $expires_at_time) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        $stmt->close();
                        $mysqli->close();
                        return false; // Token not found
                    }
                } else {
                    $stmt->close();
                    $mysqli->close();
                    return false; // Execution failed
                }
            } else {
                $mysqli->close();
                return false; // Preparation failed
            }
        }        

        public function insert_data($username, $email, $password, $created_at) {
            $connect = new Database();
            $mysqli = $connect->con;

            $stmt = $mysqli -> prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, ?)");

            if ($stmt) {
                $stmt -> bind_param("ssss", $username, $email, $password, $created_at);

                if ($stmt -> execute()) {
                    $last_id = $mysqli -> insert_id;
                    return $last_id;

                } else {
                    $res = Create_response(true, $stmt -> error, []);
                    return $res;
                }

                $stmt->close();
            } else {
                $res = Create_response(true, $mysqli -> error, []);
                return $res;
            }

            $mysqli->close();
        }

        public function insert_token($id, $token, $token_created_at, $expires_at) {
            $connect = new Database();
            $mysqli = $connect -> con;
            $stmt = $mysqli -> prepare("INSERT INTO tokens (user_id, token, token_created_at, expires_at) VALUES (?, ?, ?, ?)");

            if ($stmt) {
                $stmt -> bind_param("ssss", $id, $token, $token_created_at, $expires_at);

                if ($stmt -> execute()) {
                    $last_id = $mysqli -> insert_id;
                    return $last_id;
                } else {
                    $res = Create_response(true, $stmt->error, []);
                    return $res;
                }

                $stmt->close();
            } else {
                $res = Create_response(true, $mysqli->error, []);
                return $res;
            }

            $mysqli->close();
        }

        public function update_token($user_id, $token, $token_created_at, $expires_at) {
            $connect = new Database();
            $mysqli = $connect -> con;
            $stmt = $mysqli -> prepare("UPDATE tokens SET token = ?, token_created_at = ?, expires_at = ? WHERE user_id = ?");

            if ($stmt) {
                $stmt -> bind_param("sssi", $token, $token_created_at, $expires_at, $user_id);

                if ($stmt -> execute()) {
                    return true;
                } else {
                    $res = Create_response(true, $stmt->error, []);
                    return $res;
                }

                $stmt -> close();
            } else {
                $res = Create_response(true, $mysqli->error, []);
                return $res;
            }

            $mysqli -> close();
        }

        public function get_user_data($email, $password) {
            $connect = new Database();
            $mysqli = $connect->con;
            
            if ($mysqli) {
                $stmt = $mysqli -> prepare("
                    SELECT 
                        tokens.token, tokens.token_created_at, tokens.expires_at, users.password, users.id
                    FROM 
                        users
                    LEFT JOIN 
                        tokens 
                    ON 
                        users.id = tokens.user_id
                    WHERE 
                        users.email = ?
                    ORDER BY 
                        tokens.token_created_at DESC
                    LIMIT 1
                ");
                
                if ($stmt) {
                    $stmt -> bind_param("s", $email);

                    if ($stmt -> execute()) {
                        $stmt -> bind_result($token, $token_created_at, $expires_at, $hashedPassword, $id);

                        if ($stmt -> fetch()) {
                            if (password_verify($password, $hashedPassword)) {
                                $current_time = new DateTime();
                                $expires_at_time = new DateTime($expires_at);
                                
                                if ($current_time < $expires_at_time) {
                                    $user_data = [
                                        'token' => $token,
                                        'token_created_at' => $token_created_at,
                                        'expires_at' => $expires_at
                                    ];
                                    $res = Create_Response(false, "Login Successfull", $user_data);
                                    return $res;
                                } else {
                                    $tokens = new Tokens_controller();
                                    $new_token = $tokens -> generateSecureToken();
                                    $token_created_at = $current_time -> format('H:i:s');
                                    $expires_at = $current_time -> add(new DateInterval('PT1H')) -> format('H:i:s');

                                    $this -> update_token($id, $new_token, $token_created_at, $expires_at);

                                    $user_data = [
                                        'token' => $new_token,
                                        'token_created_at' => $token_created_at,
                                        'expires_at' => $expires_at
                                    ];

                                    $res = Create_Response(false, "Login Successfull with new token", $user_data);
                                    return $res;                                
                                }
                            } else {
                                $res = Create_Response(true, "Incorrect password", []);
                                return $res;
                            }
                        } else {
                            $res = Create_Response(true, "User doesn't exist", []);
                            return $res;
                        }
                    } else {
                        $res = Create_Response(true, $stmt->error, []);
                        return $res;
                    }

                    $stmt->close();
                } else {
                    $res = Create_Response(true, $mysqli->error, []);
                    return $res;
                }

                $mysqli->close();
            } 
            else {
                $res = Create_Response(true, "Error in connection", []);
                return $res;
            }
        }
    }
?>