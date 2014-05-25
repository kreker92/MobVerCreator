<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<ul>
<?php
if( version_compare( JVERSION, '2.5.0', 'ge' ) ) { ?>
			<li><a href="index.php?option=com_adsmanager&c=tools&task=installfalang"><?php echo JText::_('ADSMANAGER_INSTALL_FALANG');?></a></li>
			<?php
		} else { ?>
			<li><a href="index.php?option=com_adsmanager&c=tools&task=installjoomfish"><?php echo JText::_('ADSMANAGER_INSTALL_JOOMFISH');?></a></li>
			<?php
		}
?>		
<?php if (ADSMANAGER_SPECIAL == "newspaper") { 
$db = JFactory::getDBO();
$db->setQuery("SELECT fieldvalue AS value,fieldtitle AS name 
	      FROM #__adsmanager_field_values WHERE fieldid IN (SELECT fieldid FROM #__adsmanager_fields WHERE name = 'ad_magazine') ORDER BY ordering ASC");
$mags = $db->loadObjectList();
?>
<li> 
<form name="exportform" action="index.php?option=com_adsmanager&c=tools&task=exportmagazine" method="post">
Magazine: <select name="magazine" id="magazine">
<?php foreach($mags as $mag) { ?>
<option value="<?php echo $mag->value ?>"><?php echo $mag->name ?></option>
<?php } ?>
</select><br/>
Date: <input type="text" name="date" value="<?php echo date("Y-m-d");?>"/><br/>
Version: <input type="text" name="version" value="1"/><br/>
Page Number: <input type="text" name="pagenumber" value="1"/><br/>
Footer: <input type="text" size="100" name="footer" value="Quaûng caùo: Tel.972-675-4383 - Ban Bieân Taäp: editor@trenews.net - www.baotreonline.com or www.trenews.net" /><br/>
<input type="submit" value="Export Magazine"/>
</form>
</li>
<?php } ?>
</ul>