<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Adscripcion;
use DB;

class AdscripcionController extends Controller
{
    //   
    public function index(Request $request){
       
        $adscripciones = Adscripcion::limit(100)->get();          
        return response()->json(array(
            'adscripciones' => $adscripciones,
            'status' => 'success'
        ), 200);
         
    }

    public function show($id) { 
        
        $adscripcion = Adscripcion::find($id)->limit(100);

        if(is_object($adscripcion)){
            $adscripcion = Adscripcion::find($id);
            return response()->json(array('adscripcion' => $adscripcion, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'La adscripcion no existe', 'status' => 'error'), 200);
        }
        
    }
    
    public function listado($criterio) { 
        
        $adscripciones = Adscripcion::select('id', 'user_id', 'codigo','descripcion', 'lada', 'telefono')
                            ->where('codigo', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('descripcion', 'LIKE', '%'.$criterio.'%')                            
                            ->limit(100)
                            ->get();       

        if(is_object($adscripciones)){           
            return response()->json(array('adscripciones' => $adscripciones, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de adscripciones vacio', 'status' => 'error'), 200);
        }
        
    }

    public function filtrado($criterio) { 
        
        $adscripciones = Adscripcion::select('id', 
                            DB::raw("CONCAT(codigo,' ', descripcion) as full_descripcion"))
                            ->where('codigo', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('descripcion', 'LIKE', '%'.$criterio.'%')
                            ->get();     

        if(is_object($adscripciones)){           
            return response()->json(array('adscripciones' => $adscripciones, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de adscripciones vacío', 'status' => 'error'), 200);
        }
        
    }

    public function filtradoUno($id) { 
        
        $adscripcion = Adscripcion::select('id', 
                            DB::raw("CONCAT(codigo,' ', descripcion) as full_descripcion"))
                            ->where('id', '=', $id)
                            ->get();
        if(is_object($adscripcion)){           
            return response()->json(array('adscripcion' => $adscripcion, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de adscripciones vacio', 'status' => 'error'), 200);
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

            // Conseguir el usuario identificado
            $user = $jwtAuth->checkToken($hash, true);            

            // Validación           
            
            $validate = \Validator::make($params_array, [
                'codigo' => 'required',                
                'descripcion' => 'required'                
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }                                

            $adscripcion = new Adscripcion();
            $adscripcion->user_id = $user->sub;
            $adscripcion->codigo = strtoupper($params->codigo);
            $adscripcion->descripcion = strtoupper($params->descripcion);
            $adscripcion->direccion = strtoupper($params->direccion);
            $adscripcion->lada = strtoupper($params->lada);
            $adscripcion->telefono = strtoupper($params->telefono);                       
            $adscripcion->ubicacion_lt = $params->ubicacion_lt;
            $adscripcion->ubicacion_ln = $params->ubicacion_ln;                   

            $adscripcion->save();    

            $data = array(
                'adscripcion' => $adscripcion,
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
                'codigo' => 'required',               
                'descripcion' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            } 

            // Actualizar adscripcion
           
            $params_array['codigo'] = strtoupper($params->codigo);
            $params_array['descripcion'] = strtoupper($params->descripcion);
            $params_array['direccion'] = strtoupper($params->direccion);
            $params_array['lada'] = strtoupper($params->lada);
            $params_array['telefono'] = strtoupper($params->telefono);

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            $adscripcion = Adscripcion::where('id', $id)->update($params_array);

            $data = array(
                'adscripcion' => $params,
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

    /* FINANZAS */

    public function getAdscripciones(Request $request) {

        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Recoger parametros POST

            $json = $request->input('json', null);

            $params = json_decode($json);

            $params_array = json_decode($json, true);

            $criterio = $params->criterio;

            if ($criterio == '' || $criterio ='*') {

                $adscripciones = Adscripcion::select('id', 'user_id', 'codigo','descripcion', 'ingresa_facturas', 'estatus')
                    ->where('codigo', 'LIKE', '%'.$criterio.'%')
                    ->orWhere('descripcion', 'LIKE', '%'.$criterio.'%')                            
                    ->limit(50)
                    ->get();   

            } else {
                $adscripciones = Adscripcion::select('id', 'user_id', 'codigo','descripcion', 'ingresa_facturas', 'estatus')                                           
                    ->limit(50)
                    ->get(); 
            }            
            
            if(is_object($adscripciones)){           
                return response()->json(array('adscripciones' => $adscripciones, 'status' => 'success'), 200);
            }else {
                return response()->json(array('message' => 'Catálogo vacio', 'status' => 'error'), 200);
            }

        }else {
            // Devolver error

            $data = array(
                'mesaage' => 'Login incorrecto',
                'status' => 'error',
                'code' => 300
            );

        };
        
    }
    
}
