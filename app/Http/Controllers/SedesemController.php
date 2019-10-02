<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Nacionalidad;
use App\Documento;
use App\Efederativa;
use DB;

class SedesemController extends Controller
{
       
    public function getNacionalidades($criterio){                

         $nacionalidades = DB::table('nacionalidades')                        
                        ->select('id', 'nacionalidad')
                        ->orWhere('nacionalidad', 'LIKE', '%'.$criterio.'%')
                        ->orderBy('id', 'ASC')                                        
                        ->get();

        return response()->json(array(
            'nacionalidades' => $nacionalidades,
            'status' => 'success'
        ), 200);         
    }

    public function getDocumentos($dummy){                

         $documentos = DB::table('documentos')                        
                        ->select('id', 'documento')                        
                        ->orderBy('id', 'ASC')                                        
                        ->get();

        return response()->json(array(
            'documentos' => $documentos,
            'status' => 'success'
        ), 200);         
    }

    public function getEfederativas($criterio){                

         $efederativas = DB::table('efederativas')                        
                        ->select('id', 'efederativa')                        
                        ->orderBy('id', 'ASC')                                        
                        ->get();

        return response()->json(array(
            'efederativas' => $efederativas,
            'status' => 'success'
        ), 200);         
    }


}