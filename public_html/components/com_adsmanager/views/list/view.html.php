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
class AdsmanagerViewList extends TView
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
		
		$catid = JRequest::getInt( 'catid',	0 );
		$this->assignRef('catid',$catid);
		if ($catid != "0") {
			$category = $catmodel->getCategory($catid);
			$category->img = TTools::getCatImageUrl($catid,true);
		}
		else
		{
			$category = new stdClass();
			$category->name = JText::_("ADSMANAGER_ALL_ADS");
			$category->description = "";
			$category->img = $this->get('baseurl').'components/com_adsmanager/images/default.gif';
		}
		
		jimport( 'joomla.session.session' );	
		$currentSession = JSession::getInstance('none',array());
		$currentSession->set("search_fields","");
        $currentSession->set("searchfieldssql"," 1 ");
		$currentSession->set("searchfieldscatid",0);
		$currentSession->set("tsearch","",'adsmanager');
		
		$global_filter = JRequest::getInt( 'global_filter',	0 );
		if ($global_filter == 1){
			$searchfields = $fieldmodel->getFields(0);
			$globalfiltersql = $fieldmodel->getSearchFieldsSql($searchfields);
			$currentSession->set("sqlglobalfilter",$globalfiltersql);
			$currentSession->set("globalfilter_values",JRequest::get( 'request' ));
		}
		
		$filters = array();
		$filters['publish'] =  1;
		if ($catid != 0)
			$filters['category'] = $catid;
			
        if(isset($conf->publication_date) && $conf->publication_date == 1) {
            $filters['publication_date'] = 1;
        }
        
		$modeuser = 0;
			
		$listuser = JRequest::getInt( 'user',	-1 );
		if (($listuser == 0)&&($user->id != 0))
			$listuser = $user->id;
			
		if ($listuser != -1) {			
			$filters['user'] = $listuser;
			$user = $usermodel->getUser($listuser);
			if ($conf->display_fullname) {
				$category->name = JText::_('ADSMANAGER_LIST_USER_TEXT')." ".$user->name;
			} else {
				$category->name = JText::_('ADSMANAGER_LIST_USER_TEXT')." ".$user->username;
			}
			$this->assignRef('listuser',$listuser);
			$modeuser = 1;		
		}
		
		$this->assignRef('modeuser',$modeuser);
		
		$session = JFactory::getSession();
		$list_type = $session->get('list_type','','adsmanager');
		$list_value = $session->get('list_value','','adsmanager');

		if ($listuser != -1) {	
			if (($list_type == 'user')&&($list_value == $listuser)) {
				$tsearch = JRequest::getVar( 'tsearch',	$session->get('tsearch','','adsmanager'));
				$session->set('tsearch',$tsearch,'adsmanager');
			} else {
				$session->set('list_type','user','adsmanager');
				$session->set('list_value',$listuser,'adsmanager');
				$session->set('tsearch','','adsmanager');
				$tsearch = '';
			}
		} else {
			if (($list_type == 'category')&&($list_value == $catid)) {
				$tsearch = JRequest::getVar( 'tsearch',	$session->get('tsearch','','adsmanager'));
				$session->set('tsearch',$tsearch,'adsmanager');
			} else {
				$session->set('list_type','category','adsmanager');
				$session->set('list_value',$catid,'adsmanager');
				$session->set('tsearch','','adsmanager');
				$tsearch = '';
			}
		}
		
		if ($tsearch != "")
		{
			$filters['search'] = $tsearch;
		}
		$this->assignRef('tsearch',$tsearch);

		if ($listuser == -1) {
			$subcats = $catmodel->getSubCats($catid,'read');
			$pathlist = $catmodel->getPathList($catid,'read');
		}
		else
		{
			$subcats = array();
			$pathlist = array();
		}
		
		$orderfields = $fieldmodel->getOrderFields($catid);
		
		$this->assignRef('orders',$orderfields);
		
		$this->assignRef('subcats',$subcats);
		$this->assignRef('pathlist',$pathlist);
			
		$limitstart = JRequest::getInt("limitstart",0);
			
		$limit = $conf->ads_per_page;
		
		$fields = $fieldmodel->getFields();
		$this->assignRef('fields',$fields);
		
		$order = $app->getUserStateFromRequest('com_adsmanager.front_content.order','order',0,'int');
		$orderdir = $app->getUserStateFromRequest('com_adsmanager.front_content.orderdir','orderdir','DESC');
		$orderdir = strtoupper($orderdir);
		if (($orderdir != "DESC") && ($orderdir != "ASC")) {
			$orderdir = "DESC";
		}
		$filter_order = $contentmodel->getFilterOrder($order);
		$filter_order_dir = $orderdir;
		$this->assignRef('order',$order);
		$this->assignRef('orderdir',$orderdir);

		$this->assignRef('lists',$lists);
		
        $total = $contentmodel->getNbContents($filters);
		$contents = $contentmodel->getContents($filters,$limitstart, $limit,$filter_order,$filter_order_dir);
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		$this->assignRef('pagination',$pagination);
		
		$this->assignRef('list_name',$category->name);
		$this->assignRef('list_img',$category->img);
		$this->assignRef('list_description',$category->description);
		$this->assignRef('contents',$contents);
		
		$mode = $app->getUserStateFromRequest('com_adsmanager.front_content.mode','mode',$conf->display_expand);
		if ($mode == 2)
			$mode = 0;
		$this->assignRef('mode',$mode);
		
		$columns = array();
		$fcolumns = array();
		$positions = array();
		$fDisplay = array();
		
		if ($mode == 0) {
			$columns = $columnmodel->getColumns($catid);
			$fcolumns = $fieldmodel->getFieldsbyColumns();
			$this->assignRef('columns',$columns);	
			$this->assignRef('fColumns',$fcolumns);	
		}
		else {
			$positions = $positionmodel->getPositions();
			$fDisplay = $fieldmodel->getFieldsbyPositions();
			$this->assignRef('positions',$positions);	
			$this->assignRef('fDisplay',$fDisplay);	
		}
		
		
		
		$this->assignRef('conf',$conf);
		$this->assignRef('userid',$user->id);
		
		$this->assignRef('requestURL',$requestURL);
		
		$document->setTitle( JText::_('ADSMANAGER_PAGE_TITLE'). JText::_($category->name) );
		if (($catid != 0)&&($conf->metadata_mode != 'nometadata')) {
			$document->setMetaData("description", $category->metadata_description);
			$document->setMetaData("keywords", $category->metadata_keywords);
		}
		
		$field_values = $fieldmodel->getFieldValues();
		
		$plugins = $fieldmodel->getPlugins();
		$field = new JHTMLAdsmanagerField($conf,$field_values,$mode,$plugins);
		$this->assignRef('field',$field);
		
		//set breadcrumbs 
		$nb = count($pathlist);
		for ($i = $nb - 1 ; $i >=0;$i--)
		{
			$pathway->addItem($pathlist[$i]->text, $pathlist[$i]->link);
		}
		
		$general = new JHTMLAdsmanagerGeneral($catid,$conf,$user);
		$this->assignRef('general',$general);
		
		$searchfields = $fieldmodel->getSearchFields();
		$cats = $catmodel->getCatTree();
		$this->assignRef('searchfields',$searchfields);
		$this->assignRef('cats',$cats);
		
		$defaultvalues = array();
		$this->assignRef('defaultvalues',$defaultvalues);
		$this->assignRef('catid',$catid);
		$text_search = "";
		$this->assignRef('text_search',$text_search);

		parent::display($tpl);
	}
	
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
				w.document.write("<td valign=\"middle\" align=\"center\"><img src=\""+img+"\" border=0 alt=\"image\">"); 
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
