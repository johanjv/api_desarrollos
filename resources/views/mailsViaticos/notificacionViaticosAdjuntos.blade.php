<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN GESTIÓN DE VIÁTICOS</title>
</head>

<body>
    <h1>Hola!.</h1>
    <p>Le informamos que el viaje ha sido programado de forma correcta, a continuación encontrará las recomendaciones generales de viaje e información
        del transporte, hospedaje y viáticos.
    </p>
    <table BORDER CELLPADDING=7 CELLSPACING=0>
        <thead style="background-color: #84baa7;">
            <tr>
                <th># Solicitud</th>
                <th>Departamento Origen</th>
                <th>Departamento Destino</th>
                <th>Fecha Salida</th>
                <th>Fecha Retorno</th>
                <th>Total Viáticos</th>
                <th>Valor Aeropuerto-Departamento Destino</th>
                <th>Nombre</th>
                <th>Cargo</th>
                <th>Documento</th>
                <th>Código</th>
            </tr>
        </thead>
        <tbody>
            <tr align="center">
                <td>{{$datosTabla->idSolicitud}}</td>
                <td>{{$datosTabla->DepOrigen}}</td>
                <td>{{$datosTabla->DepDestino}}</td>
                <td>{{$datosTabla->fechaSalida}}</td>
                <td>{{$datosTabla->fechaRetorno}}</td>
                <td>{{$totalViaticos}}</td>
                <td>{{ $DOC_COLABORADOR != $docAignacionValorViaticos ? 0 : $totalRecorridos}}</td>
                <td>{{$nombre}}</td>
                <td>{{$cargo}}</td>
                <td>{{$DOC_COLABORADOR}}</td>
                <td>{{$codigo}}</td>
            </tr>
        </tbody>
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