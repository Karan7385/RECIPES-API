<?php

    require_once 'config/database.php';
    require_once 'helpers/helpers.php';

    class Recipe {

        public function getAllRecipes() {
            $connect = new Database();
            $mysqli = $connect->con;
            
            $stmt = $mysqli -> prepare("SELECT id, recipe_name, prep_time, difficulty, vegeterian FROM recipes");

            if ($stmt) {
                $stmt -> execute();
                $result = $stmt -> get_result();
                $recipes = [];

                while ($row = $result -> fetch_assoc()) {
                    $recipes[] = $row;
                }

                $stmt -> close();
            } else {
                $recipes = [];
            }

            $mysqli -> close();

            $res = Create_Response(false, "Data retrived successfully", $recipes);
            return $res;
        }

        public function createRecipe($name, $prep_time, $difficulty, $vegeterian, $created_at) {
            $connect = new Database();
            $mysqli = $connect->con;

            $stmt = $mysqli -> prepare("INSERT INTO recipes (recipe_name, prep_time, difficulty, vegeterian, created_at) VALUES (?, ?, ?, ?, ?)");

            if ($stmt) {
                $stmt -> bind_param("ssiss", $name, $prep_time, $difficulty, $vegeterian, $created_at);
                $result = $stmt->execute();
                $stmt -> close();
            } else {
                $result = false;
            }

            $mysqli -> close();
            return $result;
        }

        public function getRecipeById($id) {
            $connect = new Database();
            $mysqli = $connect->con;

            $stmt = $mysqli -> prepare("SELECT id, recipe_name, prep_time, difficulty, vegeterian FROM recipes WHERE id = ?");

            if ($stmt) {
                $stmt -> bind_param("i", $id);
                $stmt -> execute();
                $stmt -> bind_result($id, $name, $prep_time, $difficulty, $vegeterian);
                $recipe = null;
                if ($stmt -> fetch()) {
                    $recipe = [
                        'id' => $id,
                        'name' => $name,
                        'prep_time' => $prep_time,
                        'difficulty' => $difficulty,
                        'vegeterian' => $vegeterian
                    ];
                }
                $stmt -> close();
            }

            $mysqli -> close();
            if($recipe == null)
                return Create_Response(true, "Invalid id, Recipe not found", [$recipe]);
            else
                return Create_Response(false, "Recipe fetched successfully", $recipe);
        }

        public function updateRecipe($id, $name, $prep_time, $difficulty, $vegeterian) {
            $connect = new Database();
            $mysqli = $connect->con;

            $stmt = $mysqli -> prepare("UPDATE recipes SET recipe_name = ?, prep_time = ?, difficulty = ?, vegeterian = ? WHERE id = ?");

            if ($stmt) {
                $stmt -> bind_param("ssisi", $name, $prep_time, $difficulty, $vegeterian, $id);
                $result = $stmt -> execute();
                $stmt -> close();
            } else {
                $result = false;
            }

            $mysqli -> close();
            return $result;
        }

        public function deleteRecipe($id) {
            $connect = new Database();
            $mysqli = $connect->con;

            $stmt = $mysqli -> prepare("DELETE FROM recipes WHERE id = ?");

            if ($stmt) {
                $stmt -> bind_param("i", $id);
                $result = $stmt -> execute();
                $stmt -> close();
            } else {
                $result = false;
            }

            $mysqli -> close();
            return $result;
        }

        public function searchRecipes($query) {
            $connect = new Database();
            $mysqli = $connect->con;

            $stmt = $mysqli->prepare("SELECT id, recipe_name, prep_time, difficulty, vegeterian FROM recipes WHERE recipe_name LIKE ?");
    
            if ($stmt) {
                $search_query = "%{$query}%";
                $stmt->bind_param("s", $search_query);
                $stmt->execute();
                $result = $stmt->get_result();
                $recipes = [];
    
                while ($row = $result->fetch_assoc()) {
                    $recipes[] = $row;
                }
    
                $stmt->close();
            } else {
                $recipes = [];
            }
    
            $mysqli->close();
            if($result != [])
                return Create_Response(false, "Search result found", $recipes);
            else
                return Create_Response(false, "Search result not found", $recipes);
        }

        public function rateRecipe($recipe_id, $rating) {
            $connect = new Database();
            $mysqli = $connect -> con;

            $stmt = $mysqli -> prepare("INSERT INTO ratings (recipe_id, rating) VALUES (?, ?)");

            if ($stmt) {
                $stmt -> bind_param("ii", $recipe_id, $rating);
                $result = $stmt -> execute();
                $stmt -> close();
            } else {
                $result = false;
            }

            $mysqli -> close();
            if($result)
                return Create_Response(false, "Rating of recipe is done successfully", []);
            else
                return Create_Response(true, "Rating of recipe is unsuccessfull", []);
        }
    }
?>