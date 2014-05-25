<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<script type="text/javascript">
  function getObject(obj) {
    var strObj;
    if (document.all) {
      strObj = document.all.item(obj);
    } else if (document.getElementById) {
      strObj = document.getElementById(obj);
    }
    return strObj;
  }
  
   function showimage(preview,obj) {
		//if (!document.images) return;
		var img = getObject(preview);
		img.src = '<?php echo $this->baseurl."images/com_adsmanager/";?>/fields/' + getSelectedValue( obj );
	}
	
	function getSelectedValue(obj) {
		i = obj.selectedIndex;
		if (i != null && i > -1) {
			return obj.options[i].value;
		} else {
			return null;
		}
	}
  
   <?php if(version_compare(JVERSION,'1.6.0','>=')){ ?>
   Joomla.submitbutton = function(pressbutton) {
   <?php } else { ?>
   function submitbutton(pressbutton) {
   <?php } ?>
    if (pressbutton == 'cancel') {
	   submitform(pressbutton);	
	   return;
    }
     if (pressbutton == 'showField') {
       document.adminForm.type.disabled=false;
       submitform(pressbutton);
       return;
     }
     var coll = document.adminForm;
     var errorMSG = '';
     var iserror=0;
     if (coll != null) {
       var elements = coll.elements;
       // loop through all input elements in form
       for (var i=0; i < elements.length; i++) {
         // check if element is mandatory; here mosReq=1
         if (elements.item(i).getAttribute('mosReq') == 1) {
           if (elements.item(i).value == '') {
             //alert(elements.item(i).getAttribute('mosLabel') + ':' + elements.item(i).getAttribute('mosReq'));
             // add up all error messages
             errorMSG += elements.item(i).getAttribute('mosLabel') + ' : <?php echo JText::_('ADSMANAGER_REGWARN_ERROR'); ?>\n';
             // notify user by changing background color, in this case to red
             elements.item(i).style.background = "red";
             iserror=1;
           }
         }
       }
     }
     if(iserror==1) {
       alert(errorMSG);
     } else {
       document.adminForm.type.disabled=false;
       submitform(pressbutton);
     }
   }
  
  function insertImageRow() {
    var oTable = getObject("ImagesfieldValuesBody");
    var oRow, oCell ,oCellCont;
    var oCell2 ,oCellCont2, oInput2,oImage,oSelect,oOption;
    var i, j,k;
    i=document.adminForm.ImagevalueCount.value;
    i++;
    // Create and insert rows and cells into the first body.
    oRow = document.createElement("tr");
    oTable.appendChild(oRow);

    oCell = document.createElement("td");
    oSelect=document.createElement("select");
    oSelect.onchange = function(){
		showimage('preview'+i,this); //Gestion de la particularite d'ie qui n'accepte pas d'ajouter un evement avec setAttribute. ie ignore la ligne au dessus, ff ignore cette ligne
	}
    oSelect.id = 'vSelectImages['+i+']';
    oSelect.name = 'vSelectImages['+i+']';
    k=0;
	oSelect.length++;
	oSelect.options[0].text= 'No Image';
	oSelect.options[0].value= 'null';
	<?php 
	if(isset($this->fieldimages)) {
	foreach($this->fieldimages as $image) {
	?>
	k++;
	oSelect.length++;
	oSelect.options[k].text= '<?php echo $image; ?>';
	oSelect.options[k].value= '<?php echo $image; ?>';
	<?php
	}
	}
	?>
	oCell.appendChild(oSelect);
	oImage = document.createElement("img");
	oImage.setAttribute('src',"");
	oImage.setAttribute('id',"preview"+i);
	oImage.setAttribute('name',"preview"+i);
	oCell.appendChild(oImage);		
    oCell2 = document.createElement("td");
    oInput2=document.createElement("input");
    oInput2.name="vImagesValues["+i+"]";
    oInput2.setAttribute('mosLabel','Value');
    oInput2.setAttribute('mosReq',0); 
    oCell2.appendChild(oInput2);
     
    oRow.appendChild(oCell);
    oRow.appendChild(oCell2);
    oSelect.focus();

    document.adminForm.ImagevalueCount.value=i;
  }


  function disableAll() {
    var elem;
	elem=getObject('divCB');
    elem.style.visibility = 'hidden';
    elem.style.display = 'none';
    elem=getObject('divValues');
    elem.style.visibility = 'hidden';
    elem.style.display = 'none';
	elem=getObject('divImagesValues');
    elem.style.visibility = 'hidden';
	elem.style.display = 'none';
    elem=getObject('divColsRows');
    elem.style.visibility = 'hidden';
    elem.style.display = 'none';
    elem=getObject('divText');
    elem.style.visibility = 'hidden';
    elem.style.display = 'none';
    elem=getObject('divCurrency');
    elem.style.visibility = 'hidden';
    elem.style.display = 'none';
    elem=getObject('divNumber');
    elem.style.visibility = 'hidden';
    elem.style.display = 'none';
    
    if (elem=getObject('vNames[0]')) {
      elem.setAttribute('mosReq',0);
    }
    if (elem=getObject('vValues[0]')) {
      elem.setAttribute('mosReq',0);
    }
	if (elem=getObject('vImagesValues[0]')) {
      elem.setAttribute('mosReq',0);
    }
	
	elem=getObject('divLink');
    elem.style.visibility = 'hidden';
    elem.style.display = 'none';
	
	<?php
		if(isset($this->plugins))
		{
			foreach($this->plugins as $key => $plug) {
				echo $plug->getEditFieldJavaScriptDisable()."\n";
			}
		}
	?>
  }
  
  function selType(sType) {
    var elem;
    //alert(sType);
    switch (sType) {
      case 'editor':
      case 'textarea':
        disableAll();
        elem=getObject('divText');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        elem=getObject('divColsRows');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
      break;
      
      case 'emailaddress':
      case 'password':
      case 'text':
        disableAll();
        elem=getObject('divText');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
      break;

      case 'number':
  		disableAll();
        elem=getObject('divText');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        elem=getObject('divNumber');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
  		elem=getObject('divValues');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        if (elem=getObject('vNames[0]')) {
            elem.setAttribute('mosReq',1);
        }
        if (elem=getObject('vValues[0]')) {
  			elem.setAttribute('mosReq',1);
  		}
        updateSearchType(jQ('#number_search_type').val());
  		break;
	  
	  case 'price':
		disableAll();
        elem=getObject('divText');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        elem=getObject('divCurrency');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
		elem=getObject('divValues');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        if (elem=getObject('vNames[0]')) {
          elem.setAttribute('mosReq',1);
        }
        if (elem=getObject('vValues[0]')) {
		  elem.setAttribute('mosReq',1);
		}
        updateSearchType(jQ('#price_search_type').val());
		break;
	  
	  case 'url':
		disableAll();
        elem=getObject('divText');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        elem=getObject('divLink');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        break;
      
      case 'select':
      case 'multiselect':
        disableAll();
		elem=getObject('divCB');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        elem=getObject('divValues');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        if (elem=getObject('vNames[0]')) {
          elem.setAttribute('mosReq',1);
        }
        if (elem=getObject('vValues[0]')) {
		  elem.setAttribute('mosReq',1);
		}
      break;
	  
	  case 'radioimage':
      case 'multicheckboximage':
		disableAll();
        elem=getObject('divColsRows');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        elem=getObject('divImagesValues');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        if (elem=getObject('vSelectImages[0]')) {
          elem.setAttribute('mosReq',2);
        }
        if (elem=getObject('vImagesValues[0]')) {
          elem.setAttribute('mosReq',1);
        }
        break;
      
      case 'radio':
      case 'multicheckbox':
        disableAll();
		elem=getObject('divCB');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        elem=getObject('divColsRows');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        elem=getObject('divValues');
        elem.style.visibility = 'visible';
        elem.style.display = 'block';
        if (elem=getObject('vNames[0]')) {
          elem.setAttribute('mosReq',1);
        }
        if (elem=getObject('vValues[0]')) {
          elem.setAttribute('mosReq',1);
        }
      break;
	  
	  <?php
		if(isset($this->plugins))
		{
			foreach($this->plugins as $key => $plug) {
				echo "case '$key':\n";
				echo $plug->getEditFieldJavaScriptActive()."\n";
				echo "break;\n";
			}
		}
	  ?>

      case 'delimiter':
      default: 
        disableAll();
    }
  }

  function prep4SQL(o){
	if(o.value!='') {
		o.value=o.value.replace('ad_','');
    		o.value='ad_' + o.value.replace(/[^a-zA-Z]+/g,'').toLowerCase();
	}
  }

</script>
<style>
    table.custom {
        width: 99%;
        margin-right: 10px;
        margin-bottom: 10px;
        border-collapse: initial;
    }
    
    div.custom {
         padding: 5px;
         background-color: white;
         width: 97.4%;
         border: 1px solid #D5D5D5;
         border-top: none;
         margin-top: -11px;
    }
</style>
<form action="index.php?option=com_adsmanager" method="POST" name="adminForm" id="adminForm">
<table cellspacing="0" cellpadding="0" width="100%">
<tr valign="top">
	<td width="60%">
		<table class="adminform custom">
			<tr colspan="3">
				<th><?php echo JText::_('ADSMANAGER_FIELD_GENERAL_PARAMETERS');?></th>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_TYPE');?></td>
				<td width="20%"><?php echo $this->lists['type']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_NAME');?></td>
				<td align=left  width="20%"><input onchange="prep4SQL(this);" type="text" name="name" mosReq=1 mosLabel="Name" class="inputbox" value="<?php echo @$this->field->name; ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_TITLE');?></td>
				<td width="20%" align=left><input type="text" name="title" mosReq=1 mosLabel="Title" class="inputbox" value="<?php echo @JText::_(htmlspecialchars($this->field->title)); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_DESCRIPTION');?></td>
				<td width="20%" align=left><input type="text" name="description" mosLabel="Description" size="40" value="<?php echo @htmlspecialchars($this->field->description); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_REQUIRED');?></td>
				<td width="20%"><?php echo $this->lists['required']; ?></td>
				<td>&nbsp;</td>
			</tr>
            <tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_PUBLISHED');?></td>
				<td width="20%"><?php echo $this->lists['published']; ?></td>
				<td>&nbsp;</td>
			</tr>
            <tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_EDITABLE');?></td>
				<td width="20%"><?php echo $this->lists['editable']; ?></td>
				<td>&nbsp;</td>
			</tr>
            <tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_SEARCHABLE');?></td>
				<td width="20%"><?php echo $this->lists['searchable']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_PROFILE');?></td>
				<td width="20%"><?php echo $this->lists['profile']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_CB');?></td>
				<td width="20%"><?php echo $this->lists['cbfields']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_SORT_OPTION');?></td>
				<td width="20%"><?php echo $this->lists['sort']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_SORT_DIRECTION');?></td>
				<td width="20%"><?php echo $this->lists['sort_direction']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_SIZE');?></td>
				<td width="20%"><input type="text" name="size" mosLabel="Size" class="inputbox" value="<?php echo @$this->field->size; ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_EDIT_ADMIN_ONLY');?></td>
				<td width="20%">
				<input type="radio" name="options_common_edit_admin_only" value="1" <?php if (@$this->options->edit_admin_only == "1") echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_YES')?>&nbsp;
				<input type="radio" name="options_common_edit_admin_only" value="0" <?php if (@$this->options->edit_admin_only == "0" || !isset($this->options->edit_admin_only)) echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_NO')?><br/><br/>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_CONDITIONAL_FIELD');?></td>
				<td width="20%">
				<input type="radio" name="options_common_is_conditional_field" value="1" <?php if (@$this->options->is_conditional_field == "1") echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_YES')?>&nbsp;
				<input type="radio" name="options_common_is_conditional_field" value="0" <?php if (@$this->options->is_conditional_field == "0" || !isset($this->options->is_conditional_field)) echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_NO')?><br/><br/>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_CONDITIONAL_PARENT');?></td>
				<td width="20%">
				<?php echo JText::_('ADSMANAGER_FIELD_NAME');?><input type="text" name="options_common_conditional_parent_name" value="<?php echo htmlspecialchars(@$this->options->conditional_parent_name)?>" /><br/> 
				<?php echo JText::_('ADSMANAGER_FIELD_VALUE_VALUE');?><input type="text" name="options_common_conditional_parent_value" value="<?php echo htmlspecialchars(@$this->options->conditional_parent_value)?>" />
				</td>
				<td>&nbsp;</td>
			</tr>
        </table>
        <table class="adminform custom">
			<tr colspan="3">
				<th><?php echo JText::_('ADSMANAGER_FIELD_DISPLAY_PARAMETERS');?></th>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_DISPLAY_TITLE');?></td>
				<td width="20%"><?php echo $this->lists['display_title']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_COLUMN');?></td>
				<td width="20%"><?php echo $this->lists['columns']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_COLUMN_ORDER');?></td>
				<td width="20%" align=left><input type="text" name="columnorder" mosLabel="Title" class="inputbox" value="<?php echo @$this->field->columnorder; ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_POSITION_DISPLAY');?></td>
				<td width="20%"><?php echo $this->lists['positions']; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_POSITION_ORDER');?></td>
				<td width="20%" align=left><input type="text" name="posorder" mosLabel="Title" class="inputbox" value="<?php echo @$this->field->posorder; ?>" /></td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<div id="divText"  class="pagetext">
			<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform custom">
                <tr colspan="3">
                    <th><?php echo JText::_('ADSMANAGER_FIELD_SPECIFIC_PARAMETERS');?></th>
                </tr>
				<tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_MAX_LENGTH');?></td>
					<?php
						if (!isset($this->field->maxlength)||($this->field->maxlength ==""))
							$this->field->maxlength = 20;
					?>
					<td width="20%"><input type="text" name="maxlength" mosLabel="Max Length" class="inputbox" value="<?php echo $this->field->maxlength; ?>" /></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
        <div id="divCurrency"  class="pagetext">
			<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform custom">
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_CURRENCY_SYMBOL');?></td>
					<td width="20%"><input type="text" name="options_price_currency_symbol" mosReq=1 mosLabel="Currency Code" class="inputbox" value="<?php if(isset($this->options->currency_symbol)){ echo htmlspecialchars($this->options->currency_symbol); }else{ echo '€'; } ?>" /></td>
					<td>&nbsp;</td>
				</tr>
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_CURRENCY_POSITION');?></td>
					<td width="20%">
                        <input type="radio" name="options_price_currency_position" mosReq=1 value="before" <?php if (@$this->options->currency_position == "before") echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_FIELD_CURRENCY_POSITION_BEFORE')?>&nbsp;
                        <input type="radio" name="options_price_currency_position" mosReq=1 value="after" <?php if (@$this->options->currency_position == "after" || !isset($this->options->currency_position)) echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_FIELD_CURRENCY_POSITION_AFTER')?>
                    </td>
					<td>&nbsp;</td>
				</tr>
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_CURRENCY_NUMBER_DECIMALS');?></td>
                    <td width="20%"><input type="text" name="options_price_currency_number_decimals" mosReq=1 mosLabel="Currency Number of Decimals" class="inputbox" value="<?php if(isset($this->options->currency_number_decimals)){ echo htmlspecialchars($this->options->currency_number_decimals); }else{ echo '2'; } ?>" /></td>
					<td>&nbsp;</td>
				</tr>
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_CURRENCY_DECIMAL_SEPARATOR');?></td>
					<td width="20%"><input type="text" name="options_price_currency_decimal_separator" mosReq=1 mosLabel="Currency Decimal Separator" class="inputbox" value="<?php if(isset($this->options->currency_decimal_separator)){ echo htmlspecialchars($this->options->currency_decimal_separator); }else{ echo '.'; } ?>" /></td>
					<td>&nbsp;</td>
				</tr>
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_CURRENCY_THOUSANDS_SEPARATOR');?></td>
					<td width="20%"><input type="text" name="options_price_currency_thousands_separator" mosReq=1 mosLabel="Currency Thousands Separator" class="inputbox" value="<?php if(isset($this->options->currency_thousands_separator)){ echo htmlspecialchars($this->options->currency_thousands_separator); }else{ echo ' '; } ?>" /></td>
					<td>&nbsp;</td>
				</tr>
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_CURRENCY_DISPLAY_FREE_PRICE');?></td>
					<td width="20%">
                        <input type="radio" name="options_price_currency_display_free_price" mosReq=1 value="0" <?php if (@$this->options->currency_display_free_price == "0" || !isset($this->options->currency_display_free_price)) echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_NO')?>&nbsp;
                        <input type="radio" name="options_price_currency_display_free_price" mosReq=1 value="1" <?php if (@$this->options->currency_display_free_price == "1") echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_YES')?>
					</td>
                    <td>&nbsp;</td>
				</tr>
				<tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_SEARCH_TYPE');?></td>
					<td width="20%">
					<select id='price_search_type' name='options_price_search_type'>
			            <option value='textfield' <?php if (@$this->options->search_type == 'textfield') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_FIELD_SEARCH_TYPE_TEXTFIELD'); ?></option>
						<option value='select' <?php if (@$this->options->search_type == 'select') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_FIELD_SEARCH_TYPE_SELECT'); ?></option>
						<option value='minmax' <?php if (@$this->options->search_type == 'minmax') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_FIELD_SEARCH_TYPE_MINMAX'); ?></option>
					  </select>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
		<div id="divNumber"  class="pagetext">
			<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform custom">
				<tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_SEARCH_TYPE');?></td>
					<td width="20%">
					<select id='number_search_type' name='options_number_search_type'>
			            <option value='textfield' <?php if (@$this->options->search_type == 'textfield') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_FIELD_SEARCH_TYPE_TEXTFIELD'); ?></option>
						<option value='select' <?php if (@$this->options->search_type == 'select') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_FIELD_SEARCH_TYPE_SELECT'); ?></option>
						<option value='minmax' <?php if (@$this->options->search_type == 'minmax') { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_FIELD_SEARCH_TYPE_MINMAX'); ?></option>
					  </select>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
		<div id="divCB"  class="pagetext">
			<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform custom">
				<tr colspan="3">
                    <th><?php echo JText::_('ADSMANAGER_FIELD_SPECIFIC_PARAMETERS');?></th>
                </tr>
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_CBFIELDVALUES');?></td>
					<td width="20%"><?php echo $this->lists['cbfieldvalues']; ?></td>
					<td><?php echo JText::_('ADSMANAGER_CBFIELDVALUES_LONG');?></td>
				</tr>
			</table>
		</div>
		<div id="divLink" class="pagetext">
			<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform custom">
				<tr colspan="3">
                    <th><?php echo JText::_('ADSMANAGER_FIELD_SPECIFIC_PARAMETERS');?></th>
                </tr>
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_LINK_TEXT');?></td>
					<td width="20%"><input type="text" name="link_text" mosLabel="Link Text" class="inputbox" value="<?php echo @$this->field->link_text; ?>" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_LINK_IMAGE');?></td>
					<td width="20%">
						<select id='link_image' mosLabel='Image' mosReq=0 name='link_image' onchange="showimage('previewlink',this)">
							<option value='null' selected="selected">No Image</option>
							<?php 
							if (isset($this->fieldimages))
							{
							foreach($this->fieldimages as $image) {
							?>
							<option value='<?php echo $image; ?>' <?php if (@$this->field->link_image == $image) { echo "selected"; } ?>><?php echo $image; ?></option>
							<?php
							}
							}
							?>
						</select>
					
					</td>
					<td>
						<img src="<?php echo $this->baseurl."images/com_adsmanager/fields/".@$this->field->link_image; ?>" id='previewlink' name="previewlink" />
					</td>
				</tr>
			</table>
		</div>
		<div id="divColsRows"  class="pagetext">
			<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform custom">
				<tr colspan="3">
                    <th><?php echo JText::_('ADSMANAGER_FIELD_SPECIFIC_PARAMETERS');?></th>
                </tr>
                <tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_COLS');?></td>
					<td width="20%"><input type="text" name="cols" mosLabel="Cols" class="inputbox" value="<?php echo @$this->field->cols; ?>" /></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td width="20%"><?php echo JText::_('ADSMANAGER_FIELD_ROWS');?></td>
					<td width="20%"><input type="text" name="rows"  mosLabel="Rows" class="inputbox" value="<?php echo @$this->field->rows; ?>" /></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
		<div id="divValues" class="custom" style="text-align:left;">
			<?php $options = $this->options ?>
			<?php if (@$options->select_values_storage_type == "") $options->select_values_storage_type = "internal";?>
			<input type="radio" name="options_multiple_select_values_storage_type" value="internal" <?php if ($options->select_values_storage_type == "internal") echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_VALUES_STORAGE_TYPE_INTERNAL')?>&nbsp;
			<input type="radio" name="options_multiple_select_values_storage_type" value="db" <?php if ($options->select_values_storage_type == "db") echo "checked='checked'";?> /><?php echo JText::_('ADSMANAGER_VALUES_STORAGE_TYPE_EXTERNAL')?><br/><br/>
			<div class="storage_values" id="storage_values_internal">
				<?php echo JText::_('ADSMANAGER_FIELD_VALUES_EXPLANATION');?>
				<input type="button" id="addfvalue" class="button btn" value="<?php echo JText::_('ADSMANAGER_ADD_FIELD_VALUE')?>" /><br/>
				<ul id="values">
					<li class="ui-state-disabled"> 
						<span id='empty'>&nbsp;</span> 
						<input type="text" value="<?php echo JText::_('ADSMANAGER_FIELD_VALUE_NAME');?>"/> 
						<input type="text" value="<?php echo JText::_('ADSMANAGER_FIELD_VALUE_VALUE');?>"/> 
					</li>
				</ul>
			</div>
			<div class="storage_values" id="storage_values_db">
			<?php echo JText::_('ADSMANAGER_DB_STORAGE_DB_NAME')?> <input type="text" name="options_multiple_select_db_storage_db_name" value="<?php echo @$options->select_db_storage_db_name ?>" /><br/>
			<?php echo JText::_('ADSMANAGER_DB_STORAGE_COLUMN_NAME')?> <input type="text" name="options_multiple_select_db_storage_column_name" value="<?php echo @$options->select_db_storage_column_name ?>" /><br/>
			<?php echo JText::_('ADSMANAGER_DB_STORAGE_COLUMN_VALUE')?> <input type="text" name="options_multiple_select_db_storage_column_value" value="<?php echo @$options->select_db_storage_column_value ?>" /><br/>
			</div>
		</div>
		<style>
			#divValues ul { list-style-type: none; margin: 0; padding: 0; width: 83%; }
			#divValues #values li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; }
			#divValues #values li span.ui-icon-arrowthick-2-n-s { position: absolute; margin-left: -1.3em; }
			#divValues #values li span.ui-icon-cancel { float:right }
			#divValues #empty { position: absolute; margin-left: -1.3em; }
			#divValues  .ui-state-highlight { height:18px; } 
		</style>
		<script type="text/javascript">
			function addFValue(label,value) {
				data = '<li class="ui-state-default"> \
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span> \
					<input type="text" name="vNames[]" value="'+label+'"/> \
					<input type="text" name="vValues[]" value="'+value+'"/> \
					<span class="ui-icon ui-icon-cancel"></span> \
					</li>';
				jQ('#divValues  #values').append(data);
			}
			<?php 
			foreach($this->fieldvalues as &$f) {
				$f->fieldvalue = htmlspecialchars($f->fieldvalue);
			}
			?>
			values = <?php echo json_encode($this->fieldvalues);?>;
			for(i=0;i<values.length;i++) {
				addFValue(values[i].fieldtitle,values[i].fieldvalue);
			}
			if (values.length == 0) {
				for(i=0;i<4;i++) {
					addFValue("","");
				}
			}
			jQ(function() {
				jQ( "#divValues li.ui-state-disabled input").attr('disabled','disabled');
				jQ( "#divValues #values" ).sortable({placeholder: "ui-state-highlight",items: "li:not(.ui-state-disabled)"});
				jQ( "#divValues #addfvalue" ).click(function() {
					addFValue("","","");});
				jQ( "#divValues").on("click", ".ui-icon-cancel",function() {
					jQ(this).parent().remove();
				});

				jQ('input[name=options_multiple_select_values_storage_type]:radio').change(function() {
					jQ(".storage_values").hide();
					val = jQ('input[name=options_multiple_select_values_storage_type]:radio:checked').val();
					jQ("#storage_values_"+val).show();
				});
				jQ(".storage_values").hide();
				jQ("#storage_values_<?php echo $options->select_values_storage_type?>").show();
			});
		</script>
		<div id="divImagesValues" class="custom" style="text-align:left;">
			<?php echo JText::_('ADSMANAGER_FIELD_VALUES_EXPLANATION');?>
			<input type="button" id="addivalue" class="button btn" value="<?php echo JText::_('ADSMANAGER_ADD_FIELD_VALUE')?>" /><br/>
			<ul id="values">
				<li class="ui-state-disabled"> 
					<span id='empty'>&nbsp;</span> 
					<input type="text" value="<?php echo JText::_('ADSMANAGER_FIELD_VALUE_IMAGE');?>"/> 
					<input type="text" value="<?php echo JText::_('ADSMANAGER_FIELD_VALUE_VALUE');?>"/> 
				</li>
			</ul>
		</div>
		<style>
			#divImagesValues ul { list-style-type: none; margin: 0; padding: 0; width: 83%; }
			#divImagesValues #values li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 100px; }
			#divImagesValues #values li span.ui-icon-arrowthick-2-n-s { position: absolute; margin-left: -1.3em; }
			#divImagesValues #values li span.ui-icon-cancel { float:right }
			#divImagesValues #empty { position: absolute; margin-left: -1.3em; }
			#divImagesValues #preview { max-width:150px;max-height:100px; }
			#divImagesValues #values li img {vertical-align:top}
		</style>
		<script type="text/javascript">
			imgcount = 0;
			function addIValue(image,value) {
				imgcount++;
				data = '<li class="ui-state-default"> \
					    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span> \
					    <select previewid="preview_'+imgcount+'" id="img_'+imgcount+'" mosLabel="Image" mosReq="0" name="vSelectImages[]"> \
						  <option value="" selected="selected">No Image</option>';
						
					<?php 
					if (isset($this->fieldimages))
					{
					foreach($this->fieldimages as $image) {
					?>
					data += '<option value="<?php echo $image; ?>"><?php echo $image; ?></option>';
					<?php
					}
					}
					?>
				img = '<?php echo JURI::root()?>components/com_adsmanager/images/default_empty.gif';
                if (image != "")
					img = '<?php echo JURI::root()?>/images/com_adsmanager/fields/'+image;
				data += '</select> \
						<img src="'+img+'" style="max-height: 50px;" id="preview_'+imgcount+'"/> \
						<input type="text" name="vImagesValues[]" value="'+value+'"/> \
					    <span class="ui-icon ui-icon-cancel"></span> \
					    </li>';
				jQ('#divImagesValues  #values').append(data);
				jQ('#img_'+imgcount).change(function() {
					if(jQ(this).val() == '') {
                        srcImg = '<?php echo JURI::root()?>components/com_adsmanager/images/default_empty.gif';
                    } else {
                        srcImg = '<?php echo JURI::root()?>/images/com_adsmanager/fields/'+jQ(this).val();
                    }
                        
					jQ('#divImagesValues #'+jQ(this).attr('previewid')).attr('src',srcImg);
				});
				jQ('#img_'+imgcount).val(image);
			}
			values = <?php echo json_encode($this->fieldvalues); ?>;
			for(i=0;i<values.length;i++) {
				addIValue(values[i].fieldtitle,values[i].fieldvalue);
			}
			if (values.length == 0) {
				for(i=0;i<4;i++) {
					addIValue("","");
				}
			}
			jQ(function() {
				jQ( "#divImagesValues li.ui-state-disabled input").attr('disabled','disabled');
				jQ( "#divImagesValues #values" ).sortable({placeholder: "ui-state-highlight",items: "li:not(.ui-state-disabled)"});
				jQ( "#divImagesValues #addivalue" ).click(function() {
					addIValue("","","");});
				jQ( "#divImagesValues").on("click", ".ui-icon-cancel",function() {
					jQ(this).parent().remove();
				});
			});
		</script>
		<?php
		if(isset($this->plugins))
			foreach($this->plugins as $key => $plug) {
				echo $plug->getEditFieldOptions(@$this->field->fieldid);
			} 
		?>
	  </td>
	  <td width="40%">
		  <table class="adminform custom">
				<th><?php echo JText::_('ADSMANAGER_FORM_CATEGORY'); ?></th>
				<tr><td>	
					<select name="field_catsid[]" mosReq="1" mosLabel="<?php echo htmlspecialchars(JText::_('ADSMANAGER_FORM_CATEGORY')) ?>" multiple='multiple' id="field_catsid[]" size="<?php echo $this->nbcats+2;?>">
					<?php
					if (strpos(@$this->field->catsid, ",-1,") === false)
						echo "<option value='-1'>".JText::_('ADSMANAGER_MENU_ALL_ADS')."</option>";
					else
						echo "<option value='-1' selected>".JText::_('ADSMANAGER_MENU_ALL_ADS')."</option>";
					$this->selectCategories(0,"",$this->cats,-1,-1,1,@$this->field->catsid);
					?>
					</select>
				</td></tr>
		  </table>
  		</td></tr>
  </table>
  <input type="hidden" name="fieldid" value="<?php echo @$this->field->fieldid; ?>" />
  <input type="hidden" name="ordering" value="<?php echo @$this->field->ordering; ?>" />
  <input type="hidden" name="option" value="com_adsmanager" />
  <input type="hidden" name="c" value="fields" />
  <input type="hidden" name="task" value="" />
</form>
  
<?php 
if(@$this->field->fieldid > 0) {
	print "<script type=\"text/javascript\"> document.adminForm.name.readOnly=true; </script>";	
	/*print "<script type=\"text/javascript\"> document.adminForm.type.disabled=true; </script>";*/
}

?>
<script type="text/javascript"> 
function updateSearchType(val) {
	if (val == 'select'){
		jQ('#divValues').show();
	} else {
		jQ('#divValues').hide();
	}
}

jQ(document).ready(function() {
	disableAll();
	jQ('#price_search_type').change(function() {updateSearchType(jQ(this).val());});
	jQ('#number_search_type').change(function() {updateSearchType(jQ(this).val());});
	selType('<?php echo @$this->field->type?>');
});
</script>	
