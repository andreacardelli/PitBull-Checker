<?php


$config = json_decode(file_get_contents('/home/pi/www/pitbullcheck/config/checks/template/aperion.dasded3.json.template'),true);

// ecco cosa devo cambiare:
for ($i=0; $i <30 ; $i++) { 
	$new=$config;
	$tochange=[
		"id"=>uniqid(),
		"name"=>"Hosting".rand(10,50),
		"frequency"=>rand(1,10),
		"url"=>"http://www.cardelli.info/testparrallel.php?wait=".rand(1,7)
	];
	$new['id']=$tochange['id'];
	$new['name']=$tochange['name'];
	$new['frequency']=$tochange['frequency'];
	$new['check']['url']=$tochange['url'];
	$new['check']['success_criteria']=array($config['check']['success_criteria'][0],$config['check']['success_criteria'][rand(1,3)]);
	
	file_put_contents('/home/pi/www/pitbullcheck/config/checks/aperion.'.$new['id'].'.json', json_encode($new,JSON_PRETTY_PRINT));
	echo "salvato file....".'aperion.'.$new['id'].'.json'."\n";
}



