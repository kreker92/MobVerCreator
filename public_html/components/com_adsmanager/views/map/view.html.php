<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

require_once(JPATH_ROOT."/components/com_adsmanager/helpers/field.php");
require_once(JPATH_ROOT."/components/com_adsmanager/helpers/general.php");

if (!function_exists("adsmanagerAdvModuleSelectCategories")) {
	function adsmanagerAdvModuleSelectCategories($id, $level, $children,$catid) {
		if (@$children[$id]) {
			foreach ($children[$id] as $row) {
				if ($level == "") { ?>
					<option style="background-color:#dcdcc3;" value="<?php echo $row->id; ?>" <?php if ($catid == $row->id) echo "selected='selected'"; ?>><?php echo "-- ". $row->name." --"; ?></option>
				<?php } else { ?>
					<option value="<?php echo $row->id; ?>" <?php if ($catid == $row->id) echo "selected='selected'"; ?>><?php echo $row->name; ?></option>
				<?php } 
				adsmanagerAdvModuleSelectCategories($row->id, $level." >> ",$children,$catid);
			}
		}
	}
}

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class AdsmanagerViewMap extends TView
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$user		= JFactory::getUser();
		$document	= JFactory::getDocument();
		
		$contentmodel	= $this->getModel( "content" );
		$catmodel		= $this->getModel( "category" );
		$columnmodel	= $this->getModel( "column" );
		$positionmodel	= $this->getModel( "position" );
		$fieldmodel	    = $this->getModel( "field" );
		$configurationmodel	= $this->getModel( "configuration" );
		
		$uri = JFactory::getURI();
		$this->requestURL = $uri->toString();

		$conf = $configurationmodel->getConfiguration();
		$this->assignRef('conf',$conf);
		
		$filters = array();
		
		$new_search = JRequest::getInt( 'new_search',	0 );
		jimport( 'joomla.session.session' );
        
        $catid = JRequest::getInt( 'catid',	0 );
        if ($catid == -1)
            $catid = 0;
        $searchfields = $fieldmodel->getFields($catid);
        $filters['fields'] = $fieldmodel->getSearchFieldsSql($searchfields);
        $currentSession = JSession::getInstance('none',array());
        $currentSession->set("searchfieldssql",$filters['fields']);
        $currentSession->set("search_fields",JRequest::get( 'request' ));
        $currentSession->set("searchfieldscatid",$catid);

        $tsearch = JRequest::getVar( 'tsearch',	$currentSession->get('tsearch','','adsmanager'));
        $currentSession->set('tsearch',$tsearch,'adsmanager');
		
        //var_dump($filters['fields']);
		
		$defaultvalues = $currentSession->get("search_fields","1");
		$this->assignRef('searchfieldvalues',$defaultvalues);
		
		$catid = $currentSession->get("searchfieldscatid",JRequest::getInt('catid', 0 ));
		$this->assignRef('catid',$catid);
		
		$filters['publish'] =  1;
		if ($catid != 0)
			$filters['category'] = $catid;
		
		if ($tsearch != "")
		{
			$filters['search'] = $tsearch;
		}
		$this->assignRef('text_search',$tsearch);
		
		$orderfields = $fieldmodel->getOrderFields($catid);
		
		$this->assignRef('orders',$orderfields);
			
		$contents = $contentmodel->getContents($filters);
		$this->assignRef('contents',$contents);
		
		$this->assignRef('requestURL',$requestURL);
		
		$document->setTitle(JText::_('ADSMANAGER_MAP'));
		
		$field_values = $fieldmodel->getFieldValues();
		
		$fields = $fieldmodel->getFields();
		$this->assignRef('fields',$fields);
		
		$plugins = $fieldmodel->getPlugins();
		$field = new JHTMLAdsmanagerField($conf,$field_values,0,$plugins);
		$this->assignRef('field',$field);
				
		$general = new JHTMLAdsmanagerGeneral($catid,$conf,$user);
		$this->assignRef('general',$general);
		
		$searchfields = $fieldmodel->getSearchFields();
		$cats = $catmodel->getCatTree();
		$this->assignRef('searchfields',$searchfields);
		$this->assignRef('cats',$cats);
        $field = new JHTMLAdsmanagerField($conf,$field_values,"2",$plugins);
		$this->assignRef('field',$field);

		parent::display($tpl);
	}
}
