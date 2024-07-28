<?php

    require_once 'helpers/helpers.php';
    require_once 'models/Recipe_model.php';

    class RecipeController {
        private function isValidRecipeData($name, $prep_time, $difficulty, $vegetarian) {
            return !empty($name) && !empty($prep_time) && is_numeric($difficulty) && in_array($vegetarian, [0, 1]);
        }

        public function listRecipes() {
            $recipe = new Recipe();
            $result = $recipe -> getAllRecipes();

            return $result;
        }

        public function addRecipe() {
            $name = $_POST['name'];
            $prep_time = $_POST['prep_time'];
            $difficulty = $_POST['difficulty'];
            $vegetarian = $_POST['vegetarian'];

            $current_time = new DateTime();
            $created_at_formatted = $current_time -> format('Y-m-d H:i:s');

            if ($name != '' && $prep_time != '' && $difficulty != '' && $vegetarian != '') {
                $createRecipe = new Recipe();
                $result = $createRecipe -> createRecipe($name, $prep_time, $difficulty, $vegetarian, $created_at_formatted);

                return Create_Response(false,"Recipe create succesfully",[]);
            } else {
                return Create_Response(true,"Invalid input data",[]);
            }
        }

        public function getRecipe($id) {
            if (is_numeric($id)) {
                $recipe = new Recipe();
                $result = $recipe -> getRecipeById($id);

                return $result;
            } else {
                return Create_Response(true,"Invalid ID",[]);
            }
        }

        public function updateRecipe($id) {
            parse_str(file_get_contents("php://input"), $_PUT);
            $name = $_PUT['name'];
            $prep_time = $_PUT['prep_time'];
            $difficulty = $_PUT['difficulty'];
            $vegetarian = $_PUT['vegetarian'];

            if (is_numeric($id) && $name != '' && $prep_time != '' && $difficulty != '' && $vegetarian != '') {
                $recipe = new Recipe();
                $result = $recipe -> updateRecipe($id, $name, $prep_time, $difficulty, $vegetarian);

                return Create_Response(false, "Recipe updated successfully", []);
            } else {
                return Create_Response(true, "Invalid input data", []);
            }
        }

        public function deleteRecipe($id) {
            if (is_numeric($id)) {
                $recipe = new Recipe();
                $result = $recipe -> deleteRecipe($id);

                return Create_Response(true, "Recipe not found to be deleted", []);
            } else {
                return Create_Response(true,"Invalid ID",[]);
            }
        }

        public function searchRecipes() {
            $query = $_POST['query'] ?? '';

            if (!empty($query)) {
                $recipe = new Recipe();
                $result = $recipe -> searchRecipes($query);
    
                return $result;
            } else {
                return Create_Response(true, "Search query is empty", []);
            }
        }

        public function rateRecipe($recipe_id) {
            $rating = $_POST['rating'];
            
            if (is_numeric($recipe_id) && is_numeric($rating) && $rating >= 1 && $rating <= 5) {
                $recipe = new Recipe();
                $result = $recipe -> rateRecipe($recipe_id, $rating);

                return $result;
            } else {
                return Create_Response(true,"Invalid input data",[]);
            }
        }
    }
?>