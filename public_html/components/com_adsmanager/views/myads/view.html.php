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

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class AdsmanagerViewMyads extends TView
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$user		= JFactory::getUser();
		$pathway	= $app->getPathway();
		$document	= JFactory::getDocument();
		
		if ($user->id == 0) {
			TTools::redirectToLogin("index.php?option=com_adsmanager&view=myads");
			return;  
	    }
		
		$contentmodel	=$this->getModel( "content" );
		$catmodel		=$this->getModel( "category" );
		$positionmodel	=$this->getModel( "position" );
		$columnmodel	=$this->getModel( "column" );
		$fieldmodel	    =$this->getModel( "field" );
		$usermodel	    =$this->getModel( "user" );
		$configurationmodel	=$this->getModel( "configuration" );
		
		$uri = JFactory::getURI();
		$this->requestURL = $uri->toString();

		// Get the parameters of the active menu item
		$menus	= $app->getMenu();
		$menu    = $menus->getActive();
		
		$conf = $configurationmodel->getConfiguration();
		
		jimport( 'joomla.session.session' );	
		$currentSession = JSession::getInstance('none',array());
		$currentSession->set("search_fields","");
		$currentSession->set("searchfieldscatid",0);
		$currentSession->set("searchfieldssql"," 1 ");
		$currentSession->set("tsearch","");
		
		$filters = array();
		$filters['user'] = $user->id;
		
		$tsearch = JRequest::getVar( 'tsearch',	'');
		if ($tsearch != "")
		{
			$filters['search'] = $tsearch;
		}
		$this->assignRef('tsearch',$tsearch);
		
		$username = $usermodel->getUser($user->id);
		if ($conf->display_fullname) {
			$name = JText::_('ADSMANAGER_LIST_USER_TEXT')." ".$user->name;
		} else {
			$name = JText::_('ADSMANAGER_LIST_USER_TEXT')." ".$user->username;
		}
			
		$orderfields = $fieldmodel->getOrderFields(0);
		
		$this->assignRef('orders',$orderfields);
					
		$limitstart = JRequest::getInt("limitstart",0);	
		$limit = $app->getUserStateFromRequest('com_adsmanager.front_ads_per_page','limit',$conf->ads_per_page, 'int');
		
		
		$order = $app->getUserStateFromRequest('com_adsmanager.front_content.order','order',0,'int');
		$orderdir = $app->getUserStateFromRequest('com_adsmanager.front_content.orderid','orderdir','DESC');
		$orderdir = strtoupper($orderdir);
		if (($orderdir != "DESC") && ($orderdir != "ASC")) {
			$orderdir = "DESC";
		}
		$filter_order = $contentmodel->getFilterOrder($order);
		$filter_order_dir = $orderdir;
		$this->assignRef('order',$order);
		$this->assignRef('orderdir',$orderdir);

        $total = $contentmodel->getNbContents($filters,1);
		$contents = $contentmodel->getContents($filters,$limitstart, $limit,$filter_order,$filter_order_dir,1);
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		$this->assignRef('pagination',$pagination);
		
		$this->assignRef('list_name',$name);
		$this->assignRef('contents',$contents);
		
		$fields = $fieldmodel->getFields();
		$this->assignRef('fields',$fields);
		
		$this->assignRef('conf',$conf);
		$this->assignRef('userid',$user->id);
		
		$document->setTitle( JText::_('ADSMANAGER_PAGE_MY_ADS'));		
		$field_values = $fieldmodel->getFieldValues();
		
		$plugins = $fieldmodel->getPlugins();
		$field = new JHTMLAdsmanagerField($conf,$field_values,'1',$plugins);
		$this->assignRef('field',$field);
				
		$general = new JHTMLAdsmanagerGeneral(0,$conf,$user);
		$this->assignRef('general',$general);
		
		if (PAIDSYSTEM == 1) {
			require_once(JPATH_ROOT.'/administrator/components/com_paidsystem/models/top.php');
			$topmodel =  new PaidsystemModelTop();
			$tops = $topmodel->getTops();
			$topoption = null;
			foreach($tops as $t) {
				if ($t->duration == 1) {
					$topoption = $t;
					break;
				}
			}
			$this->assignRef('topoption',$topoption);
		}
		
		parent::display($tpl);
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
