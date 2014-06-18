<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die();

class JElementAdsmanagerContent extends JElement
{
   var   $_name = 'AdsmanagerContent';

   function fetchElement($name, $value, &$node, $control_name)
   {
      $db =JFactory::getDBO();

      $query = "SELECT a.ad_headline AS text, a.id AS value FROM #__adsmanager_ads as a WHERE a.published = 1 ORDER BY a.ad_headline ASC";
      $db->setQuery($query);
      $options = $db->loadObjectList();
      array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Content').' -', 'value', 'text'));

      return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );   
   }
}