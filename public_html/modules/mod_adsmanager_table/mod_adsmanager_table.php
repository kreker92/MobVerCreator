<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die( 'Restricted access' );
	
require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/configuration.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/content.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/adsmanager.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/column.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/category.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/content.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/field.php');
require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/user.php');

require_once(JPATH_ROOT."/components/com_adsmanager/helpers/field.php");

$uri = JFactory::getURI();
$baseurl = JURI::base();
	
if (!defined( '_ADSMANAGER_CSS' )) {
	/** ensure that functions are declared only once */
	define( '_ADSMANAGER_CSS', 1 );
	$document = JFactory::getDocument();
	$app = JFactory::getApplication();
	$templateDir = JPATH_ROOT . '/templates/' . $app->getTemplate();
	if (is_file($templateDir.'/html/com_adsmanager/css/adsmanager.css')) {
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
		$document->addStyleSheet($templateDir.'/html/com_adsmanager/css/adsmanager.css');
	} else {
		$document->addStyleSheet($baseurl.'components/com_adsmanager/css/adsmanager.css');
	}
}

if (!defined('_ADSMANAGER_MODULE_ADS')) {
	define( '_ADSMANAGER_MODULE_ADS', 1 );
	function isNewContent($date,$nbdays) {
		$time = strtotime($date);
		if ($time >= (time()-($nbdays*24*3600)))
			return true;
		else
			return false;
	}
	
	function reorderDate( $date ){
		$format = JText::_('ADSMANAGER_DATE_FORMAT_LC');
		
		if ($date && (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$date,$regs))) {
			$date = mktime( 0, 0, 0, $regs[2], $regs[3], $regs[1] );
			$date = $date > -1 ? strftime( $format, $date) : '-';
		}
		return $date;
	}
}

if ( file_exists( JPATH_BASE. "/components/com_paidsystem/api.paidsystem.php")) 
{
	require_once(JPATH_BASE . "/components/com_paidsystem/api.paidsystem.php");
}

$sort_sql = intval($params->get( 'random',0));

$catid = $params->get('catselect',"0");
$catselect = $catid;

$confmodel  = new AdsmanagerModelConfiguration();
$conf = $confmodel->getConfiguration();
$nb_images = $conf->nb_images;
$nb_ads = intval($params->get( 'nb_ads', 3 )) ;

$contentmodel  = new AdsmanagerModelContent();
$contents = $contentmodel->getLatestContents($nb_ads,$sort_sql,$catselect);

if (function_exists("getMaxPaidSystemImages"))
{
	$nb_images += getMaxPaidSystemImages();
}

$uri = JFactory::getURI();
$baseurl = JURI::base();

$catmodel		 = new AdsmanagerModelCategory();
$columnmodel	 = new AdsmanagerModelColumn();
$fieldmodel	     = new AdsmanagerModelField();
$usermodel		 = new AdsmanagerModelUser();
$adsmanagermodel = new AdsmanagerModelAdsmanager();

$user	= JFactory::getUser();
$userid = $user->id;

$columns = $columnmodel->getColumns($catid);
$fColumns = $fieldmodel->getFieldsbyColumns();
		
$field_values = $fieldmodel->getFieldValues();	
$plugins = $fieldmodel->getPlugins();
$field = new JHTMLAdsmanagerField($conf,$field_values,"1",$plugins,null);
	
require(JModuleHelper::getLayoutPath('mod_adsmanager_table','table'));
$content="";
$path = JPATH_ADMINISTRATOR.'/../libraries/joomla/database/table';
JTable::addIncludePath($path);