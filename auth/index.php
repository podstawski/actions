<?php
	file_put_contents(__DIR__.'/../.log.txt',print_r([$_SERVER['REQUEST_URI'],$_REQUEST],1),FILE_APPEND);
	
	$code=md5(time());
	$refresh_token = md5($code.rand(0,2000000));
	
	if (isset($_POST) && isset($_POST['username']) && isset($_POST['password']) ) {
		
		$users=json_decode(file_get_contents(__DIR__.'/.users.json'),1);
		
		foreach ($users AS $user) {
			if ($user['username'] == $_POST['username'] && $user['password'] == $_POST['password']) {
				$data=[
					'code'=>$code,
					'token'=>$refresh_token
				];
				file_put_contents(__DIR__."/../tokens/.".$refresh_token,json_encode($data));
				$url=$_REQUEST['redirect_uri'].'?code='.$code.'&state='.$_REQUEST['state'].'&response_type='.$_REQUEST['response_type'];
				Header('Location: '.$url);
				file_put_contents(__DIR__.'/../.log.txt','Redirect: '.$url."\n",FILE_APPEND);
	
				die();
			}
			
		}
		
		
	}
	
	
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Login to action</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<link href='//fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
	
	<style>
		body {
			font-family: 'Ubuntu', sans-serif;
			font-size:14px;
			color: #131313;
			background: #f3f3f3;
			padding: 0;
			margin: 0;
		}
		form {
			margin-top: 2em;
		}
		
		form .row {
			margin-top: 2em;
		}
	</style>
	
</head>
<body>
<form method="post" class="container">
	<?php foreach ($_REQUEST AS $k=>$v) {?>
	<input type="hidden" name="<?php echo $k;?>" value="<?php echo $v;?>"/>
	<?php } ?>
	
	<input type="hidden" name="code" value="<?php echo $code;?>"/>
	<input type="hidden" name="refresh_token" value="<?php echo $refresh_token;?>"/>
	<div class="row">
		<div class="col-sm-1">Username:</div>
		<div class="col-sm-1"><input type="text" name="username"/></div>
	</div>
	<div class="row">
		<div class="col-sm-1">Password: </div>
		<div class="col-sm-1"><input type="password" name="password"/></div>
	</div>
	
	<div class="row">
		<div class="col-sm-2"><input type="submit" value="Login"/></div>
	</div>
	
</form>
</body>
</html>