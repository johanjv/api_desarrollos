<?php

namespace App\Http\Controllers\Factucontrol;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\Factucontrol\Proveedores\Proveedores;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\ExcelMatch;
use PhpParser\Node\Stmt\Return_;
use Maatwebsite\Excel\Facades\Excel;

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

        return response()->json(['message' => 'uploaded successfully'], 200);
    }
}
