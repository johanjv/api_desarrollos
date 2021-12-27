<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\Controller;
use App\Models\Bitacora\Bitacora;
use Illuminate\Http\Request;
use DB;

class BitacoraController extends Controller
{
    public function getHistorial(Request $request)
    {
        $bitacora = DB::table('BITACORA.REGISTRO_BITACORA AS B')
            ->join('MASTER.desarrollos AS D', 'D.id', '=', 'B.ID_APP')
            ->join('users AS u', 'u.nro_doc', '=', 'B.USER_ACT')
            ->orderBy('B.FECHA', 'DESC')
        ->get();

        return response()->json(["bitacora" => $bitacora], 200);

    }
}
