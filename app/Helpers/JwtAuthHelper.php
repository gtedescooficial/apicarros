<?php 
namespace App\Helpers;

use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;




class JwtAuthHelper{
    
    
    protected    $key = "ducktales";

    public function signUp($email, $password, $getIdentity = null){
        
        $user = User::where(array(
            'email' =>$email,
            'password' => $password
        ))->first();

        if( $user ):
            
            $payload = array(
                    "sub" => $user->id,
                    "email" => $user->email,
                    "name" => $user->name,
                    "surname" =>$user->surname,
                    "role" => $user->role,
                    "iat" => time(),
                    "exp" => time() + (7 * 24 * 60 * 60)
            );

                $jwt = JWT::encode($payload, $this->key, 'HS256');
                $decoded = JWT::decode($jwt, $this->key, array('HS256'));



                    if(!$getIdentity):
                        return $jwt;
                    else:
                        return $decoded;
                    endif;
            else:
                return array("status"=>"Error", "message"=>"O login falhou");
            endif;

}

public function checkToken($jwt, $getIdentity = false){



    $decoded = JWT::decode($jwt,$this->key, array('HS256'));

    try{
            if( $decoded && isset($decoded->sub) && $getIdentity ):

                return $decoded;
            elseif($decoded && isset($decoded->sub) && !$getIdentity):
                return true;
            else:
                return false;
            endif;
        }catch(\UnexpectedValueException $e){
            return false;
        }catch( \DomainException $e){
            return false;
        }



}

}