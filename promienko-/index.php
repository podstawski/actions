<?php

	const TOKENS = __DIR__.'/../tokens';
	const PROMIENKO = 'http://10.11.1.7:8080/smarthome';
	
	$pass=false;
	$contentType='';
	foreach( getallheaders() AS $h=>$header) {
		if (strtolower($h)=='content-type') {
			$contentType=$header;
		}
		if (strtolower($h)=='authorization') {
			$auth_token=trim(str_replace('Bearer','',$header));
			
			foreach(scandir(TOKENS) AS $token) {
				if ($token=='.' || $token=='..')
					continue;
			
				$access_token=json_decode(file_get_contents(TOKENS.'/'.$token),1);
				
				if ($auth_token==$access_token['access_token'])
					$pass=true;
			}
		}
	}
	
	file_put_contents(__DIR__.'/../.log.txt',print_r(['auth:'.$pass,$_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'],$_REQUEST,file_get_contents('php://input'),getallheaders()],1),FILE_APPEND);
	
	echo '<pre>';print_r($_SERVER);
	$script=str_replace('index.php','',$_SERVER['SCRIPT_NAME']);
	$url=str_replace($script,'',$_SERVER['REQUEST_URI'])
	
	$ch = curl_init();
	
	