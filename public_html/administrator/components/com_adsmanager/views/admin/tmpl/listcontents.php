<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table>
				<tr>
					<td width="100%">
					</td>
					<td nowrap="nowrap">
						<select name="catid" id="catid" onchange="document.adminForm.submit();">
							<option value="0" <?php if (!isset($this->cat)) echo "selected"; ?>><?php echo JText::_('ADSMANAGER_MENU_ALL_ADS'); ?></option>
							<?php $this->selectCategories(0,"",$this->cats,$this->cat,-1); ?>
						</select>
						<?php if (ADSMANAGER_SPECIAL == "newspaper") {?>
						<select name="filteronline" id="filteronline" onchange="document.adminForm.submit();">
							<option value="" <?php if ($this->filteronline == "") echo "selected='selected'"; ?>>
								<?php echo ""; ?>
							</option>
							<option value="1" <?php if ($this->filteronline == "1") echo "selected='selected'"; ?>>
								<?php echo JText::_('Online'); ?>
							</option>
							<option value="0" <?php if ($this->filteronline == "0") echo "selected='selected'"; ?>>
								<?php echo JText::_('Offline'); ?>
							</option>
						</select>
						<select name="filtermag" id="filtermag" onchange="document.adminForm.submit();">
						<option value="" <?php if ($this->filtermag == "") echo "selected='selected'"; ?>></option>
						<?php foreach($this->mags as $mag) { ?>
						<option value="<?php echo $mag->value ?>" <?php if ($this->filtermag == $mag->value) echo "selected='selected'"; ?>><?php echo $mag->name ?></option>
						<?php } ?>
						</select>
						<?php } ?>
						<?php if ((ADSMANAGER_SPECIAL == "newspaper")|| (ADSMANAGER_SPECIAL == "thiago")){?>
						<span>
						<?php echo JText::_('Phone')?>:
						</span>
						<span>
						<input type="text" name="filterphone" value="<?php echo htmlspecialchars( $this->filterphone );?>" class="text_area" onChange="document.adminForm.submit();" />
						</span>
						<?php } ?>
						<?php if (ADSMANAGER_SPECIAL == "thiago"){?>
						<span>
						<?php echo JText::_('Ip')?>:
						</span>
						<span>
						<input type="text" name="filterip" value="<?php echo htmlspecialchars( $this->filterip );?>" class="text_area" onChange="document.adminForm.submit();" />
						</span>
						<?php } ?>
						<select name="filterpublish" id="filterpublish" onchange="document.adminForm.submit();">
							<option value="" <?php if ($this->filterpublish == "") echo "selected='selected'"; ?>>
								<?php echo ""; ?>
							</option>
							<option value="1" <?php if ($this->filterpublish == "1") echo "selected='selected'"; ?>>
								<?php echo JText::_('ADSMANAGER_PUBLISH'); ?>
							</option>
							<option value="0" <?php if ($this->filterpublish == "0") echo "selected='selected'"; ?>>
								<?php echo JText::_('ADSMANAGER_NO_PUBLISH'); ?>
							</option>
						</select>
						<span>
						<?php echo JText::_('ADSMANAGER_TH_USER')?>:
						</span>
						<span>
						<input type="text" name="search" value="<?php echo htmlspecialchars( $this->search );?>" class="text_area" onChange="document.adminForm.submit();" />
						</span>
						<span>
						<?php echo JText::_('Id')?>:
						</span>
						<span>
						<input type="text" name="content_id" value="<?php echo htmlspecialchars( $this->content_id );?>" class="text_area" onChange="document.adminForm.submit();" />
						</span>
						<?php if(version_compare(JVERSION, '3.0', 'ge')) {
						echo $this->pagination->getLimitBox();
						} ?>
					</td>
				</tr>
			</table>
