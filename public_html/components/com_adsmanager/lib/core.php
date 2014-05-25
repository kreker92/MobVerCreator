<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}
/**
 * Guess what? TModel,Controller are interfaces in Joomla! 3.0. Holly smoke, Batman!
 */
if(!class_exists('TController')) {
	if(interface_exists('JController')) {
		abstract class TController extends JControllerLegacy {}
	} else {
		jimport('joomla.application.component.controller');
		class TController extends JController {}
	}
}

if(!class_exists('TModel')) {
	if(interface_exists('JModel')) {
		abstract class TModel extends JModelLegacy {}
	} else {
		jimport('joomla.application.component.model');
		class TModel extends JModel {}
	}
}
if(!class_exists('TView')) {
	if(interface_exists('JView')) {
		abstract class TView extends JViewLegacy {}
	} else {
		jimport('joomla.application.component.view');
		class TView extends JView {}
	}
}

if (version_compare(JVERSION,'3.0.0','>=')) {
	define('JOOMLA_J3',1);		
} else {
	define('JOOMLA_J3',0);
}

require_once('phpcompat.php');
require_once('route.php');
require_once('pagination.php');
require_once('tools.php');
require_once('pane.php');
require_once('mail.php');
require_once('tconf.php');
require_once('link.php');
if(version_compare(JVERSION, '1.6', 'ge')) {
    require_once('tpermissions.php');
}
require_once('ttext.php');

require_once(JPATH_ROOT."/administrator/components/com_adsmanager/helpers/select.php");

// Load Paidsystem
if ( file_exists( JPATH_ROOT. "/components/com_paidsystem/api.paidsystem.php")) 
{
	define('PAIDSYSTEM',1);
	require_once(JPATH_ROOT . "/components/com_paidsystem/api.paidsystem.php");
} else {
	define('PAIDSYSTEM',0);
}

//special override for images
$app = JFactory::getApplication();
$templateDir = JPATH_ROOT . '/templates/' . $app->getTemplate();
if (is_file($templateDir.'/html/com_adsmanager/images/nopic.gif')) {
	define('ADSMANAGER_NOPIC_IMG',JURI::root() . 'templates/' . $app->getTemplate().'/html/com_adsmanager/images/nopic.gif');
} else {
	define('ADSMANAGER_NOPIC_IMG',JURI::root() . 'components/com_adsmanager/images/nopic.gif');
}

// Special config
$db = JFactory::getDBO();
$db->setQuery("SELECT * FROM #__adsmanager_config");
$config = $db->loadObject();
if (isset($config->special))
	define('ADSMANAGER_SPECIAL',$config->special);
else
	define('ADSMANAGER_SPECIAL','');

//Community Builder settings
if (file_exists(JPATH_ROOT.'/components/com_comprofiler/')) {
	define('COMMUNITY_BUILDER',1);
	if (($config->comprofiler == 2)&&(file_exists(JPATH_ROOT.'/components/com_comprofiler/plugin/user/plug_adsmanager-tab'))) {
		define('COMMUNITY_BUILDER_ADSTAB',1);
	} else {
		define('COMMUNITY_BUILDER_ADSTAB',0);
	}
} else {
	define('COMMUNITY_BUILDER',0);
	define('COMMUNITY_BUILDER_ADSTAB',0);
}

if (file_exists(JPATH_ROOT.'/components/com_community/')) {
	define('JOMSOCIAL',1);
    
	if ($config->comprofiler == 2) {
		define('JOMSOCIAL_ADSTAB',1);
	} else {
		define('JOMSOCIAL_ADSTAB',0);
	}
} else {
	define('JOMSOCIAL',0);
	define('JOMSOCIAL_ADSTAB',0);
}

//Lib/core.php is called in router.php. In this case, if joomla is set to use add suffix, url is like XXXX.feed
// there is no format=feed. This will be transform to format=feed only onAfterRoute so little hack here to force format
// for getDocument otherview document will be JDocumentHTML
if (strpos($_SERVER['REQUEST_URI'],'.feed') !== false) {
	JRequest::setVar('format', 'feed');
}
//Jquery non conflict mode
$document = JFactory::getDocument();
$app = JFactory::getApplication();

// we can decide to don't load jquery in the frontend
if ($app->isAdmin() == false) {
	if ($config->jquery) {
		$document->addScript(JURI::root().'components/com_adsmanager/js/jquery-1.8.0.min.js');
		$document->addScript(JURI::root().'components/com_adsmanager/js/fullnoconflict.js');
	} else {
		$document->addScript(JURI::root().'components/com_adsmanager/js/noconflict.js');
	}
} else {
	$document->addScript(JURI::root().'components/com_adsmanager/js/jquery-1.8.0.min.js');
	$document->addScript(JURI::root().'components/com_adsmanager/js/fullnoconflict.js');
}
$document->addScript(JURI::root().'components/com_adsmanager/js/jquery-ui-1.8.23.custom.min.js');
$document->addStyleSheet(JURI::root().'components/com_adsmanager/css/ui-lightness/jquery-ui-1.8.23.custom.css');

require_once(JPATH_ROOT.'/components/com_adsmanager/helpers/category.php');

if (!function_exists("getMultiLangText")) {
	function getMultiLangText($value) {
		$values = @json_decode($value);
		$lg = JFactory::getLanguage();
		$currenttag =  str_replace("-","_",$lg->getTag());
		if ($values != null) {
			if (@$values->$currenttag != "")
				return $values->$currenttag;
			else if (($currenttag != "en_GB")&&(@$values->en_GB != ""))
				return $values->en_GB;
			else {
				foreach($values as $tag => $val) {
					if ($val != null)
						return $val;
				}
			}
		} else
			return null;
	}
}

// Merge the language overrides
$paths = array(JPATH_ADMINISTRATOR, JPATH_ROOT);
$jlang = JFactory::getLanguage();
$jlang->load("com_adsmanager", $paths[0], 'en-GB', true);
$jlang->load("com_adsmanager", $paths[0], null, true);
$jlang->load("com_adsmanager", $paths[1], 'en-GB', true);
$jlang->load("com_adsmanager", $paths[1], null, true);