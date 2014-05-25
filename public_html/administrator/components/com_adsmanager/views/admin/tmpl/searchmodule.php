<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if (!isset($this->conf->simple_fields)) {
	$this->conf->simple_fields = array();
	$this->conf->advanced_fields = array();
}
?>
<style>
	#fields, #simplesearch,#advancedsearch { 
		list-style-type: none; margin: 0; padding: 0 0 2.5em;
		width: 100%;min-height:400px;background-color:white;
	 }
	.connectedSortable li { 
		margin: 0 5px 5px 5px; padding: 5px; font-size: 10px; 
	}
	.column { float:left;width:30%; margin-right: 10px;}
	.column h2 { text-align:center;}
	.ui-state-highlight { height: 1.5em; line-height: 1.2em; }
</style>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
<div class="column">
<h2>All Fields</h2>
<ul id="fields" class="connectedSortable">
<?php foreach($this->fields as $field) {?>
<li class='ui-state-default' id="<?php echo $field->fieldid?>"><?php echo JText::_($field->title)." (".$field->name." ".$field->fieldid.")" ?></li>
<?php } ?>
</ul>
</div>
<div class="column">
<h2>Simple Search</h2>
<input type="hidden" id="simple_fields" name="params_simple_fields" value="" />
<ul id="simplesearch" class="connectedSortable">
</ul>
</div>
<div class="column">
<h2>Advanced Search</h2>
<input type="hidden" id="advanced_fields" name="params_advanced_fields" value="" />
<ul id="advancedsearch" class="connectedSortable">
</ul>
</div>
<div style="clear:both"></div>
</div>

<input type="hidden" name="option" value="com_adsmanager" />

<input type="hidden" name="task" value="" />

<input type="hidden" name="id" value="<?php echo $this->conf->id ?>" />

<input type="hidden" name="c" value="searchmodule" />
</form> 

<script>
	jQ(function() {
		jQ( "#fields" ).sortable({
			connectWith: ".connectedSortable",
			placeholder: "ui-state-highlight",
		}).disableSelection();
		jQ( "#simplesearch" ).sortable({
			connectWith: ".connectedSortable",
			placeholder: "ui-state-highlight",
			update: function() {
				list = jQ(this).sortable('toArray');
				jQ('#simple_fields').val(list.join(','));
			}
		}).disableSelection();
		jQ( "#advancedsearch" ).sortable({
			connectWith: ".connectedSortable",
			placeholder: "ui-state-highlight",
			update: function() {
				list = jQ(this).sortable('toArray');
				jQ('#advanced_fields').val(list.join(','));
			}
		}).disableSelection();

		<?php foreach($this->conf->simple_fields as $f) {?>
		jQ('#<?php echo $f?>').appendTo('#simplesearch');	
		<?php }?>
		<?php foreach($this->conf->advanced_fields as $f) {?>
		jQ('#<?php echo $f?>').appendTo('#advancedsearch');	
		<?php }?>

		list = jQ("#advancedsearch").sortable('toArray');
		jQ('#advanced_fields').val(list.join(','));

		list = jQ("#simplesearch").sortable('toArray');
		jQ('#simple_fields').val(list.join(','));
	});
</script>
