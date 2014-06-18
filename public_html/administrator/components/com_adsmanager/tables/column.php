<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die();

class AdsmanagerTableColumn extends JTable
{
	var $id=null;
    var $name=null;
    var $ordering=null;
    var $catsid=null;
    var $published = 1;
			
    function __construct(&$db)
    {
    	parent::__construct( '#__adsmanager_columns', 'id', $db );
    }
}
   