<table class="adminlist table table-striped">
<thead>
<tr>
    <?php if (version_compare(JVERSION,'2.5.0','>=')) { ?>
    <th width="3%" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
    <?php } else { ?>
    <th width="3%" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->contents); ?>);" />
    <?php } ?>
	<th class="title hidden-phone" width="5%"><?php echo JHTML::_('grid.sort',JText::_('Id'),'a.id',@$this->lists['order_Dir'], @$this->lists['order'] );?></th>
	<th class="title" width="20%">
		<?php echo JHTML::_('grid.sort',JText::_('ADSMANAGER_TH_TITLE'), 'a.ad_headline', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
	</th>
	<th class="title" width="5%">
		<?php echo JHTML::_('grid.sort',JText::_('ADSMANAGER_TH_PUBLISH'), 'a.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
	</th>
	<th class="title hidden-phone" width="30%">
		<?php echo JText::_('ADSMANAGER_TH_IMAGE');?>
	</th>
	<?php if (ADSMANAGER_SPECIAL == "newspaper") {?>
	<th class="title" width="10%">
		Cells
	</th>
	<th class="title" width="10%">
		Mag
	</th>
	<th class="title" width="10%">
		Phone
	</th>
	<?php } ?>
	<?php if (ADSMANAGER_SPECIAL == "thiago"){?>
	<th class="title" width="10%">
		Ip
	</th>
	<?php } ?>
	<th class="title" width="10%">
		<?php echo JHTML::_('grid.sort',JText::_('ADSMANAGER_TH_USER'), 'user', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
	</th>
	<th class="title" width="10%">
		<?php echo JHTML::_('grid.sort',JText::_('ADSMANAGER_TH_CATEGORY'), 'c.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
	</th>
	<th class="title" width="10%">
		<?php echo JHTML::_('grid.sort',JText::_('ADSMANAGER_TH_DATE'), 'a.date_created', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
	</th>
	<th class="title" width="10%">
		<?php echo JHTML::_('grid.sort',JText::_('ADSMANAGER_TH_EXPIRATION_DATE'), 'a.expiration_date', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
	</th> 
</tr>
</thead>
<tbody>

<?php
	$k = 0;
	for($i=0; $i < count( $this->contents ); $i++) {
	$content = $this->contents[$i];

    ?>
     <tr class="row<?php echo $k; ?>">
	<td class="hidden-phone"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $content->id; ?>" onclick="isChecked(this.checked);" /></td>

	<td class="hidden-phone"><a href="<?php echo "index.php?option=com_adsmanager&c=contents&task=edit&id=".$content->id ?>"><?php echo $content->id; ?></a></td>
	<td><a href="<?php echo "index.php?option=com_adsmanager&c=contents&task=edit&id=".$content->id ?>"><?php echo $content->ad_headline; ?></a></td>
	<td align='center'><?php echo JHTML::_('grid.published', $content, $i ); ?></td>
	<td class="hidden-phone">
	<?php
	foreach($content->images as $img) {
		echo "<img width='50' height='50' src='".$this->baseurl."images/com_adsmanager/ads/".$img->thumbnail."?time=".time()."'>";
		echo '&nbsp;';  
	}
	?>
	</td>
	<?php if (ADSMANAGER_SPECIAL == "newspaper") {?>
	<td>
	<?php echo $content->ad_celltype ?>
	</td>
	<td>
	<?php echo $content->ad_magazine ?>
	</td>
	<td>
	<?php echo $content->ad_phone." ".$content->ad_phone2 ?>
	</td>
	<?php } ?>
	<?php if (ADSMANAGER_SPECIAL == "thiago"){?>
	<td>
	<?php echo $content->ad_ip ?>
	</td>
	<?php } ?>
	<td>
	<?php $target = TRoute::_($this->baseurl."index.php?option=com_adsmanager&view=list&user=".$content->userid);
	       
	       echo "<a target='_blank' href='$target'>".$content->user."</a>"; ?>	
	
	
</td>
	<td><?php echo '<a href="index.php?option=com_adsmanager&c=contents&catid='.$content->catid.'">'.$content->cat.'</a>'; ?></td>
	<td><?php echo $content->date_created; ?></td>
	<td><?php echo $content->expiration_date; ?></td>
	</tr>
<?php
	$k++;
	if ($k==2)
		$k = 0;
	} 

?>
</tbody>
<tfoot>
	<tr>
		<td colspan="9">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="contents" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>  
</form> 
