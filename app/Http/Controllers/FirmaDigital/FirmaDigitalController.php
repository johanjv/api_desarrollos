<?php

namespace App\Http\Controllers\FirmaDigital;

use App\Http\Controllers\Controller;
use App\Models\FirmaDigital\Direccion;
use App\Models\Hvsedes\TalentoHumano\Colaboradores;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FirmaDigitalController extends Controller
{
    public function getDireccion(Request $request)
    {
        $direccion = Direccion::all();

        return response()->json([
            "direccion"   => $direccion,
        ], 200);

    }

    public function getDettaleColaborador (Request $request)
    {
        $colab = Colaboradores::with([
            'cargos' => function($q){
                return $q->with('cargoDetalle');
            }])->where('DOC_COLABORADOR', $request["doc"])->first();

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

}
