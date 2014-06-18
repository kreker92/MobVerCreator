<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JHTMLAdsmanagerUserGroups {
    static private $groups;
    
    /**
     * Return in an array the usergroups of the user
     * 
     * @param int $userid
     * @return Array
     */
    public static function getUserGroup($userid) {
        
        if(self::$groups == null) {
            $db = JFactory::getDBO();

            $db->setQuery('SELECT group_id FROM #__user_usergroup_map
                            WHERE user_id = ' . (int)$userid);

            $groups = $db->loadColumn();

            if($groups == null)
                $groups = array();
            
            self::$groups = $groups;
        }
        
        return self::$groups;
    }
    
    static function checkUserGroups($id, $type, $mode){
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $userGroups = self::getUserGroup($user->id);
        
        switch($type){
            case 'category' : $query = "SELECT usergroups".$mode." as userGroups FROM #__adsmanager_categories
                                        WHERE id = " . $db->quote($id);
                              break;
            default : return false;
        }
        
        $db->setQuery($query);
        $object = $db->loadObject();
        
        if($object->userGroups == '')
            return true;
        
        $objectUserGroupsArray = explode(',', $object->userGroups);
        
        $authorisedAccess = false;
        
        foreach($userGroups as $userGroup) {
            if(array_search($userGroup, $objectUserGroupsArray) !== false){
                $authorisedAccess = true;
            }
        }
        
        return $authorisedAccess;
    }
    
	static function getUserGroups($name = 'usergroups', $selected = '', $attribs = array()) {
        // Get a database object.
		$db = JFactory::getDBO();

		// Get the user groups from the database.
		$query = $db->getQuery(true);
		$query->select(array(
			$db->qn('a').'.'.$db->qn('id'),
			$db->qn('a').'.'.$db->qn('title'),
			$db->qn('a').'.'.$db->qn('parent_id').' AS '.$db->qn('parent'),
			'COUNT(DISTINCT '.$db->qn('b').'.'.$db->qn('id').') AS '.$db->qn('level')
		))->from($db->qn('#__usergroups').' AS '.$db->qn('a'))
		->join('left', $db->qn('#__usergroups').' AS '.$db->qn('b').' ON '.
			$db->qn('a').'.'.$db->qn('lft').' > '.$db->qn('b').'.'.$db->qn('lft').
			' AND '.$db->qn('a').'.'.$db->qn('rgt').' < '.$db->qn('b').'.'.$db->qn('rgt')
		)->group(array(
			$db->qn('a').'.'.$db->qn('id')
		))->order(array(
			$db->qn('a').'.'.$db->qn('lft').' ASC'
		))
		;
		$db->setQuery($query);
		$groups = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ADSMANAGER_COMMON_SELECT').' -');

		foreach ($groups as $group) {
			$options[] = JHTML::_('select.option', $group->id, JText::_($group->title));
		}

		return self::genericlist($options, $name, $attribs, $selected, $name);
    }
    
    protected static function genericlist($list, $name, $attribs, $selected, $idTag)
	{
		if(empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$temp = '';
			foreach($attribs as $key=>$value)
			{
				$temp .= $key.' = "'.$value.'"';
			}
			$attribs = $temp;
		}

		return JHTML::_('select.genericlist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}
}