<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="adsmanager_search_box">
<div class="adsmanager_inner_box">
	<div align="left">
		<table>
			<tr>
				<td><?php echo JText::_('ADSMANAGER_FORM_CATEGORY'); ?></td>
				<td>
					<select onchange="jumpmenu('parent',this)">			
					 <option value="<?php echo TRoute::_("index.php?option=com_adsmanager&view=search&catid=0"); ?>" <?php if ($this->catid == 0) echo 'selected="selected"'; ?>><?php echo JText::_('ADSMANAGER_MENU_ALL_ADS'); ?></option>
					<?php
					 $link = "index.php?option=com_adsmanager&view=search";
					 $this->selectCategories(0,"",$this->cats,$this->catid,1,$link,0); 
					?>
					</select>
				</td>
			</tr>
		</table> 
		<?php $link = TRoute::_("index.php?option=com_adsmanager&view=result&catid=".$this->catid); ?>
		<form action="<?php echo $link ?>" method="post" id="adminForm">
		<table>
			
			<?php 
			foreach($this->searchfields as $fsearch) {
				$title = $this->field->showFieldTitle($this->catid,$fsearch);
				echo "<tr id='tr_".$fsearch->name."'><td>".htmlspecialchars($title)."</td><td>";
				$this->field->showFieldSearch($fsearch,$this->catid,null);
				echo "</td></tr>";
			}?>			
		</table> 
		<input type="hidden" value="1" name="new_search" />
		<input type="submit" value="<?php echo JText::_('ADSMANAGER_SEARCH_BUTTON'); ?>" />
		</form>
	</div>		  
</div>
</div>
<script>
function jumpmenu(target,obj,restore){
  eval(target+".location='"+obj.options[obj.selectedIndex].value+"'");	
  obj.options[obj.selectedIndex].innerHTML="<?php echo JText::_('ADSMANAGER_WAIT');?>";	
}		
function checkdependency(child,parentname,parentvalue) {
	//Simple checkbox
	if (jQ('input[name="'+parentname+'"]').is(':checkbox')) {
		//alert("test");
		if (jQ('input[name="'+parentname+'"]').attr('checked')) {
			jQ('#adminForm #'+child).show();
			jQ('#adminForm #tr_'+child).show();
		}
		else {
			jQ('#adminForm #'+child).hide();
			jQ('#adminForm #tr_'+child).hide();
			
			//cleanup child field 
			if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
				jQ('#adminForm #'+child).attr('checked', false);
			}
			else {
				jQ('#adminForm #'+child).val('');
			}
		} 
	}
	//If checkboxes or radio buttons, special treatment
	else if (jQ('input[name="'+parentname+'"]').is(':radio')  || jQ('input[name="'+parentname+'[]"]').is(':checkbox')) {
		var find = false;
		var allVals = [];
		jQ("input:checked").each(function() {
			if (jQ(this).val() == parentvalue) {	
				jQ('#adminForm #'+child).show();
				jQ('#adminForm #tr_'+child).show();
				find = true;
			}
		});
		
		if (find == false) {
			jQ('#adminForm #'+child).hide();
			jQ('#adminForm #tr_'+child).hide();
			
			//cleanup child field 
			if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
				jQ('#adminForm #'+child).attr('checked', false);
			}
			else {
				jQ('#adminForm #'+child).val('');
			}
		}

	}
	//simple text
	else if (jQ('#adminForm #'+parentname).val() == parentvalue) {
		jQ('#adminForm #'+child).show();
		jQ('#adminForm #tr_'+child).show();
	} 
	else {
		jQ('#adminForm #'+child).hide();
		jQ('#adminForm #tr_'+child).hide();
		
		//cleanup child field 
		if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
			jQ('#adminForm #'+child).attr('checked', false);
		}
		else {
			jQ('#adminForm #'+child).val('');
		}
	}
}
function dependency(child,parentname,parentvalue) {
	//if checkboxes
	jQ('input[name="'+parentname+'[]"]').change(function() {
		checkdependency(child,parentname,parentvalue);
	});
	//if buttons radio
	jQ('input[name="'+parentname+'"]').change(function() {
		checkdependency(child,parentname,parentvalue);
	});
	jQ('#'+parentname).click(function() {
		checkdependency(child,parentname,parentvalue);
	});
	checkdependency(child,parentname,parentvalue);
}

jQ(document).ready(function() {

	<?php foreach($this->searchfields as $field) { 
		if (@$field->options->is_conditional_field == 1) { ?>
	dependency('<?php echo $field->name?>',
			   '<?php echo $field->options->conditional_parent_name?>',
			   '<?php echo $field->options->conditional_parent_value?>');
		<?php } 
	}?>
});
</script>