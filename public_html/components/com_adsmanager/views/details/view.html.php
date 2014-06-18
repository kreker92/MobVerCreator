<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

require_once(JPATH_BASE."/components/com_adsmanager/helpers/field.php");
require_once(JPATH_BASE."/components/com_adsmanager/helpers/general.php");

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class AdsmanagerViewDetails extends TView
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$user		= JFactory::getUser();
		$pathway	= $app->getPathway();
		$document	= JFactory::getDocument();
		
		$contentmodel	=$this->getModel( "content" );
		$catmodel		=$this->getModel( "category" );
		$positionmodel	=$this->getModel( "position" );
		$fieldmodel	    =$this->getModel( "field" );
		$configurationmodel	=$this->getModel( "configuration" );

		// Get the parameters of the active menu item
		$menus	= $app->getMenu();
		$menu    = $menus->getActive();
		
		$conf = $configurationmodel->getConfiguration();
		
		$catid = JRequest::getInt( 'catid',	0 );
		if ($catid != "0") {
			$category = $catmodel->getCategory($catid);
			$category->img = TTools::getCatImageUrl($catid,true);
		}
		else
		{
			$category = new stdClass();
			$category->name = JText::_("ADSMANAGER_ALL_ADS");
			$category->description = "";
			$category->img = "";
		}
		
		$pathlist = $catmodel->getPathList($catid,'read');
		$this->assignRef('pathlist',$pathlist);
		
		$positions = $positionmodel->getPositions();
		$fDisplay = $fieldmodel->getFieldsbyPositions();
		
		$field_values = $fieldmodel->getFieldValues();
		
		$contentid = JRequest::getInt( 'id',	0 );
		$content = $contentmodel->getContent($contentid,false);
		if (($content->published == false)&&($content->userid != $user->id)) {
			if (ADSMANAGER_SPECIAL == 'sale') {
				require_once(JPATH_ROOT.'/administrator/components/com_sale/models/configuration.php');
				$model = new SaleModelConfiguration();
				$saleconf = $model->getConfiguration();
				if ($content->userid != $saleconf->userid) {
					$app->redirect( TRoute::_('index.php?option=com_adsmanager&view=list&catid='.$catid));
				}
				//Display Ad
			} else if (ADSMANAGER_SPECIAL == 'barter') {
			} else {
				//JError::raiseError(404, JText::_("Page Not Found"));
				$app->redirect( TRoute::_('index.php?option=com_adsmanager&view=list&catid='.$catid));
			}
		}
		
		if ($content == null)
			$app->redirect( JRoute::_('index.php?option=com_adsmanager&view=front&status=error'), JText::_('ADSMANAGER_ADS_NO_RIGHT'),'message' );
		
		$this->assignRef('list_name',$category->name);
		$this->assignRef('list_img',$category->img);
		$this->assignRef('list_description',$category->description);
		$this->assignRef('positions',$positions);	
		$this->assignRef('fDisplay',$fDisplay);	
		$this->assignRef('conf',$conf);
		$this->assignRef('userid',$user->id);
		
		$fields = $fieldmodel->getFields();
		$this->assignRef('fields',$fields);
		
		$document->setTitle( JText::_('ADSMANAGER_PAGE_TITLE')." ".JText::_($category->name)." - ".$content->ad_headline);

		//set breadcrumbs 
		$pathlist = $catmodel->getPathList($catid);
		$nb = count($pathlist);
		for ($i = $nb - 1 ; $i >=0;$i--)
		{
			$pathway->addItem($pathlist[$i]->text, $pathlist[$i]->link);
		}
		
		// need to be before getMultiLangText
		$plugins = $fieldmodel->getPlugins();
		
		if (ADSMANAGER_SPECIAL == "abrivac") {
			$pathway->addItem(getMultiLangText($content->ad_headline), "#");
		} else {
			$pathway->addItem($content->ad_headline, "#");
		}
		
		
		$field = new JHTMLAdsmanagerField($conf,$field_values,'1',$plugins);
		
		$this->assignRef('field',$field);
		
		$general = new JHTMLAdsmanagerGeneral($catid,$conf,$user);
		$this->assignRef('general',$general);
		
		//
		// Process the content plugins.
		//
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('adsmanagercontent');
		
		
		$results = $dispatcher->trigger('ADSonContentPrepare', array ($content));

		$event = new stdClass();
		$results = $dispatcher->trigger('ADSonContentAfterTitle', array ($content));
		$event->onContentAfterTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('ADSonContentBeforeDisplay', array ($content));
		$event->onContentBeforeDisplay = trim(implode("\n", $results));

		$results = $dispatcher->trigger('ADSonContentAfterDisplay', array ($content));
		$event->onContentAfterDisplay = trim(implode("\n", $results));
		
		$content->event = $event;
		$this->assignRef('content',$content);
		
		if ($conf->metadata_mode == 'automatic') {
			if (ADSMANAGER_SPECIAL == "abrivac") {
				$content->metadata_description = getMultiLangText($content->ad_description);
				$content->metadata_keywords = str_replace(" ",",",getMultiLangText($content->ad_headline));
			} else {
				$content->metadata_description = $content->ad_text;
				$content->metadata_keywords = str_replace(" ",",",$content->ad_headline);
			}
		}
		
		parent::display($tpl);
	}
	
	function loadScriptImage($image_display)
	{
		$document = JFactory::getDocument();
		
		switch($image_display)
		{
			case 'popup':
				$document->addCustomTag('
				<script language="JavaScript" type="text/javascript">
				<!--
				function popup(img) {
				titre="Popup Image";
				titre="Agrandissement"; 
				w=open("","image","width=400,height=400,toolbar=no,scrollbars=no,resizable=no"); 
				w.document.write("<html><head><title>"+titre+"</title></head>"); 
				w.document.write("<script language=\"javascript\">function checksize() { if	(document.images[0].complete) {	window.resizeTo(document.images[0].width+10,document.images[0].height+50); window.focus();} else { setTimeout(\'checksize()\',250) }}</"+"script>"); 
				w.document.write("<body onload=\"checksize()\" leftMargin=0 topMargin=0 marginwidth=0 marginheight=0>");
				w.document.write("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" height=\"100%\"><tr>");
				w.document.write("<td valign=\"middle\" align=\"center\"><img src=\""+img+"\" border=0 alt=\"Mon image\">"); 
				w.document.write("</td></tr></table>");
				w.document.write("</body></html>"); 
				w.document.close(); 
				} 
				
				-->
				</script>');
				break;
			case 'lightbox':
			case 'lytebox': 
 				$document->addCustomTag('<script type="text/javascript" src="'.$this->get("baseurl").'/components/com_adsmanager/lytebox/js/lytebox_322cmod1.3.js"></script>'); 
 				$document->addCustomTag('<link rel="stylesheet" href="'.$this->get("baseurl").'/components/com_adsmanager/lytebox/css/lytebox_322cmod1.3.css" type="text/css" media="screen" />');
 				break; 
			case 'highslide': 
				$document->addCustomTag('<script type="text/javascript" src="'.$this->get("baseurl").'/components/com_adsmanager/highslide/js/highslide-full.js"></script>'); 
				$document->addCustomTag('<script type="text/javascript">hs.graphicsDir = "'.$this->get("baseurl").'" + hs.graphicsDir;</script>'); 
				$document->addCustomTag('<link rel="stylesheet" href="'.$this->get("baseurl").'/components/com_adsmanager/highslide/css/highslide-styles.css" type="text/css" media="screen" />'); 
				break; 
			default:
				break;
		}
	}
}
