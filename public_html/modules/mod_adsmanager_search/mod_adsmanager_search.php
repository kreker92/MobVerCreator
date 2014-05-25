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
require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/field.php');
require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/category.php');
require_once(JPATH_BASE."/components/com_adsmanager/helpers/field.php");

/****************************************************/
jimport( 'joomla.session.session' );	
$currentSession = JSession::getInstance('none',array());
$defaultvalues = $currentSession->get("search_fields",array());
			
$catid = $currentSession->get("searchfieldscatid",JRequest::getInt('catid', 0 ));
$app = JFactory::getApplication();
$text_search = $currentSession->get("tsearch",$app->getUserStateFromRequest('com_adsmanager.front_content.tsearch','tsearch',""));
		
$advanced_search = intval($params->get( 'advanced_search', 1)) ;
$search_by_cat = intval($params->get( 'search_by_cat', 1)) ;
$search_by_text = intval($params->get( 'search_by_text', 1)) ;

$fields[] = $params->get( 'field1', "") ;
$fields[] = $params->get( 'field2', "") ;
$fields[] = $params->get( 'field3', "") ;
$fields[] = $params->get( 'field4', "") ;
$fields[] = $params->get( 'field5', "") ;
$type = $params->get( 'type', "table") ;
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
if ($listfields != "")
{
	$searchfields = $fieldmodel->getFieldsByName($listfields);
	$field_values = $fieldmodel->getFieldValues();

	foreach($searchfields as $field)
	{
		if ($field->cbfieldvalues != "-1")
		{
			/*get CB value fields */
			$cbfieldvalues = $fieldmodel->getCBFieldValues($field->cbfieldvalues);
			$field_values[$field->fieldid] = $cbfieldvalues;
		}
	}
}

$confmodel = new AdsmanagerModelConfiguration();
$conf = $confmodel->getConfiguration();

$categorymodel = new AdsmanagerModelCategory();

switch(@$conf->single_category_selection_type) {
	default:
	case 'normal':
	case 'color':
	case 'combobox':
		$cats = $categorymodel->getFlatTree(true, false, $nbcontents, 'read');
		break;
	case 'cascade':
		$cats = $categorymodel->getCategoriesPerLevel(true, false, $nbcontents, 'read');
		break;
}

$baseurl = JURI::base();

$field = new JHTMLAdsmanagerField($conf,$field_values,"2",$fieldmodel->getPlugins());//0 =>list

$url = "index.php";

require(JModuleHelper::getLayoutPath('mod_adsmanager_search'));
$content="";
$path = JPATH_ADMINISTRATOR.'/../libraries/joomla/database/table';
JTable::addIncludePath($path);