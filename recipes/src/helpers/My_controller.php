<?php
require_once '../config/config.php';

class My_controller{
    protected  $pdb ;
    protected  $phost ;
    protected  $pusername ;
    protected  $ppassword ;
    public function __construct(){
        
        $this->pdb=$database[$active_database]["dbname"];
        $this->phost=$database[$active_database]["host"];
        $this->pusername=$database[$active_database]["username"];
        $this->ppassword=$database[$active_database]["password"];
    }

}