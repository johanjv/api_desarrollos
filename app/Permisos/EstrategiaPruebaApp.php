<?php 

namespace App\Permisos;

class EstrategiaPruebaApp {

    public function validarRol($grupos)
    {
        $permisos = [];
        $permiso = 0;

        $rolesPerUser = array(
            (object)([
                'ap' =>  'CN=APD_AGSuperAdmin',
                'id' =>  1
            ]),
            
            (object)([
                'ap' =>  'CN=APD_Mamitas2_0',
                'id' =>  15
            ]),
        
            (object)([
                'ap' =>  'CN=APD_SupAdmResiduos',
                'id' =>  16
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
            'permiso'   =>  $permiso
        ]);

        $this->getModulos($rolesUser);

        return $rolesUser;
    }

    public function getModulos($rolesUser)
    {
        
    }





}
