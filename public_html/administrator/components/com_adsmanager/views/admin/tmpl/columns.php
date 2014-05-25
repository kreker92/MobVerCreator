<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<br />
<style>
    .fieldsarea {
	  border: 1px solid #CBCBCB;
	  font-size: 12px !important;
	  margin-top: 10px;
	  padding: 15px 5px 5px;
	  position: relative;
	}
	.fieldsarea .title {
	    background-color: #EDEDED;
	    border: 1px solid #CBCBCB;
	    font-weight: bold;
	    left: 10px;
	    position: absolute;
	    top: -5px;
	}
	.connectedSortable {
	  background-color: #EEDDFF;
	  list-style-type: none;
	  margin: 10px 0 0;
	  max-width: 200px;
	  min-height: 50px;
	  padding: 5px;
	  position: relative;
	}
	.connectedSortable li { 
		margin: 0 5px 5px 5px; padding: 5px; font-size: 10px; 
	}
	.column { float:left;width:20%;}
	.mainarea { float:left;width:80%;}
	.column h2 { text-align:center;}
	.ui-state-highlight { height: 1.5em; line-height: 1.2em; }
</style>
<form action="index.php" method="post" name="adminForm" id="adminForm">
 <p><?php echo JText::_('ADSMANAGER_COLUMN_EXPLICATION'); ?></p>
<div>
	<div class="column">
		<h2>All Fields</h2>
		<ul id="fields" class="connectedSortable">
		<?php foreach($this->fields as $field) {?>
		<li class='ui-state-default' id="<?php echo $field->fieldid?>"><?php echo JText::_($field->title)." (".$field->name." ".$field->fieldid.")" ?></li>
		<?php } ?>
		</ul>
	</div>
	<div class="mainarea">
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist table table-striped">
	<tr>
	<?php
	for($i=0,$nb=count($this->columns);$i < $nb ;$i++) {
	?>
		<td>
		<div class="fieldsarea">
				<span class="title"><?php echo htmlspecialchars($this->columns[$i]->name) ?></span>
				<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $this->columns[$i]->id; ?>" onclick="isChecked(this.checked);" />
				<input type="text" size="35" id="title_column_<?php echo $this->columns[$i]->id?>" name="title_column_<?php echo $this->columns[$i]->id?>" value="<?php echo htmlspecialchars($this->columns[$i]->name) ?>" />
				<ul id="fields_<?php echo $this->columns[$i]->id?>" class="connectedSortable"></ul>
				<input type="hidden" id="listfields_<?php echo $this->columns[$i]->id?>" name="listfields_<?php echo $this->columns[$i]->id?>" />
			</div>
		</td>
	<?php
	}
	?>
	</tr>
	</table>
	</div>
</div>

<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="columns" />
<input type="hidden" name="boxchecked" value="0" />
</form> 

<script>
	jQ(function() {
		jQ( "#fields" ).sortable({
			connectWith: ".connectedSortable",
			placeholder: "ui-state-highlight",
		}).disableSelection();
		jQ( ".fieldsarea .connectedSortable" ).sortable({
			connectWith: ".connectedSortable",
			placeholder: "ui-state-highlight",
			update: function() {
				list = jQ(this).sortable('toArray');
				jQ(this).next().val(list.join(','));
			}
		}).disableSelection();

		<?php foreach($this->fColumns as $key => $fields) {
			foreach($fields as $f) {?>
				jQ('#<?php echo $f->fieldid?>').appendTo('#fields_<?php echo $key?>');
			<?php } ?>
			list = jQ('#fields_<?php echo $key?>').sortable('toArray');
			jQ('#listfields_<?php echo $key?>').val(list.join(','));
		<?php 
		} 
		?>
	});
</script>