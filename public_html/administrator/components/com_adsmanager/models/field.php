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
class AdsmanagerModelField extends TModel
{
	public static $_plugins;
	
	function getNbFields()
    {
    	$this->_db->setQuery( "SELECT count(*) FROM #__adsmanager_fields WHERE 1 ORDER by ordering");
		$nb = $this->_db->loadResult();
		return $nb;
    }
    
    function getSearchFields($catid=0)
    {
	    $this->_db->setQuery( "SELECT f.* FROM #__adsmanager_fields AS f ".
							 "WHERE f.searchable = 1 AND f.published = 1 ORDER by f.ordering" );
	
		$results = $this->_db->loadObjectList();
    	$fields = array();
  		foreach ($results as $r ) {
  			if ($catid === 0) 
  				$fields[] = $r;
			else if ($r->catsid == ",-1,")				
				$fields[] = $r;
			else 
			{	
				if ($catid != 0) {
					$find = ",".$catid.",";
					if (strstr($r->catsid, $find))
						$fields[] = $r;
				}
			}
		}
		foreach($fields as $key => $field) {
			$fields[$key]->options = json_decode($field->options);
		}
		return $fields;
    }
    
    function getOrderFields($catid)
    {
    	$this->_db->setQuery( "SELECT f.title,f.fieldid,f.catsid FROM #__adsmanager_fields AS f WHERE f.sort = 1 AND f.published = 1 ORDER BY f.ordering ASC" );
		$results = $this->_db->loadObjectList();
		$orders = array();
  		foreach ($results as $r ) {
			if ($r->catsid == ",-1,")				
				$orders[] = $r;
			else 
			{	
				if ($catid != 0) {
					$find = ",".$catid.",";
					if (strstr($r->catsid, $find))
						$orders[] = $r;
				}
			}
		}
		return $orders;
    }
    
	function getFields($onlyPublished=true,$limitstart=null,$limit=null,$filter_order="fieldid",$filter_order_Dir="ASC") {
		if ($onlyPublished == true)
			$published = " f.published = 1 ";
		else
			$published = " 1 ";
			
		if (($limitstart === null)||($limit === null))
			$this->_db->setQuery( "SELECT * FROM #__adsmanager_fields as f WHERE $published ORDER by f.ordering ASC");
	    else
	   	 	$this->_db->setQuery( "SELECT * FROM #__adsmanager_fields as f WHERE $published ORDER by $filter_order $filter_order_Dir",
	    						 $limitstart,$limit );
		//f.published = 1
		$fields = $this->_db->loadObjectList('name');
		foreach($fields as $key => $field) {
			$fields[$key]->options = json_decode($field->options);
		}
		return $fields;
    }
    
