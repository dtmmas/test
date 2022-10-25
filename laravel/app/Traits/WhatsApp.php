<?php

namespace App\Traits;

class WhatsApp
{
	private $basic_token;
	private $number_id;
	private $url_base;
	
	function __construct($token='', $number='')
	{
		$this->basic_token 		= $token;
		$this->number_id 		= $number;
		$this->url_base 		= 'https://graph.facebook.com/v13.0/';
	}

	public function peticion($ruta, $metodo, $data){
		$datos =  $data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url_base.$ruta);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($datos));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: Application/json",
            "Authorization: Bearer ".$this->basic_token
        ));
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $rps =json_decode($response, true);
		$respuesta = array('respuesta' => !(isset($rps['error'])), 'http_code'=> $http_code, 'error' => @$rps['error'] );
		return $respuesta;
	}

	public function SendMsm($cellphone, $name, $nro_factura, $monto){
		$ruta 		= $this->number_id.'/messages';
		$metodo 	= 'POST';

		$data =  [
			"messaging_product" =>"whatsapp",
			"to"=> $cellphone,
			"type"=> "template", 
			"template"=> [ 
				"name"=> "vencimiento_factura", 
				"language"=> [ "code"=> "es" ] ,
				"components"=> [
			        [
			            "type"=> "body",
			            "parameters"=> [
			                [
		                        "type"=> "text",
		                        "text"=> $name
		                    ],
			                [
		                        "type"=> "text",
		                        "text"=> $nro_factura
		                    ],
			                [
		                        "type"=> "text",
		                        "text"=> $monto
		                    ],
						]
					]
				]
			]
		];
		return $respuesta 	= $this->peticion($ruta, $metodo, $data);
	}
}