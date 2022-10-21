<?php 

namespace App\Permisos;

use App\Models\AdminGlobal\Modulos;

class EstrategiaLineaEtica {

    public function validarRol($grupos, $app)
    {
        $permisos = [];
        $permiso = 0;

        $rolesPerUser = array(
            /*
                *    Este objeto permiso debe mantenerse para que los administradores puedan ingresar
            */
            (object)([
                'ap' =>  'CN=APD_AGSuperAdmin',
                'id' =>  1
            ]),

            /* 
                * Agergar de aca en adelante los objetos permisos 
                *   (object)([ 
                *       'ap' =>  'CN=APD_EXAMPLE', 
                *       'id' =>  1 
                *   ])
            */
            (object)([
                'ap' =>  'CN=APD_LineaEtica',
                'id' =>  40
            ]),
        
           
        );

        
        foreach ($grupos as $grupo) {
            foreach ($rolesPerUser as $rol) {
                if ($rol->ap == $grupo) {
                    $permiso = 1;
                    array_push($permisos, $rol->id);
                }
            }
        }

        $rolesUser = (object)([
            'roles'     =>  json_encode($permisos),
            'permiso'   =>  $permiso,
        ]);

        $rolesUser->modulos = $this->getModulos($rolesUser->roles, $app);

        return $rolesUser;
    }

    public function getModulos($rolesUser, $app)
    {
        $permisos = json_decode($rolesUser);
        /*
            *    Esta condicion debe mantenerse para que los administradores puedan visualizar todos los modulos
        */
        if ((in_array(1, $permisos)) || (in_array(2, $permisos))) {
            $modulos    = Modulos::where('desarrollo_id', $app)->orderBy('orden', 'ASC')->get();
            $loads = ['submodulos'];
            $modulos->load($loads);
        }else{
            $modulos = null;
        }

        return $modulos;
    }





}
