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
    <p>
        Le informamos que el viaje # {{$RegistroSolicitud->idSolicitud}} fue <span class="badge badge-success">APROBADO</span>.<br><br>
    </p>
    <table BORDER CELLPADDING=7 CELLSPACING=0>
        <thead style="background-color: #84baa7;">
            <tr>
                <th># Solicitud</th>
                <th>Departamento Origen</th>
                <th>Departamento Destino</th>
                <th>Fecha Salida</th>
                <th>Fecha Retorno</th>
            </tr>
        </thead>
        <tbody>
            <tr align="center">
                <td>{{$RegistroSolicitud->idSolicitud}}</td>
                <td>{{$datos->DepOrigen}}</td>
                <td>{{$datos->DepDestino}}</td>
                <td>{{$datos->fechaSalida}}</td>
                <td>{{$datos->fechaRetorno}}</td>
            </tr>
        </tbody>
    </table><br><br>
    <strong>Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>
    </p>
</body>

</html>