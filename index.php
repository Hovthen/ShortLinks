<?php 

$UrlRequest = $_SERVER["REQUEST_URI"];
if( $_GET['debug'] == 'hovthen' ){
	$debug = true;
	$UrlRequest = str_replace('?debug=hovthen',null,$UrlRequest);
}
$DataT = DataRe($UrlRequest);
$DataT = url($DataT);
$DataT = DataRe($DataT);

if( $debug != true ){
	$host = host($DataT);
	$DataM = $host['self']['data'];
	switch ($host['self']['type']){
	case 'link':
		if( $host['self']['code'] == true ){
			Header('HTTP/1.1 302 Moved Temporarily');
			Header("Location:$DataM");
		}else if( $host['allow']['code'] == true && $host['block']['code'] == false ){
			Header('HTTP/1.1 302 Moved Temporarily');
			Header("Location:$DataM");
		}else if( $host['friend']['code'] == true && $host['block']['code'] == false ){
			Header('HTTP/1.1 302 Moved Temporarily');
			Header("Location:$DataM");
		}else if( $host['block']['code'] == true ){
			$Template = EnvFile('Template','link');
			if( !empty($Template) ){
				$Template = str_replace('[URL]',$DataM,$Template);
				$Template = str_replace('[button]','link-button',$Template);
				$Template = str_replace('[Info]','已证实该链接存在潜在的安全风险或不适内容，请谨慎访问。',$Template);
				Header('Content-type:text/html;charset=utf-8');
				echo $Template;
			}else{
				Header('HTTP/1.1 302 Moved Temporarily');
				Header("Location:https://support.qq.com/embed/phone/286265/link-jump?jump=$DataM");
			}
		}else{
			$Template = EnvFile('Template','link');
			if( !empty($Template) ){
				$Template = str_replace('[URL]',$DataM,$Template);
				$Template = str_replace('[button]','button',$Template);
				$Template = str_replace('[Info]','您请求的链接可能包含未知的安全风险，请注意保护个人隐私和财产安全。',$Template);
				Header('Content-type:text/html;charset=utf-8');
				echo $Template;
			}else{
				Header('HTTP/1.1 302 Moved Temporarily');
				Header("Location:https://support.qq.com/embed/phone/286265/link-jump?jump=$DataM");
			}
		}
		break;
	case 'text':
		$Template = EnvFile('Template','text');
		if( !empty($Template) ){
			$Template = str_replace('[Text]',$DataM,$Template);
			if( !empty($Template) ){
				$Template = str_replace('[Tips]','Tips：'.$Tips,$Template);
			}else{
				$Template = str_replace('[Tips]',null,$Template);
			}
			Header('Content-type:text/html;charset=utf-8');
			echo $Template;
		}else{
		}
		break;
	case 'image':
		Header('HTTP/1.1 302 Moved Temporarily');
		Header("Location:$DataM");
		break;
	default:
		Header('HTTP/1.1 302 Moved Temporarily');
		Header("Location:https://support.qq.com/embed/phone/286265/link-jump?jump=$DataM");
	}
}else{
	$DebugData = array(
		"Data" => array(
			"Request" => $UrlRequest,
			"Type" => host($DataT)['self']['type'],
			"content" => host($DataT)['self']['data']
		),
		//*
		"Debug" => array(
			"Host" => host($DataT),
			"Host" => host($DataT),
		),
		//*/
		"Time" => date("y/m/d H:s:m")
	);
	Header('Content-type:text/js;charset=utf-8');
	echo str_replace('\/','/',json_encode($DebugData, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}
		

function url($UrlRequest=null){
	preg_match("/\/([^_\/\?]*)([_]?)([^\/\?]*)([\/]?)([^_\/\?]*)([_]?)([^\/\?]*)([\/]?)([^\?]*)([\?]?)([\S]*)/",$UrlRequest,$URL);
	$URL = array(
		0 => $URL[0], // /1_3/5_7/9?11 
		1 => urldecode( strtolower($URL[1]) ),
		2 => $URL[2],
		3 => urldecode( strtolower($URL[3]) ),
		4 => $URL[4],
		5 => urldecode( $URL[5] ),
		6 => $URL[6],
		7 => urldecode( $URL[7] ),
		8 => $URL[8],
		9 => urldecode( $URL[9] ),
		10 => $URL[10],
		11 => $URL[11]
	);
	$URLs = array(
		/* 1_3/5_7/9? */ 0 => $URL[1].$URL[2].$URL[3].$URL[4].$URL[5].$URL[6].$URL[7].$URL[8].$URL[9].$URL[10],
		/* 1_3         */ 1 => $URL[1].$URL[2].$URL[3],
		/* 5_7         */ 2 => $URL[5].$URL[6].$URL[7],
		/* /9          */ 3 => $URL[8].$URL[9],
		/* /5_7/9     */ 4 => $URL[4].$URL[5].$URL[6].$URL[7].$URL[8].$URL[9],
		/* ?11         */ 5 => $URL[10].$URL[11]
	);	
	
	if( empty($URL[1]) ){
		$DataT = Env('link','index','index').$URLs[4].$URLs[5];
	}else if( !empty( Env('link','index',$URLs[1]) ) ){
		$DataT = Env('link','index',$URLs[1]).$URLs[4].$URLs[5];
	}else{
		switch ($URL[1]){
		case 'img':
			if( empty($URL[3]) && empty($URL[5]) ){
				$DataT = "https://libs.hovthen.com";
			}else if( $URL[3] == "gravatar" || $URL[3] == "mail" || $URL[3] == "typecho" || $URL[3] == "wordpress" ){
				if( !empty( Env('cdn','gravatar',$URL[9]) ) && !empty($URL[9]) ){
					$DataT = Env('cdn','gravatar',$URL[9]).md5(strtolower(trim($URLs[2]))).$URLs[5];
				}else{
					$DataT = Env('cdn','gravatar','index').md5(strtolower(trim($URLs[2]))).$URLs[5];
				}
			}else{
				switch ($URL[3]){
				case 'qq':
					$suffix = (empty($URL[11]))?"140":$URL[11];
					if( empty($URL[5]) || $URL[5] == "me" || $URL[5] == "hovthen" ){
						$DataT = "http://thirdqq.qlogo.cn/g?b=oidb&k=qWak3lKWHOxtt19bPUkmIw&s=".$suffix;
					}else{
						$DataT = "https://q.qlogo.cn/g?b=qq&nk=".$URL[5]."&s=".$suffix;
					}
					break;
				default:
					$DataT = "https://libs.hovthen.com".$URL[0];
					Header("Location:$DataT");
				}
			}
			break;
		case 'libs':
			$DataT = "https://libs.hovthen.com".$URLs[4].$URLs[5];
			break;
		case 'qq':
			if( empty($URL[3]) && empty($URL[5]) ){
				$DataT = "https://libs.hovthen.com";
			}else if( $URL[3] == "user" ){
				switch ($URL[5]){
				case 'head':
					$suffix = (empty($URL[11]))?"140":$URL[11];
					if( empty($URL[7]) || $URL[7] == "me" || $URL[7] == "hovthen" ){
						$DataT = "http://thirdqq.qlogo.cn/g?b=oidb&k=qWak3lKWHOxtt19bPUkmIw&s=".$suffix;
					}else{
						$DataP[0] = file_get_contents("https://ptlogin2.qq.com/getface?&imgtype=1&uin=".$URL[7]);
						preg_match("/\&k=([^\&]*)([\&]?)/",$DataP[0],$DataP[1]);
						$DataT = "http://thirdqq.qlogo.cn/g?b=oidb&k=".$DataP[1][1]."&s=".$suffix;
					}
					break;
				case 'key':
					$DataP[0] = file_get_contents("https://ptlogin2.qq.com/getface?&imgtype=1&uin=".$URL[7]);
					preg_match("/\&k=([^\&]*)([\&]?)/",$DataP[0],$DataP[1]);
					$DataT = "text://".$DataP[1][1];
					break;
				default:
					$DataT = "https://libs.hovthen.com".$URL[0];
					Header("Location:$DataT");
				}
			}else{
				switch ($URL[3]){
				case 'user':
					$suffix = (empty($URL[11]))?"140":$URL[11];
					if( empty($URL[5]) || $URL[5] == "me" || $URL[5] == "hovthen" ){
						$DataT = "http://thirdqq.qlogo.cn/g?b=oidb&k=qWak3lKWHOxtt19bPUkmIw&s=".$suffix;
					}else{
						$DataT = "https://q.qlogo.cn/g?b=qq&nk=".$URL[5]."&s=".$suffix;
					}
					break;
				default:
					$DataT = "https://libs.hovthen.com".$URL[0];
					Header("Location:$DataT");
				}
			}
			break;
		case 'sms':
			$DataEnv = Env('link','sms',null);
			if( !empty($URL[3]) && count($DataEnv) >= 1 ){
				$DataT = $DataEnv[mt_rand(0,count($DataEnv))];
			}else{
				$DataT = "https://www.hovthen.com/113".$URLs[5];
			}
			break;
		case 'weixin':
			if( empty($URL[3]) || $URL[3] == "qr" ){
				$DataT = base64_decode($URL[5]);
			}else if( $URL[3] == "link" ){
				$DataT = 'https://servicewechat.com/wxascheme/jump_wxa?url='.$URLs[2].$URL[8].$URLs[3].$URLs[5];
			}else if( $URL[3] == "miniapp" ){
				$Tips = "由于微信限制，无法直接打开小程序。请复制文本后打开任一聊天窗口发送，点击发送内容即可！";
				$DataT = '#小程序://'.$URLs[2].$URLs[3].$URLs[5];
			}else{
				$DataT = base64_decode($URL[5]);
			}
			break;
		case 'base64':
			if( empty($URL[3]) || $URL[3] == "decode" || $URL[3] == "de" ){
				$DataT = base64_decode($URL[5]);
			}else if( $URL[3] == "encode" || $URL[3] == "en" ){
				$DataT = base64_encode($URL[5]);
			}else{
				$DataT = base64_decode($URL[5]);
			}
			break;
		case 'mail':
			if( empty($URL[3]) && empty($URL[5]) ){
				$DataT = "http://mail.hovthen.com";
					}else if( $URL[3] == "me" || $URL[3] == "hovthen" || $URL[3] == "mine" || $URL[3] == "my" || $URL[3] == "dear" || $URL[3] == "hi" ){
				if( !empty( Env('mail','user',$URL[5]) ) && !empty( Env('mail','host',$URL[5]) ) ){
					$DataT = 'mailto:'.Env('mail','user',$URL[5])."@".Env('mail','host',$URL[5]).$URLs[5];
				}else{
					$DataT = "mailto:me@hovthen.com".$URLs[5];
				}
			}else if( $URL[3] == "link" ){
				if( !empty( Env('mail','link',$URL[5]) ) ){
					$DataT = "https://".Env('mail','user',$URL[5]).$URLs[5];
				}else if( !empty( Env('mail','host',$URL[5]) ) ){
					$DataT = "https://mail.".Env('mail','host',$URL[5]).$URLs[5];
				}else{
					$DataT = "https://mail.hovthen.com".$URLs[5];
				}
			}else{
				if( !empty( Env('mail','host',$URL[5]) ) ){
					$DataT = 'mailto:'.$URL[3]."@".Env('mail','host',$URL[5]).$URLs[5];
				}else{
					$DataT = 'mailto:'.$URL[3]."@".$URL[5].$URLs[5];
				}
			}
			break;
		case 'lab':
			if( empty($URL[3]) ){
				$DataT = "https://lab.hovthen.com/".$URL[5].$URLs[5];
			}else if( $URL[3] == "page" ){
				$DataT = "https://lab.hovthen.com/page/".$URL[5].$URLs[5];
			}else if( $URL[3] == "tool" ){
				$DataT = "https://lab.hovthen.com/tool/".$URL[5].$URLs[5];
			}else{
				$DataT = "https://lab.hovthen.com/".$URLs[4].$URLs[5];
			}
			break;
		case 'github':
			if( empty($URL[3]) || $URL[3] == "link" || $URL[3] == "url" || $URL[3] == "href" ){
				$DataT = "https://github.com".$URLs[4].$URLs[5];
			}else if( $URL[3] == "down" ){
			
			}else{
			
			}
			break;
		case 'url':
			$DataT = $URLs[2].$URLs[3].$URLs[5];
			break;
		case 'blog':
			$DataT = "https://blog.hovthen.com".$URLs[4].$URLs[5];
			break;
		case 'boke':
			$DataT = "https://boke.hovthen.com".$URLs[4].$URLs[5];
			break;
		case 'forum':
			$DataT = "https://forum.hovthen.com".$URLs[4].$URLs[5];
			break;
		default:
			$DataT = Env('link','index','index').$URLs[4].$URLs[5];
		}
	}
	return $DataT;
}

function host($DataT=null){
	$Env = Env('host',null,null);
	// IPv6 [:FFF:] => [#FFF#]
	$DataD[0] = $DataT;
	preg_match("/([\[])([^\]?]*)([\]]?)/",$DataD[0],$DataD[1]);
	$DataD[2] = str_replace(':','#',$DataD[1]);
	$DataD[3] = str_replace($DataD[1],$DataD[2],$DataD[0]);
	
	preg_match("/(https|http|ftp|[^:]*):([\/]*)([^\/:?]*)([:]?)([\d]*)([\/?]?)/",$DataD[3],$DataN[0]);
	
	if( $DataN[0][1] == "https" || $DataN[0][1] == "http" ){
		foreach ($Env as $name => $item) {
			$host = implode('|',$item);
			preg_match('/([\/]*)([^\/:?]*)([:\/?]?)/',$DataN[0][3],$DataN[3]);
			preg_match('/([\S]?)('.$host.')([\S]?)/',$DataN[3][2],$DataN[1]);
			preg_match('/([\.\/]?)('.$host.')([\/:?]?)/',$DataN[3][2],$DataN[2]);
			$Return[$name] = array(
				"code" => ( $DataN[1] == $DataN[2] && !empty($DataN[1]) && !empty($DataN[2]) ) ? true : false,
				"type" => "link",
				"data" => $DataT,
				"full" =>  str_replace('#',':',$DataN[0][0]),
				"host" =>  str_replace('#',':',$DataN[2][2]),
				"domain" => str_replace('#',':',$DataN[3][2]),
				"port" => $DataN[0][5]
			);
		};
	}else if( $DataN[0][1] == "mailto" || $DataN[0][1] == "mail" ){
		foreach ($Env as $name => $item) {
			$host = implode('|',$item);
			preg_match('/([^\@]*)@([^\/:?]*)([:\/\]?]?)/',$DataN[0][3],$DataN[3]);
			preg_match('/([\[|\S]?)('.$host.')([\]|\S]?)/',$DataN[3][2],$DataN[1]);
			preg_match('/([\[|\.@]?)('.$host.')([\]|\/:?]?)/',$DataN[3][2],$DataN[2]);
			$Return[$name] = array(
				"code" => ( $DataN[1] == $DataN[2] && !empty($DataN[1]) && !empty($DataN[2]) ) ? true : false,
				"type" => "mail",
				"data" => $DataT,
				"full" =>  str_replace('#',':',$DataN[0][0]),
				"host" =>  str_replace('#',':',$DataN[2][2]),
				"domain" => str_replace('#',':',$DataN[3][2]),
				"port" => $DataN[0][5],
				"user" => $DataN[3][1],
				"debug" => array(
					"DataD" => $DataD[3],
					"DataN" => $DataN
				),
			);
		};
	}else if( $DataN[0][1] == "text" || $DataN[0][1] == "#小程序" || $DataN[0][1] == "weixin_miniapp"){
		$EnvReplace = Env('replace','text',null);
		$DataM = $DataT;
		foreach ($EnvReplace as $name => $item) {
			$DataM = str_replace($name,$item,$DataM);
		};
		$Return['self'] = array(
			"code" => false,
			"type" => "text",
			"data" => $DataM,
			"debug" => array(
			),
		);
	}else if( $DataN[0][1] == "image" || $DataN[0][1] == "picture"){
		$EnvReplace = Env('replace','image',null);
		$DataM = $DataT;
		foreach ($EnvReplace as $name => $item) {
			$DataM = str_replace($name,$item,$DataM);
		};
		$Return['self'] = array(
			"code" => false,
			"type" => "image",
			"data" => $DataM,
			"debug" => array(
			),
		);
	}else{
		$Return["self"] = array(
			"code" => null,
			"type" => null,
			"data" => $DataT,
			"debug" => array(
				"DataD" => $DataD,
				"DataN" => $DataN
			),
		);
	}
	return $Return;
}

function Env($file,$item=null,$name=null){
	$Path = "env/".$file.".ini";
	$Env = parse_ini_file($Path, true);
	if( !empty($Env[$item][$name]) && !empty($name) ){
		return $Env[$item][$name];
	}else if( !empty($Env[$item]) && empty($name) ){
		return $Env[$item];
	}else if( !empty($Env) && empty($item) ){
		return $Env;
	}else{
		return null;
	}
}

function EnvFile($item=null,$name=null){
	$Path = Env('file',$item,$name);
	if( file_exists($Path) ){
		return file_get_contents($Path);
	}else{
		return null;
	}
}

function DataRe($DataM=null){
	$EnvReplace = Env('replace','hovthen',null);
	foreach ($EnvReplace as $name => $item) {
		$DataM = str_replace('<'.$name.'>',$item,$DataM);
	};
	foreach ($EnvReplace as $name => $item) {
		//print_r($name);
		$DataM = str_replace('<'.$name.'>',$item,$DataM);
	};
	return $DataM;
}


?>
<?php if( $debug != true ){ ?>
<div style="display:none">
	<script type="text/javascript" src="https://s95.cnzz.com/z_stat.php?id=1260235499&web_id=1260235499"></script>
</div>
<?php }; ?>