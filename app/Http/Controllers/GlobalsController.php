<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class GlobalsController extends Controller
{
    public function getUsers(Request $request)
    {
        $countUser = User::count();
        return response()->json(["countUser" => $countUser],200);
    }    
}
