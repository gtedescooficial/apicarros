<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Helpers\JwtAuthHelper;

class UserControllerApi extends Controller
{

    public function __construct(){
        $this->middleware('authuser')->except(['register','login']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['mostrar index users'],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function register(Request $request){

        $json = $request->input('json',null);

        $params = json_decode($json);

        // return response()->json(array("dados"=>$params) ,200);
            $email = ( !is_null($json) && isset($params->email) ) ? $params->email : null;
            $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
            $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
            $role = 'USER';
            $password = (!is_null($json) && isset($params->password)) ? hash('sha256',$params->password) : null;

            if( $email && $name && $password ):

                // VERIFICAR QUE O USUARIO NAO EXISTE NO BD
                $user =  new User();


                $user->email = $email;
                $user->name = $name;
                $user->role = $role;
                $user->surname = $surname;
                $user->password = $password;


                $userExists = User::where('email','=',$email)->first();
                if(!$userExists):
                    $user->save();
                    $data= array('status'=>'success','code'=>200,'message'=>'registro feito com sucesso');
                else:
                    $data= array('status'=>'error','code'=>200,'message'=>'Usuario com este email ja existe no sistema');
                endif;


            else:
                $data= array('status'=>'error','code'=>400,'message'=>'Parametros incorrentos ou ausentes, nao pode ser feito o registro');

            endif;

            return response()->json($data);
    }

    public function login(Request $req){

        $jwtAuth = new JwtAuthHelper();

        $json = $req->input('json',null);
        $params = json_decode($json);
        $email = ( !is_null($json) && isset($params->email) ? $params->email : null );
        $password = ( !is_null($json) && isset($params->password) ? $params->password : null );
        $getData = ( !is_null($json) && isset($params->getData) && $params->getData == true ? $params->getData : null );

        $password = hash('sha256',$password);

        if($email && $password):
            
                $signUp = $jwtAuth->signUp($email, $password, $getData);

                return response()->json($signUp, 200);
            endif;


    }

}
