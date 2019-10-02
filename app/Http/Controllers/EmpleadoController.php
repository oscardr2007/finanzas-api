<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Empleado;
use DB;

class EmpleadoController extends Controller
{
       
    public function index(Request $request){        
        $empleados = Empleado::where('apellidos', '.')->limit(100)->get();           
        return response()->json(array(
            'empleados' => $empleados,
            'status' => 'success'
        ), 200);         
    }

    public function show($id) { 
        
        $empleado = Empleado::find($id);

        if(is_object($empleado)){            
            return response()->json(array('empleado' => $empleado, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'El empleado no existe', 'status' => 'error'), 200);
        }        
    }

    public function filtrado($criterio) { 
        
        $empleados = Empleado::select('id', 
                            DB::raw("CONCAT(nombre,' ', apellidos) as full_nombre"))
                            ->where('nombre', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('apellidos', 'LIKE', '%'.$criterio.'%')
                            ->get();     

        if(is_object($empleados)){           
            return response()->json(array('empleados' => $empleados, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de empleados vacio', 'status' => 'error'), 200);
        }
        
    }

    public function filtradoUno($id) { 
        
        $empleado = Empleado::select('id', 
                            DB::raw("CONCAT(nombre,' ', apellidos) as full_nombre"))
                            ->where('id', '=', $id)                                                        
                            ->get();     

        if(is_object($empleado)){           
            return response()->json(array('empleado' => $empleado, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de empleados vacio', 'status' => 'error'), 200);
        }
        
    }
    
    public function listado($criterio) { 
        
        $empleados = Empleado::select('id', 'user_id', 'clave','nombre', 'apellidos', 'fecha_nac', 'email', 'estatus')
                            ->where('nombre', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('apellidos', 'LIKE', '%'.$criterio.'%')                           
                            ->limit(100)
                            ->get();
        
        if(is_object($empleados)){                    
            return response()->json(array('empleados' => $empleados, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de empleados vacio', 'status' => 'error'), 200);
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
                'clave' => 'required',
                'nombre' => 'required',
                'apellidos' => 'required'                
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }                                

            $empleado = new Empleado();
            $empleado->user_id = $user->sub;            
            $empleado->clave = strtoupper($params->clave);
            $empleado->nombre = strtoupper($params->nombre);
            $empleado->apellidos = strtoupper($params->apellidos);
            $empleado->puesto = strtoupper($params->puesto);
            $empleado->fecha_nac = $params->fecha_nac;
            $empleado->email = strtolower($params->email);
            $empleado->estatus = '1';   // 1 = Activo, 2 = Baja                          

            $empleado->save();    
            $data = array(
                'empleado' => $empleado,
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
                'clave' => 'required',
                'nombre' => 'required',              
                'apellidos' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }            

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['updated_at']);
            unset($params_array['user']);

            $params_array['clave'] = strtoupper($params->clave);
            $params_array['nombre'] = strtoupper($params->nombre);
            $params_array['apellidos'] = strtoupper($params->apellidos);
            $params_array['puesto'] = strtoupper($params->puesto);          
            $params_array['email'] = strtolower($params->email);

            $empleado = Empleado::where('id', $id)->update($params_array);

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