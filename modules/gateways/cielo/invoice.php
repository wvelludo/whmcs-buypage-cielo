<?php
define("CLIENTAREA",true);
define("FORCESSL",true);

require("../../../init.php");
$whmcs->load_function('gateway');
$whmcs->load_function('client');
$whmcs->load_function('invoice');

$GATEWAY = getGatewayVariables("cielo");
if (!$GATEWAY["type"]) die("Módulo não ativado");

$q2 		= explode("&",base64_decode($q));
$invoiceid 	= $q2[0];
$token		= $q2[1];
$op			= $q2[2];
$tstat		= $q2[3];
$q 			= base64_encode($invoiceid . "&" . md5($invoiceid . "a294dsd03dtd") . "&captura");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="{$charset}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento CIELO</title>

    <!-- Bootstrap -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/font-awesome.min.css" rel="stylesheet">

    <!-- Styling -->
    <link href="/templates/six/css/overrides.css" rel="stylesheet">
    <link href="/templates/six/css/styles.css" rel="stylesheet">
    <link href="/templates/six/css/invoice.css" rel="stylesheet">

</head>
<body>

    <div class="container-fluid invoice-container">

            <div class="row">
                <div class="col-sm-7">
                        <h2>PAGAMENTO</h2>
                    <h3>Fatura #<?php echo $invoiceid; ?></h3>

                </div>
                <div class="col-sm-5 text-center">

                    <div class="invoice-status">

                            <span class="unpaid">EM ABERTO</span>
                     
                    </div>

                </div>
            </div>

            <hr>

            <div class="row">

				<?php require_once("sistema.php"); ?>

            </div>
    </div>
</body>
</html>