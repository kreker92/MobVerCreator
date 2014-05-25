<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

JHtml::_('behavior.tooltip');
if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('behavior.framework');
	$saveOrderingUrl = 'index.php?option=com_adsmanager&c=categories&task=saveorder&format=json';
	JHtml::_('sortablelist.sortable', 'itemsList', 'adminForm', 'asc', $saveOrderingUrl,true,true);
	$hasAjaxOrderingSupport = true;
} else {
	$hasAjaxOrderingSupport = false;
}

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
  <div style="float:right">
  <?php if(version_compare(JVERSION, '3.0', 'ge')) {
	echo $this->pagination->getLimitBox();
	} ?>
  </div>
  <div style="clear:both"></div>
</div>
<table class="adminlist table table-striped" id="itemsList">
<thead>
<tr>
<?php if($hasAjaxOrderingSupport !== false): ?>
<th width="20px">
</th>
<?php endif; ?>
<?php if (version_compare(JVERSION,'2.5.0','>=')) { ?>
    <th width="3%" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
    <?php } else { ?>
    <th width="3%" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->list); ?>);" />
    <?php } ?>
<th width="2%" class="hidden-phone"><?php echo JText::_('Id') ?></th>
<th width="30%"><?php echo JText::_('ADSMANAGER_TH_CATEGORY'); ?></th>
<th width="5%"><?php echo JText::_('ADSMANAGER_TH_IMAGE');?></th>
<th width="5%"><?php echo JText::_('ADSMANAGER_TH_ADS');?></th>
<?php if($hasAjaxOrderingSupport === false): ?>
<th width="8%" nowrap="nowrap" class="hidden-phone">
<?php echo JText::_('ADSMANAGER_ORDER_BY_TEXT'); ?>
<?php if ($this->ordering) echo JHTML::_('grid.order',  $this->list ); ?>
</th>
<?php endif; ?>
<th width="40%">
<?php echo JText::_('ADSMANAGER_TH_PUBLISH'); ?>
</th>
</tr>
</thead>
<tbody>
<?php
$num = 0;
$orders = array();
foreach ($this->list as $key => $row) {
	 if($hasAjaxOrderingSupport !== false) {
		if (!isset($orders[$row->parent])) {
			$orders[$row->parent] = 0;
		} else {
			$orders[$row->parent]++;
		}
		$row->ordering = $orders[$row->parent];
	 }
	 ?>
	 <tr class="row<?php echo ($num & 1); ?>" parents="<?php echo $row->parentslist?>" sortable-group-id="<?php echo $row->parent;?>"  level="<?php echo $row->level;?>"  item-id="<?php echo $row->id;?>" >
	 <?php if($hasAjaxOrderingSupport !== false): ?>
	 <td class="order nowrap center hidden-phone">
		<span class="sortable-handler" title="" rel="tooltip">
			<i class="icon-menu"></i>
		</span>
		<input type="text" style="display:none"  name="order[]" size="5"
			value="<?php echo $row->ordering;?>" class="input-mini text-area-order " />
	</td>
	<?php endif; ?>
	 <td class="hidden-phone"><input type="checkbox" id="cb<?php echo $num;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>

	<td class="hidden-phone"><?php echo $row->id; ?></td>
	<td>
		<a href="<?php echo "index.php?option=com_adsmanager&c=categories&task=edit&id=".$row->id ?>"><?php echo $row->treename ?></a>
	</td>
	<td align="center">
	<?php 
		echo '<img src="'.TTools::getCatImageUrl($row->id,true).'?time='.time().'"/>';
	?>
	</td>
	<td align='center'>
		<a href="<?php echo "index.php?option=com_adsmanager&c=contents&catid=".$row->id;?>">
			<img src="<?php echo JURI::root()?>components/com_adsmanager/images/items.png"/>
		</a>
	</td>
	<?php if($hasAjaxOrderingSupport === false): ?>
	<td class="order hidden-phone" nowrap="nowrap" align='center'>
	<span><?php echo $this->pagination->orderUpIcon( $num, $row->parent == 0 || $row->parent == @$this->list[$key-1]->parent, 'orderup', 'Move Up', $this->ordering); ?></span>
	<span><?php echo $this->pagination->orderDownIcon( $num, $this->total, $row->parent == 0 || $row->parent == @$this->list[$key+1]->parent, 'orderdown', 'Move Down', $this->ordering ); ?></span>
	<?php $disabled = $this->ordering ?  '' : 'disabled="disabled"'; ?>
	<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
	</td>
	<?php endif; ?>
	<td align='center'><?php echo JHTML::_('grid.published', $row, $num ); ?></td>
	</tr>
	<?php
	$num++;
}
?>
</tbody>
<tfoot>
		<tr>
			<td colspan="7">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
</table>

<input type="hidden" name="filter_order" id="filter_order" value="id" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="asc" />
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="categories" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form> 