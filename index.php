<?php

	$loader = require 'vendor/autoload.php';

	$app = new \Slim\Slim(array(
		'templates.path' => 'templates'
	));

	//envio de sms unitário para zenvia
	$app->post('/zenvia/unitario/',function() use($app){

		(new \controllers\EnvioZenvia($app))->envioUnico();
	});

	//call back de mensagens respondidas
	$app->post('/zenvia/callback/',function() use($app){

		(new \controllers\CallBackZenvia($app))->receber();
	});

	$app->run();
?>