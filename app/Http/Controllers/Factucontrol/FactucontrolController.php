<?php

namespace App\Http\Controllers\Factucontrol;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\Factucontrol\Proveedores\Proveedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\ExcelMatch;
use PhpParser\Node\Stmt\Return_;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

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
        $nit = DB::table('FACTUCONTROL.proveedor')->where('nit', $request["nit"])->count();

        if ($nit == 0) {
            $insert = Proveedores::create([
                'nit'           => $request["nit"],
                'razon_social'  => $request["razon_social"],
                'descripcion'   => $request["descripcion"],
                'dias_pago'     => $request["dias_pago"],
                'descuento'     => $request["descuento"],
                'active'        => 1,
                'pronto_pago'   => $request["pronto_pago"],
            ]);
    
            $proveedores = Proveedores::all();
            return response()->json([
                "proveedores" =>  $proveedores
            ], 200);
        }else{
            return response()->json([
                "proveedores" =>  "existe"
            ], 200);
        }       
    }

    public function editProveedor(Request $request)
    {
        $data = $request->all();
        $update = Proveedores::where("id_proveedor", $data["id_proveedor"])->update([
            'nit'           => $request["nit"],
            'razon_social'  => $request["razon_social"],
            'descripcion'   => $request["descripcion"],
            'dias_pago'     => $request["dias_pago"],
            'descuento'     => $request["descuento"],
            'active'        => $request["proveedor_activo"],
            'pronto_pago'   => $request["pronto_pago"],
        ]);

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
        $data = Excel::import(new UsersImport, $path);

        $proveedores = Proveedores::all();
        return response()->json([
            'message'   => 'uploaded successfully',
            "proveedores" =>  $proveedores
        ], 200);
    }

    public function getTemas(Request $request)
    {
        $Temas = DB::table('FACTUCONTROL.temas')->get();
        return response()->json(["temas" => $Temas, "status" => "ok"]);
    }

    public function getCategorias(Request $request)
    {
        $categorias = DB::table('FACTUCONTROL.categoria')->get();
        return response()->json(["categorias" => $categorias, "status" => "ok"]);
    }

    public function temasRol(Request $request)
    {
        $temasRol_users = DB::table('FACTUCONTROL.users as users')->where('users.active', 1)->where('temas_user.id_tema', $request["id_tema"])
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
        $proveedores = DB::table('FACTUCONTROL.proveedor')->get();
        return response()->json(["proveedores" => $proveedores, "status" => "ok"]);
    }

    public function sucursales(Request $request)
    {
        $sucursales = DB::table('FACTUCONTROL.sucursal')->get();
        return response()->json(["sucursales" => $sucursales, "status" => "ok"]);
    }

    public function insertRadicado(Request $request)
    {
        $fechaActual = date('Y-m-d H:i:s');

        $misArchivosASQL = [];
        if ($request->hasFile("files")) {
            $files = $request->file("files");
            foreach ($files as $uno) {
                $rt = public_path("uploads/factucontrol/" . $uno->getClientOriginalName());
                if (sizeOf($files) > 1) {
                    array_push($misArchivosASQL, $uno->getClientOriginalName());
                } else {
                    $misArchivosASQL = $uno->getClientOriginalName();
                }

                copy($uno, $rt);
            }
        }


        $files = $request->file("files");
        $radicado = DB::table("FACTUCONTROL.caso")->insert([
            'id_tema_user'          => $request["reportar"],
            'descripcion_tema'      => $request["notas"],

            /* 'descripcion_tema'      => $request["descripcion"], */
            'fecha_creacion'        => $fechaActual,
            'id_estado'             => 1,
            /*                         'fecha_cierre'          => $request["pronto_pago"],
                        'fecha_estimada_cierre' => $request["pronto_pago"],
                        'id_nivel_prioridad'    => $request["id_nivel_prioridad"],
                        'flag_prontopago'       => $request["pronto_pago"], */
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
            'contenido'             => $request["contenido"],
            'Nfactura'              => $request["nFactura"],
            'archivosPDF'           => json_encode($misArchivosASQL),
        ]);
        return response()->json([
            "radicado" =>  $radicado
        ], 200);
    }

    public function rolesUser($files)
    {
        /* $detalleUser = mb_convert_encoding($files, 'UTF-8', 'UTF-8');
        $archivosInssert = $detalleUser; */
        /* $a = implode(",",$files);
        $grupos = explode(",", $a); */
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
}
