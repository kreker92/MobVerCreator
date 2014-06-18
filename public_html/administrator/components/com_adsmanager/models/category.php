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

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class AdsmanagerModelCategory extends TModel
{
    
	function getCategory($id) {
    	$this->_db->setQuery( "SELECT * FROM #__adsmanager_categories WHERE id = ".(int)$id);
		$cat = $this->_db->loadObject();
		return $cat;
    }
    
    function isRootCategory($id) {
    	$this->_db->setQuery( "SELECT count(*) FROM #__adsmanager_categories WHERE parent = ".(int)$id." AND published = 1");
    	$result = $this->_db->loadResult();
    	//if there are some categories with this id as parent, means that this is a root category
    	if ($result > 0) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    function isPublishedCategory($id) {
    	$this->_db->setQuery( "SELECT count(*) FROM #__adsmanager_categories WHERE id = ".(int)$id." AND published = 1");
    	$result = $this->_db->loadResult();
    	if ($result > 0) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
	function getCategories($onlyPublished = true, $mode = 'admin') {
		
		if ($onlyPublished == true)
			$published = " c.published = 1 ";
		else
			$published = " 1 ";
        
		if(version_compare(JVERSION, '1.6', 'ge')) {
            //If $mode isn't read or write, we authorised all the categories
            if(!($mode == 'write' || $mode == 'read')){
                $listCategories = "";
            }else{
                $listCategories = TPermissions::getAuthorisedCategories($mode);

                //If the variable is an array and if it's not empty, we add a filter to the request
                //If not we're not return any category
                if(is_array($listCategories) && !empty($listCategories)){
                    $listCategories = " AND c.id IN (".implode(',',$listCategories).") "; 
                }else{
                    $listCategories = " AND 0 ";
                }
            }
        } else {
            $listCategories = "";
        }
        
        
    	$this->_db->setQuery( "SELECT c.* FROM #__adsmanager_categories as c ".
							 "WHERE $published $listCategories ORDER BY c.parent,c.ordering");
		$cats = $this->_db->loadObjectList();
        
		foreach($cats as &$cat) {
			$cat->name = JText::_($cat->name);
		}
		return $cats;
    }
    
	function getPathList($catid,$mode='admin'){
		$cats = $this->getCategories(true, $mode);
		$orderlist = array();
		$list = array();
		if(isset($cats))
		{
			foreach ($cats as $c ) {
				$orderlist[$c->id] = $c;
			}
		
			if (($catid != -1)&&($catid != 0))
			{
				$i=0;
				$list[$i] = new stdClass();
				if (ADSMANAGER_SPECIAL == "abrivac") {
					$list[$i]->text   = sprintf(JText::_('ADSMANAGER_BREADCRUMBS'),$orderlist[$catid]->name);	
				} else {
					$list[$i]->text   = $orderlist[$catid]->name;	
				}
				$list[$i]->link   = TRoute::_('index.php?option=com_adsmanager&view=list&catid='.$catid);
				$i++;
		
				$current = $catid;
				
				while($orderlist[$current]->parent != 0)
				{
					$current = $orderlist[$current]->parent;
					$list[$i] = new stdClass();
					if (ADSMANAGER_SPECIAL == "abrivac") {
						$list[$i]->text   = sprintf(JText::_('ADSMANAGER_BREADCRUMBS'),$orderlist[$current]->name);
					} else {
						$list[$i]->text   = JText::_($orderlist[$current]->name);
					}
					$list[$i]->link   = TRoute::_('index.php?option=com_adsmanager&view=list&catid='.$orderlist[$current]->id);
					$i++;	
				}
			}
		}
		$nb = count($list);
		if (ADSMANAGER_SPECIAL != "abrivac") {
			$list[$nb] = new stdClass();
			if ($catid == 0) {
				$list[$nb]->link = TRoute::_('index.php?option=com_adsmanager&view=front');
			} else
				$list[$nb]->link = TRoute::_('index.php?option=com_adsmanager&view=list');
			$list[$nb]->text = JText::_('ADSMANAGER_ROOT_TITLE');
		}
		
		return $list;
	}
    
    function getSubCats($parentid, $mode = 'admin') {
        
        if(version_compare(JVERSION, '1.6', 'ge')) {
            //If $mode isn't read or write, we authorised all the categories
            if(!($mode == 'write' || $mode == 'read')){
                $listCategories = "";
            }else{
                $listCategories = TPermissions::getAuthorisedCategories($mode);

                //If the variable is an array and if it's not empty, we add a filter to the request
                //If not we're not return any category
                if(is_array($listCategories) && !empty($listCategories)){
                    $listCategories = " AND c.id IN (".implode(',',$listCategories).") "; 
                }else{
                    $listCategories = " AND 0 ";
                }
            }
        } else {
            $listCategories = "";
        }
        
    	$this->_db->setQuery( "SELECT c.* FROM #__adsmanager_categories as c ".
							 "WHERE c.parent = ".(int)$parentid." AND c.published = 1 $listCategories ORDER BY c.ordering");
		$cats = $this->_db->loadObjectList();
        
		foreach($cats as &$cat) {
			$cat->name = JText::_($cat->name);
		}
		return $cats;
    }
    
	function getNbCats($onlyPublished = true)
	{
		if ($onlyPublished == true)
			$published = " c.published = 1 ";
		else
			$published = " 1 ";
			
		$query =  " SELECT count(*) FROM #__adsmanager_categories as c ".
				  " WHERE $published";
		$this->_db->setQuery($query);				 
		$nb = $this->_db->loadResult();
		return $nb;
	}
	
	function parseTree($id,$tree,&$result,$level=0,$parents=null) {
		if ($result == null) {
			$result = array();
		}
		if ($parents == null) {
			$parents = array();
		}
		if (@$tree[$id]) {
			foreach ($tree[$id] as $n) {
				$node = $n;
				if (!@$tree[$n->id]) {
					$node->leaf = true;
				} else {
					$node->leaf = false;
				}
				$node->level = $level;
				$node->parents = $parents;
				$tmp = $parents;
				$result[] = $node;
				if (@$tree[$n->id]) {
					$parents[] = array("id"=>$node->id,"name"=> $node->name);
					$this->parseTree($node->id,$tree,$result,$level+1,$parents);
				}
				$parents=$tmp;
			}
		}
	}
	
	function getFlatTree($onlyPublished = true,$getnbads = false,&$nbcontents=null,$mode='admin') {
		$tree = $this->getCatTree($onlyPublished,$getnbads,$nbcontents,$mode);
		$this->parseTree(0,$tree,$result,0);
		return $result;
	}
	
	function getCategoriesPerLevel($onlyPublished = true,$getnbads = false,&$nbcontents=null,$mode='admin') {
		//SOLUTION 1
		$list = $this->getFlatTree($onlyPublished,$getnbads,$nbcontents,$mode);
		$listcats = array();
		foreach($list as $cat) {
			if (!isset($listcats[$cat->level])) {
				$listcats[$cat->level] = array();
			}
			$listcats[$cat->level][] = $cat;
		}
		return $listcats;
	}
	
	function getCatTree($onlyPublished = true,$getnbads = false,&$nbcontents=null, $mode = 'admin') {
		
		if ($onlyPublished == true)
			$published = " c.published = 1 ";
		else
			$published = " 1 ";
        
        if(version_compare(JVERSION, '1.6', 'ge')) {
            //If $mode isn't read or write, we authorised all the categories
            if(!($mode == 'write' || $mode == 'read')){
                $listCategories = "";
            }else{
                $listCategories = TPermissions::getAuthorisedCategories($mode);

                //If the variable is an array and if it's not empty, we add a filter to the request
                //If not we're not return any category
                if(is_array($listCategories) && !empty($listCategories)){
                    $listCategories = " AND c.id IN (".implode(',',$listCategories).") "; 
                }else{
                    $listCategories = " AND 0 ";
                }
            }
        } else {
            $listCategories = "";
        }
        
		$query = " SELECT c.* FROM #__adsmanager_categories as c WHERE $published $listCategories "
		       . " ORDER BY c.parent,c.ordering,c.id";
		$this->_db->setQuery($query);			 
		$list = $this->_db->loadObjectList(); 
		
		if ($getnbads != null) 
		{		
			$query  = " SELECT ac.catid,ac.adid "
				    . " FROM #__adsmanager_adcat as ac"
				    . " INNER JOIN #__adsmanager_ads as a ON a.id = ac.adid "
				    . " WHERE a.published = 1 ";
			$this->_db->setQuery($query);			 
			$listads = $this->_db->loadObjectList();
			$nbadsbycat = array();
			foreach($listads as $ad) {
				if (!isset($nbadsbycat[$ad->catid]))
					$nbadsbycat[$ad->catid] = array();
				$nbadsbycat[$ad->catid][]++;
			}
		}
		  					 
		// establish the hierarchy of the menu
		$tree = array();
		// first pass - collect children
		if(isset($list))
		{
			foreach ($list as $v ) {
				$pt 	= $v->parent;
				$list_temp 	= @$tree[$pt] ? $tree[$pt] : array();
				if (isset($nbadsbycat[$v->id]))
					$v->num_ads = count($nbadsbycat[$v->id]);
				else
					$v->num_ads = 0;
				$v->name = JText::_($v->name);
				array_push( $list_temp, $v );
				$tree[$pt] = $list_temp;
			}
		}
		
		if ($getnbads != null) 
		{
			$nbcontents = $this->calc_nb_ads(0,$tree);	
		}
		
		return $tree;
	}
	

	/**
	 * This function computes the number of ads per category by adding the subcategory number of ads
	 * to the parent category number of ads
	 * @param int $id category_d
	 * @param tree $cats tree of cats with precomputed number of ads associated to each category 
	 * @return total number of ads
	 */
	function calc_nb_ads($id,&$cats) {
		$nbads = 0;
		if (@$cats[$id]) {	
			$nbsubcat = count($cats[$id]);
			for($i=0;$i < $nbsubcat;$i++)
			{
				$cats[$id][$i]->num_ads += $this->calc_nb_ads($cats[$id][$i]->id,$cats);
				$nbads += $cats[$id][$i]->num_ads;
			}
		}
		return $nbads;
	}
	
	function recurseSearch ($rows,&$list,$catid){
		if(isset($rows))
		{
			foreach($rows as $row) {
				if ($row->parent == $catid)
				{
					$list[]= $row->id;
					$this->recurseSearch($rows,$list,$row->id);
				} 
			}
		}
	}
}
