<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die( 'Restricted access' );
	
require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/configuration.php');
require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/content.php');
require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/field.php');
require_once(JPATH_BASE."/components/com_adsmanager/helpers/field.php");

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

require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

$nb_ads = intval($params->get( 'nb_ads', 3 )) ;
$image = intval($params->get( 'image', 1 )) ;
$imagesize = $params->get( 'imagesize', "small" ) ;
switch($imagesize) {
	case "large":
		$imagetype = "image";break;
	case "medium":
		$imagetype = "medium";break;
	default:
	case "small":
		$imagetype = "thumbnail";break;
}
$sort_sql = intval($params->get( 'random',0));

$ads_by_row = intval($params->get('nb_ads_by_row',3));

$catselect = $params->get('catselect',"no");
$displaycategory = intval($params->get( 'displaycategory',1));
$displaydate = intval($params->get( 'displaydate',1));

$confmodel  = new AdsmanagerModelConfiguration();
$conf = $confmodel->getConfiguration();
$nb_images = $conf->nb_images;

$contentmodel  = new AdsmanagerModelContent();
$contents = $contentmodel->getLatestContents($nb_ads,$sort_sql,$catselect);

$fields = array();
$fields[] = $params->get( 'field1', "") ;
$fields[] = $params->get( 'field2', "") ;
$fields[] = $params->get( 'field3', "") ;
$fields[] = $params->get( 'field4', "") ;
$fields[] = $params->get( 'field5', "") ;
$listfields="";
foreach($fields as $field)
{
	if (($listfields == "")&&($field != ""))
		$listfields .= "'$field'";
	if ($field != "")
		$listfields .= ",'$field'";
}

$fieldmodel  = new AdsmanagerModelField();
$field_values = array();
$adfields = array();
if ($listfields != "")
{
	$adfields = $fieldmodel->getFieldsByName($listfields);
	$field_values = $fieldmodel->getFieldValues();

	foreach($adfields as $field)
	{
		if ($field->cbfieldvalues != "-1")
		{
			/*get CB value fields */
			$cbfieldvalues = $fieldmodel->getCBFieldValues($field->cbfieldvalues);
			$field_values[$field->fieldid] = $cbfieldvalues;
		}
	}
}
$field = new JHTMLAdsmanagerField($conf,$field_values,"4",$fieldmodel->getPlugins());

if (function_exists("getMaxPaidSystemImages"))
{
	$nb_images += getMaxPaidSystemImages();
}

switch ( $params->get( 'align', 'hor' ) ) {
	case 'ver':
		require(JModuleHelper::getLayoutPath('mod_adsmanager_ads','vertical'));
		break;
	
	case 'hor':
	default:
		require(JModuleHelper::getLayoutPath('mod_adsmanager_ads','horizontal'));
		break;
}
$content="";
$path = JPATH_ADMINISTRATOR.'/../libraries/joomla/database/table';
JTable::addIncludePath($path);