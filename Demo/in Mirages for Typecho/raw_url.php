<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('component/header.php'); ?>
<?php 

/**
 * Raw Url
 *
 * @package custom
 * @var $this Widget_Archive
 */

$UrlRequest = $_SERVER["REQUEST_URI"];
preg_match("/\/([^?]*)([?]?)([^:\/]*)([:\/]*)([\S]*)/",$UrlRequest,$URL);

switch($URL[1]){
case 'url':
	if( $URL[3] == "http" || $URL[3] == "https" ){
		$Data = array(
			"type" => "link",
			"content" => $URL[3].$URL[4].$URL[5],
		);
	}else if( $URL[3] == "@" || $URL[3] == "index" ){
		$Data = array(
			"type" => "link",
			"content" => "https://hovthen.com/".$URL[5],
		);
	}else if( $URL[3] == "lab" || $URL[3] == "boke" || $URL[3] == "forum" || $URL[3] == "libs" ){
		$Data = array(
			"type" => "link",
			"content" => "https://".$URL[3].".hovthen.com/".$URL[5],
		);
	}else if( $URL[3] == "mail" || $URL[3] == "mailto" ){
		$Data = array(
			"type" => "mail",
			"content" => $URL[5],
		);
	}else if( $URL[3] == "base64" ){
		$Data = array(
			"type" => "text",
			"content" => base64_decode($URL[5]),
		);
	}else if( $URL[3] == "text" ){
		$Data = array(
			"type" => "text",
			"content" => $URL[5],
		);
	}else{
		$Data = array(
			"type" => "text",
			"content" => $URL[3].$URL[4].$URL[5],
		);
	};
	break;
case null:
	$Data = array(
		"type" => "info",
		"content" => $URL[3].$URL[4].$URL[5],
	);
	break;
default:
	$Data = array(
		"type" => "text",
		"content" => $URL[3].$URL[4].$URL[5],
	);
}

$SelfLink = '<a href="'. Mirages::$options->siteUrl .'" target="_self">'. Mirages::$options->title .'</a>';

