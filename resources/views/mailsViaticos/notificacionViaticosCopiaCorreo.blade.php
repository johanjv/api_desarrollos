<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN GESTIÓN DE VIÁTICOS</title>
</head>

<body>
    <h1>Hola!.</h1>
    <p>Le informamos que la solicitud ha sido programado de forma correcta, a continuación encontrará la información de los colaboradores que viajan.
    </p>
    <table BORDER CELLPADDING=7 CELLSPACING=0>
        <thead style="background-color: #84baa7;">
            <tr>
                <th># Solicitud</th>
                <th>Ciudad Origen</th>
                <th>Ciudad Destino</th>
                <th>Fecha Salida</th>
                <th>Fecha Retorno</th>
                <th>Nombre Colaborador</th>
                <th>Cargo Colaborador</th>
                <th>Código Colaborador</th>
                <th>Valor Aeropuerto-Ciudad Destino</th>
                <th>Total Viáticos</th>

            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($datosCopiaCorreo as $value) {
            ?>
                <tr align="center">

                    <td>{{$datosTabla->idSolicitud}}</td>
                    <td>{{$datosTabla->DepOrigen}}</td>
                    <td>{{$datosTabla->DepDestino}}</td>
                    <td>{{$datosTabla->fechaSalida}}</td>
                    <td>{{$datosTabla->fechaRetorno}}</td>
                    <td>{{$value->NOMB_COLABORADOR}}</td>
                    <td>{{$value->NOMBRE_CARGO}}</td>
                    <td>{{$value->COD_CARGO}}</td>
                    <td>{{$value->totalRecorridos}}</td>
                    <td>{{$value->valorTotalRecorrido}}</td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <p>Por favor revisar los archivos adjuntos que contienen la información de transporte y/o hospedaje.
    </p>
    <strong>Este mensaje es una notificación automática, por lo tanto le solicitamos no responder a esta dirección.</strong>

</body>

</html>