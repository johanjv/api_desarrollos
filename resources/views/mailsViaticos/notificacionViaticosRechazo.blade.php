<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN GESTIÓN DE VIÁTICOS</title>
</head>

<body>
    <p style="background-color: brown">
    <h1>Hola!.</h1>
    <h3></span> Te informamos que el viaje fue <span class="badge badge-success">RECHAZADO</span>.<br><br>
        <table class="default" align="center" border="1">
            <tr>
                <th># Solicitud</th>
                <th>Ciudad Origen</th>
                <th>Ciudad Destino</th>
                <th>Fecha Salida</th>
                <th>Fecha Retorno</th>
                <th>Observaciones Rechazo</th>
            </tr>
            <tr align="center">
                <td>{{$RegistroSolicitud->idSolicitud}}</td>
                <td>{{$datos->DepOrigen}}</td>
                <td>{{$datos->DepDestino}}</td>
                <td>{{$datos->fechaSalida}}</td>
                <td>{{$datos->fechaRetorno}}</td>
                <td>{{$RegistroSolicitudObs->observaciones}}</td>
            </tr>
        </table><br><br>
        <strong>Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>
    </h3>
    </p>
</body>

</html>