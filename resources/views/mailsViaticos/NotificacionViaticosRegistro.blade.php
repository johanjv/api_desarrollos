<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN GESTIÓN DE VIÁTICOS</title>
</head>

<body>
    <h1>Hola!.</h1>
    <p>
        {{$nombreDirectivo}} Te informamos que el viaje # {{$data->idSolicitud}} fue registrado de forma correcta, por favor ingrese al aplicativo de viáticos para realizar la aprobación del viaje.
    </p>
    <table class="default" align="center" border="1">
        <tr>
            <th># Solicitud</th>
            <th>Ciudad Origen</th>
            <th>Ciudad Destino</th>
            <th>Fecha Salida</th>
            <th>Fecha Retorno</th>
        </tr>
        <tr align="center">
            <td>{{$data->idSolicitud}}</td>
            <td>{{$departamentos->DepOrigen}}</td>
            <td>{{$departamentos->DepDestino}}</td>
            <td>{{$data->fechaSalida}}</td>
            <td>{{$data->fechaRetorno}}</td>
        </tr>
    </table><br><br>
    <strong>Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>

</body>

</html>