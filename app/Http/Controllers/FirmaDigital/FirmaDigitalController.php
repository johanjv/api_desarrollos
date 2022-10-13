<?php

namespace App\Http\Controllers\FirmaDigital;

use App\Models\FirmaDigital\Colaborador;
use App\Models\FirmaDigital\Direccion;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Bitacora\Bitacora;
use Illuminate\Http\Request;
use App\Imports\FirmaImport;
use App\Models\FirmaDigital\CargosColab;





class FirmaDigitalController extends Controller
{
    private $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function getDireccion(Request $request)
    {
        $direccion = Direccion::all();

        return response()->json([
            "direccion"   => $direccion,
        ], 200);

    }

    public function getDettaleColaborador (Request $request)
    {

        $colab = Colaborador::with([
            'cargos' => function($q){
                return $q->with('cargoDetalle');
            }])->where('documento', $request["doc"])->first();

        /* $colab = Colaboradores::with([
            'cargos' => function($q){
                return $q->with('cargoDetalle');
            }])->where('DOC_COLABORADOR', $request["doc"])->first(); */

        return response()->json([
            "colab"   => $colab,
        ], 200);
    }

    public function saveEdit(Request $request)
    {

        $item = Direccion::where('Id', $request["item"]["Id"])->update([
            'Direccion' => $request["item"]["Direccion"]
        ]);

        $direccion = Direccion::all();

        return response()->json([
            "direccion"   => $direccion,
        ], 200);

    }


    public function saveNewImagen(Request $request)
    {

        if ($request->hasFile("files")) {
            $files = $request->file("files");
            foreach ($files as $file) {
                DB::insert('insert into FIRMA.hist_img (img, fecha_creacion) values (?, ?)', [$request['imgSql'], date('Y-m-d h:m:s')]);
                /* $rt = public_path('background_firma.png');
                copy($file, $rt); */
            }
            /* REGISTRO EN BITACORA */
            Bitacora::create([
                'ID_APP' => $request["idApp"],
                'USER_ACT' => Auth::user()->nro_doc,
                'ACCION' => 'CAMBIO - SUBIO UNA NUEVA IMAGEN AL APLICATIVO ' . Auth::user()->nro_doc, 
                'FECHA' => date('Y-m-d h:i:s'),
                'USER_EMPRESA' => Auth::user()->empresa
            ]);

        }


    }

    public function saveNew(Request $request)
    {

        $item = Direccion::create([
            'Direccion' => strtoupper($request["item"])
        ]);

        $direccion = Direccion::all();

        return response()->json([
            "direccion"   => $direccion,
        ], 200);

    }

    public function getImage(Request $request)
    {
        $img = DB::table('FIRMA.hist_img')
            ->orderBy('id', 'DESC')->limit(1)
        ->first();
        /* $img = DB::select('select * from FIRMA.hist_img order by fecha_creacion desc'); */

        return response()->json([
            "img"   => $img,
        ], 200);
    }

    public function saveBit(Request $request)
    {
         /* REGISTRO EN BITACORA */
         Bitacora::create([
             'ID_APP' => $request["idApp"],
             'USER_ACT' => Auth::user()->nro_doc,
             'ACCION' => 'DESCARGO - GENERO SU FIRMA DIGITAL ' . Auth::user()->nro_doc, 
             'FECHA' => date('Y-m-d h:i:s'),
             'USER_EMPRESA' => Auth::user()->empresa
        ]);
    }

    public function importPlantaFirma(Request $request){
       

        set_time_limit(8000);
        try {
            $request->validate([
                'import_file' => 'required|file|mimes:xls,xlsx'
            ]);

            $path = $request->file('import_file');

            $excelFile = Excel::toCollection(new FirmaImport, $path);
        
            CargosColab::truncate();
            Colaborador::truncate();

            foreach ($excelFile[0] as $row) {
                $doc    = trim($row['documento']);
                $nomb   = trim($row['apellidos_y_nombres']);
                $cargo  = trim($row['cod_cargo']);

                Colaborador::create([
                    'documento'       => $doc,
                    'nombreCompleto'  => $nomb,
                ]);

                CargosColab::create([
                    'documento' => $doc,
                    'codCargo'  => $cargo
                ]);

            }
            $status = 1;
        } catch (\Throwable $th) {
            $status = 0;
        }
        



        return response()->json([
            "status" => $status,
        ], 200);
    }
}
