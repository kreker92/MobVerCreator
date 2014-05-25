<?php
/**
* Tab to display recently posted classified ads in the AdsManager component in a Community Builder profile
* Author: Thomas PAPIN (support@juloa.com)
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

require_once(JPATH_ROOT."/components/com_adsmanager/lib/core.php");

class AdsManagerTab extends cbTabHandler {

	function getAdsSessionsTab() {
		$cbTabHandler();
	}
			
  function getDisplayTab($tab,$user,$ui) {
	$app = JFactory::getApplication();
	
  	require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/adsmanager.php');
	require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/column.php');
	require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/category.php');
	require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/configuration.php');
	require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/content.php');
	require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/field.php');
	require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/position.php');
	require_once(JPATH_ROOT.'/administrator/components/com_adsmanager/models/user.php');
	
	$juser = JFactory::getUser();

	if (($user->id == $juser->id))   {
		$myads = true;
	} else {
		$myads = false;
	}

	if ($myads == true) 
		require_once(JPATH_ROOT.'/components/com_adsmanager/views/myads/view.html.php');
	else
		require_once(JPATH_ROOT.'/components/com_adsmanager/views/list/view.html.php');
	
	if ( file_exists( JPATH_ROOT. "/components/com_paidsystem/api.paidsystem.php")) 
	{
		require_once(JPATH_ROOT . "/components/com_paidsystem/api.paidsystem.php");
	}

	$uri = JFactory::getURI();
	$baseurl = JURI::base();
	$document = JFactory::getDocument();
  	$templateDir = JPATH_ROOT . '/templates/' . $app->getTemplate();
  	if (is_file($templateDir.'/html/com_adsmanager/css/adsmanager.css')) {
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
		$document->addStyleSheet($templateDir.'/html/com_adsmanager/css/adsmanager.css');
	} else {
		$document->addStyleSheet($baseurl.'components/com_adsmanager/css/adsmanager.css');
	}
	
  	$contentmodel	 = new AdsmanagerModelContent();
	$catmodel		 = new AdsmanagerModelCategory();
	$positionmodel	 = new AdsmanagerModelPosition();
	$columnmodel	 = new AdsmanagerModelColumn();
	$fieldmodel	     = new AdsmanagerModelField();
	$usermodel		 = new AdsmanagerModelUser();
	$adsmanagermodel = new AdsmanagerModelAdsmanager();
	$configurationmodel	= new AdsmanagerModelConfiguration();
	
	$catid = 0;
	
	$config = array();
	
	
	$config = array();
	$templateDir = JPATH_ROOT . '/templates/' . $app->getTemplate();
    
	if ($myads == true) {
		if (is_file($templateDir.'/html/com_adsmanager/myads/default.php')) {
			$config['template_path'] = JPATH_BASE.'/templates/' . $app->getTemplate().'/html/com_adsmanager/myads';
		} else {
			$config['template_path'] = JPATH_BASE.'/components/com_adsmanager/views/myads/tmpl';
		}
		$view = new AdsmanagerViewMyads($config);
	} else {
		if (is_file($templateDir.'/html/com_adsmanager/list/default.php')) {
			$config['template_path'] = JPATH_BASE.'/templates/' . $app->getTemplate().'/html/com_adsmanager/list';
		} else {
			$config['template_path'] = JPATH_BASE.'/components/com_adsmanager/views/list/tmpl';
		}
		$view = new AdsmanagerViewList($config);
	}

	$uri = JFactory::getURI();
	$requestURL = $uri->toString();
	
	$conf = $configurationmodel->getConfiguration();
	
	$filters = array();
	if ($myads == false) {
		$filters['publish'] =  1;
	}
	$filters['user'] = $user->id;
	
  	$tsearch = $app->getUserStateFromRequest('com_adsmanager.front_content.tsearch','tsearch',"");
	if ($tsearch != "")
	{
		$filters['search'] = $tsearch;
		$view->assignRef('tsearch',$tsearch);
	}

	$category = new stdClass();
	if ($conf->display_fullname) {
		$category->name = JText::_('ADSMANAGER_LIST_USER_TEXT')." ".$user->name;
	} else {
		$category->name = JText::_('ADSMANAGER_LIST_USER_TEXT')." ".$user->username;
	}
	
	$subcats = array();
	$pathlist = array();
	
	$orderfields = $fieldmodel->getOrderFields(0);
	
	$uri = JFactory::getURI();
	$baseurl = JURI::base();
	$view->assign("baseurl",$baseurl);
	$view->assignRef("baseurl",$baseurl);
	
	$view->assignRef('catid',$catid);
	

	$view->assignRef('listuser',$user->id);
	
	$modeuser = 1;
	$view->assignRef('modeuser',$modeuser);
	
	$tsearch = "";
	$view->assignRef('tsearch',$tsearch);
	
	$view->assignRef('orders',$orderfields);
	$view->assignRef('subcats',$subcats);
	$view->assignRef('pathlist',$pathlist);
		
	$limit			  = $app->getUserStateFromRequest('com_adsmanager.front_ads_per_page','limit',$conf->ads_per_page, 'int');
	$limitstart		  = JRequest::getInt("limitstart",0);
	
	$order = $app->getUserStateFromRequest('com_adsmanager.front_content.order','order',0,'int');
	$orderdir = $app->getUserStateFromRequest('com_adsmanager.front_content.orderdir','orderdir','DESC');
	$orderdir = strtoupper($orderdir);
	if (($orderdir != "DESC") && ($orderdir != "ASC")) {
		$orderdir = "DESC";
	}
	$filter_order = $contentmodel->getFilterOrder($order);
	$filter_order_dir = $orderdir;
	$view->assignRef('order',$order);
	$view->assignRef('orderdir',$orderdir);

	$view->assignRef('lists',$lists);
	
	$total = $contentmodel->getNbContents($filters);
	$contents = $contentmodel->getContents($filters,$limitstart, $limit,$filter_order,$filter_order_dir,1);
	
	$userId = JRequest::getInt("user",0);
    
    if($userId != 0){
        $userId = '&user='.$userId;
    }else{
        $userId = '';
    }
    
	$pagination = new JPagination2($total, $limitstart, $limit,"index.php?option=com_comprofiler&tab=AdsmanagerTab".$userId);
	$view->assignRef('pagination',$pagination);
	
	$view->assignRef('list_name',$category->name);
	$view->assignRef('list_img',$category->img);
	$view->assignRef('list_description',$category->description);
	$view->assignRef('contents',$contents);
	
	$mode = $app->getUserStateFromRequest('com_adsmanager.front_content.mode','mode',$conf->display_expand);
	if ($mode == 2)
		$mode = 0;
	$view->assignRef('mode',$mode);
	
	if ($mode == 0) {
		$columns = $columnmodel->getColumns($catid);
		$fcolumns = $fieldmodel->getFieldsbyColumns();
		$view->assignRef('columns',$columns);	
		$view->assignRef('fColumns',$fcolumns);	
	}
	else {
		$positions = $positionmodel->getPositions();
		$fDisplay = $fieldmodel->getFieldsbyPositions();
		$view->assignRef('positions',$positions);	
		$view->assignRef('fDisplay',$fDisplay);	
	}
	
	$fields = $fieldmodel->getFields();
	$view->assignRef('fields',$fields);
	
	$view->assignRef('conf',$conf);
	
	$my = JFactory::getUser();
	$view->assignRef('userid',$my->id);
	
	$view->assignRef('requestURL',$requestURL);
	
	$field_values = $fieldmodel->getFieldValues();
	
	$plugins = $fieldmodel->getPlugins();
	$field = new JHTMLAdsmanagerField($conf,$field_values,$mode,$plugins,null);
	$view->assignRef('field',$field);
	
	$general = new JHTMLAdsmanagerGeneral($catid,$conf,$user);
	$view->assignRef('general',$general);

	$return = $view->loadTemplate(null);
	
	$path = JPATH_ADMINISTRATOR.'/../libraries/joomla/database/table';
	JTable::addIncludePath($path);
	
	return $return;
  }
}
