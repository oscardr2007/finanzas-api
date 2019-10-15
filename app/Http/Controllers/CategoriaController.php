<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Categoria;

class CategoriaController extends Controller
{
       
    public function index(Request $request){        
        $categorias = Categoria::limit(100)->get();           
        return response()->json(array(
            'categorias' => $categorias,
            'status' => 'success'
        ), 200);         
    }

    public function show($id) { 
        
        $categoria = Categoria::find($id);

        if(is_object($categoria)){
            $categoria = Categoria::find($id);
            return response()->json(array('categoria' => $categoria, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'La categoria no existe', 'status' => 'error'), 200);
        }        
    }
    
    public function listado($criterio) { 
        
        $categorias = Categoria::select('id', 'user_id', 'descripcion','estatus')
                            ->where('descripcion', 'LIKE', '%'.$criterio.'%')
                            ->limit(100)
                            ->get();
        
        if(is_object($categorias)){                    
            return response()->json(array('categorias' => $categorias, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de categorias vacio', 'status' => 'error'), 200);
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

            $categoria = new Categoria();
            $categoria->user_id = $user->sub;                        
            $categoria->descripcion = strtoupper($params->descripcion);           
            $categoria->estatus = '1';   // 1 = Activo, 2 = Baja                          

            $categoria->save();    
            $data = array(
                'categoria' => $categoria,
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

            $categoria = Categoria::where('id', $id)->update($params_array);

            $data = array(
                'categoria' => $params,
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