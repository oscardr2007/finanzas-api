<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Servicio;
use DB;

class ServicioController extends Controller
{
       
    public function index($categoria_id){        
        $servicios = Servicio::select('id', 'user_id', 'categoria_id', 'descripcion','estatus')
                            ->where('categoria_id', '=', $categoria_id)
                            ->limit(100)                            
                            ->get();        
     
        return response()->json(array(
            'servicios' => $servicios,
            'status' => 'success'
        ), 200);         
    }      

     public function show($id) { 
        
        $servicio = Servicio::find($id);

        if(is_object($servicio)){
            $servicio = Servicio::find($id);
            return response()->json(array('servicio' => $servicio, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'El servicio no existe', 'status' => 'error'), 200);
        }        
    }

    public function filtrado($criterio) { 
        
        $servicios = Servicio::select('id', 'descripcion')
                            ->where('descripcion', 'LIKE', '%'.$criterio.'%')
                            ->where('estatus', '1')                            
                            ->get();     

        if(is_object($servicios)){           
            return response()->json(array('servicios' => $servicios, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de servicios vacio', 'status' => 'error'), 200);
        }
        
    }

        
    public function filtradoUno($id) { 
        
        $servicio = Servicio::select('id', 'descripcion')
                            ->where('id', '=', $id)                           
                            ->get();     

        if(is_object($servicio)){           
            return response()->json(array('servicio' => $servicio, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de servicios vacio', 'status' => 'error'), 200);
        }
        
    }


    public function listado($categoria_id, $criterio) {        

        $servicios = Servicio::select('id', 'user_id', 'categoria_id', 'descripcion','estatus')
                            ->where('categoria_id', '=', $categoria_id)
                            ->where('descripcion', 'LIKE', '%'.$criterio.'%')
                            ->limit(100)                            
                            ->get();            
        if(is_object($servicios)){                    
            return response()->json(array('servicios' => $servicios, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de servicios vacio', 'status' => 'error'), 200);
        }        
    }  

    public function store($id, Request $request) {
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Recoger datos por POST
            $json = $request->input('json', null);
            $params = json_decode($json);           

            $params_array = json_decode($json, true);           

            // Conseguir el usuario identificado
            $user = $jwtAuth->checkToken($hash, true);            

            // Validación           
            
            $validate = \Validator::make($params_array, [               
                'descripcion' => 'required'                
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }                                

            $servicio = new Servicio();
            $servicio->user_id = $user->sub;   
            $servicio->categoria_id = $id;                    
            $servicio->descripcion = strtoupper($params->descripcion);            
            $servicio->estatus = '1';   // 1 = Activo, 2 = Baja   

            $servicio->save();    
            
            $data = array(
                'servicio' => $servicio,
                'status' => 'success',
                'code' => 200
            );          

        }else {           

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

            $validate = \Validator::make($params_array, [
                'descripcion' => 'required'              
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }            

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            $params_array['descripcion'] = strtoupper($params->descripcion);            

            $empleado = Servicio::where('id', $id)->update($params_array);

            $data = array(
                'empleado' => $params,
                'status' => 'success',
                'code' => 200
            );

        }else {       
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code' => 300
            );
        };
        return response()->json($data, 200);
    }
    
}