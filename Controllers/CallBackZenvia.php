<?php
	namespace controllers
	{

		require_once('./config/Database.php');
		/**
		* 
		*/
		class CallBackZenvia
		{
			function __construct()
			{
				$dataBase = new \Database();
				$this->PDO = $dataBase->getConnection();
			}

			public function receber()
			{
				global $app;
				$json = json_decode($app->request->getBody());

				

				$query = $this->PDO->prepare("INSERT INTO TAB_ENTRADA_SMS(ESM_DATA,ESM_TELEFONE,ESM_MENSAGEM,ESM_ID_SAIDA)
					VALUES (:data,:telefone,:mensagem,:idSaida)");

				$query -> bindParam(':data', $json ->{'callbackMoRequest'}->{'received'});
				$query -> bindParam(':telefone', $json ->{'callbackMoRequest'}->{'mobile'});
				$query -> bindParam(':mensagem', $json ->{'callbackMoRequest'}->{'body'});
				$query -> bindParam(':idSaida', $json ->{'callbackMoRequest'}->{'correlatedMessageSmsId'});

				$query->execute();

				$app->render('default.php',["Retorno" => "OK"]);
			}
		}
	}


?>