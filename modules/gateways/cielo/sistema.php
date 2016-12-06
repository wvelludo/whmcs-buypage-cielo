<?php
if (is_numeric($invoiceid) && $invoiceid > 0 && $token == md5($invoiceid . "a294dsd03dtd")) {

	$result = full_query("SELECT * FROM `tblinvoices` WHERE id = " . $invoiceid);
	$data = mysql_fetch_array($result);
	$invoice["id"] 			= $data["id"];
	$invoice["userid"] 		= $data["userid"];
	$invoice["date"] 		= $data["date"];
	$invoice["duedate"] 	= $data["duedate"];
	$invoice["subtotal"] 	= $data["subtotal"];
	$invoice["credit"] 		= $data["credit"];
	$invoice["tax"] 		= $data["tax"];
	$invoice["taxrate"] 	= $data["taxrate"];
	$invoice["total"] 		= $data["total"];
	$invoice["status"] 		= $data["status"];
	$invoice["tid"] 		= $data["notes"];
	$url_cielo				= ($GATEWAY["testmode"] == "on") ? "https://qasecommerce.cielo.com.br/servicos/ecommwsec.do" : "https://ecommerce.cielo.com.br/servicos/ecommwsec.do";
	$url_retorno		 	= ($_SERVER["HTTPS"] == "on") ? "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . "?q=" . $q : "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . "?q=" . $q;
	$base_redir				= str_replace("modules/gateways/cielo/invoice.php","",$_SERVER["PHP_SELF"]);	

	if ($invoice["status"] == "Unpaid") {
					
		if ($op == "erro") {
						
			$codigosretorno = array(
									"0"=>"Transação Criada",
									"1"=>"Transação em Andamento",
									"2"=>"Transação Autenticada",
									"3"=>"Transação não Autenticada",
									"4"=>"Transação Autorizada",
									"5"=>"Transação não Autorizada",
									"6"=>"Transação Capturada",
									"9"=>"Transação Cancelada",
									"10"=>"Transação em Autenticação",
									"12"=>"Transação em Cancelamento"
									);
		
			echo "<center><strong>" . $codigosretorno[$tstat] . "</strong></center>";
		
		} elseif ($op == "captura") {
			
			echo "<center>Aguarde.... Verificando...</center>";
			
			if (strlen($invoice["tid"]) > 0) {
		
				$xml =  "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n".
						"<requisicao-consulta versao=\"1.2.0\" id=\"" . md5(date("YmdHisu")) . "\">\n".
						"<tid>" . $invoice["tid"] . "</tid>\n".
						"<dados-ec>\n".
						"<numero>" . $GATEWAY["estabelecimento"] . "</numero>\n".
						"<chave>" . $GATEWAY["chave"] . "</chave>\n".
						"</dados-ec>\n".
						"</requisicao-consulta>\n";
		
				$sessao_curl = curl_init();
				curl_setopt($sessao_curl, CURLOPT_URL, $url_cielo);
				curl_setopt($sessao_curl, CURLOPT_FAILONERROR, true);
				curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($sessao_curl, CURLOPT_SSLVERSION,1);
				curl_setopt($sessao_curl, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($sessao_curl, CURLOPT_TIMEOUT, 40);
				curl_setopt($sessao_curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($sessao_curl, CURLOPT_POST, true);
				curl_setopt($sessao_curl, CURLOPT_POSTFIELDS, "mensagem=".$xml );
				$result = trim(curl_exec($sessao_curl));
				curl_close($curl);
	
				$XML = simplexml_load_string($result);
				$dados_pedido = "dados-pedido";
				$pedido		  = (string) $XML->$dados_pedido->numero;
				$tid 	   	  = (string) $XML->tid;
				$status		  = (string) $XML->autorizacao->codigo;
		
				if (($status == 4 || $status == 6) && $pedido == $invoiceid) {		
					full_query("update tblinvoices set notes = '' where id = " . $invoice["id"]);		
				    addInvoicePayment($invoice["id"],$invoice["tid"],$invoice["total"],0,"cielo");
					logTransaction("cielo",$result,"Successful");
					echo "<script>window.location='" . $base_redir . "viewinvoice.php?id=" . $invoice["id"] . "'</script>";
				} else {
					echo "<script>window.location='invoice.php?q=" . base64_encode($invoiceid . "&" . md5($invoiceid . "a294dsd03dtd") . "&erro&" . $status) . "'</script>";
				}
			} else {
				echo "erro";
			}

	
		} else {
								
			if (strlen($tipo) > 1) {
	
				$xml =  "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n      ".
						"<requisicao-transacao versao=\"1.2.0\" id=\"" . md5(date("YmdHisu")) . "\">\n      ".
						"<dados-ec>\n      ".
						"<numero>" . $GATEWAY["estabelecimento"] . "</numero>\n      ".
						"<chave>" . $GATEWAY["chave"] . "</chave>\n      ".
						"</dados-ec>\n      ".
						"<dados-pedido>\n      ".
						"<numero>" . $invoice["id"] . "</numero>\n      ".
						"<valor>" . number_format($invoice["total"], 2, '', '') . "</valor>\n      ".
						"<moeda>986</moeda>\n      ".
						"<data-hora>" . date("Y-m-d") . "T" . date("H:i:s") . "</data-hora>\n      ".
						"<idioma>PT</idioma>\n      ".
						"</dados-pedido>\n      ".
						"<forma-pagamento>\n      ".
						"<bandeira>" . $tipo . "</bandeira>\n      ".
						"<produto>1</produto>\n      ".
						"<parcelas>1</parcelas>\n      ".
						"</forma-pagamento>\n      ".
						"<url-retorno>" . $url_retorno . "</url-retorno>\n      ".
						"<autorizar>3</autorizar>\n      ".
						"<capturar>true</capturar>\n      ".
						"</requisicao-transacao>\n      ";
			
				$sessao_curl = curl_init();
				curl_setopt($sessao_curl, CURLOPT_URL, $url_cielo);
				curl_setopt($sessao_curl, CURLOPT_FAILONERROR, true);
				curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($sessao_curl, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($sessao_curl, CURLOPT_TIMEOUT, 40);
				curl_setopt($sessao_curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($sessao_curl, CURLOPT_SSLVERSION,1);
				curl_setopt($sessao_curl, CURLOPT_POST, true);
				curl_setopt($sessao_curl, CURLOPT_VERBOSE, true);
				curl_setopt($sessao_curl, CURLOPT_POSTFIELDS, "mensagem=".utf8_decode($xml));
				$result = trim(curl_exec($sessao_curl));
				curl_close($sessao_curl);
				
				$XML = simplexml_load_string($result);
				$url_redir = "url-autenticacao";
				$url_redir = (string) $XML->$url_redir;
				$tid 	   = (string) $XML->tid;
									
				if (strlen($url_redir) > 1 && strlen($tid) > 1) {
			
					full_query("update tblinvoices set notes = '" . $tid . "' where id = " . $invoiceid);
					echo "<center>Aguarde.... Redirecionando...</center>";
					echo "<script>window.location='" . $url_redir . "'</script>";
		
				} else {
					echo "<center>Aguarde.... Redirecionando...</center>";
					echo "<script>window.location='" . $base_redir . "viewinvoice.php?id=" . $invoiceid . "'</script>";
				}
			} else {
				
				echo "<center><h3>Valor Total: R$ " . $invoice["total"] . "</h3></center>";
				echo "<center><br><strong>Clique abaixo na bandeira de seu cartão para ser redirecionado para a Cielo:</strong><br><br><br>";
				
				if ($GATEWAY["visa"] == "on") echo "<a href=\"invoice.php?q=" . $_GET["q"] . "&tipo=visa\"><img src=\"visa.png\"></a>";
				if ($GATEWAY["master"] == "on") echo "<a href=\"invoice.php?q=" . $_GET["q"] . "&tipo=master\"><img src=\"mastercard.png\"></a>";
				if ($GATEWAY["amex"] == "on") echo "<a href=\"invoice.php?q=" . $_GET["q"] . "&tipo=amex\"><img src=\"americanexpress.png\"></a>";
				if ($GATEWAY["elo"] == "on") echo "<a href=\"invoice.php?q=" . $_GET["q"] . "&tipo=elo\"><img src=\"elo.png\"></a>";
				if ($GATEWAY["diners"] == "on") echo "<a href=\"invoice.php?q=" . $_GET["q"] . "&tipo=diners\"><img src=\"diners.png\"></a>";
				if ($GATEWAY["discover"] == "on") echo "<a href=\"invoice.php?q=" . $_GET["q"] . "&tipo=discover\"><img src=\"discover.png\"></a>";
				if ($GATEWAY["jcb"] == "on") echo "<a href=\"invoice.php?q=" . $_GET["q"] . "&tipo=jcb\"><img src=\"jcb.png\"></a>";
				if ($GATEWAY["aura"] == "on") echo "<a href=\"invoice.php?q=" . $_GET["q"] . "&tipo=aura\"><img src=\"aura.png\"></a>";
				
				echo "</center>";
				
			}
		}		
	} else {
		echo "<center>Aguarde.... Redirecionando...</center>";
		echo "<script>window.location='" . $base_redir . "viewinvoice.php?id=" . $invoiceid . "'</script>";
	}


} else {
	die("Falha de Validacao");
}
?>