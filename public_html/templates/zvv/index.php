<?php
/**
 * @package      Joomla.Site
 * @subpackage   Templates.zvv
 * @copyright    Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * developer site zaycev-web.ru
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');

JHtml::_('behavior.framework', true);

// Get params
$tpl_minwidth	= $this->params->get('minwidth',1000);
$tpl_maxwidth	= $this->params->get('maxwidth',1250);
$tpl_leftwidth	= $this->countModules('position-31') ? $this->params->get('leftwidth') : 0;
$tpl_rightwidth	= $this->countModules('position-39') ? $this->params->get('rightwidth') : 0;
$tpl_contentleftwidth	= $this->countModules('position-33') ? $this->params->get('contentleftwidth') : 0;
$tpl_contentrightwidth	= $this->countModules('position-36') ? $this->params->get('contentrightwidth') : 0;

$color			= $this->params->get('templatecolor');
$logo			= $this->params->get('logo');
$navposition	= $this->params->get('navposition');
$app			= JFactory::getApplication();
$doc			= JFactory::getDocument();
$templateparams	= $app->getTemplate(true)->params;
$doc->_generator = 'zaycev-web.ru';
$config = JFactory::getConfig();

$bootstrap = explode(',', $templateparams->get('bootstrap'));
$jinput = JFactory::getApplication()->input;
$option = $jinput->get('option', '', 'cmd');

$doc->addStyleSheet(JUri::base() . 'templates/system/css/system.css');
$doc->addStyleSheet(JUri::base() . 'templates/' . $this->template . '/css/reload.css', $type = 'text/css', $media = 'screen,projection');
//$doc->addStyleSheet(JUri::base() . 'templates/' . $this->template . '/css/print.css', $type = 'text/css', $media = 'print');
$doc->addStyleSheet(JUri::base() . 'templates/' . $this->template . '/css/style.css', $type = 'text/css', $media = 'screen,projection');

$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/jquery.min.js', 'text/javascript');
?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="<?php echo $this->language; ?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?php echo $this->language; ?>" > <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<?
		if(isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)){
			header('X-UA-Compatible: IE=edge,chrome=1');
			echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />';
		}
		?>
		<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes" />
		<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />

		<jdoc:include type="head" />

		<!--[if lte IE 7]>
		<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/ie7lte.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<style><?php
			echo '#body { min-width: '.$tpl_minwidth.'px; }'."\n";
			echo 'div.wrapper { min-width: '.$tpl_minwidth.'px; max-width: '.$tpl_maxwidth.'px; }'."\n";
			echo '#content { padding: 0 '.$tpl_rightwidth.'px 0 '.$tpl_leftwidth.'px; }'."\n";
			echo '#sideLeft { width: '.$tpl_leftwidth.'px; }'."\n";
			echo '#sideRight { width: '.$tpl_rightwidth.'px; margin-left:-'.$tpl_rightwidth.'px; }'."\n";
			echo '#contentCenter { padding: 0 '.$tpl_contentrightwidth.'px 0 '.$tpl_contentleftwidth.'px; }'."\n";
			echo '#contentLeft { width: '.$tpl_contentleftwidth.'px; }'."\n";
			echo '#contentRight { width: '.$tpl_contentrightwidth.'px; margin-left:-'.$tpl_contentrightwidth.'px; }'."\n";
		?></style>
	</head>
<body id="body">
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->

<div id="wrapper">

	<header id="header">
		<div class="wrapper">
			<jdoc:include type="modules" name="position-11" />
		</div>
	</header><!-- #header-->
	
	<nav id="nav">
		<div class="wrapper">
			<div id="topmenu">
				<jdoc:include type="modules" name="position-21" />
				<b>position-21</b>
				<ul>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Link</a></li>
				</ul>
			</div>
		</div>
	</nav><!-- #nav-->
	
	<section id="middle">
		<div class="wrapper clearfix">
			<?php if($this->countModules('position-25')){ ?>
				<div id="containerTop">
					<jdoc:include type="modules" name="position-25" />
				</div>
			<?php } ?>
			<div id="container" class="clearfix">
				<div id="content">
					<div id="contentTop">
						<jdoc:include type="message" />
						<jdoc:include type="modules" name="position-32" />
					</div>
					<div id="containerInner" class="clearfix">
						<div id="containerCenter" class="clearfix">
							<div id="contentCenter">
								<?php if($this->countModules('position-34')){ ?>
									<jdoc:include type="modules" name="position-34" />
								<?php } ?>
								<strong>component:</strong> Sed placerat accumsan ligula. Aliquam felis magna, congue quis, tempus eu, aliquam vitae, ante. Cras neque justo, ultrices at, rhoncus a, facilisis eget, nisl. Quisque vitae pede. Nam et augue. Sed a elit. Ut vel massa. Suspendisse nibh pede, ultrices vitae, ultrices nec, mollis non, nibh. In sit amet pede quis leo vulputate hendrerit. Cras laoreet leo et justo auctor condimentum. Integer id enim. Suspendisse egestas, dui ac egestas mollis, libero orci hendrerit lacus, et malesuada lorem neque ac libero. Morbi tempor pulvinar pede. Donec vel elit.
								<jdoc:include type="component" />
								<?php if($this->countModules('position-35')){ ?>
									<jdoc:include type="modules" name="position-35" />
								<?php } ?>
							</div>
						</div>
						<?php if($this->countModules('position-33')){ ?>
							<div id="contentLeft">
								<jdoc:include type="modules" name="position-33" />
							</div>
						<?php } ?>
						<?php if($this->countModules('position-36')){ ?>
							<div id="contentRight">
								<jdoc:include type="modules" name="position-36" />
							</div>
						<?php } ?>
					</div>
					<div id="contentBottom">
						<jdoc:include type="modules" name="position-37" />
					</div>
				</div><!-- #content-->
			</div><!-- #container-->

			<?php if($this->countModules('position-31')){ ?>
				<aside id="sideLeft">
					<jdoc:include type="modules" name="position-31" />
				</aside><!-- #sideLeft -->
			<?php } ?>
			
			<?php if($this->countModules('position-39')){ ?>
				<aside id="sideRight">
					<jdoc:include type="modules" name="position-39" />
				</aside><!-- #sideRight -->
			<?php } ?>
			
			<?php if($this->countModules('position-40')){ ?>
				<div id="containerBottom" class="clr">
					<jdoc:include type="modules" name="position-40" />
				</div>
			<?php } ?>
		</div>
	</section><!-- #middle-->
</div>

<footer id="footer" class="clr">
	<div class="wrapper">
		<jdoc:include type="modules" name="position-51" />
	</div>
</footer><!-- #footer -->
<jdoc:include type="modules" name="debug" />
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/js.js"></script>
</body>
</html>
