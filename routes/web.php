<?php

header('Access-Control-Allow-Headers: X-CSRF-Token, Access-Control-Request-Headers, Access-Control-Request-Method, Accept, X-Requested-With, Content-Type, X-Auth-Token, Origin, Authorization');
header('Access-Control-Allow-Methods: PATCH, GET, POST, PUT, DELETE, OPTIONS');


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::get('/api/equipos/listado/{criterio}', 'EquipoController@listado');
Route::get('/api/equipos/pdf', 'EquipoController@equipopdf');
Route::get('/api/adscripcions/listado/{criterio}', 'AdscripcionController@listado');

Route::get('/api/usuarios', 'UserController@index');
Route::get('/api/usuarios/listado/{criterio}', 'UserController@listado');
Route::put('/api/usuarios/{id}', 'UserController@update');

Route::get('/api/usuario/{id}', 'UserController@show');
Route::get('user/{id}', 'UserController@showProfile');
Route::put('/api/resetpassword/{id}', 'UserController@resetPassword');

Route::get('/api/empleados/listado/{criterio}', 'EmpleadoController@listado');
Route::get('/api/categorias/listado/{criterio}', 'CategoriaController@listado');
Route::get('/api/refaccions/listado/{criterio}', 'RefaccionController@listado');

Route::resource('/api/cars', 'CarController');
Route::resource('/api/equipos', 'EquipoController');
Route::resource('/api/adscripcions', 'AdscripcionController');
Route::resource('/api/empleados', 'EmpleadoController');
Route::resource('/api/categorias', 'CategoriaController');
Route::resource('/api/refaccions', 'RefaccionController');
Route::resource('/api/solicituds', 'SolicitudController');

Route::get('/api/servicios/{categoria_id}', 'ServicioController@index');
Route::post('/api/servicios/{categoria_id}', 'ServicioController@store');
Route::get('/api/servicios/listado/{categoria}/{criterio}', 'ServicioController@listado');
Route::get('/api/servicios_ser/{id}', 'ServicioController@Show');
Route::put('/api/servicios/{id}', 'ServicioController@update');

Route::get('/api/adscripcions/filtrado/{criterio}', 'AdscripcionController@filtrado');
Route::get('/api/servicios/filtrado/{criterio}', 'ServicioController@filtrado');
Route::get('/api/empleados/filtrado/{criterio}', 'EmpleadoController@filtrado');
Route::get('/api/equipos/filtrado/{criterio}', 'EquipoController@filtrado');
Route::get('/api/refacciones/desolicitud/{id}/{criterio}', 'RefaccionController@deSolicitud');

Route::get('/api/servicios/filtradouno/{servicio_id}', 'ServicioController@filtradoUno');
Route::get('/api/adscripcions/filtradouno/{adscripcion_id}', 'AdscripcionController@filtradoUno');
Route::get('/api/empleados/filtradouno/{empleado_id}', 'EmpleadoController@filtradoUno');
Route::get('/api/equipos/filtradouno/{equipo_id}', 'EquipoController@filtradoUno');

Route::put('/api/solicituds_diag/{id}', 'SolicitudController@updateDiag');
Route::put('/api/solicituds_ser/{id}', 'SolicitudController@updateSer');
Route::put('/api/solicituds_eval/{id}', 'SolicitudController@updateEval');

Route::put('/api/agregardetalle/{id}', 'SolicitudController@agregarDetalle');
Route::get('/api/detallesolicitud/{id}', 'SolicitudController@detalleSolicitud');
Route::put('/api/eliminadetalle/{id}', 'SolicitudController@eliminaDetalle');

Route::get('/api/solicituds/deusuario/{id}', 'SolicitudController@deUsuario');
Route::put('/api/solicituds/cerrarsolicitud/{id}', 'SolicitudController@cerrarSolicitud');

Route::get('/api/tecnicos/{criterio}/{id}', 'UserController@tecnicos'); // Regresa técnicos con id <> del param id

Route::put('/api/turnar/{id}', 'SolicitudController@turnarSolicitud');
Route::get('/api/allsolicitudes', 'SolicitudController@allSolicitudes'); 
Route::get('/api/solicitudhistorico/{id}', 'SolicitudController@solicitudHistorico'); 
Route::get('/api/rolusuario/{id}', 'UserController@rolUsuario'); 

Route::get('/api/resumensolicitud/{fecini}/{fecfin}', 'SolicitudController@resumenSolicitud'); 
Route::get('/api/resumensustantiva/{fecini}/{fecfin}', 'SolicitudController@resumenSustantiva'); 

Route::get('/api/resumenadscripcion/{fecini}/{fecfin}', 'SolicitudController@resumenAdscripcion'); 

Route::get('/api/solicitudesdeequipo/{id}', 'SolicitudController@SolicitudesDeEquipo'); 

Route::get('/api/serviciosanual', 'SolicitudController@serviciosAnual'); 

Route::get('/api/uvehiculos/{user_id}', 'KilometrajeController@uVehiculos'); 

Route::post('/api/addkm/', 'KilometrajeController@addKm'); 

Route::get('/api/rutaskm/{user_id}', 'KilometrajeController@rutasKm'); 

Route::get('/api/registrokm/{vehiculo_id}', 'KilometrajeController@registroKm'); 

Route::post('/api/chkcurp/{curp}', 'EntregaController@chkCurp'); 

Route::post('/api/insdatosa', 'EntregaController@insDatosA'); 

Route::post('/api/insdatosb', 'EntregaController@insDatosB'); 

Route::get('/api/getnacionalidades/{criterio}', 'SedesemController@getNacionalidades'); 

Route::get('/api/getdocumentos/{dummy}', 'SedesemController@getDocumentos'); 

Route::get('/api/getefederativas/{dummy}', 'SedesemController@getEfederativas'); 


