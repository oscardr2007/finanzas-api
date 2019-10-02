<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Solicitud;
use App\Detalle;
use DB;

class SolicitudController extends Controller
{
       
    public function index(Request $request){                

        $solicitudes = DB::table('solicitudes')
                        ->join('adscripciones', 'solicitudes.adscripcion_id', '=', 'adscripciones.id')
                        ->join('servicios', 'solicitudes.servicio_id', '=', 'servicios.id')
                        ->select('solicitudes.id as id', 'adscripciones.descripcion as adscripcion', 'servicios.descripcion as servicio')
                        ->where('solicitudes.estatus', '=', 'R')                        
                        ->orderBy('id', 'DESC')
                        ->limit(100)
                        ->get();
            
        return response()->json(array(
            'solicitudes' => $solicitudes,
            'status' => 'success'
        ), 200);         
    }

    public function deUsuario($id){                

        $solicitudes = DB::table('solicitudes')
                        ->join('adscripciones', 'solicitudes.adscripcion_id', '=', 'adscripciones.id')
                        ->join('servicios', 'solicitudes.servicio_id', '=', 'servicios.id')
                        ->select('solicitudes.id as id', 'adscripciones.descripcion as adscripcion', 'servicios.descripcion as servicio', 'solicitudes.fecha')
                        ->where('solicitudes.user_id', $id)   
                        ->where('solicitudes.estatus','R')                     
                        ->orderBy('id', 'DESC')                        
                        ->get();
            
        return response()->json(array(
            'solicitudes' => $solicitudes,
            'status' => 'success'
        ), 200);         
    }

    public function allSolicitudes(){                

        $solicitudes = DB::table('solicitudes')
                        ->join('adscripciones', 'solicitudes.adscripcion_id', '=', 'adscripciones.id')
                        ->join('servicios', 'solicitudes.servicio_id', '=', 'servicios.id')
                        ->join('users', 'solicitudes.user_id', '=', 'users.id')
                        ->select('solicitudes.id as id', 'adscripciones.descripcion as adscripcion', 'servicios.descripcion as servicio', 'solicitudes.fecha', 'solicitudes.estatus',
                            DB::raw("CONCAT(users.name,' ', users.surname) as full_nombre"), 'solicitudes.indicador_equipo as indicador_equipo')
                        ->orderBy('id', 'DESC')  
                        ->limit(100)                      
                        ->get();
            
        return response()->json(array(
            'solicitudes' => $solicitudes,
            'status' => 'success'
        ), 200);         
    }

     public function SolicitudesDeEquipo($id){                

        $solicitudes = DB::table('solicitudes')
                        ->join('adscripciones', 'solicitudes.adscripcion_id', '=', 'adscripciones.id')
                        ->join('servicios', 'solicitudes.servicio_id', '=', 'servicios.id')
                        ->join('users', 'solicitudes.user_id', '=', 'users.id')
                        ->join('equipos', 'solicitudes.equipo_id', '=', 'equipos.id')
                        ->select('solicitudes.id as id', 'adscripciones.descripcion as adscripcion', 'servicios.descripcion as servicio', 'solicitudes.fecha', 'solicitudes.estatus',
                            DB::raw("CONCAT(users.name,' ', users.surname) as full_nombre"), 'solicitudes.indicador_equipo as indicador_equipo')
                        ->where('equipos.id', $id)
                        ->orderBy('id', 'DESC')  
                        ->limit(100)                      
                        ->get();
            
        return response()->json(array(
            'solicitudes' => $solicitudes,
            'status' => 'success'
        ), 200);         
    }

    public function solicitudHistorico($id){                

        $solicitudes = DB::table('solicitudes')
                        ->join('adscripciones', 'solicitudes.adscripcion_id', '=', 'adscripciones.id')
                        ->join('servicios', 'solicitudes.servicio_id', '=', 'servicios.id')
                        ->join('users', 'solicitudes.user_id', '=', 'users.id')                        
                        ->select('solicitudes.id as id', 'adscripciones.descripcion as adscripcion', 'servicios.descripcion as servicio', 'solicitudes.fecha', 'solicitudes.estatus',
                            DB::raw("CONCAT(users.name,' ', users.surname) as full_nombre"), 'solicitudes.indicador_equipo as indicador_equipo')
                        ->where('solicitudes.equipo_id', $id)
                        ->orderBy('id', 'DESC')  
                        ->limit(100)                      
                        ->get();
            
        return response()->json(array(
            'solicitudes' => $solicitudes,
            'status' => 'success'
        ), 200);         
    }


    public function show($id) { 
        
        $solicitud = Solicitud::find($id);

        if(is_object($solicitud)){
            $solicitud = Solicitud::find($id)->load('user');
            return response()->json(array('solicitud' => $solicitud, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'El solicitud no existe', 'status' => 'error'), 200);
        }        
    }