switch($Data["type"]){
case 'link':
	$Links = LinkType("link",$Data["content"]);
	if( empty($Links['self']['domain']) ){
		// 链接错误或不存在
		$Return = array(
			"type" => "link",
			"action" => "stop",
			"blockquote" => "好像出错了嗷~",
			"collapse" => array(
				0 => array(
					"header" => "温馨提示",
					"content" => "报告冒险家，现在的情况是：您无意间访问到了一个正常情况下不可能被访问到的页面。具体请参考以下详细说明！",
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => false,
				"copy" => false,
				"search" => true,
			),
		);
	}else if( $Links['self']['code'] == true ){
		// 内部链接
		$Return = array(
			"type" => "link",
			"action" => "go",
			"blockquote" => "您正在访问 万里淘知 内部链接。",
			"collapse" => array(
				0 => array(
					"header" => "正在访问至",
					"content" => $Data["content"]." ",
				),
				1 => array(
					"header" => "已证实的内部链接",
					"content" => "己证实该链接为 ".$SelfLink." 所属。请放心访问。",
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => "立即访问",
				"copy" => false,
				"search" => false,
			),
		);
	}else if( $Links['allow']['code'] == true && $Links['block']['code'] == false ){
		// 白名单域名且域名不在黑名单
		$Return = array(
			"type" => "link",
			"action" => "stop",
			"blockquote" => "您即将访问 万里淘知 白名单链接：",
			"collapse" => array(
				0 => array(
					"header" => "您即将访问",
					"content" => $Data["content"],
				),
				1 => array(
					"header" => "关于此链接",
					"content" => "该链接已经加入 ".$SelfLink." 白名单。但这并不代表该链接没有安全风险，您仍需注意识别及防范未知的安全风险。",
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => "立即访问",
				"copy" => array(
					"复制链接",
					$Data["content"],
				),
				"search" => true,
			),
		);
	}else if( $Links['friend']['code'] == true && $Links['block']['code'] == false ){
		// 友链名单域名且域名不在黑名单
		$Return = array(
			"type" => "link",
			"action" => "stop",
			"blockquote" => "您即将访问 万里淘知 友情链接：",
			"collapse" => array(
				0 => array(
					"header" => "您即将访问",
					"content" => $Data["content"],
				),
				1 => array(
					"header" => "安全的友情链接",
					"content" => "己证实该链接为 ".$SelfLink." 友情链接，您可以放心访问。",
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => "立即访问",
				"copy" => false,
				"search" => true,
			),
		);
	}else if( $Links['block']['code'] == true ){
		// 黑名单域名
		$Return = array(
			"type" => "link",
			"action" => "stop",
			"blockquote" => "您即将访问的链接可能存在严重危险，请注意保护您的帐户和财产安全！",
			"collapse" => array(
				0 => array(
					"header" => "己阻止您访问",
					"content" => $Data["content"],
				),
				1 => array(
					"header" => "关于潜在的安全风险",
					"content" => "随着互联网的发展，资本无序扩张以及技术门槛的降低，并非所有网址链接绿色且安全。请注意识别及防范。",
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => false,
				"copy" => array(
					"复制链接",
					$Data["content"],
				),
				"search" => true,
			),
		);
	}else{
		$Return = array(
			"type" => "link",
			"action" => "stop",
			"blockquote" => "即将离开 万里淘知，请注意确认：",
			"collapse" => array(
				0 => array(
					"header" => "请确认您即将访问",
					"content" => $Data["content"],
				),
				1 => array(
					"header" => "关于潜在的安全风险",
					"content" => "随着互联网的发展，资本无序扩张以及技术门槛的降低，并非所有网址链接绿色且安全。请注意识别及防范。",
					"show" => false,
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => "仍然访问",
				"copy" => array(
					"复制链接",
					$Data["content"],
				),
				"search" => true,
			),
		);
	};
	break;
case 'text':
	$Return = array(
		"type" => "text",
		"action" => "stop",
		"blockquote" => "您请求的为文本内容，请注意查看。",
		"collapse" => array(
			0 => array(
				"header" => "详细内容如下",
				"content" => $Data["content"],
			),
			1 => array(
				"header" => "关于此文本内容的提示",
				"content" => "该文本内容未经 ".$SelfLink." 验证，请注意可能存在的不安全内容。",
			),
		),
		"button" => array(
			"back" => "返回上一页",
			"browser" => false,
			"copy" => array(
				"复制文本",
				$Data["content"],
			),
			"search" => true,
		),
	);
	break;
case 'mail':
	$Links = LinkType("mail",$Data["content"]);
	if( empty($Links['self']['domain']) ){
		// 链接错误或不存在
		$Return = array(
			"type" => "mail",
			"action" => "stop",
			"blockquote" => "好像出错了嗷~",
			"collapse" => array(
				0 => array(
					"header" => "温馨提示",
					"content" => "报告冒险家，现在的情况是：<br/>您无意间访问到了一个正常情况下不可能被访问到的页面。具体请参考以下详细说明！",
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => false,
				"copy" => false,
				"search" => true,
			),
		);
	}else if( $Links['self']['code'] == true ){
		// 内部链接
		$Return = array(
			"type" => "mail",
			"action" => "go",
			"blockquote" => "您即将向 万里淘知 发送邮件：",
			"collapse" => array(
				0 => array(
					"header" => "正在打开邮箱应用",
					"content" => "mailto:".$Data["content"],
				),
				1 => array(
					"header" => "关于此电子邮箱",
					"content" => "己证实该电子邮箱域名为 ".$SelfLink." 所属。<br/>站长有该邮箱的管理员权限，但这并不代表该邮箱正被站长所使用。",
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => "打开邮箱",
				"copy" => false,
				"search" => false,
			),
		);
	}else{
		// 非内部域名
		$Return = array(
			"type" => "link",
			"action" => "stop",
			"blockquote" => "即将打开邮箱应用：",
			"collapse" => array(
				0 => array(
					"header" => "收件人的邮箱",
					"content" => "mailto:".$Data["content"],
				),
				1 => array(
					"header" => "温馨提示",
					"content" => "请勿给来路不明的邮箱发送电子邮件，以免暴露邮箱账号后遭受垃圾邮件骚扰！<br/>QQ邮箱用户建议关闭数字邮箱账号并使用英文邮箱账号，可一定程度减少收到垃圾邮件的频率。",
				),
			),
			"button" => array(
				"back" => "返回上一页",
				"browser" => "打开邮箱",
				"copy" => array(
					"复制收件人",
					$Links['self']['user']."@".$Links['self']['domain'],
				),
				"search" => true,
			),
		);
	};
	break;
case 'info':
	$Return = array(
		"type" => "info",
		"action" => "stop",
		"blockquote" => "您即将离开 ".$SelfLink."，请注意您的帐户和您的财产安全。",
		"collapse" => array(
			0 => array(
				"header" => "温馨提示",
				"content" => "报告冒险家，现在的情况是：您无意间访问到了一个正常情况下不可能被访问到的页面。具体请参考以下详细说明！",
			),
			1 => array(
				"header" => "详细说明",
				"content" => "本页面默认隐藏，正常情况下只有当您点击网站页面中的链接时才会跳转到本页面。但间题在于传递数据为空值，因此无法确定下一步怎么做。<br/>您可以先返回上一页目，清除浏览器缓存后重复几次，如仍无法解决，请写清过程后发电子邮件向我反馈！",
			),
		),
		"button" => array(
			"back" => "返回上一页",
			"browser" => false,
			"copy" => false,
			"search" => true,
		),
	);
	break;
default:
	$Return = array(
		"type" => "info",
		"action" => "stop",
		"blockquote" => "您即将离开 ".$SelfLink."，请注意您的帐户和您的财产安全。",
		"collapse" => array(
			0 => array(
				"header" => "温馨提示",
				"content" => "报告冒险家，现在的情况是：您无意间访问到了一个正常情况下不可能被访问到的页面。具体请参考以下详细说明！",
			),
			1 => array(
				"header" => "详细说明",
				"content" => "本页面默认隐藏，正常情况下只有当您点击网站页面中的链接时才会跳转到本页面。但间题在于传递数据为空值，因此无法确定下一步怎么做。<br/>您可以先返回上一页目，清除浏览器缓存后重复几次，如仍无法解决，请写清过程后发电子邮件向我反馈！",
			),
		),
		"button" => array(
			"back" => "返回上一页",
			"browser" => false,
			"copy" => false,
			"search" => true,
		),
	);
}


if( $this->user->hasLogin() && $this->user->group == "administrator" ){
	$Debug = array(
		"debug" => true,
		"data" => array(
			LinkType($Data["type"],$Data["content"]),
		),
	);
}else{
	$Debug = array(
		"debug" => false,
		"data" => array(),
	);
	if( $Return['action'] == "go" ){
		Header('HTTP/1.1 302 Moved Temporarily');
		Header('refresh:3;url='.$Data['content']);
	};
}


function LinkType($Type,$DataT=null){
	$Path = __TYPECHO_ROOT_DIR__ ."/Env/host.ini";
	$Env = parse_ini_file($Path, true);
	// IPv6 [:FFF:] => [#FFF#]
	$DataD[0] = $DataT;
	preg_match("/([\[])([^\]?]*)([\]]?)/",$DataD[0],$DataD[1]);
	$DataD[2] = str_replace(':','#',$DataD[1]);
	$DataD[3] = str_replace($DataD[1],$DataD[2],$DataD[0]);
	switch($Type){
	case 'link':
		preg_match("/(https|http|ftp|[^:]*):([\/]*)([^\/:?]*)([:]?)([\d]*)([\/?]?)/",$DataD[3],$DataN[0]);
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
		return $Return;
		break;
	case 'mail':
		$DataD[3] = 'mailto:'.$DataD[3];
		preg_match("/(https|http|ftp|[^:]*):([\/]*)([^\/:?]*)([:]?)([\d]*)([\/?]?)/",$DataD[3],$DataN[0]);
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
		return $Return;
		break;
	default:
		return false;
	}
}


?>
<div id="post" role="main">
    <article class="post page" itemscope itemtype="http://schema.org/BlogPosting" style="margin-bottom: 20px;">
<!--        <div class="display-none" itemscope itemprop="author" itemtype="http://schema.org/Person">-->
<!--            <meta itemprop="name" content="--><?php //$this->author(); ?><!--"/>-->
<!--            <meta itemprop="url" content="--><?php //$this->author('url'); ?><!--"/>-->
<!--        </div>-->
<!--        <div class="display-none" itemscope itemprop="publisher" itemtype="http://schema.org/Organization">-->
<!--            <meta itemprop="name" content="--><?php //$this->author(); ?><!--"/>-->
<!--            <div itemscope itemprop="logo" itemtype="http://schema.org/ImageObject">-->
<!--                <meta itemprop="url" content="--><?php //echo Typecho_Common::gravatarUrl($this->author->mail, 50, Mirages::$options->commentsAvatarRating, NULL, true);?><!--">-->
<!--            </div>-->
<!--        </div>-->
        <meta itemprop="url mainEntityOfPage" content="<?php $this->permalink() ?>" />
        <meta itemprop="datePublished" content="<?php echo date('c' , $this->created);?>">
        <meta itemprop="dateModified" content="<?php echo date('c' , $this->modified);?>">
        <meta itemprop="headline" content="<?php $this->title();?>">
        <meta itemprop="image" content="<?php Mirages::$options->banner()?>">
        <?php if(!(Mirages::$options->showBanner && (Utils::isTrue($this->fields->headTitle) || (intval($this->fields->headTitle) >= 0 && Mirages::$options->headTitle__isTrue))) && !$this->is('page','about') && !$this->is('page','links')): ?>
        <h1 class="post-title <?php echo Utils::postTitleClass($this->title)?>"><?php echo Mirages::parseBiaoqing($this->title) ?>
            <?php if($this->user->hasLogin()):?>
                <a class="superscript" href="<?php Mirages::$options->adminUrl()?>write-page.php?cid=<?php $this->cid();?>" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
            <?php endif?>
        </h1>
        <?php endif?>
        <div class="post-content" itemprop="articleBody">
            <?php echo Content::parse($this->content); ?>
            
            <blockquote>
				<?php _me($Return["blockquote"]); ?>
			</blockquote>
			
			<?php foreach ( $Return["collapse"] as $Name => $Item ) { ?>
			<div class="collapse-block">
				<div class="collapse-header" data-mirages-toggle="collapse" data-target="#collapse-block-info-<?php _me($Name) ?>">
					<p class="title"><?php _me($Item["header"]) ?><span class="angle"><i class="fa fa-angle-double-down" aria-hidden="true"></i></span></p>
				</div>
				<div id="collapse-block-info-<?php _me($Name) ?>" class="collapse-content collapse <?php if( empty($Item[2]) || $Item["show"] == true ){ _me('show'); } ?>">
					<p><?php _me($Item["content"]) ?></p>
				</div>
			</div>
			<? }; ?>
			
			
			<?php if( $Debug['debug'] == true ): ?>
			<div class="collapse-block">
				<div class="collapse-header" data-mirages-toggle="collapse" data-target="#collapse-block-info-debug">
					<p class="title"><?php _me('调试面板') ?><span class="angle"><i class="fa fa-angle-double-down" aria-hidden="true"></i></span></p>
				</div>
				<div id="collapse-block-info-debug" class="collapse-content collapse">
					<p><?php print_r($Debug['data']) ?></p>
				</div>
			</div>
			<? endif; ?>
			
		</div>
    </article>
</div>

	<div id="content404" role="controls">
		<div class="controls">
			<?php if( !empty($Return["button"]["back"]) || $Return["button"]["back"] != false ): ?>
			<a class="btn btn-primary return-home" href="javascript:;" onClick="javascript:history.back(-1);"><?php _me($Return["button"]["back"])?></a>
			<? endif; ?>
			<?php if( !empty($Return["button"]["browser"]) || $Return["button"]["browser"] != false ): ?>
			<a class="btn btn-primary return-home" href="<?php _me($Return["collapse"]["0"]["content"]); ?>" target="_blank"><?php _me($Return["button"]["browser"])?></a>
			<? endif; ?>
			<?php if( !empty($Return["button"]["copy"]) || $Return["button"]["copy"] != false ): ?>
			<a class="btn btn-primary return-home" href="javascript:void(0);" id="rcopy" onclick="copy('<?php _me($Return["button"]["copy"]["1"]); ?>')"><?php _me($Return["button"]["copy"]["0"])?></a>
			<? endif; ?>
			<?php if( !empty($Return["button"]["search"]) || $Return["button"]["search"] != false ): ?>
			<div class="search-box">
				<form class="form" action="<?php Mirages::$options->rootUrl()?>/<?php echo REWRITE_FIX ?>"  role="search">
					<input type="text" name="s" required placeholder="<?php _me('搜索相关内容...')?>" class="search search-form-input">
					<button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
				</form>
			</div>
			<? endif; ?>
			
		</div>
	</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script type="text/javascript">
	function copy(a) {
		var clipboard = new ClipboardJS('#rcopy', {
			text : function() {
				return a;
			}
		});
		clipboard.on("success", function(e) {
			console.log(e);
			alert('复制成功\n'+e.text);
			clipboard.destroy();
		});
		clipboard.on("error", function(e) {
			console.log(e);
			alert('复制失败');
			clipboard.destroy();
		});
	}
</script>

<?php $this->need('component/footer.php'); ?>
