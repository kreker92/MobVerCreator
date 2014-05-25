<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
<tr valign="top">
<td width="60%">
<table class="adminform">
<th colspan="3"><?php echo JText::_('Parameters')?></th>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_TITLE'); ?></td>
<td><input type="text" size="50" maxsize="100" name="name" value="<?php echo @JText::_($this->column->name); ?>" /></td>
<td>&nbsp;</td>
</tr>

<tr>
<td><?php echo JText::_('ADSMANAGER_ORDER'); ?></td>
<td><input type="text" size="50" maxsize="100" name="ordering" value="<?php echo @$this->column->ordering; ?>" /></td>
<td>&nbsp;</td>
</tr>
</table>
</td>
<?php  // Mod by TomekOmel ?>

<td width="40%">
  <table class="adminform">
		<th><?php echo JText::_('ADSMANAGER_FORM_CATEGORY'); ?></th>
		<tr><td>
				
			<select name="catsid[]" multiple='multiple' id="catsid[]" size="<?php echo $this->nbcats+2;?>">
			<?php
			
			if (strpos(@$this->column->catsid, ",-1,") === false)
				echo "<option value='-1'>".JText::_('ADSMANAGER_MENU_ALL_ADS')."</option>";
			else
				echo "<option value='-1' selected>".JText::_('ADSMANAGER_MENU_ALL_ADS')."</option>";
			$this->selectCategories(0,"",$this->cats,-1,-1,1,@$this->column->catsid);
			?>
			</select>
		</td></tr>
  </table>
</td>
</tr>

<?php  // End Mod by TomekOmel ?> 

</table>
<input type="hidden" name="id" value="<?php echo @$this->column->id; ?>" />
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="c" value="columns" />
<input type="hidden" name="task" value="" />
</form>