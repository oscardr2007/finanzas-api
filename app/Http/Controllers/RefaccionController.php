<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Refaccion;

class RefaccionController extends Controller
{
       
    public function index(Request $request){        
        $refacciones = Refaccion::limit(100)->get()->load('user');           
        return response()->json(array(
            'refacciones' => $refacciones,
            'status' => 'success'
        ), 200);         
    }

    public function show($id) { 
        
        $refaccion = Refaccion::find($id);

        if(is_object($refaccion)){
            $refaccion = Refaccion::find($id)->load('user');
            return response()->json(array('refaccion' => $refaccion, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'La refaccion no existe', 'status' => 'error'), 200);
        }        
    }

    public function deSolicitud($id, $criterio) { 
        
        $refacciones = Refaccion::select('id', 'descripcion')
                            ->where('descripcion', 'LIKE', '%'.$criterio.'%')
                            ->whereNOTIn('id', function($query) use ($id) {
                                $query->select('refaccion_id')->from('solicitud_refaccion')
                                      ->where('solicitud_id', '=', $id );
                            })                            
                            ->get();   

        if(is_object($refacciones)){           
            return response()->json(array('refacciones' => $refacciones, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de refacciones vacio', 'status' => 'error'), 200);
        }
        
    }
        
    public function listado($criterio) { 
        
        $refacciones = Refaccion::select('id', 'user_id', 'descripcion','estatus')
                            ->where('descripcion', 'LIKE', '%'.$criterio.'%')                                                   
                            ->limit(100)
                            ->get();

        $refacciones = $refacciones->load('user');
        if(is_object($refacciones)){                    
            return response()->json(array('refacciones' => $refacciones, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de refacciones vacio', 'status' => 'error'), 200);
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
                'descripcion' => 'required'                               
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }                                

            $refaccion = new Refaccion();
            $refaccion->user_id = $user->sub;                        
            $refaccion->descripcion = strtoupper($params->descripcion);           
            $refaccion->estatus = '1';   // 1 = Activo, 2 = Baja                          

            $refaccion->save();    
            $data = array(
                'refaccion' => $refaccion,
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

            $refaccion = Refaccion::where('id', $id)->update($params_array);

            $data = array(
                'refaccion' => $params,
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
