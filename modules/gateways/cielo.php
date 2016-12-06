<?php
function cielo_MetaData() {
    return array(
        'DisplayName' => 'Cielo - Buypage Cielo',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}

function cielo_config() {
    $configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"Cielo Pagamentos"),
     "estabelecimento" => array("FriendlyName" => "Código Estabelecimento Cielo", "Type" => "text", "Size" => "50", ),
     "chave" => array("FriendlyName" => "Chave de Acesso Cielo", "Type" => "text", "Size" => "50", ),
     "visa" => array("FriendlyName" => "Visa", "Type" => "yesno", ),
     "master" => array("FriendlyName" => "MasterCard", "Type" => "yesno", ),
     "amex" => array("FriendlyName" => "American Express", "Type" => "yesno", ),
     "elo" => array("FriendlyName" => "Elo", "Type" => "yesno", ),
     "diners" => array("FriendlyName" => "Diners Club", "Type" => "yesno", ),
     "discover" => array("FriendlyName" => "Discover", "Type" => "yesno", ),
     "jcb" => array("FriendlyName" => "JCB", "Type" => "yesno", ),
     "aura" => array("FriendlyName" => "Aura", "Type" => "yesno", ),
     "testmode" => array("FriendlyName" => "Ambiente de Testes", "Type" => "yesno", ),
    );
	return $configarray;
}

function cielo_link($params) {
	
	$q = base64_encode($params['invoiceid'] . "&" . md5($params['invoiceid'] . "a294dsd03dtd"));
	$code = '<input type="button" value="'.$params['langpaynow'].'" onClick="window.location=\''.$params['systemurl'].'/modules/gateways/cielo/invoice.php?q='.$q.'\'" />';
	return $code;
	
}

?>