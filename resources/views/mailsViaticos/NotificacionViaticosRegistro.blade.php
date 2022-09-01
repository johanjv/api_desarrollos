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
        {{$nombreDirectivo}} Le informamos que el viaje # {{$data->idSolicitud}} fue registrado de forma correcta, por favor ingrese al aplicativo de viáticos para realizar la aprobación del viaje.
    </p>
    <table BORDER CELLPADDING=7 CELLSPACING=0>
        <thead style="background-color: #84baa7;">
            <tr>
                <th># Solicitud</th>
                <th>Ciudad Origen</th>
                <th>Ciudad Destino</th>
                <th>Fecha Salida</th>
                <th>Fecha Retorno</th>
            </tr>
        </thead>
        <tbody>
            <tr align="center">
                <td>{{$data->idSolicitud}}</td>
                <td>{{$departamentos->DepOrigen}}</td>
                <td>{{$departamentos->DepDestino}}</td>
                <td>{{$data->fechaSalida}}</td>
                <td>{{$data->fechaRetorno}}</td>
            </tr>
        </tbody>
    </table><br><br>
    <strong>Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>

</body>

</html>