<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN GESTIÓN DE RESIDUOS HOSPITALARIOS</title>
</head>

<body>
    <p >
        <h4>Hola!.<br>
            Te informamos que el periodo <span class="label label-info">{{ $validarMes->id_mes_ano }}</span> fue <span class="label label-danger">RECHAZADO</span>.
        </h4>
        <h3><strong> Motivo de Rechazo: </strong> {{ $validarMes->observacion }}</h3>
        <strong> Fecha de Revisión: </strong>{{ $validarMes->fecha_revision }}<br><br>

        <strong class="label label-primary">Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>
    </p>
</body>

</html>
