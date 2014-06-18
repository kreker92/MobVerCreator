<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'tables');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'models'.DS.'field.php');
require_once(JPATH_ROOT.DS.'components'.DS.'com_adsmanager'.DS.'helpers'.DS.'field.php');

//TMail is normally loaded via lib/core.php, but some users reported an issue.
require_once(JPATH_ROOT.DS.'components'.DS.'com_adsmanager'.DS.'lib'.DS.'mail.php');

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class AdsmanagerModelContent extends TModel
{
	function getPendingContent($contentid) {
		#__adsmanager_pending_ads WHERE contentid = ".(int)
		$sql = " SELECT a.*,u.username as user,u.name as fullname FROM #__adsmanager_pending_ads as a "
			 . " LEFT JOIN #__users as u ON a.userid = u.id "
			 . " WHERE a.contentid = ".(int)$contentid;
		$this->_db->setQuery($sql);
    	$pending = $this->_db->loadObject();

    	if ($pending == null) 
    		return null;
    		
		$pending->data = json_decode($pending->content);
		
		
		$content = new stdClass();
		$content->id = $contentid;
		$content->userid = $pending->userid;
		if (isset($pending->data->fields)) {
			foreach($pending->data->fields as $name => $value) {
				$content->$name= $value;	
			}
		}
		$content->user = $pending->user;
		$content->fullname = $pending->fullname;
		
		$content->pending = 1;
		
		$this->_db->setQuery(" SELECT * FROM #__adsmanager_config ");
		$conf = $this->_db->loadObject();
		
		$nbimages = $conf->nb_images;
		if (function_exists("getMaxPaidSystemImages"))
		{
			$nbimages += getMaxPaidSystemImages();
		}
		
		$baseurl = JURI::base();
		
		$content->catsid = array();
		$content->cats = array();
		if (is_array($pending->data->categories)) {
			foreach($pending->data->categories as $cat) {
				$content->catsid[] = $cat;
	    			$content->catid = $cat;
				$category = new stdClass();
				$category->catid = $cat;
				$content->cats[] = $category;
			}
		} else {
		    	$content->catid = $pending->data->categories;
		    	$content->catsid[] = $pending->data->categories;
			$category = new stdClass();
			$category->catid = $cat;
			$content->cats[] = $category;
		} 
		
		$images = array();
		
		if (isset($pending->data->images)) {
			foreach($pending->data->images as $image) {
				$images[] = $image;
			}
		}
	
		if (isset($pending->data->paid->images)) {
			foreach($pending->data->paid->images as $image) {
				$images[] = $image;
			}
		}

		$content->images = $images;
		
		if ($pending->data->paid->featured) {
			$content->featured = $pending->data->paid->featured;
		}
		
		if ($pending->data->paid->top) {
			$content->top = $pending->data->paid->top;
		}
		
		if ($pending->data->paid->highlight) {
			$content->highlight = $pending->data->paid->highlight;
		}
		$content->duration = @$pending->data->duration;

		return $content;
	}
	
	function getContentCategories($contentid) {
		$query = "SELECT catid FROM #__adsmanager_adcat WHERE adid = ".$contentid;
		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();
		$cats = array();
		foreach($results as $cat) {
			$cats[] = $cat->catid;
		}
		return $cats;
	}
	
	
	function getContent($contentid,$onlyPublished = true,$admin=0) {
		if ((JRequest::getInt('pending',0) != 0)&&(function_exists("getMaxPaidSystemImages"))) {
			$this->_db->setQuery("SELECT count(*) FROM #__adsmanager_pending_ads WHERE contentid=".$contentid);
			$total = $this->_db->loadResult();
			if ($total > 0) {
				return $this->getPendingContent($contentid);
			}
		}
		
		$sql = "SELECT a.*, p.name as parent, p.id as parentid, c.name as cat, c.id as catid,u.username as user,u.name as fullname ".
			" FROM #__adsmanager_ads as a ".
			" INNER JOIN #__adsmanager_adcat as adcat ON adcat.adid = a.id ".
			" LEFT JOIN #__users as u ON a.userid = u.id ".
			" INNER JOIN #__adsmanager_categories as c ON adcat.catid = c.id ".
			" LEFT JOIN #__adsmanager_categories as p ON c.parent = p.id ";
    
  		$sql .= " WHERE a.id = ".(int)$contentid;
  		
  		if ($onlyPublished == true)
			$sql .= " AND c.published = 1 AND a.published = 1 ";
        
        if($admin != 1){
        
            if(version_compare(JVERSION, '1.6', 'ge')) {
                
                $listCategories = TPermissions::getAuthorisedCategories('read');

                //If the variable is an array and if it's not empty, we add a filter to the request
                //If not we're not return any category
                if(is_array($listCategories) && !empty($listCategories)){
                    $categories = implode(',',$listCategories);
                    $listCategories = " AND c.id IN (".$categories.") "; 
                }else{
                    $listCategories = " AND 0 ";
                }
                
            } else {
                $listCategories = "";
            }
				
			$sql .= $listCategories;
			
        }
            
		if (function_exists("updateQuery")) {
			updateQuery($sql);
		}
			
    	$this->_db->setQuery($sql);
    	$contents = $this->_db->loadObjectList();

    	if (count($contents) > 0) {
    		$content = $contents[0];
    		$content->cats = array();
    		$content->catsid = array();
    		foreach($contents as $key => $c) {
    			$cat = new stdClass();
    			$cat->parentid = $c->parentid;
    			$cat->parent = $c->parent;
    			$cat->cat = $c->cat;
    			$cat->catid = $c->catid;
    			$content->cats[] = $cat;
    			$content->catsid[] = (int)$c->catid;
    			$content->catid = $c->catid;
    		}
    		$content->images = @json_decode($content->images);
    		if (!is_array($content->images))
    			$content->images = array();
    		return $content;
    	}
    	else
    		return null;			
    }
    
	function _recurseSearch ($rows,&$list,$catid){
		if(isset($rows))
		{
			foreach($rows as $row) {
				if ($row->parent == $catid)
				{
					$list[]= $row->id;
					$this->_recurseSearch($rows,$list,$row->id);
				} 
			}
		}
	}
    
    function _getSQLFilter($filters){
   		 /* Filters */
        $search = "";
    	if (isset($filters))
    	{
	    	foreach($filters as $key => $filter)
	    	{
	    		if ($search == "")
	    			$temp = " WHERE ";
	    		else
	    			$temp = " AND ";
	    		switch($key)
	    		{
	    			case 'category':
	    				$catid = $filter;
	    				$this->_db->setQuery( "SELECT c.id, c.name,c.parent ".
						" FROM #__adsmanager_categories as c ".
						 "WHERE c.published = 1 ORDER BY c.parent,c.ordering");
						 
						$listcats = $this->_db->loadObjectList();
						$list[] = $catid;
						$this->_recurseSearch($listcats,$list,$catid);
						$listids = implode(',', $list);
	    				$search .= $temp."c.id IN ($listids) ";break;
	    			case 'user':
	    				$search .= $temp."u.id = ".(int)$filter;break;
	    			case 'username':
                        if (version_compare(JVERSION,'1.7.0','<')) {
                            $search .= $temp."u.username LIKE '%".$this->_db->getEscaped($filter,true)."%'";
                        }else{
                            $search .= $temp."u.username LIKE '%".$this->_db->escape($filter,true)."%'";
                        }
                        break;
	    			case 'content_id':
	    				$search .= $temp."a.id = ".(int)$filter;break;
	    			case "phone":
                        if (version_compare(JVERSION,'1.7.0','<')) {
                        	$search .= $temp." a.ad_phone LIKE '%".$this->_db->getEscaped($filter,true)."%'";
                        }else{
                        	$search .= $temp." a.ad_phone LIKE '%".$this->_db->escape($filter,true)."%'";
                        }
                        break;
	    			case "ip":
                        if (version_compare(JVERSION,'1.7.0','<')) {
                            $search .= $temp." a.ad_ip LIKE '%".$this->_db->getEscaped($filter,true)."%'";
                        }else{
                            $search .= $temp." a.ad_ip LIKE '%".$this->_db->escape($filter,true)."%'";
                        }
                        break;
	    			case 'mag':
	    				$search .= $temp."a.ad_magazine = ".$this->_db->Quote($filter);break;
	    			case "online":
	    				if ($filter == 1) {
	    					$search .= $temp." (a.ad_publishtype = 'online' OR a.ad_publishtype = 'both')";
	    				} else
	    					$search .= $temp." (a.ad_publishtype = 'offline' OR a.ad_publishtype = 'both')";
	    				break;
	    			
	    			
	    			case 'publish':
	    				$search .= $temp." a.published = ".(int)$filter." AND c.published = TRUE ";break;
	    			case 'fields':
	    				$search .= $temp.$filter;break;
	    			case 'search':
	    				if (intval($filter) != 0) {
	    					$filter = JString::strtolower($filter);
	    					$id = intval($filter);
                            if (version_compare(JVERSION,'1.7.0','<')) {
                                $search .= $temp."(a.id = $id OR LOWER(a.ad_headline) LIKE '%".$this->_db->getEscaped($filter,true)."%' OR LOWER(a.ad_text) LIKE '%".$this->_db->getEscaped($filter,true)."%')";
                            }else{
                                $search .= $temp."(a.id = $id OR LOWER(a.ad_headline) LIKE '%".$this->_db->escape($filter,true)."%' OR LOWER(a.ad_text) LIKE '%".$this->_db->escape($filter,true)."%')";
                            }
	    				} else {
	    					$filter = JString::strtolower($filter);
                            if (version_compare(JVERSION,'1.7.0','<')) {
                                $search .= $temp."(LOWER(a.ad_headline) LIKE '%".$this->_db->getEscaped($filter,true)."%' OR LOWER(a.ad_text) LIKE '%".$this->_db->getEscaped($filter,true)."%')";
                            }else{
                                $search .= $temp."(LOWER(a.ad_headline) LIKE '%".$this->_db->escape($filter,true)."%' OR LOWER(a.ad_text) LIKE '%".$this->_db->escape($filter,true)."%')";
                            }
	    				}
	    				break;
                    case 'publication_date':
                        $search .= $temp." a.publication_date <= NOW()";break;
	    		}
	    	}
    	}
    	
    	$currentSession = JSession::getInstance('none',array());
    	
    	$filter = $currentSession->get("sqlglobalfilter","");
    	if ($filter != ""){
    		if (isset($filters['user'])) {
	    		$conf = TConf::getConfig();
	    		if (@$conf->globalfilter_user == 0) {
	    			return $search;
	    		}
    		}
    		// si on a que le super filter car pas de recherche classique fieldsFilter = WHERE + super filter
    		if($search == " "){
    			$search  = " WHERE $filter ";
    		} else { // si on a une recherche il faut cumuler les deux fieldsFIlters = fieldFilter + AND + super filter
    			$search  .= " AND $filter";
    		}
    	}
    	return $search;
    }
    
	function getContents($filters = null,$limitstart=null,$limit=null,$filter_order=null,$filter_order_Dir=null,$admin=0,$favorite=0)
    {
    	$sql = "SELECT a.*, p.name as parent, p.id as parentid, c.name as cat, c.id as catid,u.username as user,u.name as fullname ".
			" FROM #__adsmanager_ads as a ".
			" INNER JOIN #__adsmanager_adcat as adcat ON adcat.adid = a.id ";
		if (COMMUNITY_BUILDER == 1)
    		$sql .=	" LEFT JOIN #__comprofiler as cb ON cb.user_id = a.userid ";
		$sql .=	" LEFT JOIN #__users as u ON a.userid = u.id ".
			" INNER JOIN #__adsmanager_categories as c ON adcat.catid = c.id ".
			" LEFT JOIN #__adsmanager_categories as p ON c.parent = p.id ";
        
        if($favorite != 0) {
            $sql .= " INNER JOIN #__adsmanager_favorite as adfav ON a.id = adfav.adid";
        }
        
  		$filter = $this->_getSQLFilter($filters);
        $sql .= $filter;
           
        if($favorite != 0) {
            if($filter != null)
                $prefix = " AND ";
            else
                $prefix = " WHERE ";
            $sql .= $prefix."adfav.userid = ".(int)$favorite." ";
        }
        
        if($admin != 1) {
		
            if(version_compare(JVERSION, '1.6', 'ge')) {
                
                $listCategories = TPermissions::getAuthorisedCategories('read');

                //If the variable is an array and if it's not empty, we add a filter to the request
                //If not we're not return any category
                if(is_array($listCategories) && !empty($listCategories)){
                    $categories = implode(',',$listCategories);
                    $listCategories = " AND c.id IN (".$categories.") "; 
                }else{
                    $listCategories = " AND 0 ";
                }
                
            } else {
                $listCategories = "";
            }
				
			$sql .= $listCategories;
		
		}
        
    	if ($filter_order === null) {
    		$sql .= " GROUP BY a.id";
    	} else {
    		$sql .= " GROUP BY a.id ORDER BY $filter_order $filter_order_Dir ";
    	}
    	if (($admin == 0)&&(function_exists("updateQueryWithReorder")))
    		updateQueryWithReorder($sql);
    	else if (($admin == 1)&&(function_exists("updateQuery")))
    		updateQuery($sql);
        
    	if ($limitstart === null) {
    		$this->_db->setQuery($sql);
    	} else {
    		$this->_db->setQuery($sql,$limitstart,$limit);
    	}
    	//echo $sql;
    	$products = $this->_db->loadObjectList();
    	
    	foreach($products as &$product) {
    		$product->cat = JText::_($product->cat);
    		if ($product->parent != "")
    			$product->parent = JText::_($product->parent);
    		$product->images = @json_decode($product->images);
    		if (!is_array($product->images))
    			$product->images = array();
    	}
    	
		return $products;	
    }
    
	function getNbContents($filters = null,$admin=0)
    {
    	$sql = "SELECT a.id ".
			" FROM #__adsmanager_ads as a ".
			" INNER JOIN #__adsmanager_adcat as adcat ON adcat.adid = a.id ".
			" LEFT JOIN #__users as u ON a.userid = u.id ";
		if (COMMUNITY_BUILDER == 1) 
    		$sql .=	" LEFT JOIN #__comprofiler as cb ON cb.user_id = a.userid ";
		$sql .=	" INNER JOIN #__adsmanager_categories as c ON adcat.catid = c.id ".
			" LEFT JOIN #__adsmanager_categories as p ON c.parent = p.id ";
    
  		/* Filters */
    	$sql .= $this->_getSQLFilter($filters);

        if($admin != 1) {
		
            if(version_compare(JVERSION, '1.6', 'ge')) {
                
                $listCategories = TPermissions::getAuthorisedCategories('read');

                //If the variable is an array and if it's not empty, we add a filter to the request
                //If not we're not return any category
                if(is_array($listCategories) && !empty($listCategories)){
                    $categories = implode(',',$listCategories);
                    $listCategories = " AND c.id IN (".$categories.") "; 
                }else{
                    $listCategories = " AND 0 ";
                }
                
            } else {
                $listCategories = "";
            }
				
			$sql .= $listCategories;
		
		}
        
    	$sql .= " GROUP BY a.id";

		if (function_exists("updateQueryWithReorder"))
				updateQueryWithReorder($sql);

    	$this->_db->setQuery($sql);
    	
    	$result = $this->_db->loadObjectList();
    	$nb = count($result);
		return $nb;	
    }
    
	function increaseHits($contentid)
    {
		$sql = "UPDATE #__adsmanager_ads SET views = LAST_INSERT_ID(views+1) WHERE id = ".(int)$contentid;
		$this->_db->setQuery($sql);
		$this->_db->query();
    }
    
    function getLatestContents($nbcontents,$sort_type=0,$catselect="no")
    {
		switch($sort_type)
		{
			/* Popular */
			case 2:
				$order_sql = "ORDER BY a.views DESC,a.date_created DESC ,a.id DESC ";
				break;
				
			/* Random */
			case 1:
				$order_sql = "ORDER BY RAND() ";
				break;
				
			/* Latest */
			case 0: 
			default:
				$order_sql = "ORDER BY a.date_created DESC ,a.id DESC ";
				break;
		}
		
        if(version_compare(JVERSION, '1.6', 'ge')) {
                
            $listCategories = TPermissions::getAuthorisedCategories('read');

            //If the variable is an array and if it's not empty, we add a filter to the request
            //If not we're not return any category
            if(is_array($listCategories) && !empty($listCategories)){
                $categories = implode(',',$listCategories);
                $listCategories = " AND c.id IN (".$categories.") "; 
            }else{
                $listCategories = " AND 0 ";
            }

        } else {
            $listCategories = "";
        }
				
		$cat_query = "";
		switch($catselect)
		{
			case "no";
				break;
			
			case "-1":
				$catid = JRequest::getInt('catid', 0 );
				if (($catid != 0)&&($catid != -1))
				{	
					$this->_db->setQuery( "SELECT c.id, c.name,c.parent ".
									 " FROM #__adsmanager_categories as c ".
									 " WHERE c.published = 1 $listCategories ORDER BY c.parent,c.ordering");			 
					$listcats = $this->_db->loadObjectList();
					//List	
					$list = array();
					$list[] = $catid;
					$this->_recurseSearch($listcats,$list,$catid);
					$listids = implode(',', $list);
				
					$cat_query = "adcat.catid IN ($listids) AND ";
				}
				break;
			default:
				$this->_db->setQuery( "SELECT c.id, c.name,c.parent ".
				" FROM #__adsmanager_categories as c ".
				" WHERE c.published = 1 $listCategories ORDER BY c.parent,c.ordering");
				$listcats = $this->_db->loadObjectList();
				$catsid = explode(',',$catselect);
				//List
				$list = array();
                
                foreach($catsid as $catid){
                    $list[] = $catid;
                    $this->_recurseSearch($listcats,$list,$catid);
                }
				$listids = implode(',', $list);
				$cat_query = " adcat.catid IN ($listids) AND ";
				break;
		}
        
		if (ADSMANAGER_SPECIAL == 'newspaper') {
			$cat_query .= " (a.ad_publishtype = 'both' OR a.ad_publishtype = 'online') AND ";
		}
		
		$currentSession = JSession::getInstance('none',array());
		$sql = $currentSession->get("sqlglobalfilter","");
		$globalfilter = "";
		if ($sql != ""){
			$globalfilter  = " AND $sql ";
		}
		
		$sql =  " SELECT a.*,p.id as parentid,p.name as parent,c.id as catid, c.name as cat,u.username as user ".
			    " FROM #__adsmanager_ads as a ".
				" INNER JOIN #__adsmanager_adcat as adcat ON adcat.adid = a.id ".
				" LEFT JOIN #__users as u ON a.userid = u.id ".
				" INNER JOIN #__adsmanager_categories as c ON adcat.catid = c.id ".
				" LEFT JOIN #__adsmanager_categories as p ON c.parent = p.id ".
				" WHERE 1 $globalfilter AND $cat_query c.published = 1 and a.published = 1 GROUP BY a.id $order_sql LIMIT 0, $nbcontents";
      
		if (function_exists("updateQuery"))
    		updateQuery($sql);
    		
    	$this->_db->setQuery($sql);
    	
		$contents = $this->_db->loadObjectList();
        
		if ($contents == null)
			$contents = array();
		
		foreach($contents as &$content) {
			$content->images = @json_decode($content->images);
			$content->cat = JText::_($content->cat);
			if ($content->parent != "")
				$content->parent = JText::_($content->parent);
			if (!is_array($content->images))
				$content->images = array();
		}
		
		return $contents;
    }
	
	function getNbContentsOfUser($userid, $category = 0) {
        if($category == 0)
            $query = "SELECT count(*) FROM #__adsmanager_ads as a WHERE a.userid =".(int)$userid;
        else
            $query = "SELECT count(*) FROM #__adsmanager_ads as a
                      INNER JOIN #__adsmanager_adcat as ac
                      ON ac.adid = a.id
                      WHERE a.userid = ".(int)$userid."
                      AND ac.catid = ".(int)$category;
		$this->_db->setQuery($query);
		$nb = $this->_db-> loadResult();
		return $nb;
	}
    
	function renewContent($contentid,$ad_duration)
	{		
		$this->_db->setQuery( "SELECT expiration_date FROM #__adsmanager_ads WHERE id = ".(int)$contentid);
		$expiration_date = $this->_db->loadResult();
		$time = strtotime($expiration_date);
		if ($time < time())
		{
			$time = time();
		}
		$time = $time + ( $ad_duration * 3600 *24); 
		$newdate = date("Y-m-d",$time);
	
		$this->_db->setQuery( "UPDATE #__adsmanager_ads SET expiration_date = '$newdate', date_created = CURDATE(),recall_mail_sent=0,published=1 WHERE id=".(int)$contentid."");//TODO and recall_mail_sent = 1
		$this->_db->query();
	}
	
	function sendExpirationEmail($content,$conf)
	{
		$user = JFactory::getUser($content->userid);
		$uri	= JURI::getInstance();
		$root	= $uri->toString( array('scheme', 'host', 'port'));
		$link = $root.TRoute::_("index.php?option=com_adsmanager&view=expiration&id=".$content->id,false);
		$body = str_replace('{link}',$link,$conf->recall_text);
		$body = str_replace('{date}',strftime(JText::_('ADSMANAGER_DATE_FORMAT_LC'),strtotime($content->expiration_date)),$body);

		return $this->sendMailToUser($conf->recall_subject,$body,$user,$content,$conf,"recall");
	}
	
	function updateDate($contentid) {
		$this->_db->setQuery("UPDATE #__adsmanager_ads SET date_modified = NOW() WHERE id = ".(int)$contentid);
		$this->_db->query();
	}
	
	function manage_expiration($plugins,$conf)
	{
		$last_cron_date = date("Ymd");
		$Fnm = JPATH_BASE .'/components/com_adsmanager/cron.php';
		jimport( 'joomla.filesystem.file' );
		$content = '
	    <?php defined(\'_JEXEC\') or die( \'Restricted access\' );
	    		/**
				 * @package		AdsManager
				 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
				 * @license		GNU/GPL
				 */
	    		$last_cron_date='.$last_cron_date.';?>';
		JFile::write( $Fnm, $content );
		
		if ($conf->expiration == 1)
		{
			if ($conf->recall == 1)
			{
				$this->_db->setQuery( "SELECT * FROM #__adsmanager_ads WHERE expiration_date IS NOT NULL AND DATE_SUB(expiration_date, INTERVAL ".$conf->recall_time." DAY) <= CURDATE() AND recall_mail_sent = 0 AND published = 1");
				$contents = $this->_db->loadObjectList();
				
				$this->_db->setQuery( "UPDATE #__adsmanager_ads SET recall_mail_sent = 1 WHERE expiration_date IS NOT NULL AND DATE_SUB(expiration_date, INTERVAL ".$conf->recall_time." DAY) <= CURDATE() AND recall_mail_sent = 0 AND published = 1");
				$this->_db->query();
				
				if (isset($contents))
				{
					foreach($contents as $content)
					{
						$this->sendExpirationEmail($content,$conf);
					}
				}
				

				$this->_db->setQuery( " SELECT a.*,c.name as cat, c.id as catid FROM #__adsmanager_ads as a".
						      " INNER JOIN #__adsmanager_adcat as adcat ON adcat.adid = a.id ".
						      " INNER JOIN #__adsmanager_categories as c ON adcat.catid = c.id ".
						      " WHERE a.recall_mail_sent = 1 AND a.expiration_date <= CURDATE() AND c.published = 1 AND a.published = 1 GROUP BY a.id");
				$idsarray = $this->_db->loadObjectList();
			}	
			else
			{		
				$this->_db->setQuery( " SELECT a.*,c.name as cat, c.id as catid FROM #__adsmanager_ads as a".
						      " INNER JOIN #__adsmanager_adcat as adcat ON adcat.adid = a.id ".
						      " INNER JOIN #__adsmanager_categories as c ON adcat.catid = c.id ".
						      " WHERE expiration_date IS NOT NULL AND a.expiration_date <= CURDATE() AND c.published = 1 AND  a.published = 1 GROUP BY a.id");
				$idsarray = $this->_db->loadObjectList();
			}
			
			if (isset($idsarray) && count($idsarray) > 0) {
				foreach($idsarray as $c)
				{
					$id = $c->id;
					$userid = $c->userid;
					
					if ($conf->send_email_on_expiration_to_user == 1) {
						$body = $conf->expiration_text;
						if ($conf->after_expiration == "unpublish") {
							$uri	= JURI::getInstance();
							$root	= $uri->toString( array('scheme', 'host', 'port'));
							$link = $root.TRoute::_("index.php?option=com_adsmanager&view=expiration&id=".$c->id);
							$body = str_replace('{link}',$link,$body);
						}
						$user = JFactory::getUser($userid);
						$this->sendMailToUser($conf->expiration_subject,$body,$user,$c,$conf,"expiration");
					}	
					
					switch($conf->after_expiration) {		
						default:
						case "delete":
							$content = JTable::getInstance('contents', 'AdsmanagerTable');
							$content->deleteContent($id,$conf,$plugins);
							break;
							
						case "unpublish":
							$this->_db->setQuery( "UPDATE #__adsmanager_ads SET published=0,recall_mail_sent = 0 WHERE id = $id");
							$this->_db->query();
							break;
							
						case "archive":
							$this->_db->setQuery( "UPDATE #__adsmanager_ads SET published=0,recall_mail_sent = 0 WHERE id = $id");
							$this->_db->query();
							
							$this->_db->setQuery( "DELETE FROM #__adsmanager_adcat WHERE adid =$id");
							$this->_db->query();
							
							$this->_db->setQuery( "INSERT INTO #__adsmanager_adcat (adid,catid) VALUES ($id,$conf->archive_catid)");
							$this->_db->query();
							break;
					}
					
				}
			}
		}
	}
	
	function getFilterOrder($order,$orderdir = 'DESC')
    {
	    if ($order != 0)
		{
			$this->_db->setQuery( "SELECT f.name,f.sort_direction,f.type FROM #__adsmanager_fields AS f WHERE f.fieldid=".(int)$order." AND f.published = 1" );
			$sort = $this->_db->loadObject();
			if ($sort == null) {
				$filter_order = "a.date_created DESC ,a.id ";
			} else if (($sort->type == "number")||($sort->type == "price")) {
				$filter_order = "a.".$sort->name." * 1";
			}
			else {
				$filter_order = "a.".$sort->name;
			}
		}
		else 
		{
			$filter_order = "a.date_created ".$orderdir." ,a.id ";
		}
		return $filter_order;
    }
    
    /**
     * Prepare Mail Content, parse tags,etc..
     * @param string $subject mail subject
     * @param string $body mail body
     * @param object $user Ad owner object
     * @param object $content Content Object
     * @param object $conf Adsmanager Configuration
     * @param string $usertype "admin" or "user"
     * @param string $type expiration|recall|new|update|validation|waiting_validation|option_expiration
     */
    function prepareMail(&$subject,&$body,$user,$content,$conf,$usertype,$type) {
		$config	= JFactory::getConfig();
		$from = JOOMLA_J3 ? $config->get('mailfrom') : $config->getValue('config.mailfrom');
		$fromname = JOOMLA_J3 ? $config->get('fromname') : $config->getValue('config.fromname');
		$sitename = JOOMLA_J3 ? $config->get('sitename') : $config->getValue('config.sitename');
		
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('adsmanagercontent');
		
		$results = $dispatcher->trigger('ADSonMailPrepare', array (&$subject,&$body,$user,$content,$conf,$usertype,$type));
		
		$fieldmodel	    = new AdsmanagerModelField();
		$fields 		= $fieldmodel->getFields();
		$field_values 	= $fieldmodel->getFieldValues();
		$plugins = $fieldmodel->getPlugins();
		$baseurl = JURI::base();
		$field = new JHTMLAdsmanagerField($conf,$field_values,1,$plugins,'',$baseurl,null);
		
		foreach($fields as $f) {
			$fvalue = "";
			if (strpos($subject,"{".$f->name."}") !== false) {
				$fvalue = str_replace(array("<br/>","<br />","<br>"),"",$field->showFieldValue($content,$f));
				$subject = str_replace("{".$f->name."}",$fvalue,$subject);
			}
			if (strpos($body,"{".$f->name."}") !== false) {
				if ($fvalue == "")
					$fvalue = str_replace(array("<br/>","<br />","<br>"),"",$field->showFieldValue($content,$f));
				$body = str_replace("{".$f->name."}",$fvalue,$body);
			}
		}
		$subject = str_replace("{id}",$content->id,$subject);
		$body = str_replace("{id}",$content->id,$body);
		$subject = str_replace("{username}",$user->username,$subject);
		$body = str_replace("{username}",$user->username,$body);
		$subject = str_replace("{name}",$user->name,$subject);
		$body = str_replace("{name}",$user->name,$body);
		
		$subject = str_replace("{sitename}",$sitename,$subject);
		$body = str_replace("{sitename}",$sitename,$body);
		
		$uri	= JURI::getInstance();
		$root	= $uri->toString( array('scheme', 'host', 'port'));
        if(!version_compare(JVERSION, '1.6.0', 'ge')) {
            $root = $root.'/';
        }
		$link = $root.TRoute::_("index.php?option=com_adsmanager&view=details&catid=".$content->catid."&id=".$content->id,false);
		$link = str_replace("administrator/","",$link);
		$body = str_replace('{link}',$link,$body);
		
		$subject = str_replace("{expiration_date}",strftime(JText::_('ADSMANAGER_DATE_FORMAT_LC'),strtotime($content->expiration_date)),$subject);
		$body = str_replace("{expiration_date}",strftime(JText::_('ADSMANAGER_DATE_FORMAT_LC'),strtotime($content->expiration_date)),$body);
    }
    
    function updateContentDate($adid) {
    	$this->_db->setQuery( "UPDATE #__adsmanager_ads SET date_created = NOW() WHERE id=".(int)$adid);
    	$this->_db->query();
    }
    
    function sendMailToAdmin($subject,$body,$user,$content,$conf,$type) {
    	if ($content == null)
    		return true;
    	
    	$config	= JFactory::getConfig();
		$from = $conf->email_admin;
		$fromname = $conf->name_admin;
		$sitename = JOOMLA_J3 ? $config->get('sitename') : $config->getValue('config.sitename');
		
		$this->prepareMail($subject,$body,$user,$content,$conf,"admin",$type);

		if (!TMail::sendMail($from, $fromname, $from, $subject, $body,1))
		{
			$this->setError(JText::_('ADSMANAGER_ERROR_SENDING_MAIL'));
			return false;
		}
		return true;
    }
    
	function sendMailToUser($subject,$body,$user,$content,$conf,$type) {	
		$config	= JFactory::getConfig();
		$from = JOOMLA_J3 ? $config->get('mailfrom') : $config->getValue('config.mailfrom');
		$fromname = JOOMLA_J3 ? $config->get('fromname') : $config->getValue('config.fromname');
		$sitename = JOOMLA_J3 ? $config->get('sitename') : $config->getValue('config.sitename');
		
		$content = $this->getContent($content->id,false);
		
		$this->prepareMail($subject,$body,$user,$content,$conf,"user",$type);

		if ($user->email == '') {
			$mail = $content->email;
		} else {
			$mail = $user->email;
		}
		if ($mail != '') {
			if (!TMail::sendMail($from, $fromname, $mail, $subject, $body,1))
			{
				$this->setError(JText::_('ADSMANAGER_ERROR_SENDING_MAIL'));
				return false;
			}
		}
		return true;
	}
}