<?php

	const TOKENS = __DIR__.'/../tokens';
	const PROMIENKO = 'http://10.11.1.7:8080';
	
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
	

	$url=PROMIENKO.$_SERVER['REQUEST_URI'];
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
	if (isset($_POST))
		curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
		
	$data=file_get_contents('php://input');
	if ($data)
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: '.$contentType)                                                                       
	);
	
	$response = curl_exec($ch);
	
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = explode("\n",substr($response, 0, $header_size));
	$body = substr($response, $header_size);

	foreach ($header AS $h)
		if (strstr($h,':'))
			Header($h);
	
	die($body);