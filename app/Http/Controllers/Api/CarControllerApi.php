<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Car;
use App\Helpers\JwtAuthHelper;


class CarControllerApi extends Controller
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
        $cars = Car::paginate(15);
        return response()->json([
            'data' => $cars,
            'status' => 'success'
        ],200);
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
        $json = $request->input('json');
        $params = json_decode($json);
        if(!$json){
            return response()->json(['status'=>'error','mensagem'=>'falta parametro json']);
        }
        if ( !isset($params->title) || !isset($params->description) || !isset($params->price) ){
            return response()->json(['status'=>'error','mensagem'=>'title, description e price são parametros obrigatórios']);

        }

        
        if ( isset($params->title)){
            
            $params->title = strtolower($params->title);
        }

        if ( isset( $params->description)){
            
            $params->description = strtolower( $params->description);
        }

        if ( isset($params->price)){
            
           //$price = (float) $params->price;

        }
        $carAlreadyExists = Car::where('title',$params->title)->first();
        if( $carAlreadyExists ){
            return response()->json([
                'status'=>'error',
                'mensagem'=>"carro com o title $params->title ja existe no BD"
                ]);
            
        }

        $jwtAuth = new JwtAuthHelper();
        $token = request()->header('Authorization');
        $user_id = $jwtAuth->checkToken($token,true)->sub;

        $carModel = new Car;
        $carModel->title = $params->title;
        $carModel->description = $params->description;
        $carModel->price = $params->price;
        $carModel->status = Car::STATUS_DISP;

        $carModel->user_id = $user_id;

        try {
            $carModel->save();
            return response()->json(['status'=>'success','mensagem'=>"carro $carModel->title, $carModel->description, $carModel->price inserido com sucesso"]);


        } catch (\Throwable $th) {
            return response()->json(['status'=>'error','mensagem'=>$th]);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return response()->json([
            'data' => $car,
            'status' => 'success'
        ],200);

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
        $json = $request->input('json');
        $params = json_decode($json);
        if(!$json){
            return response()->json(['status'=>'error','mensagem'=>'falta parametro json']);
        }
        if ( !isset($params->title) && !isset($params->description) && !isset($params->price) ){
            return response()->json(['status'=>'error','mensagem'=>'Para alterar o carro necessita ou title, ou description ou price values']);

        }

        $jwtAuth = new JwtAuthHelper();
        $token = request()->header('Authorization');
        $user = $jwtAuth->checkToken($token,true);
        
        $carModel = Car::find($id)->first();

        //dd($user);

        if( $user->sub != $carModel->user_id && $user->role_user !== 'ADMIN'){

            return response()->json(['status'=>'error','mensagem'=>'somente quem criou o carro pode alterar o mesmo ou um admin']);
  
        }

        

        if ( isset($params->title)){
            
            $carModel->title = strtolower($params->title);
        }

        if ( isset( $params->description)){
            
            $carModel->description = strtolower( $params->description);
        }
        
        if ( isset( $params->price)){
            
            $carModel->price = $params->price;
        }
        
        if ( isset( $params->status)){
            
            $carModel->status = $params->status;
        }


        try {
            $carModel->save();
            if( $carModel->isDirty() ){
                return response()->json(['status'=>'error','mensagem'=>'os dados enviado foram iguais aos originais no BD']);

            }else{
                return response()->json(['status'=>'success','mensagem'=>"os dados do carro $carModel->title foram alterados com sucesso"]);

            }
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
        
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carModel = Car::findOrFail($id);
        try {
            $carModel->delete();
            return response()->json(['status'=>'success','mensagem'=>"O carro $carModel->title foi deletado com sucesso"]);

        } catch (\Throwable $th) {
            throw $th;
        }

    }
}
