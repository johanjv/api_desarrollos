<?php

namespace App\Http\Controllers\Consentimientos;

use App\Http\Controllers\Controller;
use App\Mail\MailConsentimiento;
use App\Models\Api_Afiliados_Interna\Afiliados;
use App\Models\Consentimientos\RegistroConsentimiento;
use App\Models\Consentimientos\Servicios;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ConsentimientosController extends Controller
{
    public function enviarLink(Request $request)
    {
        //return $request->all();

        $datosAfiliado = $request->all();
        $datosAfiliado = (object)$datosAfiliado;

        RegistroConsentimiento::with('servicio')->create([ 
            'doc_pac'               => $request['Documento'],
            'servicio_id'           => $request['servicio'],
            'isResponsable'         => strtoupper($request['isResponsable']), 
            'doc_responsable'       => isset($request['responsable_Documento']) ? $request['responsable_Documento'] : null,
            'nombre_responsable'    => isset($request['responsable_Nombre_Completo']) ? strtoupper($request['responsable_Nombre_Completo']) : null,
            'email'                 => strtoupper($request['Email']),
            'parentezco'            => isset($request['parentezco']['parentezco']) ? $request['parentezco']['parentezco'] : null
        ]);
        $consentimiento = RegistroConsentimiento::with('servicio')->latest('id')->first();
        Mail::to($request['Email'])->send(new MailConsentimiento ($datosAfiliado, $consentimiento));

        if (Mail::failures()) {
            $estado = 0;
        }else{
            $estado = 1;
        }

        return response()->json([
            "datosAfiliado"   => $datosAfiliado,
            "estado"   => $estado,
        ], 200);
    }

    public function getConsentimieto(Request $request)
    {

        $sql = RegistroConsentimiento::with('servicio')->where('id', $request['id']);

        $consentimiento = isset($request['clave']) ? $sql->where('doc_pac', $request['clave'])->first() : $sql->first();
        $afiliado = Afiliados::where('Documento', $request['clave'])->first();

        if ($afiliado == null) {
            $afiliado = Afiliados::where('Documento', $consentimiento->doc_pac)->first();
        }

        return response()->json([
            "consentimiento"    => $consentimiento,
            "afiliado"          => $afiliado,
        ], 200);
    }

    public function saveAccionConsentimiento(Request $request)
    {

        RegistroConsentimiento::with('servicio')->where('id', $request['item']['idConsentimiento'])->update([
            'fecha_firma' => date('Y-m-d h:m:s'),
            'firma' => $request['item']['aprobado'],
        ]);

       $consentimiento = RegistroConsentimiento::with('servicio')->where('id', $request['item']['idConsentimiento'])->first();
       $afiliado = Afiliados::where('Documento', $consentimiento->doc_pac)->first();


        return response()->json([
            "consentimiento" => $consentimiento,
            "afiliado" => $afiliado,
        ], 200);
    }

    public function getConsentimietoUser(Request $request)
    {
        $consentimiento = RegistroConsentimiento::with('servicio')->where('doc_pac', $request['clave'])->orderBy('fecha_envio', 'DESC')->get();

        $consentimiento->map(function ($item){
            $item->afiliado = Afiliados::where('Documento', $item->doc_pac)->pluck('Nombre_Completo')->first();
        });

        $afiliado = Afiliados::where('Documento', $request['clave'])->first();

        $dataConsentimiento = (object) [
            "consentimiento"    => $consentimiento,
            "afiliado"          => $afiliado,
        ];

        return response()->json([
            "dataConsentimiento"    => $dataConsentimiento,
        ], 200);

    }

    public function validarConsentimiento(Request $request)
    {

        RegistroConsentimiento::with('servicio')->where('id', $request['item']['idConsentimiento'])->update([
            'fecha_firma' => date('Y-m-d h:m:s'),
            'verificado' => 1,
            'doc_prof_verifico' => Auth::user()->nro_doc
        ]);
    }

    public function getConsentimientosValidados(Request $request)
    {
        $consentimientos = RegistroConsentimiento::with('servicio')->where('doc_prof_verifico', Auth::user()->nro_doc)->orderBy('fecha_envio', 'DESC')->get();

        $consentimientos->map(function ($item){
            $item->afiliado = Afiliados::where('Documento', $item->doc_pac)->pluck('Nombre_Completo')->first();
        });

        return response()->json([
            "consentimientos"    => $consentimientos
        ], 200);
    }

    public function imprimir(Request $request)
    {

        $extension = 'pdf';
        $archivo = PDF::loadView('Pdfs.consentimientos.ejemplo',)->output();


        switch ($extension) {
            case 'jpeg':
                $headers = array('Content-Type: image/jpeg');
                break;
            case 'png':
                $headers = array('Content-Type: image/png');
                break;
            case 'doc':
                $headers = array('Content-Type: application/msword');
                break;
            case 'docx':
                $headers = array('Content-Type:application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                break;
            case 'xls':
                $headers = array('Content-Type: application/vnd.ms-excel');
                break;
            case 'xlsx':
                $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                break;
            case 'zip':
                $headers = array('Content-Type: application/zip');
                break;
            case 'rar':
                $headers = array('Content-Type: application/x-rar-compressed');
                break;
            case 'ppt':
                $headers = array('Content-Type: application/vnd.ms-powerpoint');
                break;
            case 'pptx':
                $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
                break;
            case 'pdf':
            case 'PDF':
            case 'Pdf':
                $headers = array('Content-Type: application/x-pdf');
                break;
            case 'gif':
                $headers = array('Content-Type: image/gif');
                break;
            case 'csv':
                $headers = array('Content-Type: text/csv');
                break;
            default:
                # code...
                break;
        }

        return response()->download($archivo, 'filename.pdf', $headers);
    }

    public function getServicios(Request $request)
    {
        $servicios = Servicios::where('tipo', $request['poblacion'])->get();

        return response()->json([
            "servicios"    => $servicios
        ], 200);
    }
    
    



}
