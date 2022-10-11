<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>NOTIFICACIÓN CONSENTIMIENTO INFORMADO</title>
</head>

<body style="width: 25%;">
<div>
    <table>
    <tr>
        <td style="text-align: center; padding: 0.3em; text-align: -webkit-center;"">
            <img src="https://www.virreysolisips.com/repositorio/logoEmail.png" alt="MxToolbox Logo" style="padding:0px;border:0px;display:block;height:250px">
        </td>
    </tr>
    </table>
    <br>
    <div style="width: 1024px; color:#444;display:block;font-family:Georgia, Times New Roman, Times, serif;font-size:22px;line-height:100%;text-align:justify" align="center">
        <h5 style="color:#444;display:block;font-family:Georgia, Times New Roman, Times, serif;font-size:22px;line-height:100%;text-align:justify;margin:0 0 10px" align="center">
            Cordial saludo Sr(a). {{ $datosAfiliado->Nombre_Completo }}.<br><br>
        </h5>

        Ya se encuentra disponible el Consentimiento Informado para su consulta virtual.<br>
        Por favor ingrese en el siguiente enlace para verificarlo y especificar si ACEPTA o RECHAZA las condiciones de la atención.
        <br><br>
            Su contraseña para visualizar el archivo es su número de documento de identidad.
        <br><br>
            <h1 style="color:#444;display:block;font-family:Georgia, Times New Roman, Times, serif;font-size:18px;line-height:100%;text-align:center;margin:0 0 10px" align="center">
            <br><a href="https://consentimientos.virreysolisips.com.co:1557/consentimiento/?item={{$consentimiento->id}}">Haz clic AQUÍ para visualizarlo (Consentimiento Informado)</a>
            </h1>
        <br>
        <div style="background: #8080801c">
            <p style="width: 1024px; color:#444;display:block;font-family:Georgia, Times New Roman, Times, serif;font-size:16px;line-height:100%;text-align:justify" align="center">
                Este correo y cualquiera de sus anexos contienen datos confidenciales que serán para uso exclusivo de sus destinatarios, quienes se responsabilizarán de su custodia. Queda prohibido compartir o copiar 
                estos datos a terceros. Antes de imprimir piense en el medio ambiente.
            </p>
        
            <p style="font-weight: bold; font-style: italic; width: 1024px; color:#444;display:block;font-family:Georgia, Times New Roman, Times, serif;font-size:16px;line-height:100%;text-align:justify" align="center">
                Si usted recibe esta información por error, favor, informar al remitente y destruya el mensaje con todas sus copias.
            <p/>
        </div>
    </div>
</body>

</html>