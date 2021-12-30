<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\Controller;
use App\Models\AdminGlobal\Desarrollos;
use App\Models\Bitacora\Bitacora;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class BitacoraController extends Controller
{
    public function getHistorial(Request $request)
    {

        /* return $request->all(); */
        $bit = DB::table('BITACORA.REGISTRO_BITACORA AS B')
            ->join('MASTER.desarrollos AS D', 'D.id', '=', 'B.ID_APP')
            ->join('users AS u', 'u.nro_doc', '=', 'B.USER_ACT');

            if ($request["idApp"] == "10028") {
                $bitacora = $bit->orderBy('B.FECHA', 'DESC')->get();
            }else {
                $bitacora = $bit->where('B.ID_APP', $request["idApp"])->orderBy('B.FECHA', 'DESC')->get();
            }



        $countDesarrollos   = Desarrollos::count();
        $countUsuarios      = User::count();
        $countLogs          = Bitacora::count();

        return response()->json([
            "bitacora"          => $bitacora,
            "countDesarrollos"  => $countDesarrollos,
            "countUsuarios"     => $countUsuarios,
            "countLogs"         => $countLogs,
        ], 200);
    }

    public function getConteoBit(Request $request)
    {
        $countDesarrollos   = Desarrollos::count();
        $countUsuarios      = User::count();
        $countLogs          = Bitacora::count();

        return response()->json([
            "apps"  => $countDesarrollos,
            "users" => $countUsuarios,
            "logs"  => $countLogs,
        ], 200);
    }
}
