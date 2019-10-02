<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Vehiculo;
use App\Kilometraje;
use App\Ruta;
use DB;

class KilometrajeController extends Controller
{
       
    public function uVehiculos($usuario_id){                

        $uvehiculos = DB::table('vehiculos')
                        ->join('uvehiculos', 'uvehiculos.vehiculo_id', '=', 'vehiculos.id')
                        ->select('uvehiculos.user_id as user_id', 
                                 'uvehiculos.vehiculo_id as vehiculo_id',
                                 DB::raw("CONCAT(vehiculos.marca, ' ', vehiculos.linea, ' ', vehiculos.placas) as descripcion"))
                        ->where('uvehiculos.user_id', '=', $usuario_id)                        
                        ->orderBy('marca', 'DESC')                        
                        ->get();

        return response()->json(array(
            'uvehiculos' => $uvehiculos,
            'status' => 'success'
        ), 200);         
    }

    public function registroKm($vehiculo_id){                

        $bitacora = DB::table('kilometrajes')                        
                        ->select('user_id', 'kilometraje', 'tanque', 'dotacion', 'created_at')                                  
                        ->where('vehiculo_id', '=', $vehiculo_id)                        
                        ->orderBy('created_at', 'DESC')     
                        ->limit(50)                  
                        ->get();

        return response()->json(array(
            'bitacora' => $bitacora,
            'status' => 'success'
        ), 200);         
    }

     public function rutasKm($usuario_id){        
        $rutas = Ruta::select('id', 'ruta')
                       ->get();           
        return response()->json(array(
            'rutas' => $rutas,
            'status' => 'success'
        ), 200);         
    }
    
     public function addKm(Request $request) {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Recoger datos por POST
            
            $json = $request->input('json', null);
            $params = json_decode($json);           

            $params_array = json_decode($json, true);                                           

            $kilometraje = new Kilometraje();
            $kilometraje->user_id = $params->user_id;
            $kilometraje->vehiculo_id = $params->vehiculo_id;
            $kilometraje->kilometraje = $params->kilometraje;
            $kilometraje->tanque = $params->tanque;
            $kilometraje->ruta_id = $params->ruta_id;
            $kilometraje->ruta_tipo = $params->ruta_tipo;
            $kilometraje->dotacion = $params->dotacion;
            $kilometraje->latitud = $params->latitud;
            $kilometraje->longitud = $params->longitud;

            $kilometraje->save();    

            $data = array(
                'kilometraje' => $kilometraje,
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


}