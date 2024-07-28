<?php
    require_once 'controllers/Login_controller.php';
    require_once 'controllers/Register_controller.php';
    require_once 'controllers/Recipe_controller.php';
    require_once 'helpers/helpers.php';
    require_once 'models/CRUD.php';

    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);

    function validate_url($method) {
        if ($_SERVER['REQUEST_METHOD'] != $method) {
            http_response_code(405);
            $response = ["status_code" => 405, "msg" => "Method not allowed", "data" => []];
            Response($response);
            die;
        }

        if ($_SERVER['CONTENT_TYPE'] != 'application/x-www-form-urlencoded') {
            http_response_code(415);
            $response = ["status_code" => 415, "msg" => "Content type should be application/x-www-form-urlencoded only!", "data" => []];
            Response($response);
            die;
        }
    }

    function validate_token() {
        if (!isset($_SERVER["HTTP_TOKEN"])) {
            http_response_code(401);
            $response = ["status_code" => 401, "msg" => "Unauthorized Access!", "data" => []];
            Response($response);
            die;
        } else {
            $token = $_SERVER["HTTP_TOKEN"];
            $crud = new CRUD();
            $status = $crud -> check_expiry($token);

            if ($status) {
                return $token;
            } else {
                http_response_code(401);
                $response = ["status_code" => 401, "msg" => "Unauthorized Access!", "data" => []];
                Response($response);
                die;
            }
        }
    }

    if (count($uri_segments) < 5) {
        http_response_code(404);
        die;
    }

    # url mapping 
    function url_mapping($uri_segments) {

        if ($uri_segments[4] == "login") {

            validate_url("POST");
            $login = new LoginController();
            $output = $login -> validateLoginCredentials();
            Response($output);

        } else if ($uri_segments[4] == "register") {

            validate_url("POST");
            $register = new RegisterController();
            $output = $register -> insertRegisterCredentials();
            Response($output);

        } else if ($uri_segments[4] == "recipes") {
            
            if ($_SERVER['REQUEST_METHOD'] == "GET" && !isset($uri_segments[5])) {
                validate_token();

                $recipeController = new RecipeController();
                $output = $recipeController -> listRecipes();
                Response($output);

            } else if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($uri_segments[5])) {
                validate_url("POST");
                validate_token();

                $recipeController = new RecipeController();
                $output = $recipeController -> addRecipe();
                Response($output);

            } else if (isset($uri_segments[5])) {

                $recipe_id = $uri_segments[5];

                if ($_SERVER['REQUEST_METHOD'] == "GET") {

                    validate_token();
                    $recipeController = new RecipeController();
                    $output = $recipeController -> getRecipe($recipe_id);
                    Response($output);

                } else if ($_SERVER['REQUEST_METHOD'] == "PUT" || $_SERVER['REQUEST_METHOD'] == "PATCH") {
                    validate_url($_SERVER['REQUEST_METHOD']);
                    validate_token();

                    $recipeController = new RecipeController();
                    $output = $recipeController -> updateRecipe($recipe_id);
                    Response($output);

                } else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
                    validate_token();
                    
                    $recipeController = new RecipeController();
                    $output = $recipeController -> deleteRecipe($recipe_id);
                    Response($output);

                } else if (isset($uri_segments[6]) && $uri_segments[6] == "rating" && $_SERVER['REQUEST_METHOD'] == "POST") {
                    validate_url("POST");
                    validate_token();

                    $recipeController = new RecipeController();
                    $output = $recipeController -> rateRecipe($recipe_id);
                    Response($output);
                } else if (isset($uri_segments[5]) && $uri_segments[5] == "search" && $_SERVER['REQUEST_METHOD'] == "POST") {
                    validate_url("POST");
                    validate_token();
    
                    $recipeController = new RecipeController();
                    $output = $recipeController -> searchRecipes();
                    Response($output);
                }
            }
        } else {
            http_response_code(404);
        }
    }

    url_mapping($uri_segments);
?>