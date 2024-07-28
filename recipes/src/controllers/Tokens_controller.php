<?php

    class Tokens_controller {
        function generateSecureToken($length = 32) {
            $length = max(1, $length);
            
            return bin2hex(random_bytes($length / 2));
        }
    }
?>