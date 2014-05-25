<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<form enctype="multipart/form-data" action="index.php" method="post" name="filename">
	<table class="adminform table table-striped">
	<tr>
		<th>
		<?php echo JText::_('ADSMANAGER_UPLOAD_PACKAGE_FILE')?>
		</th>
	</tr>
	<tr>
		<td align="left">
		<?php echo JText::_('ADSMANAGER_ARCHIVE')?>
		<input class="text_area" name="userfile" type="file" size="70"/>
		<input class="button btn" type="submit" value="<?php echo JText::_('ADSMANAGER_UPLOAD_PACKAGE_FILE')?>" />
		</td>
	</tr>
	</table>

	<input type="hidden" name="task" value="upload"/>
	<input type="hidden" name="c" value="plugins"/>
	<input type="hidden" name="option" value="com_adsmanager"/>
	</form>
	<br />
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist table table-striped">
	<thead>
	<tr>
		<?php if (version_compare(JVERSION,'2.5.0','>=')) { ?>
	    <th width="3%" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
	    <?php } else { ?>
	    <th width="3%" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->plugins); ?>);" />
	    <?php } ?>
	    <th>
		</th>
		<th>
		Name
		</th>
	</tr>
	</thead>
	<?php
	if(isset($this->plugins))
	{
		$k = 0;
		$i=0;
		foreach($this->plugins as $plugin) {
		?>
		<tr class="row<?php echo $k; ?>">
		<td align="center">
			<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $plugin;?>" onclick="isChecked(this.checked);" />
		</td>
		<td>
		<?php	echo $plugin; ?>
		</td>
		</tr>
		<?php
		$k++;
		if ($k==2)
			$k = 0;
		$i++;
		}
	}
	?>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="c" value="plugins"/>
	<input type="hidden" name="option" value="com_adsmanager"/>
	<input type="hidden" name="boxchecked" value="0" />
	</form>