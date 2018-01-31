<?php
namespace controllers
{
	
	require_once('Location:../../../php-rest-api/autoload.php');
	require_once('./Config.php');
	require_once('../../../../config/Database.php');

	class Envio
	{
		private $PDO;

		function __construct()
		{
			$dataBase = new \Database();
			$this->PDO = $dataBase->getConnection();
		}

		public function envioUnico()
		{

			global $app;
			$json = json_decode($app->request->getBody());

			$smsFacade = new \SmsFacade($json->{'usuario'}, $json->{'senha'}, 'https://api-rest.zenvia360.com.br');

			$sms = new \Sms();
			$sms->setTo($json->{'to'});
			$sms->setMsg($json->{'msg'});
			$sms->setId($json->{'id'});
			$sms->setCallbackOption(\Sms::CALLBACK_NONE);
			$sms->setaggregateId($json->{'aggregateId'});

			//Formato da data deve obedecer ao padrão descrito na ISO-8601. Exemplo "2015-12-31T09:00:00"
			$sms->setSchedule(date("Y-m-d\TH:i:s.000\Z", strtotime($json->{'schedule'})));

			try{
			    //Envia a mensagem para o webservice e retorna um objeto do tipo SmsResponse com o status da mensagem enviada
			    
			    $response = $smsFacade->send($sms,$sms->getaggregateId());

			    if($response->getStatusCode()!="00"){

			       $app->render('default.php',["data"=>['StatusCode'=>$response->getStatusCode(),
			    									 'StatusDescription'=>"ERRO",
			    									 'DetailCode'=> $response->getDetailCode(),
			    									 'DetailDescription'=> "Mensagem não pode ser enviada"]]);
			    }

			    $this-> gravarSaida($json->{'schedule'},$json->{'to'},$json->{'cpf'},$json->{'msg'},$json->{'aggregateId'},$response->getDetailDescription());

			    $app->render('default.php',["data"=>['StatusCode'=>$response->getStatusCode(),
			    									 'StatusDescription'=>$response->getStatusDescription(),
			    									 'DetailCode'=> $response->getDetailCode(),
			    									 'DetailDescription'=> $response->getDetailDescription()]]);

			}     
			catch(Exception $ex){
			    echo "Falha ao fazer o envio da mensagem. Exceção: ".$ex->getMessage()."\n".$ex->getTraceAsString();
			}
		}

		public function gravarSaida($data,$telefone,$cpf,$mensagem,$centroCusto,$situacao)
		{
			$query = $this->PDO->prepare("INSERT INTO TAB_SAIDA_SMS (SSM_DATA,SSM_TELEFONE,SSM_CPF,SSM_MENSAGEM,SSM_CENTRO_CUSTO,SSM_PROVEDOR,SSM_SITUACAO) VALUES(:data,:telefone,:cpf,:mensagem,:centroCusto,:provedor,:situacao)");
			
			$provedor = "ZENVIA";

			$query-> bindParam(':data',$data);
			$query-> bindParam(':telefone',$telefone);
			$query-> bindParam(':cpf',$cpf);
			$query-> bindParam(':mensagem',$mensagem);
			$query-> bindParam(':centroCusto',$centroCusto);
			$query-> bindParam(':provedor',$provedor);
			$query-> bindParam(':situacao',$situacao);
			
			$query->execute();

		}
	}
}
?>