    function getAdminFields($filters = null,$limitstart=null,$limit=null,$filter_order="fieldid",$filter_order_Dir="ASC") {
    	$search= "";
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
    				case 'published':
    					if ($filter !== "")
    						$search .= $temp."f.published = ".(int)$filter;
    					break;
    				case 'columnid':
    					if ($filter != "")
    						$search .= $temp."f.columnid = ".(int)$filter;
    					break;
    				case 'pos':
    					if ($filter != "")
    						$search .= $temp."f.pos = ".(int)$filter;
    					break;
    				case 'type':
    					if ($filter != "")
    						$search .= $temp."f.type = ".$this->_db->Quote($filter);
    					break;
    				case 'search':
    					if ($filter != "")
    						$search .= $temp."f.name LIKE ".$this->_db->Quote("%$filter%");
    					break;
    				case 'category':
    					if ($filter != "")
    						$search .= $temp." ((f.catsid = ',-1,') OR (f.catsid LIKE ".$this->_db->Quote("%,$filter,%")."))";
    					break;
    			}
    		}
    	}

    	if (($limitstart === null)||($limit === null))
    		$this->_db->setQuery( "SELECT f.* FROM #__adsmanager_fields as f $search ORDER by f.ordering ASC");
    	else
    		$this->_db->setQuery( "SELECT f.* FROM #__adsmanager_fields as f $search ORDER by $filter_order $filter_order_Dir",
    				$limitstart,$limit );
    	//f.published = 1
    	$fields = $this->_db->loadObjectList('name');
    	foreach($fields as $key => $field) {
    		$fields[$key]->options = json_decode($field->options);
    	}
    	return $fields;
    }
    
    function getFieldsbyColumns($published = true) {
    	if ($published) {
    		$sqlpublished = "AND c.published = 1 ";
    	} else {
    		$sqlpublished = "";
    	}
      	$this->_db->setQuery( "SELECT c.* FROM #__adsmanager_fields AS c ".
							 "WHERE c.columnid != -1 $sqlpublished ORDER by c.columnorder,c.fieldid" );
	
		$fields = $this->_db->loadObjectList();
		foreach($fields as $key => $field) {
			$fields[$key]->options = json_decode($field->options);
		}
		
		// establish the hierarchy of the menu
		$fColumn = array();
		// first pass - collect children
		if (isset($fields))
		{
			foreach ($fields as $f ) {
				$pt 	= $f->columnid;
				$list 	= @$fColumn[$pt] ? $fColumn[$pt] : array();
				array_push( $list, $f );
				$fColumn[$pt] = $list;
			}
		}
		return $fColumn;
    }
    
    function getFieldsbyPositions($published = true) {
    	if ($published) {
    		$sqlpublished = "AND f.published = 1 ";
    	} else {
    		$sqlpublished = "";
    	}
	    $this->_db->setQuery( "SELECT f.* FROM #__adsmanager_fields AS f ".
							 "WHERE f.pos != -1 $sqlpublished ORDER by f.posorder" );
	
		$fields = $this->_db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			echo $this->_db->stderr();
			return;
		}
		
		foreach($fields as $key => $field) {
			$fields[$key]->options = json_decode($field->options);
		}
		
		// establish the hierarchy of the menu
		$fDisplay = array();
		// first pass - collect children
		if (isset($fields))
		{
			foreach ($fields as $f ) {
				$pt 	= $f->pos;
				$list 	= @$fDisplay[$pt] ? $fDisplay[$pt] : array();
				array_push( $list, $f );
				$fDisplay[$pt] = $list;
			}
		}
		return $fDisplay;
    }
    
    function getFieldsByName($listfields) {
		$query = "SELECT f.* FROM #__adsmanager_fields AS f ".
							 "WHERE f.name IN ($listfields) AND f.published = 1 ORDER by f.ordering" ;
		$this->_db->setQuery( $query);
		$searchfields = $this->_db->loadObjectList("name");
		
		foreach($searchfields as $key => $field) {
			$searchfields[$key]->options = json_decode($field->options);
		}
		return $searchfields;
    }
    
	function getFieldValues($fieldid = null) {
    	if ($fieldid!== null) {
    		$fvalues = $this->_db->setQuery( "SELECT * "
    					. "\n FROM #__adsmanager_field_values"
    					. "\n WHERE fieldid=".(int)$fieldid
    					. "\n ORDER BY ordering" );
    		$field_values = $this->_db->loadObjectList();
    	}
    	else {
		    $this->_db->setQuery( "SELECT * FROM #__adsmanager_field_values ORDER by ordering ");
			$fieldvalues = $this->_db->loadObjectList();
			if ($this->_db -> getErrorNum()) {
				echo $this->_db -> stderr();
				return false;
			}
			
			$field_values = array();
			// first pass - collect children
			if (isset($fieldvalues))
			{
				foreach ($fieldvalues as $v ) {
					$pt 	= $v->fieldid;
					$list 	= @$field_values[$pt] ? $field_values[$pt] : array();
					array_push( $list, $v );
					$field_values[$pt] = $list;
				}
			}	
			if (COMMUNITY_BUILDER == 1) {
				$this->_db->setQuery( "SELECT fieldid,cbfieldvalues FROM #__adsmanager_fields as f WHERE published = 1 ORDER by f.ordering ASC");
				$fields = $this->_db->loadObjectList();
				foreach($fields as $field) {
					if ($field->cbfieldvalues != "-1")
					{
						/*get CB value fields */
						$cbfieldvalues = $this->getCBFieldValues($field->cbfieldvalues);
						$field_values[$field->fieldid] = $cbfieldvalues;
					}
				}
			}
    	}
    	
	    return $field_values;
    }
    
	function getField($id) {
		$this->_db->setQuery("SELECT * FROM #__adsmanager_fields WHERE fieldid = ".(int)$id  );
		//echo "SHOW TABLES LIKE '".$mosConfig_dbprefix."comprofiler_fields'" ;
		$field = $this->_db-> loadObject();
		$field->options = json_decode($field->options);
		return $field;
	}
	
	function getSearchFieldsSql($fields)
    {
    	if (isset($this->searchSQL)&&($this->searchSQL != ""))
    		return $this->searchSQL;
    		
    	$search = "";
		foreach($fields as $fsearch)
		{
			switch($fsearch->type)
			{
				case 'multicheckbox':
				case 'multiselect':
					$value = JRequest::getVar( $fsearch->name,	array() );
					for($i = 0,$nb=count($value);$i < $nb;$i++)
					{
						if ($i == 0)
							$search .= " AND (";	
						if (version_compare(JVERSION,'1.7.0','<')) {
                            $search .= "a.$fsearch->name LIKE '%,".$this->_db->getEscaped($value[$i],true).",%'";
                        }else{
                            $search .= "a.$fsearch->name LIKE '%,".$this->_db->escape($value[$i],true).",%'";
                        }
						if ($i < $nb - 1)
							$search .= " OR ";
						else
							$search .= " )";	
					}
					break;
				case 'checkbox':
				case 'radio':
				case 'select':	
					$value = JRequest::getVar( $fsearch->name,	"");
					if ($value != "")
					{
						if ((ADSMANAGER_SPECIAL == "abrivac")&&(strpos($fsearch->name,"distance") !== false)) {
							$this->_db->setQuery( "SELECT fieldvalue FROM #__adsmanager_field_values WHERE fieldid=".$fsearch->fieldid." ORDER by ordering ASC");
							$fvalues = $this->_db->loadObjectList();
							$values = array();
							foreach($fvalues as $v) {
								if ($v->fieldvalue != $value) 
									$values[] = $this->_db->Quote($v->fieldvalue);
								else {
									$values[] = $this->_db->Quote($v->fieldvalue);
									break;
								}
							}
							$search .= " AND a.$fsearch->name IN (".implode(',',$values).")";
						} else if (is_array($value)) {
							foreach($value as &$v) {
								$v = $this->_db->Quote($v);
							}
							$search .= " AND a.$fsearch->name IN (".implode(',',$value).")";
						} else {
							$search .= " AND a.$fsearch->name = ".$this->_db->Quote($value);
						}
					}
					break;
					
				case 'price':
				case 'number':
					$options = $fsearch->options;
					if (!isset($options)){
						$options = new stdClass();
					}
					if (!isset($options->search_type)) {
						$options->search_type = "textfield";
					}
					switch($options->search_type) {
						case "textfield":
							$value = JRequest::getVar( $fsearch->name,	"");
							if ($value != "")
							{
								if (version_compare(JVERSION,'1.7.0','<')) {
									$search .= " AND a.$fsearch->name = '".$this->_db->getEscaped($value,true)."'";
								}else{
									$search .= " AND a.$fsearch->name = '".$this->_db->escape($value,true)."'";
								}
							}
							break;
							
						case "select":
							$value = JRequest::getVar( $fsearch->name,	"");
							if ($value != "")
							{
								$pos = strpos($value, '-');
								$fieldsql = "a.$fsearch->name + 0"; // Little hack to convert in number
								if ($pos !== false)
								{
									if ($pos == 0) // $pos is = 0 for $value = -x $pos is = 1 only for this format 0-10 {
									{
										$search .= " AND $fieldsql < ".(float)substr($value,1)."";
									}				
									else if ($pos == strlen($value) - 1)
									{
										$search .= " AND $fieldsql > ".(float)substr($value,0,strlen($value)-1);
									}
									else
									{
										$search .= " AND ($fieldsql >= ".(float)substr($value,0,$pos)." AND $fieldsql <= ".(float)substr($value,$pos+1).")";
									}
								}
							}
							break;
							
						case "minmax":
							$minvalue = JRequest::getVar( $fsearch->name."_min",	"");
							$maxvalue = JRequest::getVar( $fsearch->name."_max",	"");
							$fieldsql = "a.$fsearch->name + 0"; // Little hack to convert in number
							if ($minvalue != "") {
								$search .= " AND $fieldsql >= ".(float)$minvalue;
							}
							if ($maxvalue != "") {
								$search .= " AND $fieldsql <= ".(float)$maxvalue;
							}
							break;
						
					}
					break;
		
				case 'textarea':
				case 'emailaddress':
				case 'url':
				case 'text':
				case 'date':
					$value = JRequest::getVar( $fsearch->name,	"");
					if ($value != "")
					{
						if ((ADSMANAGER_SPECIAL == "abrivac")&&(($fsearch->name == "ad_capaciteconf")||($fsearch->name == "ad_capacitemax"))) {
							$search .= " AND a.$fsearch->name >= ".(int)$value." ";	
						} else {
                            if (version_compare(JVERSION,'1.7.0','<')) {
                                $search .= " AND a.$fsearch->name LIKE '%".$this->_db->getEscaped($value,true)."%'";
                            }else{
                                $search .= " AND a.$fsearch->name LIKE '%".$this->_db->escape($value,true)."%'";
                            }
						}
					}
					break;

				default:
					$value = JRequest::getVar( $fsearch->name,	"");
					if ($value == "") {
						$value = JRequest::getVar( "country_".$fsearch->fieldid,"");
					}

					if ((ADSMANAGER_SPECIAL == "abrivac")&&($fsearch->type == "region")) {
						$pays = JRequest::getVar( "ad_country","");
						if ($pays != "") {
                            if (version_compare(JVERSION,'1.7.0','<')) {
                                $search .= " AND a.ad_country LIKE '%".$this->_db->getEscaped($pays,true)."%'";
                            }else{
                                $search .= " AND a.ad_country LIKE '%".$this->_db->escape($pays,true)."%'";
                            }
						}
					}
						
					if ($value != "")
					{
						if ($fsearch->type == "zipcode") {
							$distance = intval(JRequest::getVar( "distance_".$fsearch->name,""));
							$plugins = $this->getPlugins();
							$plugin = $plugins['zipcode'];
							$list = $plugin->getZipCodes($value,$distance);
							if (count($list) > 0) {
								$search .= " AND a.$fsearch->name IN (".implode(",",$list).")";
							} else {
								$search .= " AND 0 ";
							}
						} else {
                            if (version_compare(JVERSION,'1.7.0','<')) {
                                $search .= " AND a.$fsearch->name LIKE '%".$this->_db->getEscaped($value,true)."%'";
                            }else{
                                $search .= " AND a.$fsearch->name LIKE '%".$this->_db->escape($value,true)."%'";
                            }
						}
					}
					break;
			}
		}
		
		if (function_exists("editPaidAd")) {
			if (JRequest::getInt( "ad_featured",0) == 1) {
				$search .= " AND adext.featured = 1 ";
			}
			 
			if (JRequest::getInt( "ad_highlight",0) == 1) {
				$search .= " AND adext.highlight = 1 ";
			}
			 
			if (JRequest::getInt( "ad_top",0) == 1) {
				$search .= " AND adext.top = 1 ";
			}
		}
		 
		if (JRequest::getInt( "ad_commentaires",0) == 1) {
			$search .= " AND a.id IN (SELECT DISTINCT contentid FROM #__adsmanager_reviews) ";
		}
		 
		if (JRequest::getInt( "ad_photos",0) == 1) {
			$search .= " AND (a.images != '' AND a.images != '[]') ";
		}
		 
		$search_ref = JRequest::getInt( "search_ref",0);
		if ($search_ref != 0) {
			$search .= " AND a.id = $search_ref";
		}
		 
		if (COMMUNITY_BUILDER == 1) {
			$usertype = JRequest::getVar( "usertype","");
			if ($usertype != "") {
				$search .= " AND cb.cb_typeproprietaire = ".$this->_db->Quote($usertype);
			}
		}
		
		if (ADSMANAGER_SPECIAL == 'newspaper') {
			$search .= " AND (a.ad_publishtype = 'both' OR a.ad_publishtype = 'online') ";
		}	
		
		$this->searchSQL = " 1 ".$search;
		//echo $this->searchSQL ;
		return $this->searchSQL;
    }
    
	function getAllCbFields() {
		$config	= JFactory::getConfig();
		$dbprefix = JOOMLA_J3 ? $config->get('dbprefix') : $config->getValue('config.dbprefix');
		
		$this->_db->setQuery("SHOW TABLES LIKE '".$dbprefix."comprofiler_fields'"  );
		$tables = $this->_db-> loadObjectList();
		if (count($tables) > 0)
		{
			$this->_db->setQuery("SELECT * FROM #__comprofiler_fields WHERE 1"  );
			$cb_fields = $this->_db-> loadObjectList();
			return $cb_fields;
		}
		else
			return array();
	}
    
	function getCBFieldValues($fieldcbid)
	{
		$this->_db->setQuery( "SELECT *, fieldtitle as fieldvalue FROM #__comprofiler_field_values WHERE fieldid = ".(int)$fieldcbid."	 ORDER by ordering ");
		$cbfieldvalues = $this->_db->loadObjectList();
		return $cbfieldvalues;
	}
	
	function getPlugins()
	{

		if (self::$_plugins) {
			return self::$_plugins;
		}
		else {
			$plugins = array();
			
			if(file_exists(JPATH_ROOT . "/images/com_adsmanager/plugins/")) { 
				$path = JPATH_ROOT."/images/com_adsmanager/plugins/";
				$handle = opendir( $path );
				while ($file = readdir($handle)) {
					$dir = $path.'/'.$file;
					if (is_dir($dir))
					{
						if (($file != ".") && ($file != "..")) {
							if (file_exists($path.'/'.$file.'/plug.php')) {
								include_once($path.'/'.$file.'/plug.php');
								if ($file == "zipcode")
									$plugins["zipcode"] = new AdsManagerZipCodePlugin();
								if ($file == "region")
									$plugins["region"] = new AdsManagerRegionPlugin();
							} else {
                                if(is_file($path.'/'.$file)) {
                                    JFolder::delete($path.'/'.$file);
                                }
							}
						}
					}
				}
			
				closedir($handle);
			}
			self::$_plugins = $plugins;
			
			return self::$_plugins;
		}
	}
}