    public function filtrado($criterio) { 
        
        $solicitudes = Solicitud::select('id', 
                            DB::raw("CONCAT(nombre,' ', apellidos) as full_nombre"))
                            ->where('nombre', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('apellidos', 'LIKE', '%'.$criterio.'%')
                            ->limit(100)
                            ->get();     

        if(is_object($solicitudes)){           
            return response()->json(array('solicitudes' => $solicitudes, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de solicitudes vacio', 'status' => 'error'), 200);
        }
        
    }

    public function detalleSolicitud($id) { 
        
        $detalles = DB::table('solicitud_refaccion')
                            ->join('refacciones', 'solicitud_refaccion.refaccion_id', '=', 'refacciones.id')
                            ->select('solicitud_refaccion.solicitud_id', 'solicitud_refaccion.refaccion_id', 'refacciones.descripcion',
                                'solicitud_refaccion.cantidad')
                            ->where('solicitud_refaccion.solicitud_id', $id)
                            ->get();     

        if(is_object($detalles)){           
            return response()->json(array('detalles' => $detalles, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Detalle de solicitud vacio', 'status' => 'error'), 200);
        }
        
    }

    public function listado($criterio) { 
        
        $solicitudes = Solicitud::select('id', 'user_id', 'clave','nombre', 'apellidos', 'fecha_nac', 'email', 'estatus')
                            ->where('nombre', 'LIKE', '%'.$criterio.'%')
                            ->orWhere('apellidos', 'LIKE', '%'.$criterio.'%')                           
                            ->limit(100)
                            ->get();

        $solicitudes = $solicitudes->load('user');
        if(is_object($solicitudes)){                    
            return response()->json(array('solicitudes' => $solicitudes, 'status' => 'success'), 200);
        }else {
            return response()->json(array('message' => 'Catalogo de solicitudes vacio', 'status' => 'error'), 200);
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
                'fecha' => 'required',
                'hora' => 'required'                
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }                                

            $solicitud = new Solicitud();
            $solicitud->user_id = $user->sub;            
            $solicitud->adscripcion_id = $params->adscripcion_id;
            $solicitud->empleado_id = $params->empleado_id;
            $solicitud->equipo_id = $params->equipo_id;
            $solicitud->servicio_id = $params->servicio_id;
            $solicitud->descripcion = strtoupper($params->descripcion);
            $solicitud->fecha = $params->fecha;
            $solicitud->hora = $params->hora;
            $solicitud->indicador_equipo = strtoupper($params->indicador_equipo);
            $solicitud->estatus = strtoupper($params->estatus);            
            $solicitud->ubicacion_lt = $params->ubicacion_lt;
            $solicitud->ubicacion_ln = $params->ubicacion_ln;

            $solicitud->fecha_diag = $params->fecha_diag;
            $solicitud->hora_diag = $params->hora_diag;
            $solicitud->descripcion_diag = $params->descripcion_diag;
            $solicitud->fecha_diag_entrega = $params->fecha_diag_entrega;
            $solicitud->hora_diag_entrega = $params->hora_diag_entrega;

            $solicitud->fecha_ser = $params->fecha_ser;
            $solicitud->hora_ser = $params->hora_ser;
            $solicitud->utilizados_ser = $params->utilizados_ser;
            $solicitud->descripcion_ser = $params->descripcion_ser;
            $solicitud->observaciones_ser = $params->observaciones_ser;
            $solicitud->licenciamiento_ser = strtoupper($params->licenciamiento_ser);
            $solicitud->winoriginal_ser = strtoupper($params->winoriginal_ser);
            $solicitud->ofioriginal_ser = strtoupper($params->ofioriginal_ser);
            $solicitud->cantidad_ser = $params->cantidad_ser;
            $solicitud->evaluacion_ser = $params->evaluacion_ser;

            $solicitud->save();  
            
            $data = array(
                'solicitud' => $solicitud,
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
                'descripcion' => 'required',
                'fecha' => 'required',              
                'hora' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }            

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['updated_at']);
            unset($params_array['user']);

            $params_array['descripcion'] = strtoupper($params->descripcion);
            
            $solicitud = Solicitud::where('id', $id)->update($params_array);

            $data = array(
                'solicitud' => $params,
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

    public function updateDiag($id, Request $request) {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Recoger parametros POST

            $json = $request->input('json', null);

            $params = json_decode($json);

            $params_array = json_decode($json, true);           

            $validate = \Validator::make($params_array, [
                'descripcion_diag' => 'required',
                'fecha_diag' => 'required',              
                'hora_diag' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }            

            unset($params_array['id']);
            unset($params_array['user_id']);            
            unset($params_array['user']);
            unset($params_array['servicio_id']);
            unset($params_array['adscripcion_id']);
            unset($params_array['empleado_id']);
            unset($params_array['equipo_id']);
            unset($params_array['descripcion']);
            unset($params_array['fecha']);
            unset($params_array['hora']);
            unset($params_array['indicador_equipo']);
            unset($params_array['estatus']);
            unset($params_array['ubicacion_ln']);
            unset($params_array['ubicacion_lt']);
            unset($params_array['fecha_ser']);
            unset($params_array['hora_ser']);
            unset($params_array['utilizados_ser']);
            unset($params_array['descripcion_ser']);
            unset($params_array['observaciones_ser']);
            unset($params_array['licenciamiento_ser']);
            unset($params_array['winoriginal_ser']);
            unset($params_array['ofioriginal_ser']);
            unset($params_array['cantidad_ser']);
            unset($params_array['evaluacion_ser']);
            unset($params_array['remember_token']);
            unset($params_array['created_at']);
            unset($params_array['updated_at']);

            $params_array['descripcion_diag'] = strtoupper($params->descripcion_diag);
            
            $solicitud = Solicitud::where('id', $id)->update($params_array);

            $data = array(
                'solicitud' => $params,
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

    public function updateSer($id, Request $request) {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Recoger parametros POST

            $json = $request->input('json', null);

            $params = json_decode($json);

            $params_array = json_decode($json, true);           

            $validate = \Validator::make($params_array, [
                'descripcion_ser' => 'required',
                'fecha_ser' => 'required',              
                'hora_ser' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }            

            unset($params_array['id']);
            unset($params_array['user_id']);            
            unset($params_array['user']);
            unset($params_array['servicio_id']);
            unset($params_array['adscripcion_id']);
            unset($params_array['empleado_id']);
            unset($params_array['equipo_id']);
            unset($params_array['descripcion']);
            unset($params_array['fecha']);
            unset($params_array['hora']);
            unset($params_array['indicador_equipo']);
            unset($params_array['estatus']);
            unset($params_array['ubicacion_ln']);
            unset($params_array['ubicacion_lt']);            
            unset($params_array['fecha_diag']);
            unset($params_array['hora_diag']);
            unset($params_array['descripcion_diag']);
            unset($params_array['fecha_diag_entrega']);
            unset($params_array['hora_diag_entrega']);           
            unset($params_array['evaluacion_ser']);
            unset($params_array['remember_token']);
            unset($params_array['created_at']);
            unset($params_array['updated_at']);

            $params_array['descripcion_ser'] = strtoupper($params->descripcion_ser);
            $params_array['observaciones_ser'] = strtoupper($params->observaciones_ser);
            $params_array['utilizados_ser'] = strtoupper($params->utilizados_ser);
            
            $solicitud = Solicitud::where('id', $id)->update($params_array);

            $data = array(
                'solicitud' => $params,
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

    public function updateEval($id, Request $request) {
        $hash = $request->header('Authorization', null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken) {
            // Recoger parametros POST

            $json = $request->input('json', null);

            $params = json_decode($json);

            $params_array = json_decode($json, true);           

            $validate = \Validator::make($params_array, [
                'evaluacion_ser' => 'required'                
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }            

            unset($params_array['id']);
            unset($params_array['user_id']);            
            unset($params_array['user']);
            unset($params_array['servicio_id']);
            unset($params_array['adscripcion_id']);
            unset($params_array['empleado_id']);
            unset($params_array['equipo_id']);
            unset($params_array['descripcion']);
            unset($params_array['fecha']);
            unset($params_array['hora']);
            unset($params_array['indicador_equipo']);
            unset($params_array['estatus']);
            unset($params_array['ubicacion_ln']);
            unset($params_array['ubicacion_lt']);            
            unset($params_array['fecha_diag']);
            unset($params_array['hora_diag']);
            unset($params_array['descripcion_diag']);
            unset($params_array['fecha_diag_entrega']);
            unset($params_array['hora_diag_entrega']);             
            unset($params_array['remember_token']);
            unset($params_array['created_at']);
            unset($params_array['updated_at']);

            unset($params_array['updated_at']);

            
            $solicitud = Solicitud::where('id', $id)->update($params_array);

            $data = array(
                'solicitud' => $params,
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
    
    public function agregarDetalle($id, Request $request) {
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
                'solicitud_id' => 'required',   
                'refaccion_id' => 'required',                           
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }                                

            $detalle = new Detalle();
            $detalle->solicitud_id = $params_array['solicitud_id'];
            $detalle->refaccion_id = $params_array['refaccion_id'];
            $detalle->cantidad = $params_array['cantidad'];

            Detalle::where('solicitud_id', $detalle->solicitud_id)
                     ->Where('refaccion_id', $detalle->refaccion_id)
                     ->delete();

            $detalle->save();  
            
            $data = array(
                'detalle' => $detalle,
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

    public function eliminaDetalle($id, Request $request) {
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
                'solicitud_id' => 'required',   
                'refaccion_id' => 'required',                           
            ]);

            if ($validate->fails()) {
                return response()->json($validate->errors(), 400);
            }       

            $detalle = new Detalle();    

            $detalle->solicitud_id = $params_array['solicitud_id'];                              
            $detalle->refaccion_id = $params_array['refaccion_id'];

            Detalle::where('solicitud_id', $params_array['solicitud_id'])
                     ->Where('refaccion_id', $params_array['refaccion_id'])
                     ->delete();           
            
            $data = array(
                'detalle' => $detalle,
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

    public function cerrarSolicitud($id, Request $request) {
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
            
            DB::table('solicitudes')
            ->where('id', $id)
            ->update(['estatus' => 'C']);
            
            $data = array(                
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

    public function turnarSolicitud($id, Request $request) {
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
            DB::table('solicitudes')
            ->where('id', $id)
            ->update(['user_id' => $params_array['id']]);            
            $data = array(                
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

    public function resumenSolicitud($fecini, $fecfin){                

        $resumen = DB::table('solicitudes')
                        ->join('adscripciones', 'solicitudes.adscripcion_id', '=', 'adscripciones.id')
                        ->join('servicios', 'solicitudes.servicio_id', '=', 'servicios.id')
                        ->join('users', 'solicitudes.user_id', '=', 'users.id')                       
                        ->select('solicitudes.id as id', 'solicitudes.fecha_ser as fecha', 
                            DB::raw("CONCAT(adscripciones.codigo,'-', adscripciones.descripcion) as adscripcion"),
                            DB::raw("CONCAT(solicitudes.id,'-', solicitudes.fecha_ser, '-', servicios.descripcion) as servicio"),
                            DB::raw("CONCAT(users.name,' ', users.surname) as atendio"), 
                                 'solicitudes.cantidad_ser as cantidad')                        
                        ->whereBetween('fecha_ser', array($fecini, $fecfin))
                        ->where('solicitudes.estatus', 'C')
                        ->orderBy('id', 'ASC')                                              
                        ->get();
            
        return response()->json(array(
            'resumen' => $resumen,
            'status' => 'success'
        ), 200);         
    }

    public function resumenAdscripcion($fecini, $fecfin){                

        $resumen = DB::table('solicitudes')
                        ->join('adscripciones', 'solicitudes.adscripcion_id', '=', 'adscripciones.id')
                        ->select(
                        DB::raw("CONCAT(adscripciones.codigo,'-', adscripciones.descripcion) as adscripcion"),
                        DB::raw('SUM(solicitudes.cantidad_ser) as cantidad'))
                        ->whereBetween('fecha_ser', array($fecini, $fecfin))
                        ->where('solicitudes.estatus', 'C')
                        ->groupBy('adscripcion')
                        ->orderBy('adscripcion', 'ASC')                                              
                        ->get();
            
        return response()->json(array(
            'resumen' => $resumen,
            'status' => 'success'
        ), 200);         
    }

     public function resumenSustantiva($fecini, $fecfin){                

        $resumen = DB::table('solicitudes')
                        ->join('adscripciones', 'solicitudes.adscripcion_id', '=', 'adscripciones.id')
                        ->join('usustantivas', 'solicitudes.adscripcion_id', '=', 'usustantivas.adscripcion_id')
                        ->select('usustantivas.orden as orden',
                        DB::raw("CONCAT(adscripciones.codigo,'-', adscripciones.descripcion) as adscripcion"),
                        DB::raw('SUM(solicitudes.cantidad_ser) as cantidad'))
                        ->whereBetween('fecha_ser', array($fecini, $fecfin))
                        ->where('solicitudes.estatus', 'C')
                        ->groupBy('orden', 'adscripcion')
                        ->orderBy('orden', 'ASC')                                              
                        ->get();
            
        return response()->json(array(
            'resumen' => $resumen,
            'status' => 'success'
        ), 200);         
    }

    public function serviciosAnual(){                

        $anual = DB::table('solicitudes')                        
                        ->select(                        
                        DB::raw('YEAR(solicitudes.fecha_ser) as anio'),
                        DB::raw('MONTH(solicitudes.fecha_ser) as mes'),
                        DB::raw('SUM(solicitudes.cantidad_ser) as cantidad')) 
                        ->where(function($query) {
                            $query->whereYear('solicitudes.fecha_ser', '=', date('Y'))
                                  ->whereYear('solicitudes.fecha_ser', '=', date('Y')-1, 'or');
                        })                                                            
                        ->where('solicitudes.estatus', 'C')
                        ->groupBy('anio','mes')
                        ->orderBy('anio', 'ASC')   
                        ->orderBy('mes', 'ASC')                                            
                        ->get();

        return response()->json(array(
            'anual' => $anual,
            'status' => 'success'
        ), 200);         
    }

}