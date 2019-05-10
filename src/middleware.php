<?php

use Slim\App;

return function (App $app) {
    // e.g: $app->add(new \Slim\Csrf\Guard);
    // membuat middleware
    $app->add(function($request , $response,$next){
        $key=$request->getQueryParam("key");

        if(!isset($key)){
            return $response->withJson(["status"=>"Api Key Required"],401);
        }

        $sql="select * from api_users where api_key=:api_key";
        $stmt=$this->db->prepare($sql);
        $stmt->execute([":api_key"=>$key]);

        if($stmt->rowCount() > 0){
            $result = $stmt->fetch();
            if($key == $result["api_key"]){            
                //update hit api
                $sql="update api_users set hit=hit+1 where api_key=:api_key";
                $stmt=$this->db->prepare($sql);
                $stmt->execute([":api_key"=>$key]);

                return $response = $next($request,$response);
            }
        }        
        
        return $response->withJson(["status"=>"UnAuthorize"],401);
    });

};
