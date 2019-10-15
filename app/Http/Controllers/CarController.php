<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller
{
    public function index(Request $request){
    	/* $hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if ($checkToken) {
    		echo "Index de CarController AUTENTICADO"; die();
    	}else {
    		echo "NO AUTENTICADO -> Index de CarController"; die();
    	} */

    	$cars = Car::all()->load('user');
    	return response()->json(array(
    		'cars' => $cars,
    		'status' => 'success'
    	), 200);
    	 
    }

    public function show($id) { 
    	$car = Car::find($id);

        if(is_object($car)){
            $car = Car::find($id)->load('user');
            return response()->json(array('car' => $car, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'El coche no existe', 'status' => 'error'), 200);
        }
    	
    }

    public function store(Request $request) {
    	$hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if ($checkToken) {
    		// Recoger datos por POST
    		$json = $request->input('json', null);
    		$params = json_decode($json);    		

    		$params_array = json_decode($json, true);

    		//$params_array = array_add($params_array, 'numero', 06);

    		//return response()->json($params_array, 200);

    		// Conseguir el usuario identificado
    		$user = $jwtAuth->checkToken($hash, true);

    		//return response()->json($user, 200);

    		// Validación    		
    		
    		$validate = \Validator::make($params_array, [
    			'title' => 'required',
    			'description' => 'required',
    			'price' => 'required',
    			'status' => 'required'
    		]);

    		if ($validate->fails()) {
    			return response()->json($validate->errors(), 400);
    		}
	    		   		  		

			$car = new Car();
			$car->user_id = $user->sub;
    		$car->title = $params->title;
    		$car->description = $params->description;
    		$car->price = $params->price;
    		$car->status = $params->status;

    		$car->save();    

    		$data = array(
    			'car' => $car,
    			'status' => 'success',
    			'code' => 200
    		);  		

    	}else {
    		// Devolver error

    		$data = array(
    			'mesaage' => 'Login incorrecto',
    			'status' => 'error',
    			'code' => 300
    		);

    	};

    	return response()->json($data, 200);

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
    			'title' => 'required',
    			'description' => 'required',
    			'price' => 'required',
    			'status' => 'required'
    		]);

    		if ($validate->fails()) {
    			return response()->json($validate->errors(), 400);
    		} 

    		// Actualizar coche

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

    		$car = Car::where('id', $id)->update($params_array);

    		$data = array(
    			'car' => $params,
    			'status' => 'success',
    			'code' => 200
    		);

    	}else {
    		// Devolver error

    		$data = array(
    			'mesaage' => 'Login incorrecto',
    			'status' => 'error',
    			'code' => 300
    		);

    	};

    	return response()->json($data, 200);

    }

    public function destroy($id, Request $request) {
    	$hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if ($checkToken) {
    		// Comprobar que existe el registro
    		$car = Car::find($id);    		

    		// Borrarlo
    		$car->delete();

    		// Devolverlo
			$data = array(
				'car' => $car,				    			
    			'status' => 'success',
    			'code' => 200
    		);

    	}else{
    		// Devolver error
    		$data = array(
    			'message' => 'Login incorrecto',
    			'status' => 'error',
    			'code' => 400
    		);
    	}

    	return response()->json($data, 200);
    }

} // end class
