<?php

namespace App\Models\Factucontrol\Casos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caso extends Model
{
    use HasFactory;

    protected  $table = "FACTUCONTROL.caso";

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
         'id_caso'
        ,'id_tema_user'
        ,'id_area'
        ,'descripcion_tema'
        ,'fecha_creacion'
        ,'id_estado'
        ,'fecha_cierre'
        ,'fecha_estimada_cierre'
        ,'id_nivel_prioridad'
        ,'flag_prontopago'
        ,'id_categoria'
        ,'fecha_asignacion'
        ,'id_proveedor'
        ,'id_user_create'
        ,'id_sucursal'
        ,'id_tipo_factura'
        ,'fechaRadicado'
        ,'ordenCompra'
        ,'valor'
        ,'concepto'
        ,'Nfactura'
        ,'archivosPDF'
        ,'cantidadFactutras'
        ,'documento'
        ,'idTema'
        ,'tipDoc'
        ,'nuevo'
    ];

}
