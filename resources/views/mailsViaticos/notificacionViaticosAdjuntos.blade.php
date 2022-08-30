<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN GESTIÓN DE VIÁTICOS</title>
</head>

<body>
    <h1>Hola!.</h1>
    <p>Te informamos que el viaje ha sido programado de forma correcta, a continuación, encontraras las recomendaciones generales de viaje e información
        del transportye, hospedaje y viáticos.
    </p>
    <table class="default" align="center" border="1">
        <tr>
            <th># Solicitud</th>
            <th>Ciudad Origen</th>
            <th>Ciudad Destino</th>
            <th>Fecha Salida</th>
            <th>Fecha Retorno</th>
            <th>Total Viáticos</th>
            <th>A quién se le asigne Aeropuerto-Ciudad Destino y Ciudad Destino-Aeropuerto:</th>
        </tr>
        <tr align="center">
            <td>{{$datosTabla->idSolicitud}}</td>
            <td>{{$datosTabla->DepOrigen}}</td>
            <td>{{$datosTabla->DepDestino}}</td>
            <td>{{$datosTabla->fechaSalida}}</td>
            <td>{{$datosTabla->fechaRetorno}}</td>
            <td>{{$totalViaticos}}</td>
            <td>{{$totalRecorridos}}</td>
        </tr>
    </table>
    <ol>
        <li>Para los traslados aéreos realizar check in de acuerdo a políticas de la aerolinea.</li>
        <li>Los traslados terrestres son coordinados de acuerdo a necesidades del servicio, por favor no modificar la ruta del conductor.</li>
        <li>La reserva del hospedaje incluye desayuno.</li>
        <li>Los viáticos serán transferidos a la cuenta de nómina por su empleador en un tiempo de 3 días hábiles.</li>
    </ol>
    <p>Por favor revisar los archivos adjuntos que contienen la información de transporte y/o hospedaje.
    </p>
    <strong>Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>

</body>

</html>