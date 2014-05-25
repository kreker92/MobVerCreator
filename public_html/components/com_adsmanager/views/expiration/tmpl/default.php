<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<?php echo sprintf(JText::_('ADSMANAGER_RENEW_AD_QUESTION'),$this->content->ad_headline,$this->content->expiration_date); 
$target = TRoute::_("index.php?option=com_adsmanager&task=renew&id=".$this->content->id);
?>
<form action="<?php echo $target;?>" method="post" name="adminForm" enctype="multipart/form-data">
<table class="adsmanager_header">
   <?php
   if (function_exists("showPaidDuration")) {
		showPaidDuration($this->content);
   } else { ?>
  <tr>
  	<td><?php echo "&nbsp;"; ?></td>
  	<td>
    <?php 
       echo "<input type='submit' value='".JText::_('ADSMANAGER_RENEW_AD')."' />"; 
    ?>
    </td>
  </tr>
  <?php } ?>
  </table>
</form>