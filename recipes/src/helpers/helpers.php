<?php
function Response($res){
    print(json_encode($res));
}

function Create_Response($status,$msg,$data){
    return ["error"=>$status,"msg"=>$msg,"data"=>$data];
}