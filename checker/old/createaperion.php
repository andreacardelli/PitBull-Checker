<?php
DEFINE('DIR_PITBULL',dirname(dirname(__FILE__)));


$config = json_decode(file_get_contents(DIR_PITBULL.'/config/checks/template/aperion.dasded3.json.template'),true);

$checks=[
	["name"=>"App Informabene","url"=>"https://app.informabene.it"],
	["name"=>"GoGoTerme","url"=>"http://www.gogoterme.com"],
	["name"=>"GoGoFirenze","url"=>"http://www.gogofirenze.it"],
	["name"=>"Quinews","url"=>"http://www.quinewsvaldera.it"],
	["name"=>"Nove da Firenze","url"=>"http://www.nove.firenze.it"],
	["name"=>"CafTours","url"=>"https://www.caftours.com"],
	["name"=>"GoGoAgenda Manage","url"=>"http://manage.gogoagenda.com/login"],
	["name"=>"Informabene Newsletter","url"=>"http://web.thinkermail.com/newsletter/59d356416becde83288b4567/preview"],
	["name"=>"Partylingerie","url"=>"http://www.partylingerie.it"],
	["name"=>"MerloShop","url"=>"http://www.merloshop.com"],
	["name"=>"CupSolidale","url"=>"https://www.cupsolidale.it"],
	["name"=>"ChirurgiaPlasticaEstetica","url"=>"https://www.chirurgia-plastica-estetica.it/"],
	["name"=>"GoGoSconti","url"=>"http://www.gogosconti.it/local/firenze"],
	["name"=>"Sda.Quinews","url"=>"http://sda.quinews.net/admin/index.php?login&ret=%2F"],
	["name"=>"Cartucce","url"=>"https://www.cartucce.com"],
	["name"=>"Aperion.it","url"=>"http://www.aperion.it"]
	


];

// ecco cosa devo cambiare:
foreach ($checks as $value) {
	$new=$config;
	$tochange=[
		"id"=>uniqid(),
		"name"=>$value['name'],
		"frequency"=>2,
		"url"=>$value['url']
	];
	$new['id']=$tochange['id'];
	$new['name']=$tochange['name'];
	$new['frequency']=$tochange['frequency'];
	$new['check']['url']=$tochange['url'];
	$new['check']['success_criteria']=array($config['check']['success_criteria'][0],$config['check']['success_criteria'][1]);
	
	//print_r($new);
	file_put_contents(DIR_PITBULL.'/config/checks/aperion.'.$new['id'].'.json', json_encode($new,JSON_PRETTY_PRINT));
	echo "salvato file....".'aperion.'.$new['id'].'.json'."\n";
}



