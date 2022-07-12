<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN GESTIÓN DE RESIDUOS HOSPITALARIOS</title>
</head>
<body>
    <p style="background-color: brown">
        <h4>Hola!.<br>
        Te informamos que el periodo <span span class="label label-info">{{ $validarMes->id_mes_ano }}</span> fue <span class="label label-success">APROBADO</span>.
        </h4>{{-- <strong> Revisado por: </strong> <span class="label label-info">{{ $validarMes->nro_doc_user }}</span><br> --}}
        <strong> Rango de fechas evaluado: </strong> | Desde <span class="label label-info"> {{ $validarMes->start_periodo }} </span> | Hasta <span class="label label-info">{{ $validarMes->end_periodo }} </span> |<br>
        <strong> Fecha de Revisión: </strong> <span class="label label-info">{{ $validarMes->fecha_revision }} </span><br><br>


        <strong class="label label-primary">Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>
    </p>
</body>
</html>
