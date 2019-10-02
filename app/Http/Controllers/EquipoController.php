<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Equipo;
use Fpdf;
Use DB;

class EquipoController extends Controller
{   
    public function index(Request $request){       
        
        $equipos = Equipo::limit(100)->get();           
        return response()->json(array(
            'equipos' => $equipos,
            'status' => 'success'
        ), 200);         
    }

    public function show($id) {         
        $equipo = Equipo::find($id);
        if(is_object($equipo)){
            $equipo = Equipo::find($id);
            return response()->json(array('equipo' => $equipo, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'El equipo no existe', 'status' => 'error'), 200);
        }        
    }

    public function filtrado($criterio) { 
        
        $equipos = Equipo::select('id', 
                            DB::raw("CONCAT(marca,' ', modelo, ' ', serie, ' ', inventario, ' ') as full_descripcion"))
                            ->where(function($query) use ($criterio) {
                                $query->where('marca', 'LIKE', '%'.$criterio.'%')
                                ->orWhere('modelo', 'LIKE', '%'.$criterio.'%')
                                ->orWhere('serie', 'LIKE', '%'.$criterio.'%')
                                ->orWhere('inventario', 'LIKE', '%'.$criterio.'%');
                           })->where('estatus', '1')
                            ->get();     

        if(is_object($equipos)){           
            return response()->json(array('equipos' => $equipos, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de equipos vacio', 'status' => 'error'), 200);
        }
        
    }

    public function filtradoUno($id) {         
        $equipo = Equipo::select('id', 
                            DB::raw("CONCAT(marca,' ', modelo, ' ', serie, ' ', inventario, ' ') as full_descripcion"))
                            ->where('id', '=', $id)                           
                            ->get();    
        if(is_object($equipo)){           
            return response()->json(array('equipo' => $equipo, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de equipos vacio', 'status' => 'error'), 200);
        }        
    }
    
    public function listado($criterio) { 
        
        $equipos = Equipo::select('id', 'user_id', 'marca','modelo', 'serie', 'inventario', 'estatus')
                            ->where('marca', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('modelo', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('serie', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('inventario', 'LIKE', '%'.$criterio.'%')
                            ->limit(100)
                            ->get();

        $equipos = $equipos->load('user');

        if(is_object($equipos)){
            //$equipo = Equipo::find($equipo->user_id)->load('user');            
            return response()->json(array('equipos' => $equipos, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de equipos vacio', 'status' => 'error'), 200);
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
                'descripcion' => 'required',
                'marca' => 'required',
                'modelo' => 'required'                
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }
                                
            $equipo = new Equipo();
            $equipo->user_id = $user->sub;
            $equipo->descripcion = strtoupper($params->descripcion);
            $equipo->marca = strtoupper($params->marca);
            $equipo->modelo = strtoupper($params->modelo);
            $equipo->fecha_adq = $params->fecha_adq;
            $equipo->serie = strtoupper($params->serie);
            $equipo->inventario = strtoupper($params->inventario);
            $equipo->estatus = '1';   // 1 = Activo, 2 = Deshabilitado, 3 = Baja
            $equipo->ubicacion = strtoupper($params->ubicacion);
            $equipo->ubicacion_lt = $params->ubicacion_lt;
            $equipo->ubicacion_ln = $params->ubicacion_ln;                   

            $equipo->save();    

            $data = array(
                'equipo' => $equipo,
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

            // Validar datos

            $validate = \Validator::make($params_array, [
                'marca' => 'required',
                'modelo' => 'required',
                'serie' => 'required',
                'inventario' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }             
            
            $params_array['descripcion'] = strtoupper($params->descripcion);
            $params_array['marca'] = strtoupper($params->marca);
            $params_array['modelo'] = strtoupper($params->modelo);
            $params_array['serie'] = strtoupper($params->serie);          
            $params_array['inventario'] = strtoupper($params->inventario);
            $params_array['ubicacion'] = strtoupper($params->ubicacion);

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            $equipo = Equipo::where('id', $id)->update($params_array);

            $data = array(
                'equipo' => $params,
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

    public function equipopdf() {
     $pdf = new Fpdf();
     $pdf::AddPage();
     $pdf::SetFont('Arial','B',18);
     $pdf::Cell(0,10,"Title",0,"","C");
     $pdf::Ln();
     $pdf::Ln();
     $pdf::SetFont('Arial','B',12);
     $pdf::cell(25,8,"ID",1,"","C");
     $pdf::cell(45,8,"Name",1,"","L");
     $pdf::cell(35,8,"Address",1,"","L");
     $pdf::Ln();
     $pdf::SetFont("Arial","",10);
     $pdf::cell(25,8,"1",1,"","C");
     $pdf::cell(45,8,"John",1,"","L");
     $pdf::cell(35,8,"New York",1,"","L");
     $pdf::Ln();
     $pdf::Output();
     exit;

     //return response()->json(array(
     //       'equipos' => 'si',
     //       'status' => 'success'
     //   ), 200);
    }    
}
