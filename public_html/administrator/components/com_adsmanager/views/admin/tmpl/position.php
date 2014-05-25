<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<p><?php echo JText::_('ADSMANAGER_AD_DISPLAY_EXPLICATION'); ?></p>
<link rel="stylesheet" href="../components/com_adsmanager/css/adsmanager.css" type="text/css" />
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
	  max-width: 400px;
	  min-height: 50px;
	  padding: 5px;
	  position: relative;
	}
	.connectedSortable li { 
		margin: 0 5px 5px 5px; padding: 5px; font-size: 10px; 
	}
	.column { float:left;width:30%;}
	.ads_background { float:left;width:70%;}
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
	<div class="ads_background">
		<div class="addetails" align="left">
			<div class="fieldsarea">
				<span class="title"><?php echo htmlspecialchars($this->positions[0]->name) ?></span>
				<input type="text" size="50" id="title_position_<?php echo $this->positions[0]->id?>" name="title_position_<?php echo $this->positions[0]->id?>" value="<?php echo htmlspecialchars($this->positions[0]->title) ?>" />
				<ul id="fields_<?php echo $this->positions[0]->id?>" class="connectedSortable"></ul>
				<input type="hidden" id="listfields_<?php echo $this->positions[0]->id?>" name="listfields_<?php echo $this->positions[0]->id?>" />
			</div>
			<div>
				<?php echo JText::_('ADSMANAGER_SHOW_OTHERS')."<b>USER</b>";?>
			</div>
			<div class="addetails_topright">
			    <div class="fieldsarea">
					<span class="title"><?php echo htmlspecialchars($this->positions[3]->name) ?></span>
					<input type="text" size="50" id="title_position_<?php echo $this->positions[3]->id?>" name="title_position_<?php echo $this->positions[3]->id?>" value="<?php echo htmlspecialchars($this->positions[3]->title) ?>" />
					<ul id="fields_<?php echo $this->positions[3]->id?>" class="connectedSortable"></ul>
					<input type="hidden" id="listfields_<?php echo $this->positions[3]->id?>" name="listfields_<?php echo $this->positions[3]->id?>" />
				</div>
			</div>
			<div class="addetailsmain">
				<div class="adsmanager_ads_body">
					<div class="adsmanager_ads_desc">
						<div class="fieldsarea">
							<span class="title"><?php echo htmlspecialchars($this->positions[2]->name) ?></span>
							<input type="text" size="50" id="title_position_<?php echo $this->positions[2]->id?>" name="title_position_<?php echo $this->positions[2]->id?>" value="<?php echo htmlspecialchars($this->positions[2]->title) ?>" />
							<ul id="fields_<?php echo $this->positions[2]->id?>" class="connectedSortable"></ul>
							<input type="hidden" id="listfields_<?php echo $this->positions[2]->id?>" name="listfields_<?php echo $this->positions[2]->id?>" />
						</div>
					</div>
					<div class="adsmanager_ads_price">
						<div class="fieldsarea">
							<span class="title"><?php echo htmlspecialchars($this->positions[1]->name) ?></span>
							<input type="text" size="50" id="title_position_<?php echo $this->positions[1]->id?>" name="title_position_<?php echo $this->positions[1]->id?>" value="<?php echo htmlspecialchars($this->positions[1]->title) ?>" />
							<ul id="fields_2" class="connectedSortable"></ul>
							<input type="hidden" id="listfields_<?php echo $this->positions[1]->id?>" name="listfields_<?php echo $this->positions[1]->id?>" />
						</div>
					</div>
					<div class="adsmanager_ads_desc">
						<div class="fieldsarea">
							<span class="title"><?php echo htmlspecialchars($this->positions[5]->name) ?></span>
							<input type="text" size="50" id="title_position_<?php echo $this->positions[5]->id?>" name="title_position_<?php echo $this->positions[5]->id?>" value="<?php echo htmlspecialchars($this->positions[5]->title) ?>" />
							<ul id="fields_6" class="connectedSortable"></ul>
							<input type="hidden" id="listfields_<?php echo $this->positions[5]->id?>" name="listfields_<?php echo $this->positions[5]->id?>" />
						</div>
					</div>
					<div class="adsmanager_ads_contact">
						<div class="fieldsarea">
								<span class="title"><?php echo htmlspecialchars($this->positions[4]->name) ?></span>
								<input type="text" size="50" id="title_position_<?php echo $this->positions[4]->id?>" name="title_position_<?php echo $this->positions[4]->id?>" value="<?php echo htmlspecialchars($this->positions[4]->title) ?>" />
								<ul id="fields_<?php echo $this->positions[4]->id?>" class="connectedSortable"></ul>
								<input type="hidden" id="listfields_<?php echo $this->positions[4]->id?>" name="listfields_<?php echo $this->positions[4]->id?>" />
							</div>
						</div>
				</div>
				<div class="adsmanager_ads_image">
					<img alt="nopic" src="<?php echo ADSMANAGER_NOPIC_IMG; ?>">					
				</div>
				<div class="adsmanager_spacer"></div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="positions" />
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

		<?php foreach($this->fDisplay as $key => $fields) {
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