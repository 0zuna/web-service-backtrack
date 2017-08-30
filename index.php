<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
$config['db']['host']   = "127.0.0.1";
$config['db']['user']   = "root";
$config['db']['pass']   = "Gaddp552014";
$config['db']['dbname'] = "monitoreoGa";
$config['db']['charset']= "utf8";
$config['db']['port']	= "3306";

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'] . ";charset=" . $db['charset'] . ";port=" . $db['port'],$db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->options('/{routes:.+}', function ($request, $response, $args) {
	return $response;
});

$app->add(function ($req, $res, $next) {
	$response = $next($req, $res);
	return $response
		->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
		->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


$app->post('/noticias', function (Request $request, Response $response) {
	$noti=json_decode($request->getBody())->dsNoticias->ttNotiMstr[0];
	$Seccion=(int) $noti->iSeccClv;
	$Categoria=(int) $noti->iCateClv;
	$NumeroPagina=$noti->cNotiPagiNum;
	$PaginaPeriodico=(int) $noti->iNotiRealPagiNum;
	$Autor=$noti->cNotiAutoNom;
	$Titulo=$noti->cNotiTituTxt;
	$Encabezado=$noti->cNotiEncaTxt;
	$Texto=$noti->cNotiCuerTxt;
	$Periodico=(int) $noti->iMediClv;
	$Estatus=$noti->cMoniSts;
	$Fecha=$noti->daNotiCaptFec; 
	$Hora=date("H:i:s");
	if (array_key_exists('ttNotiDetl', $noti)) {
		$haruka = array_map(function($obj) { return $obj->cNotiCuerTxt; },$noti->ttNotiDetl);
		$Texto = implode("", $haruka);
	}
	$sakura = $this->db->prepare("insert into noticiasDia(
				Periodico,
				Seccion,
				Categoria,
				NumeroPagina,
				Autor,
				Fecha,
				Hora,
				Titulo,
				Encabezado,
				Texto,
				PaginaPeriodico,
				Foto,
				idCapturista,
				estatus
				) values(
				$Periodico,
				$Seccion,
				$Categoria,
				'$NumeroPagina',
				'$Autor',
				'$Fecha',
				'$Hora',
				'$Titulo',
				'$Encabezado',
				'$Texto',
				'$PaginaPeriodico',
				0,
				1,
				'$Estatus'
			);");
	if (!$sakura->execute()){
		echo "I Hate You : (" . $sentencia->errno . ") " . $sentencia->error;
	}
});
$app->run();
