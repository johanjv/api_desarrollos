<?php

namespace App\Http\Controllers\Vacunacion;

use App\Http\Controllers\Controller;
use App\Models\Vacunacion\TipoDocumento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VacunacionController extends Controller
{
    public function getTiposDoc(Request $request)
    {
        $tiposDoc = TipoDocumento::all();


        return response()->json([
            "tiposDoc" => $tiposDoc
        ], 200);
    }

}
