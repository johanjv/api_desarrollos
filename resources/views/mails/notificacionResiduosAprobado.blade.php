<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN GESTIÓN DE RESIDUOS HOSPITALARIOS</title>
</head>
<body>
    <p style="background-color: brown">
        <h1>Hola!.<br>
        Te informamos que el periodo <span class="badge badge-info">{{ $validarMes->id_mes_ano }}</span> fue <span class="badge badge-success">APROBADO</span>.
        </h1>{{-- <strong> Revisado por: </strong> <class="badge badge-info">{{ $validarMes->nro_doc_user }}</span><br> --}}
        <strong> Rango de fechas evaluado: </strong> | Desde <class="badge badge-info"> {{ $validarMes->start_periodo }} </span> | Hasta <class="badge badge-info">{{ $validarMes->end_periodo }} </span> |<br>
        <strong> Fecha de Revisión: </strong>{{ $validarMes->fecha_revision }}<br><br>


    <strong>Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>
    </p>
</body>
</html>
