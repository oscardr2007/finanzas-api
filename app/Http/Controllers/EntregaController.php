<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Apersona;
use App\Bpersona;
use DB;

class EntregaController extends Controller
{     
  
  public function chkCurp($curp) { 
    
    $contador = DB::table('apersonas')
                     ->select(DB::raw('count(*) as total'))
                     ->where('curp', '=', $curp)                  
                     ->get();

    if(is_object($contador)){                       
        return response()->json(array('total' => $contador[0]->total, 'status' => 'success'), 200);
    }else {
        return response()->json(array('message' => 'No existe catalogo', 'status' => 'error'), 200);
    }
            
  }

  public function insDatosA(Request $request) {         
            
    $json = $request->input('json', null);
    $params = json_decode($json);           
    $params_array = json_decode($json, true);  

    $datos = new Apersona();
    $datos->user_id = $params->user_id;
    $datos->nombre = $params->nombre;
    $datos->apellido_paterno = $params->apellido_paterno;
    $datos->apellido_materno = $params->apellido_materno;
    $datos->sexo = $params->sexo;
    $datos->fecha_nacimiento = $params->fecha_nacimiento;
    $datos->curp = $params->curp;  
    $datos->latitud = $params->latitud;  
    $datos->longitud = $params->longitud;  

    $datos->save();

    $LastInsertId = $datos->id;

    $data = array(   
        'id' => $LastInsertId,     
        'status' => 'success',
        'code' => 200
    );
         
    return response()->json($data, 200);

  }

  public function insDatosB(Request $request) {         
            
    $json = $request->input('json', null);
    $params = json_decode($json);           
    $params_array = json_decode($json, true);  

    $datos = new Bpersona();
    $datos->persona_id = $params->persona_id;
    $datos->user_id = $params->user_id;
    $datos->rfc = $params->rfc;
    $datos->nacionalidad_id = $params->nacionalidad_id;
    $datos->documento_id = $params->documento_id;
    $datos->documento_folio = $params->documento_folio;
    $datos->telefono = $params->telefono;
    $datos->celular = $params->celular;
    $datos->email = $params->email;
    $datos->efederativa_id = $params->efederativa_id;      
    $datos->save();

    $data = array(   
        'status' => 'success',
        'code' => 200
    );
         
    return response()->json($data, 200);

  }
    
}



