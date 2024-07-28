<?php 

    #database configs
    class DB_config{
        protected $database=[
            'default'=>[
                "host" => 'localhost',
                "dbname" => 'recipes',
                "username" => 'root',
                "password" => '',
                "port"=>3306
            ],
            'production'=>[
                "host" => 'localhost',
                "dbname" => 'prod_db_name',
                "username" => 'prod_username',
                "password" => 'prod_pass',
                "port" => 3306
            ]
        ];

        protected $active_database='default';
    }

?>