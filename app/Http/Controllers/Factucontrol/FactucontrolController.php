<?php

namespace App\Http\Controllers\Factucontrol;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\Bitacora\Bitacora;
use App\Models\Factucontrol\Casos\Caso;
use App\Models\Factucontrol\Casos\HistorialCasos;
use App\Models\Factucontrol\Proveedores\Proveedores;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
//use Illuminate\Support\Facades\DB;
use DB;
use Illuminate\Cache\RateLimiting\Limit;

class FactucontrolController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * funcion encargada de obtener todos las sucursales disponibles en bd para retornarlas a la vista.
     *
     * @return "todas las sucursales por departamento"    => $sucursales
     */
    public function getProveedor(Request $request)
    {
        $Proveedores = Proveedores::select('*')->distinct()->get();
        return response()->json(["proveedores" => $Proveedores, "status" => "ok"]);
    }

    public function insertProveedor(Request $request)
    {
        $nit = DB::connection('sqlsrv')->table('FACTUCONTROL.proveedor')->where('nit', $request["nit"])->count();

        if ($nit == 0) {
            $insert = Proveedores::create([
                'nit'                   => $request["nit"],
                'razon_social'          => $request["razon_social"],
                'descripcion'           => $request["descripcion"],
                'dias_pago'             => $request["dias_pago"],
                'descuento'             => $request["descuento"],
                'active'                => 1,
                'pronto_pago'           => $request["pronto_pago"],
                'codigoVerificacion'    => $request["codigo"],
            ]);

            Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'CREA PROVEDOR ' . strtoupper($request["razon_social"]) . ' NIT ' .  strtoupper($request["nit"]), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);

            $proveedores = Proveedores::all();
            return response()->json([
                "proveedores" =>  $proveedores
            ], 200);
        } else {
            return response()->json([
                "proveedores" =>  "existe"
            ], 200);
        }
    }

    public function editProveedor(Request $request)
    {
        $data = $request->all();
        $update = Proveedores::where("id_proveedor", $data["id_proveedor"])->update([
            /* 'nit'           => $request["nit"], */
            'razon_social'       => $request["razon_social"],
            'descripcion'        => $request["descripcion"],
            'dias_pago'          => $request["dias_pago"],
            'descuento'          => $request["descuento"],
            'active'             => $request["proveedor_activo"],
            'pronto_pago'        => $request["pronto_pago"],
            'codigoVerificacion' => $request["codigoVerificacion"],
        ]);

        Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'EDITO EL PROVEEDOR ' . strtoupper($request["razon_social"]) . ' NIT ' .  strtoupper($request["nit"]), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);

        $update = Proveedores::all();

        return response()->json([
            "update" =>  $update
        ], 200);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx'
        ]);

        $path = $request->file('import_file');
        $data = Excel::toCollection(new UsersImport, $path);

        $valores_totales = [];
        foreach ($data[0] as $row) {
            $nit = Proveedores::where('nit', strval($row['nit']))->count();
            array_push($valores_totales, $nit);
            if ($row['nit'] == "") {
                $proveedores = Proveedores::all();
                return response()->json([
                    "proveedores" =>  "vacio"
                ], 200);
            }
            if ($row['active'] == "") {
                $proveedores = Proveedores::all();
                return response()->json([
                    "proveedores" =>  "vacio"
                ], 200);
            }
            if ($row['codigoverificacion'] == "") {
                $proveedores = Proveedores::all();
                return response()->json([
                    "proveedores" =>  "vacio"
                ], 200);
            }
            if ($row['descripcion'] == "") {
                $proveedores = Proveedores::all();
                return response()->json([
                    "proveedores" =>  "vacio"
                ], 200);
            }
            if ($row['descuento'] == "") {
                $proveedores = Proveedores::all();
                return response()->json([
                    "proveedores" =>  "vacio"
                ], 200);
            }
            if ($row['dias_pago'] == "") {
                $proveedores = Proveedores::all();
                return response()->json([
                    "proveedores" =>  "vacio"
                ], 200);
            }
            if ($row['pronto_pago'] == "") {
                $proveedores = Proveedores::all();
                return response()->json([
                    "proveedores" =>  "vacio"
                ], 200);
            }
            if ($row['razon_social'] == "") {
                $proveedores = Proveedores::all();
                return response()->json([
                    "proveedores" =>  "vacio"
                ], 200);
            }
        }

        if ($nit == 0) {
            $data = Excel::import(new UsersImport, $path);
            $proveedores = Proveedores::all();
            return response()->json([
                'message'   => 'uploaded successfully',
                "proveedores" =>  $proveedores
            ], 200);
        } else {
            $proveedores = Proveedores::all();
            return response()->json([
                "proveedores" =>  "existe"
            ], 200);
        }

        return $valores_totales;
    }

    public function getTemas(Request $request)
    {
        $Temas = DB::connection('sqlsrv')->table('FACTUCONTROL.temas')
            ->orderBy('descripcion_temar', 'ASC')
            ->get();
        return response()->json(["temas" => $Temas, "status" => "ok"]);
    }

    public function getCategorias(Request $request)
    {
        $categorias = DB::connection('sqlsrv')->table('FACTUCONTROL.categoria')
            ->where('estado', 1)
            ->orderBy('Descripcion', 'ASC')
            ->get();
        return response()->json(["categorias" => $categorias, "status" => "ok"]);
    }

    public function temasRol(Request $request)
    {
        $temasRol_users = DB::connection('sqlsrv')->table('FACTUCONTROL.users as users')->where('users.active', 1)->where('temas_user.id_tema', $request["id_tema"])
            ->selectRaw('cargo.id_cargo,
			    users.id_user,
			    cargo.nombre_cargo,
			    users.name,
			    temas_user.id_tema_user,
			    temas.descripcion_temar,
			    temas.id_tema')
            ->join('FACTUCONTROL.cargo AS cargo', 'users.id_cargo', '=', 'cargo.id_cargo')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'users.id_user', '=', 'temas_user.id_user')
            ->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->orderBy('cargo.nombre_cargo', 'ASC')
            ->get();
        return response()->json(["temasRol_users" => $temasRol_users, "status" => "ok"]);
    }

    public function proveedores(Request $request)
    {
        $proveedores = DB::connection('sqlsrv')->table('FACTUCONTROL.proveedor')
            ->orderBy('razon_social', 'ASC')
            ->get();
        return response()->json(["proveedores" => $proveedores, "status" => "ok"]);
    }

    public function sucursales(Request $request)
    {
        /* $sucursales = DB::connection('sqlsrv')->table('FACTUCONTROL.sucursal')->get();
        return response()->json(["sucursales" => $sucursales, "status" => "ok"]); */
        $sucursales = DB::connection('sqlsrv')->table('HOJADEVIDASEDES.SED_SEDE AS SED')
            ->selectRaw('SUC.SUC_DEPARTAMENTO, COUNT(SED.SED_NOMBRE_SEDE) as CantidadSedes, SUC.SUC_CODIGO_DEPARTAMENTO')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS SUC', 'SUC.SUC_CODIGO_DANE', '=', 'SED.SUC_CODIGO_DANE')
            ->groupBy('SUC.SUC_DEPARTAMENTO', 'SUC.SUC_CODIGO_DEPARTAMENTO')
            ->orderBy('CantidadSedes', 'DESC')
            ->get();
        return response()->json(["sucursales" => $sucursales, "status" => "ok"]);
    }

    public function insertRadicado(Request $request)
    {
        if ($request["tema"] == 3) {
            $fechaActual = date('Y-m-d H:i:s');
            $fecha = date('Y-m-d');

            $misArchivosASQL = [];
            if ($request->hasFile("files")) {
                $files = $request->file("files");
                if (sizeOf($files) == 1) {
                    if ($files[0]->guessExtension() == "xlsx" || $files[0]->guessExtension() == "xls") {
                        $rt = public_path("uploads/factucontrol/" . $files[0]->getClientOriginalName());

                        $misArchivosASQL = $files[0]->getClientOriginalName();
                        Storage::disk('ftp')->put($files[0]->getClientOriginalName(), $files[0]);
                        //copy($files[0], $rt);
                    } else {
                        return response()->json([
                            "radicado" =>  "formatoErrado"
                        ], 200);
                    }
                } else {
                    return response()->json([
                        "radicado" =>  "masdeuno"
                    ], 200);
                }
            }

            if ($request["fechaRadicado"] <= $fecha) {
                $files[0] = $request->file("files");
                $radicado = Caso::create([
                    'id_tema_user'          => $request["reportar"],
                    'descripcion_tema'      => $request["notas"],
                    'fecha_creacion'        => $fechaActual,
                    'id_estado'             => 1,
                    'id_categoria'          => $request["categoria"],
                    'fecha_asignacion'      => $fechaActual,
                    'id_proveedor'          => $request["proveedor"],
                    'id_user_create'        => Auth::user()->nro_doc,
                    'id_sucursal'           => $request["sucursal"],
                    'id_tipo_factura'       => $request["formatoFactura"],
                    'fechaRadicado'         => $request["fechaRadicado"],
                    'ordenCompra'           => $request["ordenCompra"],
                    'valor'                 => $request["valor"],
                    'concepto'              => $request["concepto"],
                    'cantidadFactutras'     => $request["cantidadFactutras"],
                    'Nfactura'              => $request["nFactura"],
                    'archivosPDF'           => json_encode($misArchivosASQL),
                    'idTema'                => $request["tema"],
                    'documento'             => $request["reportar"],
                    'tipDoc'                => $request["tipDoc"],
                    'nuevo'                 => 1,
                ]);

                /* REGISTRO EN BITACORA */
                $data = Caso::latest('id_caso')->first();
                if (!empty($misArchivosASQL)) {
                    $insertAdjunto = DB::connection('sqlsrv')->table("FACTUCONTROL.attachment")->insert([
                        'file_name'    => $misArchivosASQL,
                        'encrypt_name' => $misArchivosASQL,
                        'id_caso'      => $data->id_caso,
                        'date_upload'  => $fechaActual,
                        'title'        => $misArchivosASQL,
                    ]);
                }
                Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'RADICAR - FACTURA ' . strtoupper($request["nFactura"]) . ' CASO ' . strtoupper($data->id_caso), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);

                return response()->json([
                    "radicado" =>  $data->id_caso
                ], 200);
            } else {
                return response()->json([
                    "radicado" =>  "existe"
                ], 200);
            }
        } else {
            $fechaActual = date('Y-m-d H:i:s');
            $fecha = date('Y-m-d');

            $misArchivosASQL = [];
            if ($request->hasFile("files")) {
                $files = $request->file("files");
                foreach ($files as $uno) {
                    if ($uno->guessExtension() == "pdf") {
                        $rt = public_path("uploads/factucontrol/" . $uno->getClientOriginalName());
                        if (sizeOf($files) > 1) {
                            array_push($misArchivosASQL, $uno->getClientOriginalName());
                        } else {
                            $misArchivosASQL = $uno->getClientOriginalName();
                        }
                        Storage::disk('ftp')->put($uno->getClientOriginalName(), $uno);
                        /* copy($uno, $rt); */
                    } else {
                        return response()->json([
                            "radicado" =>  "formatoErrado"
                        ], 200);
                    }
                }
            }

            if ($request["fechaRadicado"] <= $fecha) {
                $files = $request->file("files");
                $radicado = Caso::create([
                    'id_tema_user'          => $request["reportar"],
                    'descripcion_tema'      => $request["notas"],
                    'fecha_creacion'        => $fechaActual,
                    'id_estado'             => 1,
                    'id_categoria'          => $request["categoria"],
                    'fecha_asignacion'      => $fechaActual,
                    'id_proveedor'          => $request["proveedor"],
                    'id_user_create'        => Auth::user()->nro_doc,
                    'id_sucursal'           => $request["sucursal"],
                    'id_tipo_factura'       => $request["formatoFactura"],
                    'fechaRadicado'         => $request["fechaRadicado"],
                    'ordenCompra'           => $request["ordenCompra"],
                    'valor'                 => $request["valor"],
                    'concepto'              => $request["concepto"],
                    'Nfactura'              => $request["nFactura"],
                    'archivosPDF'           => json_encode($misArchivosASQL),
                    'idTema'                => $request["tema"],
                    'documento'             => $request["reportar"],
                    'tipDoc'                => $request["tipDoc"],
                    'nuevo'                 => 1,
                ]);

                $data = Caso::latest('id_caso')->first();
                if (!empty($files)) {
                    foreach ($files as $uno) {
                        $insertAdjunto = DB::connection('sqlsrv')->table("FACTUCONTROL.attachment")->insert([
                            'file_name'    => $uno->getClientOriginalName(),
                            'encrypt_name' => $uno->getClientOriginalName(),
                            'id_caso'      => $data->id_caso,
                            'date_upload'  => $fechaActual,
                            'title'        => $uno->getClientOriginalName(),
                        ]);
                    }
                }
                Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'RADICAR - FACTURA ' . strtoupper($request["nFactura"]) . ' CASO ' . strtoupper($data->id_caso), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);

                return response()->json([
                    "radicado" =>  $data->id_caso
                ], 200);
            } else {
                return response()->json([
                    "radicado" =>  "existe"
                ], 200);
            }
        }
    }

    public function rolesUser($files)
    {
        $Archivos = [];
        if ($files->hasFile("files")) {
            $files_N = $files->file("files");
            foreach ($files_N as $key => $value) {
                $nombre = $value->getClientOriginalName();
                array_push($Archivos, $nombre);
            }
        }
        return $Archivos;
    }

    public function usuarioda(Request $request)
    {
        require("usuario.php");
        header("Content-Type: text/html; charset=utf-8");
        ////// Este usuario es generico y no cambia la contraseña
        $user = 'Desarrollo.VS';
        $pass = 'D3s4rr0ll02021.*$';
        /////////////////////////////////////////////////////////
        $usuario = usuarioda($user, $pass, $request["nombre"]);

        if (gettype($usuario) == 'array') //LO ENCUENTRO EN EL DIRECTORIO ACTIVO
        {
            $detalleUser = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');

            $datos = [];
            array_shift($detalleUser);
            if (count((array)$detalleUser) == 1) {
                for ($i = 0; $i < count((array)$detalleUser); $i++) {
                    $x = explode(',', $detalleUser[0]['distinguishedname'][0]);
                    if (!in_array("OU=Depuracion de usuarios", $x)) {
                        foreach ($detalleUser[0]['memberof'] as $key => $value) {
                            $p = explode(',', $detalleUser[0]['memberof'][$key]);
                            if (in_array("CN=Ap_Factucontrol", $p) || in_array("CN=AGSuperAdmin", $p)) {
                                /* consulta para obtener lor roles de los usuarios*/
                                /* $rol = DB::connection('sqlsrv')->table('dbo.users')->where('nro_doc', $detalleUser[0]['wwwhomepage'][0])
                                    ->pluck("rol");
                                $e = $rol[0]; */
                                $cadaUno = array(
                                    'documento' => $detalleUser[0]['wwwhomepage'][0],
                                    'nombres'   => $detalleUser[0]['displayname'][0]/* ,
                                    'perfiles'  => json_decode($e) */
                                );
                                array_push($datos, $cadaUno);
                                return $datos;
                            }
                        }
                    }
                }
            }
            if (count((array)$detalleUser) > 1) {
                for ($i = 0; $i < count((array)$detalleUser); $i++) {
                    $x = explode(',', $detalleUser[$i]['distinguishedname'][0]);
                    if (!in_array("OU=Depuracion de usuarios", $x)) {
                        if (isset($detalleUser[$i]["memberof"])) {
                            foreach ($detalleUser[$i]['memberof'] as $key => $value) {
                                $p = explode(',', $detalleUser[$i]['memberof'][$key]);
                                if (in_array("CN=Ap_Factucontrol", $p) || in_array("CN=AGSuperAdmin", $p)) {
                                    $cadaUno = array(
                                        'documento' => $detalleUser[$i]['wwwhomepage'][0],
                                        'nombres'   => $detalleUser[$i]['displayname'][0]
                                    );
                                    array_push($datos, $cadaUno);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $datos;
    }

    public function permisosFac(Request $request)
    {
        $permisos  = json_encode($request["permisosFinales"]);
        $documento = $request["documento"];
        $insert    = $request["insert"];

        if (COUNT($request["permisosFinales"]) == 0) {
            $rol = DB::connection('sqlsrv')->table('FACTUCONTROL.caso as caso')
                ->where('id_tema_user', $documento)
                ->where(function ($query) {
                    $query->where('id_estado', '=', 1)
                        ->orWhere('id_estado', '=', 2);
                })
                ->get();

            if (COUNT($rol) == 0) {
                $updatePermisos = DB::connection('sqlsrv')->table('dbo.users')->where('nro_doc', $documento)->update([
                    'rol'           => $permisos,
                    'is_directory'  => 2,
                ]);
                Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'ASIGNA PERMISOS A: ' . strtoupper($documento) . ' NOMBRES ' . strtoupper($request["info"]['nombres2']) . ' ' . strtoupper($request["info"]['last_name']), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);
                return response()->json(["permisos" => 1, "status" => "ok"]);
            } else {
                return response()->json(["casos" => true, "infoCasos" => $rol, "status" => "ok"]);
            }
        } else {
            if ($insert == false) {
                $valInsert = DB::connection('sqlsrv')->table('dbo.users')->where('nro_doc', $documento)
                    ->get();

                if (COUNT($valInsert) == 0) {
                    $updatePermisos = User::create([
                        'nro_doc'       => $documento,
                        'name'          => $request["info"]['nombres2'],
                        'last_name'     => $request["info"]['last_name'],
                        'email'         => $request["info"]['email'],
                        'correo'        => $request["info"]['correo'],
                        'cargo'         => $request["info"]['cargo'],
                        'empresa'       => $request["info"]['empresa'],
                        'password'      => bcrypt('contraseñapordefecto'),
                        'is_directory'  => 2,
                        'estado'        => 1,
                        'rol'           => $permisos,
                    ]);
                    Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'ASIGNA PERMISOS A: ' . strtoupper($documento) . ' NOMBRES ' . strtoupper($request["info"]['nombres2']) . ' ' . strtoupper($request["info"]['last_name']), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);
                    return response()->json(["permisos" => 1, "status" => "ok"]);
                } else {
                    $updatePermisos = DB::connection('sqlsrv')->table('dbo.users')->where('nro_doc', $documento)->update([
                        'rol'           => $permisos,
                        'is_directory'  => 2,
                    ]);
                    Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'ASIGNA PERMISOS A: ' . strtoupper($documento) . ' NOMBRES ' . strtoupper($request["info"]['nombres2']) . ' ' . strtoupper($request["info"]['last_name']), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);
                    return response()->json(["permisos" => 1, "status" => "ok"]);
                }

                return $valInsert;
            } else {
                $updatePermisos = DB::connection('sqlsrv')->table('dbo.users')->where('nro_doc', $documento)->update([
                    'rol'           => $permisos,
                    'is_directory'  => 2,
                ]);
                Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'ASIGNA PERMISOS A: ' . strtoupper($documento) . ' NOMBRES ' . strtoupper($request["info"]['nombres2']) . ' ' . strtoupper($request["info"]['last_name']), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);
                return response()->json(["permisos" => 1, "status" => "ok"]);
            }
        }
    }

    public function usuariodaPermisos(Request $request)
    {
        require("usuario.php");
        header("Content-Type: text/html; charset=utf-8");
        ////// Este usuario es generico y no cambia la contraseña
        $user = 'Desarrollo.VS';
        $pass = 'D3s4rr0ll02021.*$';
        /////////////////////////////////////////////////////////
        $usuario = usuarioda($user, $pass, $request["nombre"]);

        if (gettype($usuario) == 'array') //LO ENCUENTRO EN EL DIRECTORIO ACTIVO
        {
            $detalleUser = mb_convert_encoding($usuario, 'UTF-8', 'UTF-8');

            $datos = [];
            /* $r = array_shift($detalleUser); */
            $x = explode(',', $detalleUser[0]['distinguishedname'][0]);
            if (!in_array("OU=Depuracion de usuarios", $x)) {
                /* consulta para obtener lor roles de los usuarios*/

                $rol = DB::connection('sqlsrv')->table('dbo.users')->where('nro_doc', $detalleUser[0]['wwwhomepage'][0])
                    ->pluck("rol");

                if (COUNT($rol) > 0) {
                    $e = $rol[0];
                    $cadaUno = array(
                        'documento'     => $detalleUser[0]['wwwhomepage'][0],
                        'nombres'       => $detalleUser[0]['displayname'][0],
                        'nombres2'      => $detalleUser[0]['givenname'][0],
                        'last_name'     => $detalleUser[0]['sn'][0],
                        'email'         => $detalleUser[0]['samaccountname'][0],
                        'correo'        => $detalleUser[0]['mail'][0],
                        'cargo'         => $detalleUser[0]['description'][0],
                        'empresa'       => $detalleUser[0]['physicaldeliveryofficename'][0],
                        'is_directory'   => 2,
                        'estado'        => 1,
                        'perfiles'      => json_decode($e),
                    );
                    array_push($datos, $cadaUno);
                } else {
                    $cadaUno = array(
                        'documento'     => $detalleUser[0]['wwwhomepage'][0],
                        'nombres'       => $detalleUser[0]['displayname'][0],
                        'nombres2'      => $detalleUser[0]['givenname'][0],
                        'last_name'     => $detalleUser[0]['sn'][0],
                        'email'         => $detalleUser[0]['samaccountname'][0],
                        'correo'        => $detalleUser[0]['mail'][0],
                        'cargo'         => $detalleUser[0]['description'][0],
                        'empresa'       => $detalleUser[0]['physicaldeliveryofficename'][0],
                        'is_directory'   => 2,
                        'estado'        => 1,
                        'perfiles'      => false
                    );
                    array_push($datos, $cadaUno);
                }
            }
        }
        return $datos;
    }

    public function getCasos(Request $request)
    {
        $documento = Auth::user()->nro_doc;

        $casosRegistradoOld1 = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')->where('caso.id_estado', 1)->where('users.documento', $documento)->where('caso.nuevo', null)
            ->selectRaw('DISTINCT caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,

            users.id_user,
            users.name,
            users.documento,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.nombre AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            DATEDIFF(DAY, GETDATE(), DATEADD(DAY, p.dias_pago, caso.fecha_creacion)) AS dias_restantes
            ')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_user')
            //->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->orderBy('dias_restantes', 'ASC')
        ->get();

        $casosRegistradoOld2 = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')->where('caso.id_estado', 1)->where('users.documento', $documento)->where('caso.nuevo', null)
            ->selectRaw('DISTINCT caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,

            users.id_user,
            users.name,
            users.documento,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.nombre AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            DATEDIFF(DAY, GETDATE(), DATEADD(DAY, p.dias_pago, caso.fecha_creacion)) AS dias_restantes
            ')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            //->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->orderBy('dias_restantes', 'ASC')
        ->get();


        $casosRegistradoNew = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')->where('caso.id_estado', 1)->where('caso.id_tema_user', $documento)->where('caso.nuevo', 1)
            ->selectRaw('DISTINCT caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,

            users.name,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.SUC_DEPARTAMENTO AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            DATEDIFF(DAY, GETDATE(), DATEADD(DAY, p.dias_pago, caso.fecha_creacion)) AS dias_restantes
            ')
            /* ->join('FACTUCONTROL.temas AS temas', 'caso.idTema', '=', 'temas.id_tema') */
            ->join('users', 'caso.documento', '=', 'users.nro_doc')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            //->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS sucursal', 'caso.id_sucursal', '=', 'sucursal.SUC_CODIGO_DEPARTAMENTO', 'LEFT')
            ->orderBy('dias_restantes', 'ASC')
        ->get();

        $casosRegistradoOld = [];

        foreach ($casosRegistradoOld1 as $value) {
            array_push($casosRegistradoOld, $value);
        }

        foreach ($casosRegistradoOld2 as $value) {
            array_push($casosRegistradoOld, $value);
        }


        foreach ($casosRegistradoOld as $valueOld) {
            $valueOld->danger = ceil($valueOld->diasProveedor - ($valueOld->diasProveedor * 0.8));
            $valueOld->warning = ceil($valueOld->diasProveedor - ($valueOld->diasProveedor * 0.6));

            if ($valueOld->dias_restantes < $valueOld->danger) {
                $valueOld->tag = 'danger';
            } else if ($valueOld->dias_restantes > $valueOld->danger && $valueOld->dias_restantes <= $valueOld->warning) {
                $valueOld->tag = 'warning';
            } else {
                $valueOld->tag = 'succeess';
            }
        }

        foreach ($casosRegistradoNew as $value) {
            $value->danger = ceil($value->diasProveedor - ($value->diasProveedor * 0.8));
            $value->warning = ceil($value->diasProveedor - ($value->diasProveedor * 0.6));

            if ($value->dias_restantes < $value->danger) {
                $value->tag = 'danger';
            } else if ($value->dias_restantes > $value->danger && $value->dias_restantes <= $value->warning) {
                $value->tag = 'warning';
            } else {
                $value->tag = 'succeess';
            }
        }

        return response()->json(["casosRegistrado" => $casosRegistradoOld, "casosRegistradoNew" => $casosRegistradoNew, "status" => "ok"]);
    }

    public function conceptos(Request $request)
    {
        $conceptos = DB::connection('sqlsrv')->table('FACTUCONTROL.conceptos')
            ->orderBy('nameConceptos', 'ASC')
            ->get();
        return response()->json(["conceptos" => $conceptos, "status" => "ok"]);
    }

    public function tipodoc(Request $request)
    {
        $tipodoc = DB::connection('sqlsrv')->table('FACTUCONTROL.tipoDoc')
            ->orderBy('nameTipoDoc', 'ASC')
            ->get();
        return response()->json(["tipodoc" => $tipodoc, "status" => "ok"]);
    }

    public function editCasoEstado(Request $request)
    {
        $data = $request->all();
        $observaciones = $data["observaciones"];
        $documento = Auth::user()->nro_doc;
        $fechaActual = date('Y-m-d H:i:s');

        foreach ($data["casos"] as $value) {
            $insertupdate = DB::connection('sqlsrv')->table("FACTUCONTROL.historial_caso")->insert([
                'id_caso'          => $value["id_caso"],
                'fecha_movimiento' => $fechaActual,
                'observaciones'    => $observaciones,
                'id_user'          => $documento,
                'fecha_asignacion' => $fechaActual,
                'fecha_pasa_caso'  => $fechaActual,
                'primerMovimiento' => 1,
                'seguimiento'      => $documento,
            ]); //
            $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $value["id_caso"])->update([
                'id_estado'     => 2,
            ]);
        }

        return response()->json([
            "insertupdate" =>  $insertupdate
        ], 200);
    }

    public function getCasosProceso(Request $request)
    {
        $documento = Auth::user()->nro_doc;

        $casosRegistradoOldPro1 = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')->where('caso.id_estado', 2)->where('users.documento', $documento)->where('caso.nuevo',  null)
            ->selectRaw('DISTINCT caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,

            users.id_user,
            users.name,
            users.documento,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.nombre AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            DATEDIFF(DAY, GETDATE(), DATEADD(DAY, p.dias_pago, caso.fecha_creacion)) AS dias_restantes
            ')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_user')
            //->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->leftJoin('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->orderBy('dias_restantes', 'ASC')
        ->get();


        $casosRegistradoOldPro2 = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')->where('caso.id_estado', 2)->where('users.documento', $documento)->where('caso.nuevo', null)
            ->selectRaw('DISTINCT caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,

            users.id_user,
            users.name,
            users.documento,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.nombre AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            DATEDIFF(DAY, GETDATE(), DATEADD(DAY, p.dias_pago, caso.fecha_creacion)) AS dias_restantes
            ')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            //->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->leftJoin('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->orderBy('dias_restantes', 'ASC')
        ->get();

        $casosRegistradoOldPro = [];

        foreach ($casosRegistradoOldPro1 as $value) {
            array_push($casosRegistradoOldPro, $value);
        }

        foreach ($casosRegistradoOldPro2 as $value) {
            array_push($casosRegistradoOldPro, $value);
        }

        $casosRegistradoOldProCount = $casosRegistradoOldPro1->count() + $casosRegistradoOldPro2->count() ;

        //return gettype($casosRegistradoOldPro);


        $casosRegistradoNewPro = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')->where('caso.id_estado', 2)->where('caso.id_tema_user', $documento)->where('caso.nuevo', 1)
            ->selectRaw('DISTINCT caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,

            users.name,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.SUC_DEPARTAMENTO AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            DATEDIFF(DAY, GETDATE(), DATEADD(DAY, p.dias_pago, caso.fecha_creacion)) AS dias_restantes
            ')
            /* ->join('FACTUCONTROL.temas AS temas', 'caso.idTema', '=', 'temas.id_tema') */
            ->join('users', 'caso.documento', '=', 'users.nro_doc')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            //->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS sucursal', 'caso.id_sucursal', '=', 'sucursal.SUC_CODIGO_DEPARTAMENTO', 'LEFT')
            ->orderBy('dias_restantes', 'ASC')
            ->get();
        $casosRegistradoNewProCount = $casosRegistradoNewPro->count();

        foreach ($casosRegistradoOldPro as $valueOld) {
            $valueOld->danger = ceil($valueOld->diasProveedor - ($valueOld->diasProveedor * 0.8));
            $valueOld->warning = ceil($valueOld->diasProveedor - ($valueOld->diasProveedor * 0.6));

            if ($valueOld->dias_restantes < $valueOld->danger) {
                $valueOld->tag = 'danger';
            } else if ($valueOld->dias_restantes > $valueOld->danger && $valueOld->dias_restantes <= $valueOld->warning) {
                $valueOld->tag = 'warning';
            } else {
                $valueOld->tag = 'succeess';
            }
        }

        foreach ($casosRegistradoNewPro as $value) {
            $value->danger = ceil($value->diasProveedor - ($value->diasProveedor * 0.8));
            $value->warning = ceil($value->diasProveedor - ($value->diasProveedor * 0.6));

            if ($value->dias_restantes < $value->danger) {
                $value->tag = 'danger';
            } else if ($value->dias_restantes > $value->danger && $value->dias_restantes <= $value->warning) {
                $value->tag = 'warning';
            } else {
                $value->tag = 'succeess';
            }
        }

        //return gettype($casosHistorialOld);

        return response()->json(["casosRegistradoOldPro" => $casosRegistradoOldPro, "casosRegistradoNewPro" => $casosRegistradoNewPro, "casosRegistradoOldProCount" => $casosRegistradoOldProCount, "casosRegistradoNewProCount" => $casosRegistradoNewProCount, "status" => "ok"]);
    }

    public function getEstado(Request $request)
    {
        $Estado = DB::connection('sqlsrv')->table('FACTUCONTROL.estado')->get();
        return response()->json(["estados" => $Estado, "status" => "ok"]);
    }

    public function getConcDevo(Request $request)
    {
        $ConcDevo = DB::connection('sqlsrv')->table('FACTUCONTROL.conceptosDevo')->get();
        return response()->json(["ConcDevo" => $ConcDevo, "status" => "ok"]);
    }

    public function getConcPago(Request $request)
    {
        $ConcPago = DB::connection('sqlsrv')->table('FACTUCONTROL.conceptosPago')->get();
        return response()->json(["ConcPago" => $ConcPago, "status" => "ok"]);
    }

    public function getOtros(Request $request)
    {
        $Otros = DB::connection('sqlsrv')->table('FACTUCONTROL.anulado')->get();
        return response()->json(["Otros" => $Otros, "status" => "ok"]);
    }

    public function insertCaso(Request $request)
    {
        $data = $request->all();
        $idCaso = $data["idCaso"];
        $observaciones = $data["observaciones"];
        $idEstado = $data["estado"]["id_estado"];
        $documento = Auth::user()->nro_doc;
        $fechaActual = date('Y-m-d H:i:s');

        $insertCaso = DB::connection('sqlsrv')->table("FACTUCONTROL.historial_caso")->insert([
            'id_caso'          => $idCaso,
            'fecha_movimiento' => $fechaActual,
            'observaciones'    => $observaciones,
            'id_user'          => $documento,
            'fecha_asignacion' => $fechaActual,
            'fecha_pasa_caso'  => $fechaActual,
            'primerMovimiento' => 1,
            'seguimiento'      => $documento,
        ]); ///
        $insertCaso = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $idCaso)->update([
            'id_estado'     => $idEstado,
        ]);

        return response()->json([
            "insertCaso" =>  $insertCaso
        ], 200);
    }

    public function gethistorial(Request $request)
    {
        $dataHistorial = [];
        $casosHistorialOld = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso AS historial_caso')
            ->selectRaw('caso.nuevo, historial_caso.fecha_movimiento, historial_caso.observaciones, users.name, caso.id_caso, caso.descripcion_tema, caso.Nfactura,
            caso.fechaRadicado, caso.fecha_creacion, caso.valor, conceptos.nameConceptos, caso.ordenCompra,
            estado.descripcion_estado AS estado,
            datediff(DAY, caso.fecha_creacion, GETDATE()) AS dias,
            datediff(HOUR, caso.fecha_creacion, GETDATE()) %24 AS horas,
            datediff(MINUTE, caso.fecha_creacion, GETDATE()) %60 AS minutos
            ')
            ->leftjoin('FACTUCONTROL.caso AS caso', 'historial_caso.id_caso', '=', 'caso.id_caso')
            ->leftjoin('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->leftjoin('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            ->leftjoin('FACTUCONTROL.users AS users', 'historial_caso.id_user', '=', 'users.id_user')
            ->leftjoin('FACTUCONTROL.conceptos AS conceptos', 'caso.concepto', '=', 'conceptos.idConcepto', 'LEFT')
            ->where('caso.id_caso', $request["idCaso"])
            ->orderBy('id_hcaso', 'ASC')
            ->get();

        $casosHistorialNew = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso AS historial_caso')
            ->selectRaw('caso.nuevo, historial_caso.fecha_movimiento, historial_caso.observaciones, users.name, users.last_name, caso.id_caso, caso.descripcion_tema, caso.Nfactura,
                caso.fechaRadicado, caso.fecha_creacion, caso.valor, conceptos.nameConceptos, caso.ordenCompra, historial_caso.fecha_pasa_caso, historial_caso.primerMovimiento
                , historial_caso.devolucion, historial_caso.docDevo, historial_caso.nomDevo,

                estado.descripcion_estado AS estado,
                datediff(DAY, caso.fecha_creacion, GETDATE()) AS dias,
                datediff(HOUR, caso.fecha_creacion, GETDATE()) %24 AS horas,
                datediff(MINUTE, caso.fecha_creacion, GETDATE()) %60 AS minutos
            ')
            ->join('FACTUCONTROL.caso AS caso', 'historial_caso.id_caso', '=', 'caso.id_caso')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('users', 'historial_caso.id_user', '=', 'users.nro_doc')
            ->join('FACTUCONTROL.conceptos AS conceptos', 'caso.concepto', '=', 'conceptos.idConcepto')
            ->where('caso.id_caso', $request["idCaso"])
            ->orderBy('id_hcaso', 'ASC')
            ->get();

        foreach ($casosHistorialOld as $value) {
            $a = explode('\\n', $casosHistorialOld[0]->descripcion_tema);
            $b = implode($a);
            $value->descripcionMejorada = $b;
            $value->antiguo = true;
            array_push($dataHistorial, $value);
        }

        $r = 0;
        foreach ($casosHistorialNew as $key => $value) {
            array_push($dataHistorial, $value);
            $value->antiguo = false;
            if ($key < 1 && $casosHistorialNew[0]->primerMovimiento == 1) {
                $fecha1 = new DateTime($value->fecha_creacion);
                $fecha2 = new DateTime($value->fecha_pasa_caso);
                $intervalo = $fecha1->diff($fecha2);
                $value->tiemposUsuario = $intervalo->format('%d Días/ %H Horas/ %i Minutos');
            }

            if ($key > 0) {
                $fecha1 = new DateTime($value->fecha_pasa_caso);
                $fecha2 = new DateTime($dataHistorial[$r - 1]->fecha_pasa_caso);
                $intervalo = $fecha1->diff($fecha2);
                $value->tiemposUsuario = $intervalo->format('%d Días/ %H Horas/ %i Minutos');
            }
            $r++;
        }

        return response()->json(["dataHistorial" => $dataHistorial,  "status" => "ok"]);
    }

    public function editCasoProcesoEstado(Request $request)
    {
        $data = $request->all();

        $obserCasosEnProceso = $data["obserCasosEnProceso"];
        $docNuevaAsignacion = $data["documento"];
        $nomDevo = $data["nombre"];
        $documento = Auth::user()->nro_doc;
        $fechaActual = date('Y-m-d H:i:s');
        $ultimoMovimiento = [];
        $ultimoMovimientoOld = [];

        if ($data["casosEnProceso"][0]["prioridad"] == 0) {
            foreach ($data["casosEnProceso"] as $value) {

                $ultimoMovimientoCasos = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso')
                    ->selectRaw('id_hcaso, id_caso, fecha_movimiento, fecha_asignacion')
                    ->where('id_caso', $value["id_caso"])
                    ->where('id_user', $documento)
                    ->orderBy('fecha_asignacion', 'DESC')
                    ->first();

                $ultimoMovimientoCasosOdl = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso')
                    ->selectRaw('id_hcaso, id_caso, fecha_movimiento, fecha_asignacion, users.id_user')
                    ->join('FACTUCONTROL.users AS users', 'historial_caso.id_user', '=', 'users.id_user')
                    ->where('id_caso', $value["id_caso"])
                    ->where('users.documento', $documento)
                    ->orderBy('fecha_asignacion', 'DESC')
                    ->first();

                array_push($ultimoMovimiento, $ultimoMovimientoCasos);
                array_push($ultimoMovimientoOld, $ultimoMovimientoCasosOdl);

                $insertupdate = DB::connection('sqlsrv')->table("FACTUCONTROL.historial_caso")->insert([
                    'id_caso'          => $value["id_caso"],
                    'fecha_movimiento' => $fechaActual,
                    'observaciones'    => $obserCasosEnProceso,
                    'id_user'          => $documento,
                    'fecha_asignacion' => $fechaActual,
                    'fecha_pasa_caso'  => $fechaActual,
                    'primerMovimiento' => 2,
                    'seguimiento'      => $docNuevaAsignacion,

                ]);
                $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $value["id_caso"])->update([
                    'id_tema_user'  => $docNuevaAsignacion,
                    'id_estado'     => 1,
                ]);
            }
            /// validar con casos antiguoss/////////////////////////////////////////////////////////////////////////////////////////////
            /// validar con casos antiguoss/////////////////////////////////////////////////////////////////////////////////////////////
            /// validar con casos antiguoss/////////////////////////////////////////////////////////////////////////////////////////////
            /// validar con casos antiguoss/////////////////////////////////////////////////////////////////////////////////////////////
            foreach ($ultimoMovimiento as $value) {
                if (!empty($value)) {
                    $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso')->where('id_hcaso', $value->id_hcaso)->update([
                        'fecha_pasa_caso'  => $fechaActual,
                    ]);
                }
            }
            foreach ($ultimoMovimientoOld as $value) {
                if (!empty($value)) {
                    $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso')->where('id_hcaso', $value->id_hcaso)->update([
                        'fecha_pasa_caso'  => $fechaActual,
                    ]);
                }
            }
            /// validar con casos antiguoss/////////////////////////////////////////////////////////////////////////////////////////////
            /// validar con casos antiguoss/////////////////////////////////////////////////////////////////////////////////////////////
            /// validar con casos antiguoss/////////////////////////////////////////////////////////////////////////////////////////////
            /// validar con casos antiguoss/////////////////////////////////////////////////////////////////////////////////////////////

            Bitacora::create(['ID_APP' => $request["idApp"], 'USER_ACT' => $request->user()->nro_doc, 'ACCION' => 'ASIGNA CASO A CC: ' . strtoupper($docNuevaAsignacion) . ' Caso # ' . strtoupper($data["casosEnProceso"][0]["id_caso"]), 'FECHA' => date('Y-m-d h:i:s'), 'USER_EMPRESA' => $request->user()->empresa]);

            return response()->json([
                "insertupdate" =>  $insertupdate
            ], 200);
        } else if ($data["casosEnProceso"][0]["prioridad"] == 1) {
            foreach ($data["casosEnProceso"] as $value) {
                $insertupdate = DB::connection('sqlsrv')->table("FACTUCONTROL.historial_caso")->insert([
                    'id_caso'          => $value["id_caso"],
                    'fecha_movimiento' => $fechaActual,
                    'observaciones'    => $obserCasosEnProceso,
                    'id_user'          => $documento,
                    'fecha_asignacion' => $fechaActual,
                    'fecha_pasa_caso'  => $fechaActual,
                    'primerMovimiento' => 2,
                    'seguimiento'      => $docNuevaAsignacion,
                ]);
                $cambiarPrioridad = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')
                    ->selectRaw("flag_prontopago")
                    ->where('caso.id_caso', $value["id_caso"])
                    ->get();

                if ($cambiarPrioridad[0]->flag_prontopago == 0) {
                    $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $value["id_caso"])->update([
                        'flag_prontopago' => 1,
                    ]);
                } else {
                    $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $value["id_caso"])->update([
                        'flag_prontopago' => 0,
                    ]);
                }
            }
            return response()->json([
                "insertupdate" =>  $insertupdate
            ], 200);
        } else {
            foreach ($data["casosEnProceso"] as $value) {
                $insertupdate = DB::connection('sqlsrv')->table("FACTUCONTROL.historial_caso")->insert([
                    'id_caso'          => $value["id_caso"],
                    'fecha_movimiento' => $fechaActual,
                    'observaciones'    => $obserCasosEnProceso,
                    'id_user'          => $documento,
                    'fecha_asignacion' => $fechaActual,
                    'fecha_pasa_caso'  => $fechaActual,
                    'primerMovimiento' => 2,
                    'devolucion'       => 2,
                    'docDevo'          => $docNuevaAsignacion,
                    'nomDevo'          => $nomDevo,
                    'seguimiento'      => $docNuevaAsignacion,
                ]);

                $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $value["id_caso"])->update([
                    'id_tema_user'  => $docNuevaAsignacion,
                    'id_estado'     => 1,
                ]);
            }
            return response()->json([
                "insertupdate" =>  $insertupdate
            ], 200);
        }
    }

    public function insertTransfiere(Request $request)
    {
        $data = $request->all();

        $obserCasosTransfiere = $data["obserCasosTransfiere"];
        $docNuevaAsignacion = $data["documento"];
        $documento = Auth::user()->nro_doc;
        $fechaActual = date('Y-m-d H:i:s');

        foreach ($data["casosAtransferir"] as $value) {
            $insertupdate = DB::connection('sqlsrv')->table("FACTUCONTROL.historial_caso")->insert([
                'id_caso'          => $value["id_caso"],
                'fecha_movimiento' => $fechaActual,
                'observaciones'    => $obserCasosTransfiere,
                'id_user'          => $documento,
                'fecha_asignacion' => $fechaActual,
                'fecha_pasa_caso'  => $fechaActual,
                'primerMovimiento' => 2,
                'seguimiento'      => $docNuevaAsignacion,

            ]);
            $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $value["id_caso"])->update([
                'id_tema_user'  => $docNuevaAsignacion,
                'id_estado'     => 1,
            ]);
        }
        return response()->json([
            "insertupdate" =>  $insertupdate
        ], 200);
    }

    public function cierraCasosProceso(Request $request)
    {
        $data = $request->all();

        if ($data["idConcPagoCierre"] != null) {
            $idPago = $data["idConcPagoCierre"]["idPago"];
            $nomConcePago = $data["idConcPagoCierre"]["nomConcePago"];
        }

        $obserCasosEnProcesoCierra = $data["obserCasosEnProcesoCierra"];
        $fechaCierreMasivo = $data["fechaCierreMasivo"] . "T" . date('H:i:s');
        $documento = Auth::user()->nro_doc;
        $fechaActual = date('Y-m-d H:i:s');
        $fecha = date('Y-m-d');

        if ($data["fechaCierreMasivo"] <= $fecha) {
            foreach ($data["casosEnProceso"] as $value) {

                $insertupdate = DB::connection('sqlsrv')->table("FACTUCONTROL.historial_caso")->insert([
                    'id_caso'          => $value["id_caso"],
                    'fecha_movimiento' => $fechaActual,
                    'observaciones'    => $obserCasosEnProcesoCierra,
                    'id_user'          => $documento,
                    'fecha_asignacion' => $fechaActual,
                    'fecha_pasa_caso'  => $fechaActual,
                    'idPago'           => $data["idConcPagoCierre"] == null ? null : $idPago,
                    'nomConcePago'     => $data["idConcPagoCierre"] == null ? null : $nomConcePago,
                    'fechaCierre'      => $fechaCierreMasivo,
                ]);
                $insertupdate = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $value["id_caso"])->update([
                    'id_estado'     => 3,
                ]);
            }
            return response()->json([
                "insertupdate" =>  $insertupdate,
                "fechaMayor"   =>  false
            ], 200);
        } else {
            return response()->json([
                "fechaMayor" =>  true
            ], 200);
        }
    }

    public function insertAdjuntoFac(Request $request)
    {
        $fechaActual = date('Y-m-d H:i:s');
        $fecha = date('Y-m-d');

        $misArchivosASQL = [];
        if ($request->hasFile("files")) {
            $files = $request->file("files");
            foreach ($files as $uno) {
                $adjuntosRepetidos = DB::connection('sqlsrv')->table('FACTUCONTROL.attachment as attachment')->where("attachment.file_name", $uno->getClientOriginalName())->selectRaw('attachment.*')->get();
                if (sizeOf($adjuntosRepetidos) == 0) {
                    if ($uno->guessExtension() == "pdf") {
                        $rt = public_path("uploads/factucontrol/" . $uno->getClientOriginalName());
                        if (sizeOf($files) > 1) {
                            array_push($misArchivosASQL, $uno->getClientOriginalName());
                        } else {
                            $misArchivosASQL = $uno->getClientOriginalName();
                        }
                        Storage::disk('ftp')->put($uno->getClientOriginalName(), $uno);
                        /* copy($uno, $rt); */
                        $adjuntoPorCaso = DB::connection('sqlsrv')->table("FACTUCONTROL.attachment")->insert([
                            'file_name'    => $uno->getClientOriginalName(),
                            'encrypt_name' => $uno->getClientOriginalName(),
                            'id_caso'      => $request["idCaso"],
                            'date_upload'  => $fechaActual,
                            'title'        => $uno->getClientOriginalName(),
                        ]);
                    } else {
                        return response()->json([
                            "radicado" =>  "formatoErrado"
                        ], 200);
                    }
                } else {
                    return response()->json([
                        "adjuntoPorCaso" =>  "yaExisteArchivo"
                    ], 200);
                }
            }
        }
        return response()->json([
            "adjuntoPorCaso" =>  $adjuntoPorCaso
        ], 200);
    }

    public function adjuntarArchivo(Request $request)
    {
        $adjuntosPorCaso = DB::connection('sqlsrv')->table('FACTUCONTROL.attachment as attachment')->where('attachment.id_caso', $request["id_caso"])->get();
        return response()->json(["adjuntosPorCaso" => $adjuntosPorCaso, "status" => "ok"]);
    }

    public function insertCasoProceso(Request $request)
    {
        $data = $request->all();

        if ($data["idConcDevo"] != null) {
            $idConcepDevo = $data["idConcDevo"]["idConcepDevo"];
            $nomConceDevo = $data["idConcDevo"]["nomConceDevo"];
        }
        if ($data["idConcPago"] != null) {
            $idPago = $data["idConcPago"]["idPago"];
            $nomConcePago = $data["idConcPago"]["nomConcePago"];
        }
        if ($data["idOtros"] != null) {
            $idAnulado = $data["idOtros"]["idAnulado"];
            $nomAnulado = $data["idOtros"]["nomAnulado"];
        }

        $idCaso = $data["idCaso"];
        $observaciones = $data["observaciones"];
        $idEstado = $data["estado"]["id_estado"];
        $documento = Auth::user()->nro_doc;
        $fechaActual = date('Y-m-d H:i:s');
        $fecha = date('Y-m-d');
        if ($data["fechaCierreMasivoIndividual"] == '') {
            $fechaCierreMasivoIndividual =  null;
        }else{
            $fechaCierreMasivoIndividual = $data["fechaCierreMasivoIndividual"] . "T" . date('H:i:s');
        }
        //$fechaCierreMasivoIndividual = $data["fechaCierreMasivoIndividual"] == '' ? null : $data["fechaCierreMasivoIndividual"] . "T" . date('H:i:s');
        /* anticipo pendiente por legalizar $625.000 */
        if ($data["fechaCierreMasivoIndividual"] <= $fecha) {
            $insertCaso = DB::connection('sqlsrv')->table("FACTUCONTROL.historial_caso")->insert([
                'id_caso'          => $idCaso,
                'fecha_movimiento' => $fechaActual,
                'observaciones'    => $observaciones,
                'id_user'          => $documento,
                'fecha_asignacion' => $fechaActual,
                'fecha_pasa_caso'  => $fechaActual,
                'primerMovimiento' => 2,
                'idConcepDevo'     => $data["idConcDevo"] == null ? null : $idConcepDevo,
                'nomConceDevo'     => $data["idConcDevo"] == null ? null : $nomConceDevo,
                'idPago'           => $data["idConcPago"] == null ? null : $idPago,
                'nomConcePago'     => $data["idConcPago"] == null ? null : $nomConcePago,
                'idAnulado'        => $data["idOtros"]    == null ? null : $idAnulado,
                'nomAnulado'       => $data["idOtros"]    == null ? null : $nomAnulado,
                'fechaCierre'      => $fechaCierreMasivoIndividual,
            ]);
            $insertCaso = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $idCaso)->update([
                'id_estado'     => $idEstado,
            ]);

            return response()->json([
                "insertCaso" =>  $insertCaso,
                "fechaMayor" =>  false
            ], 200);
        } else {
            return response()->json([
                "fechaMayor" =>  true
            ], 200);
        }
    }

    public function gethistorialTime(Request $request)
    {
        $documento = Auth::user()->nro_doc;

        $ultimaFecha = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso')
            ->where('id_caso', $request["idCaso"])
            ->orderBy('fecha_movimiento', 'desc')
            ->limit(1)
            ->get();

        $fecha_movimiento = $ultimaFecha[0]->fecha_movimiento;

        $tiempoAbiertoCaso = DB::connection('sqlsrv')->table('FACTUCONTROL.caso')->where('caso.id_caso', $request["idCaso"])
            ->selectRaw("
                id_caso,
                datediff(DAY, fecha_creacion, GETDATE()) AS dias,
                datediff(HOUR, fecha_creacion, GETDATE()) %24 AS horas,
                datediff(MINUTE, fecha_creacion, GETDATE()) %60 AS minutos")
            ->first();

        $pagoCierre = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS c')
            ->selectRaw("datediff(day,GETDATE(), DATEADD(day, p.dias_pago, c.fecha_creacion)) as dias_restantes,
                        c.fecha_creacion + p.dias_pago as fecha_estimada_cierre,
                        c.fecha_creacion,
                        p.dias_pago as dias_pago,
                        c.id_caso as caso,
                        p.razon_social as razon_social,
                        p.nit as nit")
            ->join('FACTUCONTROL.proveedor AS p', 'c.id_proveedor', '=', 'p.id_proveedor')
            ->where('c.id_caso', $request["idCaso"])
            ->orderBy('dias_restantes', 'DESC')
            ->get();

        $danger = ceil($pagoCierre[0]->dias_pago - ($pagoCierre[0]->dias_pago * 0.8));
        $warning = ceil($pagoCierre[0]->dias_pago - ($pagoCierre[0]->dias_pago * 0.6));

        if ($pagoCierre[0]->dias_restantes < $danger) {
            $tag = 'danger';
        } else if ($pagoCierre[0]->dias_restantes > $danger && $pagoCierre[0]->dias_restantes <= $warning) {
            $tag = 'warning';
        } else {
            $tag = 'info';
        }

        return response()->json(["tiempoAbiertoCaso" => $tiempoAbiertoCaso, "pagoCierre" => $pagoCierre,  "fecha_movimiento" => $fecha_movimiento, "tag" => $tag, "status" => "ok"]);
    }

    public function getcasosMasivos(Request $request)
    {
        $documento = Auth::user()->nro_doc;


        $Cierre = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso AS historial_caso')
            ->selectRaw('caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,
            temas.descripcion_temar,
            users.name,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.SUC_DEPARTAMENTO AS nombre_sucursal,
            p.dias_pago as diasProveedor,
                datediff(DAY, caso.fecha_creacion, historial_caso.fecha_pasa_caso) AS dias,
                datediff(HOUR, caso.fecha_creacion, historial_caso.fecha_pasa_caso) %24 AS horas,
                datediff(MINUTE, caso.fecha_creacion, historial_caso.fecha_pasa_caso) %60 AS minutos
            ')
            ->join('FACTUCONTROL.caso AS caso', 'historial_caso.id_caso', '=', 'caso.id_caso')
            ->join('FACTUCONTROL.temas AS temas', 'caso.idTema', '=', 'temas.id_tema')
            ->join('users', 'caso.documento', '=', 'users.nro_doc')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            // ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS sucursal', 'caso.id_sucursal', '=', 'sucursal.SUC_CODIGO_DEPARTAMENTO', 'LEFT')
            ->orderBy('caso.id_caso', 'ASC')
            ->where('caso.id_tema_user', $documento)
            //$data["idConcDevo"] == null ? null : $idConcepDevo,
            ->where('caso.id_estado', 3)
            ->orderBy('historial_caso.fecha_pasa_caso', 'DESC')
            ->get();



        $datos = $Cierre;
        $idCaso = [];
        $validaCierre = [];

        foreach ($datos as $val) {
            array_push($idCaso, $val->id_caso);
        }
        $idCasosUnicos = array_unique($idCaso);

        foreach ($idCasosUnicos as $val) {
            $a = $this->getValoresUnicos($val);
            array_push($validaCierre, $a);
        }

        $a = DB::table('FACTUCONTROL.historial_caso')->selectRaw('DISTINCT caso.fecha_creacion, caso.id_caso, caso.descripcion_tema,
                caso.flag_prontopago, caso.id_tipo_factura, temas.descripcion_temar, users.id_user, users.name, users.documento,
                estado.descripcion_estado AS estado, categoria.descripcion AS categoria_descripcion, p.razon_social, caso.id_user_create,
                sucursal.nombre AS nombre_sucursal, p.dias_pago as diasProveedor, datediff(DAY, caso.fecha_creacion, GETDATE()) AS dias,
                datediff(HOUR, caso.fecha_creacion, GETDATE()) %24 AS horas,  datediff(MINUTE, caso.fecha_creacion, GETDATE()) %60 AS minutos')
            ->Join('FACTUCONTROL.caso', 'historial_caso.id_caso', '=', 'caso.id_caso')
            ->leftJoin('FACTUCONTROL.temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            ->Join('FACTUCONTROL.temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->leftJoin('FACTUCONTROL.users', 'temas_user.id_user', '=', 'users.id_user')
            ->Join('FACTUCONTROL.estado', 'estado.id_estado', '=', 'caso.id_estado')
            ->Join('FACTUCONTROL.categoria', 'categoria.id_categoria', '=', 'caso.id_categoria')
            ->Join('FACTUCONTROL.proveedor as p', 'p.id_proveedor', '=', 'caso.id_proveedor')
            ->leftJoin('FACTUCONTROL.sucursal', 'sucursal.suc_legal', '=', 'caso.id_sucursal');


        $b = DB::table('FACTUCONTROL.historial_caso')->selectRaw('DISTINCT caso.fecha_creacion, caso.id_caso, caso.descripcion_tema,
                caso.flag_prontopago, caso.id_tipo_factura, temas.descripcion_temar, users.id_user, users.name, users.documento,
                estado.descripcion_estado AS estado, categoria.descripcion AS categoria_descripcion, p.razon_social, caso.id_user_create,
                sucursal.nombre AS nombre_sucursal, p.dias_pago as diasProveedor, datediff(DAY, caso.fecha_creacion, GETDATE()) AS dias,
                datediff(HOUR, caso.fecha_creacion, GETDATE()) %24 AS horas,  datediff(MINUTE, caso.fecha_creacion, GETDATE()) %60 AS minutos')
            ->Join('FACTUCONTROL.caso', 'historial_caso.id_caso', '=', 'caso.id_caso')
            ->Join('FACTUCONTROL.temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            ->Join('FACTUCONTROL.temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->Join('FACTUCONTROL.users', 'historial_caso.id_user', '=', 'users.id_user')
            ->Join('FACTUCONTROL.estado', 'estado.id_estado', '=', 'caso.id_estado')
            ->Join('FACTUCONTROL.categoria', 'categoria.id_categoria', '=', 'caso.id_categoria')
            ->Join('FACTUCONTROL.proveedor as p', 'p.id_proveedor', '=', 'caso.id_proveedor')
            ->leftJoin('FACTUCONTROL.sucursal', 'sucursal.suc_legal', '=', 'caso.id_sucursal');

        $casosRegistradoOldHis = [];
        $per = json_decode(Auth::user()->rol);

        if (in_array(9, $per)) {
            //para el radicador
            $casosRegistradoOldHis1 = $a->where('FACTUCONTROL.caso.id_user_create', Auth::user()->nro_doc)->get();
            foreach ($casosRegistradoOldHis1 as $val1) {
                array_push($casosRegistradoOldHis, $val1);
            }
        } else {
            $casosRegistradoOldHis1 = $a->where('FACTUCONTROL.users.id_user', Auth::user()->nro_doc)->orWhere('FACTUCONTROL.caso.id_tema_user', Auth::user()->nro_doc)->get();
            $casosRegistradoOldHis2 = $b->where('FACTUCONTROL.users.id_user', Auth::user()->nro_doc)->orWhere('FACTUCONTROL.caso.id_tema_user', Auth::user()->nro_doc)->get();
            foreach ($casosRegistradoOldHis1 as $val1) {
                array_push($casosRegistradoOldHis, $val1);
            }
            foreach ($casosRegistradoOldHis2 as $val2) {
                array_push($casosRegistradoOldHis, $val2);
            }
        }

        $cNew = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')
            ->selectRaw('DISTINCT caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,
            temas.descripcion_temar,
            users.name,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.SUC_DEPARTAMENTO AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            datediff(DAY, caso.fecha_creacion, GETDATE()) AS dias,
            datediff(HOUR, caso.fecha_creacion, GETDATE()) %24 AS horas,
            datediff(MINUTE, caso.fecha_creacion, GETDATE()) %60 AS minutos
            ')
            ->leftJoin('FACTUCONTROL.historial_caso AS hc', 'hc.id_caso', '=', 'caso.id_caso')
            ->join('FACTUCONTROL.temas AS temas', 'caso.idTema', '=', 'temas.id_tema')
            ->join('users', 'caso.documento', '=', 'users.nro_doc')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            /* ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal') */
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS sucursal', 'caso.id_sucursal', '=', 'sucursal.SUC_CODIGO_DEPARTAMENTO', 'LEFT')
            ->orderBy('caso.id_caso', 'ASC');
        $per = json_decode(Auth::user()->rol);
        if (in_array(9, $per)) {
            //para el radicador
            $casosRegistradoNewHis = $cNew->where('caso.id_user_create', $documento)
                ->where('caso.nuevo', 1)
                ->where('caso.id_estado', '!=', 3)
                ->get();
        } else {
            $casosRegistradoNewHis = $cNew->where('caso.nuevo', 1)
                ->where('estado.id_estado', '!=', 3)
                ->where('hc.seguimiento', $documento)->orWhere('caso.id_estado', '!=', 3)
                ->where('hc.id_user', $documento)->orWhere('caso.id_estado', '!=', 3)
                ->get();
        }

        return response()->json([
            "casosRegistradoOldHis" => $casosRegistradoOldHis,
            "casosRegistradoNewHis" => $casosRegistradoNewHis,
            "validaCierre" => $validaCierre,
            "status" => "ok"
        ], 200);
    }

    public function getcasosMasivosAdmin(Request $request)
    {
        $Cierre = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso AS historial_caso')
            ->selectRaw('caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,
            temas.descripcion_temar,
            users.name,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.SUC_DEPARTAMENTO AS nombre_sucursal,
            p.dias_pago as diasProveedor,
                datediff(DAY, caso.fecha_creacion, historial_caso.fecha_pasa_caso) AS dias,
                datediff(HOUR, caso.fecha_creacion, historial_caso.fecha_pasa_caso) %24 AS horas,
                datediff(MINUTE, caso.fecha_creacion, historial_caso.fecha_pasa_caso) %60 AS minutos
            ')
            ->join('FACTUCONTROL.caso AS caso', 'historial_caso.id_caso', '=', 'caso.id_caso')
            ->join('FACTUCONTROL.temas AS temas', 'caso.idTema', '=', 'temas.id_tema')
            ->join('users', 'caso.documento', '=', 'users.nro_doc')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            // ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS sucursal', 'caso.id_sucursal', '=', 'sucursal.SUC_CODIGO_DEPARTAMENTO', 'LEFT')
            ->orderBy('caso.id_caso', 'ASC')
            ->where('caso.id_estado', 3)
            ->orderBy('historial_caso.fecha_pasa_caso', 'DESC')
            ->get();

        $datos = $Cierre;
        $idCaso = [];
        $validaCierre = [];

        foreach ($datos as $val) {
            array_push($idCaso, $val->id_caso);
        }
        $idCasosUnicos = array_unique($idCaso);

        foreach ($idCasosUnicos as $val) {
            $a = $this->getValoresUnicos($val);
            array_push($validaCierre, $a);
        }

        $casosRegistradoOldHis = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')->where('caso.id_estado', '!=', 3)
            ->selectRaw('caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,
            temas.descripcion_temar,
            users.id_user,
            users.name,
            users.documento,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.nombre AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            datediff(DAY, caso.fecha_creacion, GETDATE()) AS dias,
            datediff(HOUR, caso.fecha_creacion, GETDATE()) %24 AS horas,
            datediff(MINUTE, caso.fecha_creacion, GETDATE()) %60 AS minutos
            ')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            ->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->orderBy('caso.id_caso', 'ASC')
            ->get();

        $casosRegistradoNewHis = DB::connection('sqlsrv')->table('FACTUCONTROL.caso AS caso')->where('caso.nuevo', 1)->where('caso.id_estado', '!=', 3)
            ->selectRaw('caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,
            temas.descripcion_temar,
            users.name,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.SUC_DEPARTAMENTO AS nombre_sucursal,
            p.dias_pago as diasProveedor,
            datediff(DAY, caso.fecha_creacion, GETDATE()) AS dias,
            datediff(HOUR, caso.fecha_creacion, GETDATE()) %24 AS horas,
            datediff(MINUTE, caso.fecha_creacion, GETDATE()) %60 AS minutos
            ')
            ->join('FACTUCONTROL.temas AS temas', 'caso.idTema', '=', 'temas.id_tema')
            ->join('users', 'caso.documento', '=', 'users.nro_doc')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            //->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal')
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS sucursal', 'caso.id_sucursal', '=', 'sucursal.SUC_CODIGO_DEPARTAMENTO', 'LEFT')
            ->orderBy('caso.id_caso', 'ASC')
            ->get();

        return response()->json([
            "casosRegistradoOldHis" => $casosRegistradoOldHis,
            "casosRegistradoNewHis" => $casosRegistradoNewHis,
            "validaCierre" => $validaCierre,
            /* "CierreOld" => $CierreOld, */
            "status" => "ok"
        ]);
    }

    public function getValoresUnicos($val)
    {
        $validaCierre = DB::connection('sqlsrv')->table('FACTUCONTROL.historial_caso AS historial_caso')
            ->selectRaw('caso.fecha_creacion,
            caso.id_caso,
            caso.descripcion_tema,
            caso.flag_prontopago,
            caso.id_tipo_factura,
            temas.descripcion_temar,
            users.name,
            estado.descripcion_estado AS estado,
            categoria.descripcion AS categoria_descripcion,
            p.razon_social,
            sucursal.SUC_DEPARTAMENTO AS nombre_sucursal,
            p.dias_pago as diasProveedor,
                datediff(DAY, caso.fecha_creacion, historial_caso.fecha_pasa_caso) AS dias,
                datediff(HOUR, caso.fecha_creacion, historial_caso.fecha_pasa_caso) %24 AS horas,
                datediff(MINUTE, caso.fecha_creacion, historial_caso.fecha_pasa_caso) %60 AS minutos
            ')
            ->join('FACTUCONTROL.caso AS caso', 'historial_caso.id_caso', '=', 'caso.id_caso')
            ->join('FACTUCONTROL.temas AS temas', 'caso.idTema', '=', 'temas.id_tema')
            ->join('users', 'caso.documento', '=', 'users.nro_doc')
            ->join('FACTUCONTROL.estado AS estado', 'caso.id_estado', '=', 'estado.id_estado')
            ->join('FACTUCONTROL.categoria AS categoria', 'caso.id_categoria', '=', 'categoria.id_categoria')
            ->join('FACTUCONTROL.proveedor AS p', 'caso.id_proveedor', '=', 'p.id_proveedor')
            /* ->join('FACTUCONTROL.sucursal AS sucursal', 'caso.id_sucursal', '=', 'sucursal.id_sucursal') */
            ->join('HOJADEVIDASEDES.SUC_SUCURSAL AS sucursal', 'caso.id_sucursal', '=', 'sucursal.SUC_CODIGO_DEPARTAMENTO', 'LEFT')
            ->orderBy('caso.id_caso', 'ASC')
            ->where('caso.id_estado', 3)
            ->where('caso.id_caso', $val)
            ->orderBy('historial_caso.fecha_pasa_caso', 'DESC')
            ->limit('1')
            ->get();

        return response()->json([
            "validaCierre" => $validaCierre, "status" => "ok"
        ]);
    }

    public function getProveedoresDash()
    {
        $documento = Auth::user()->nro_doc;
        $datosGrafico = [];
        $getCasosDash = DB::connection('sqlsrv')->table('FACTUCONTROL.caso as caso')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            ->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->where('caso.id_estado', 3)
            ->where('users.documento', $documento)
            ->count();
        $getCasosDashTramite = DB::connection('sqlsrv')->table('FACTUCONTROL.caso as caso')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            ->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->where('caso.id_estado', 2)
            ->where('users.documento', $documento)
            ->count();
        $getCasosDashAnulados = DB::connection('sqlsrv')->table('FACTUCONTROL.caso as caso')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            ->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->where('caso.id_estado', 4)
            ->where('users.documento', $documento)

            ->count();
        $getCasosDashDevolucion = DB::connection('sqlsrv')->table('FACTUCONTROL.caso as caso')
            ->join('FACTUCONTROL.temas_user AS temas_user', 'caso.id_tema_user', '=', 'temas_user.id_tema_user')
            ->join('FACTUCONTROL.temas AS temas', 'temas_user.id_tema', '=', 'temas.id_tema')
            ->join('FACTUCONTROL.users AS users', 'temas_user.id_user', '=', 'users.id_user')
            ->where('caso.id_estado', 5)
            ->where('users.documento', $documento)

            ->count();
        $getProveedoresDash = DB::connection('sqlsrv')->table('FACTUCONTROL.proveedor')
            ->count();

        array_push($datosGrafico, $getCasosDash);
        array_push($datosGrafico, $getCasosDashTramite);
        array_push($datosGrafico, $getCasosDashAnulados);
        array_push($datosGrafico, $getCasosDashDevolucion);
        array_push($datosGrafico, $getProveedoresDash);

        return response()->json([
            "datosGrafico" =>  $datosGrafico
        ], 200);
    }
}
