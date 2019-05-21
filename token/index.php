<?php
	const TOKENS = __DIR__.'/../tokens';
	
	file_put_contents(__DIR__.'/../.log.txt',print_r([$_SERVER['REQUEST_URI'],$_REQUEST],1),FILE_APPEND);
	
	
	if (!isset($_REQUEST['client_id']) || !isset($_REQUEST['client_secret']))
		die();
		
		
	function newAccessToken($tokenFile,$access_token) {
		$access_token['access_token']=md5($access_token['token'].rand(1,100000));
		
		
		header('Content-type: application/json');
		$token=[
			'token_type'=>'bearer',
			'access_token'=>$access_token['access_token'],
			'refresh_token'=>$access_token['token'],
			'expires_in'=>3600
		];
		file_put_contents($tokenFile,json_encode($access_token));
		file_put_contents(__DIR__.'/../.log.txt','Access_token: '.print_r($token,1),FILE_APPEND);
		die(json_encode($token));
	}

	if (isset($_REQUEST['grant_type']) && $_REQUEST['grant_type']) {
		switch ($_REQUEST['grant_type']) {
			case 'authorization_code': {
				foreach(scandir(TOKENS) AS $token) {
					if ($token=='.' || $token=='..')
						continue;
				
					$access_token=json_decode(file_get_contents(TOKENS.'/'.$token),1);
					
					if ( isset($_REQUEST['code']) && $access_token['code']==$_REQUEST['code']) {
						newAccessToken(TOKENS.'/'.$token,$access_token);
					}
				}
				break;
				
				
			}
			case 'refresh_token': {
				
				foreach(scandir(TOKENS) AS $token) {
					if ($token=='.' || $token=='..')
						continue;
				
					$access_token=json_decode(file_get_contents(TOKENS.'/'.$token),1);
					
					if ( isset($_REQUEST['refresh_token']) && $access_token['token']==$_REQUEST['refresh_token']) {
						newAccessToken(TOKENS.'/'.$token,$access_token);
					}
				}
				break;
				
			}
		}
	}
