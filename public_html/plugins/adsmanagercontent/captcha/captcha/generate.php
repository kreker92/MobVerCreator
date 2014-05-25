<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
//Load Framework
define( '_JEXEC', 1 );
defined('_JEXEC') or die( 'Restricted access' );
$path = substr(dirname(__FILE__),0,strpos(dirname(__FILE__),'/plugins'));
define('JPATH_BASE', $path );
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe = JFactory::getApplication('site');
$mainframe->initialise();
$JConfig = JFactory::getConfig();
	
require_once( 'captchasecurityimages.php' );
$captchaGenerator = new CaptchaSecurityImages();
$code = $captchaGenerator->generate();

$session = JFactory::getSession();
$session->set('security_code', $code);