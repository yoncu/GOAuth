<?php
// API Documents: https://www.yoncu.com/API/
define('GOOGLE_TOKEN',[
	'ClientID'	=> '123121212121-ghtrhrthtrhtrhtrhtrhtthrhrh.apps.googleusercontent.com',
	'ClientSecret'	=> 'GFGFGGFG-gfgfgffgfgfgfgffgfggf',
	'RedirectUri'	=> 'https://'.$_SERVER["HTTP_HOST"].'/'.trim($_SERVER['SCRIPT_NAME'],'/'),
	'ScopeList'	=> 'openid profile email https://www.googleapis.com/auth/drive'
]);
function GOAuth($Action,$Data){
	$Curl = curl_init('https://www.yoncu.com/API/Tools/GOAuth/'.$Action);
	curl_setopt_array($Curl,[
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_HEADER		=> false,
	    	CURLOPT_HTTPHEADER	=> ['Accept: application/json'],
		CURLOPT_CUSTOMREQUEST	=> 'POST',
		CURLOPT_POST		=> true,
		CURLOPT_POSTFIELDS	=> json_encode(array_merge($Data,GOOGLE_TOKEN)),
	]);
	$Response	= curl_exec($Curl);
	$HttpCode	= curl_getinfo($Curl,CURLINFO_HTTP_CODE);
	curl_close($Curl);
	if($Json=json_decode($Response,true)){
		return $Json;
	}else{
		return [false,'Blank Data'];
	}
}
if(isset($_COOKIE['GOAuth']) and md5(GOOGLE_TOKEN['ClientSecret'].substr($_COOKIE['GOAuth'],32)) == substr($_COOKIE['GOAuth'],0,32)){
	PrintInfo:
	setcookie('GOAuth',$_COOKIE['GOAuth']);
	list($Status,$Info)	= GOAuth('TokenGet',['Email'=>substr($_COOKIE['GOAuth'],32)]);
	echo 'E-Mail:<br>'.substr($_COOKIE['GOAuth'],32);
	echo '<br><br>';
	echo 'Token:<br>'.$Info;
}elseif(isset($_REQUEST['code'])){
	list($Status,$Info)	= GOAuth('LoginCode',['Code'=>$_REQUEST['code']]);
	if($Status){
		$_COOKIE['GOAuth']=md5(GOOGLE_TOKEN['ClientSecret'].$Info['email']).$Info['email'];
		Goto PrintInfo;
	}else{
		echo 'Error: '.$Info;
	}
}else{
	list($Status,$Info)	= GOAuth('LoginURL',[]);
	if($Status){
		header("location: ".$Info['URL']);exit;
	}else{
		echo 'Error: '.$Info;
	}
}
