<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\DB;
use App\User;

class UserController extends Controller
{

    public function index(Request $request){                
        $usuarios = User::limit(100)->get();
        return response()->json(array(
            'usuarios' => $usuarios,
            'status' => 'success'
        ), 200);         
    }

    public function show($id) { 
        $usuario = User::find($id);
        if(is_object($usuario)){
            $usuario = User::find($id);
            return response()->json(array('usuario' => $usuario, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'El usuario no existe', 'status' => 'error'), 200);
        }        
    }

    public function rolUsuario($id) { 
        $usuario = User::find($id);
        if(is_object($usuario)){
            $usuario = User::find($id);
            return response()->json(array('usuario' => $usuario, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'El usuario no existe', 'status' => 'error'), 200);
        }        
    }

    public function listado($criterio) {         
        $usuarios = User::select('id', 'name','surname', 'email', 'role', 'created_at', 'updated_at')
                            ->where('name', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('surname', 'LIKE', '%'.$criterio.'%')
                            ->limit(100)
                            ->get();       
       
        if(is_object($usuarios)){                       
            return response()->json(array('usuarios' => $usuarios, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de usuarios vacio', 'status' => 'error'), 200);
        }        
    }

    public function register(Request $request){
        
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$email = (!is_null($json) && isset($params->email)) ? $params->email : null;
    	$name = (!is_null($json) && isset($params->name)) ? $params->name : null;
    	$surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;     	
    	//$role = (!is_null($json) && isset($params->role)) ? $params->role : null;
        $role = 3; // Role usuario TECNICO
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null;

    	if (!is_null($email) && !is_null($password) && !is_null($name)){
    		
    		$user = new User();
    		$user->email = $email;

    		$pwd = hash('sha256', $password);

    		$user->password = $pwd;
    		$user->name = strtoupper($name);
    		$user->surname = strtoupper($surname);
            $user->email = strtolower($email);
    		$user->role = $role;

    		// Comprobar usuario duplicado

    		$isset_user = User::where('email', '=', $email)->first();

    		if (!is_object($isset_user ) || count($isset_user) == 0) {
    			// Guardar usuario
    			$user->save();
    			$data = array(
    			'status' => 'success',
    			'code' => 200,
    			'message' => 'Usuario registrado correctamente'
    			);

    		}else{
    			// No Guardar usuario porque ya existe
    			$data = array(
    			'status' => 'error',
    			'code' => 400,
    			'message' => 'Usuario duplicado, no puede registrarse'    			
    			);
    		}

    	} else {
    		$data = array(
    			'status' => 'error',
    			'code' => 400,
    			'message' => 'Usuario no creado'
    		);
    	}
    	return response()->json($data, 200);
    }

    public function login(Request $request){
    	
        $jwtAuth = new JwtAuth();
    	// Recibir POST
    	$json = $request->input('json', null);
    	$params = json_decode($json);

    	$email = (!is_null($json) && isset($params->email)) ? $params->email : null;
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null;
    	$getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;
    	// Cifrar el password

    	$pwd = hash('sha256', $password);

    	if (!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')) {
    		$signup = $jwtAuth->signup($email, $pwd);    		 
    	}elseif($getToken != null) {
    		$signup = $jwtAuth->signup($email, $pwd, $getToken);    		
    	}else {
    		$signup = array(
    			'status' => 'error', 
    			'message' => 'Envía tu datos por post'
    		);
    	}
    	return response()->json($signup, 200);
    }


    public function update($id, Request $request) {
        
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Recoger parametros POST

            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            // Validar datos

            $validate = \Validator::make($params_array, [
                'name' => 'required',
                'surname' => 'required' 
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            } 

            // Actualizar coche

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            $params_array['name'] = strtoupper($params_array['name']);  
            $params_array['surname'] = strtoupper($params_array['surname']);   
            $params_array['email'] = strtolower($params_array['email']);              

            $usuario = User::where('id', $id)->update($params_array);

            $data = array(
                'usuario' => $params,
                'status' => 'success',
                'code' => 200
            );

        }else {
            // Devolver error

            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code' => 300
            );
        };
        return response()->json($data, 200);
    }

    public function resetPassword($id, Request $request) {

        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
           
            // Recoger parametros POST

            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);                       

            // Actualizar coche

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['name']);
            unset($params_array['surname']);
            unset($params_array['email']);
            unset($params_array['created_at']);
            unset($params_array['role']); 

            $params_array['password'] = hash('sha256', $params_array['password']); 
            $usuario = User::where('id', $id)->update($params_array);
            $data = array(
                'usuario' => $params,
                'status' => 'success',
                'code' => 200
            );

        }else {
            // Devolver error
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code' => 300
            );
        };
        return response()->json($data, 200);
    }

    public function tecnicos($criterio, $id) { 

        $tecnicos = User::select('id', 
                            DB::raw("CONCAT(surname,' ', name) as full_nombre"))
                            ->where(function($query) use ($criterio) {
                                $query->where('name', 'LIKE', '%'.$criterio.'%')
                                      ->orWhere('surname', 'LIKE', '%'.$criterio.'%');
                            })
                            ->where('id', '!=', $id)
                            ->where('role', '!=', 1)
                            ->get();      
       
        if(is_object($tecnicos)){                       
            return response()->json(array('tecnicos' => $tecnicos, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de tecnicos vacio', 'status' => 'error'), 200);
        }        
    }

}
