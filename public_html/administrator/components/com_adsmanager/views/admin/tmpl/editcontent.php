<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2013 JoomPROD.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>

<!--[if IE]>
	<style>
		.plupload_start {
			display : none;
		}
		</style>
<![endif]-->

<script type="text/javascript">
function CaracMax(text, max)
{
	if (text.value.length >= max)
	{
		text.value = text.value.substr(0, max - 1) ;
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
	   var form = document.adminForm;
	   var iserror = 0;
	   var errorMSG = '';
		
	   <?php if ($this->nbcats > 1) { ?>
			var srcList = eval( 'form.selected_cats' );
			var srcLen = srcList.length;
	  
		   if (srcLen == 0)
		   {
				errorMSG += <?php echo json_encode(JText::_('ADSMANAGER_FORM_CATEGORY')); ?>+" : "+<?php echo json_encode(JText::_('ADSMANAGER_REGWARN_ERROR')); ?>+'\n';
				srcList.style.background = "red";
				iserror=1;
			}
			else
			{
				for (var i=0; i < srcLen; i++) {
					srcList.options[i].selected = true;
				}
			}
		<?php } ?>
		
		if(iserror==1) {
			alert(errorMSG);
		} else {
		
			<?php
			if (function_exists("loadEditFormCheck")) {
				loadEditFormCheck("admin");
			}
		   ?>

		   <?php 
			$nbimages = $this->conf->nb_images;
			if (function_exists("getMaxPaidSystemImages")){
				$nbimages += getMaxPaidSystemImages();
			}?>
			<?php if ($nbimages > 0) {?>
			 var uploader = jQ('#imagesupload').pluploadQueue();
				
	        // Files in queue upload them first
	        if (uploader.files.length !== (uploader.total.uploaded + uploader.total.failed)) {
	            // When all files are uploaded submit form
	            uploader.bind('StateChanged', function() {
	                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
	                    //jQ('#adminForm')[0].submit();
	                	submitform("save");	
	                }
	            });
	                
	            uploader.start();
	            return false;
	        }  
	        <?php } ?>
	        
		   <?php if ($this->nbcats > 1) { ?>
			srcList.name = "selected_cats[]"; 
		   <?php } ?>
		   submitform(pressbutton);
		}
   }

function updateFields() {
	var form = document.adminForm;
	var singlecat = 0;
	var length = 0;
	
	if ( typeof(document.adminForm.category ) != "undefined" ) {
		singlecat = 1;
		length = 1;
	}
	else if ( typeof(document.adminForm.selected_cats ) != "undefined" ) 
	{
		length = form.selected_cats.length;
	} else {
		length = 1;
	}
	
	<?php
	foreach($this->fields as $field)
	{ 
		if (strpos($field->catsid, ",-1,") === false)
		{
			$name = $field->name;
			if (($field->type == "multicheckbox")||($field->type == "multiselect"))
				$name .= "[]";
		?>
		var input = document.getElementById('<?php echo $name;?>');
		var trzone = document.getElementById('tr_<?php echo $field->name;?>');
		if (((singlecat == 0)&&(length == 0))||
		    ((singlecat == 1)&&(document.adminForm.category.value == 0)))
		{
			if (input != null)
				input.style.visibility = 'hidden';
			trzone.style.visibility = 'hidden';
			trzone.style.display = 'none';
		}
		else
		{
			for (var i=0; i < length; i++) {
				
				
				var field_<?php echo $field->name;?> = '<?php echo $field->catsid;?>';
				var temp;
				if (singlecat == 0)
					temp = form.selected_cats.options[i].value;
				else
					temp = document.adminForm.category.value;
					
				var test = field_<?php echo $field->name;?>.indexOf( ","+temp+",", 0 );
				if (test != -1)
				{
					if (input != null)
						input.style.visibility = 'visible';
					trzone.style.visibility = 'visible';
					trzone.style.display = '';
					break;
				}
				else
				{
					if (input != null)
						input.style.visibility = 'hidden';
					trzone.style.visibility = 'hidden';
					trzone.style.display = 'none';
				}
			}
		}
	<?php
		}
	} 
	?>
}
</script>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
<tr>
<td><?php echo JText::_('ADSMANAGER_FORM_CATEGORY');?></td>
<td>
<?php
if ($this->nbcats == 1)
{
	if ($this->catid != 0)
		$catid = $this->catid;
	else if (isset($this->content->cats[0]))
		$catid = $this->content->cats[0]->catid;
	else 
		$catid = 0;
	switch($this->conf->single_category_selection_type) {
		default:
		case 'normal':
			JHTMLAdsmanagerCategory::displayNormalCategories("categoryselect",$this->cats,$catid,array("root_allowed"=>$this->conf->root_allowed,"display_price"=>true));break;
		case 'color':
			JHTMLAdsmanagerCategory::displayColorCategories("categoryselect",$this->cats,$catid,array("root_allowed"=>$this->conf->root_allowed));break;
		case 'combobox':
			JHTMLAdsmanagerCategory::displayComboboxCategories("categoryselect",$this->cats,$catid,array("root_allowed"=>$this->conf->root_allowed));break;
			break;
		case 'cascade':
			$separator = "<br/>";
			JHTMLAdsmanagerCategory::displaySplitCategories("categoryselect",$this->cats,$catid,array("root_allowed"=>$this->conf->root_allowed));break;
	}
	?>
	</td></tr></table>
	<?php if (@$this->content->id) { 
			$write_url = 'index.php?option=com_adsmanager&c=contents&task=edit&id='.$this->content->id;
		} else {
			$write_url = 'index.php?option=com_adsmanager&c=contents&task=edit';
		}?>
	<script type="text/javascript">
		jQ(document).ready(function() {
			jQ('#categoryselect').change(function() {
				if (jQ(this).val() != "") {
					location.href = "<?php echo $write_url?>&catid="+jQ(this).val();
				}
			});
		});
		</script>
	<?php 
	if ($catid == 0)
		return;
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
	<?php
	echo "<input type='hidden' name='category' value='$catid' />";
	?><table border='0'>
	<?php 
	
}
else
{
	?>
	</td></tr></table>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
	<table border='0'><tr><td colspan="2">
	<?php
	if (isset($this->content->catsid)) {
		$catids = $this->content->catsid;
	} else {
		$catids = array();
	}
	JHTMLAdsmanagerCategory::displayMultipleCategories("cats",$this->cats,$catids,array("root_allowed"=>$this->conf->root_allowed),$this->nbcats);
	
}
?>
<?php if (isset($this->content->userid)) { $userid = $this->content->userid; } else { $userid = $this->userid; } ?>

<tr>
<td><?php echo JText::_('ADSMANAGER_TH_USER'); ?></td>
<td>
<select name="userid" id="userid">
<option value=""></option>
<?php foreach($this->users as $user) { ?>
<option value="<?php echo $user->id;?>" <?php if ($user->id == $userid) { echo "selected"; } ?>><?php echo $user->username; ?></option>
<?php } ?>
</select>
</td>
<td>&nbsp;</td>
</tr>


<tr>
<td><?php echo JText::_('ADSMANAGER_TH_DATE'); ?></td>
<td>
<?php echo JHTML::_('behavior.calendar'); 
if (!isset($this->content->id)) 
	$created_date = date("Y-m-d");
else
	$created_date = $this->content->date_created;
$time = date('H:i:s',strtotime($created_date)); 
echo JHTML::_('calendar', $created_date, "date_created", "date_created", "%Y-%m-%d $time", null); ?>
</td>
<td>&nbsp;</td>
</tr>

<?php 
if (!isset($this->content->id)) 
	$expiration_date =  date("Y-m-d",time() + $this->conf->ad_duration * 3600 * 24);
else
	$expiration_date = $this->content->expiration_date;
?>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_EXPIRATION_DATE'); ?></td>
<td>
<?php echo JHTML::_('calendar', $expiration_date, "expiration_date", "expiration_date", "%Y-%m-%d", null); ?>
</td>
<td>&nbsp;</td>
</tr>


<?php
foreach($this->fields as $field)
{
	echo "<tr id=\"tr_{$field->name}\"><td>".$this->field->showFieldLabel($field,$this->content,$this->default)."</td>";
	echo "<td>".$this->field->showFieldForm($field,$this->content,$this->default)."</td></tr>";
}
?>

<!-- fields -->
<!-- image -->
	<?php 
	$nbimages = $this->conf->nb_images;
	if (function_exists("getMaxPaidSystemImages")){
		$nbimages += getMaxPaidSystemImages();
	}?>
	<?php if ($nbimages > 0) {?>
	<tr><td><?php echo JText::_('Pictures')?></td><td id="uploader_td">

	<div id="imagesupload">&nbsp;</div>
	<div class="legend_photos"><span class="max_photos"><?php echo JText::_('ADSMANAGER_MAX_NUMBER_OF_PICTURES')?> : </span><span id="maximum"><?php echo $this->conf->nb_images?></span><span class="max_photos"> / </span><span id="totalcount"><?php echo $this->conf->nb_images?></span></div>
	<style>
	<?php
	$width = $this->conf->max_width_t; 
	$height = $this->conf->max_height_t + 20; 
	?>
	#currentimages li { width: <?php echo $width ?>px; height: <?php echo $height ?>px; }
	</style>
	<ul id="currentimages">
	<?php 
	$currentnbimages = 0;
	if (@$this->content->pending == 1) {
		$i=1;
		$ad_id = $this->content->id;
		foreach($this->content->images as $img) {
			$dir = JURI::root()."images/com_adsmanager/ads/waiting/";
			$thumb = $dir.$img->thumbnail;
			$index = $img->index;
			echo "<li class='ui-state-default' id='li_img_$index'><img src='".$thumb."?time=".time()."' align='top' border='0' alt='image".$ad_id."' />";
			echo "<br/><input type='checkbox' name='cb_image$i' onClick='removeImage($index,$index)' value='delete' />".JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE').'</li>';
			$currentnbimages++;
			$i++;
		}
	} else if ($this->isUpdateMode) {
		$i=0;
		foreach($this->content->images as $img) {
			$i++;
			$index = $img->index;
			$currentnbimages++;
			echo "<li class='ui-state-default' id='li_img_$index' ><img src='".JURI::root()."images/com_adsmanager/ads/".$img->thumbnail."?time=".time()."' align='top' border='0' alt='image".$this->content->id."' />";
			echo "<br/><input type='checkbox' name='cb_image$i' onClick='removeImage($index,$index)' value='delete' />".JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE').'</li>';
		}
	}
	?>
	</ul>
	<input type="hidden" name="deleted_images" id="deleted_images" value=""/>
	<input type="hidden" name="orderimages" id="orderimages" value="" />
	<script type="text/javascript">
	var current_uploaded_files_count = <?php echo $currentnbimages?>;
	var nb_files_in_queue = 0;
	var max_total_file_count =  <?php echo ($this->conf->nb_images)?>;

	function removeTmpImage(fileid){
		if (confirm("<?php echo htmlspecialchars(JText::_('ADSMANAGER_CONFIRM_DELETE_IMAGE'),ENT_QUOTES)?>")) {
			jQ('#li_img_'+fileid).remove();
			var uploader = jQ('#imagesupload').pluploadQueue();
			jQ.each(uploader.files, function(i, file) {
				if (file.id == fileid)
					uploader.removeFile(file);
			});
			var inputCount = 0, inputHTML= "";
			jQ.each(uploader.files, function(i, file) {
				if (file.status == plupload.DONE) {
					if (file.target_name) {
						inputHTML += '<input type="hidden" name="uploader_' + inputCount + '_tmpname" value="' + plupload.xmlEncode(file.target_name) + '" />';
					}

					inputHTML += '<input type="hidden" name="uploader_' + inputCount + '_id" value="' + plupload.xmlEncode(file.id) + '" />';
					inputHTML += '<input type="hidden" name="uploader_' + inputCount + '_name" value="' + plupload.xmlEncode(file.name) + '" />';
					inputHTML += '<input type="hidden" name="uploader_' + inputCount + '_status" value="' + (file.status == plupload.DONE ? 'done' : 'failed') + '" />';

					inputCount++;

					jQ('#uploader_count').val(inputCount);
				} 
			});
			jQ('#pluploadfield').html(inputHTML);
			nb_files_in_queue = uploader.files.length;
			setCurrentFileCount();
		} else {
			jQ('#li_img_'+fileid+' input').attr('checked', false);
		}
	}
	
	function removeImage(id,index) {
		if (confirm("<?php echo htmlspecialchars(JText::_('ADSMANAGER_CONFIRM_DELETE_IMAGE'),ENT_QUOTES)?>")) {
			deleted_images = jQ('#deleted_images').val();
			if (deleted_images == "")
				deleted_images = index;
			else
				deleted_images = deleted_images+","+index;
			jQ('#deleted_images').val(deleted_images);
		
			jQ('#li_img_'+id).remove();
			if (typeof updatePaidCurrentFileCount != "undefined") {
		    	updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
		    							   current_uploaded_files_count+nb_files_in_queue-1);
		} else {	
			jQ('#cb_image'+id).attr('checked', false);
	    	}
	    }
		current_uploaded_files_count -= 1;
		setCurrentFileCount();
	}
	
	function setCurrentFileCount() {
		jQ('#maximum').html(current_uploaded_files_count+nb_files_in_queue);
		jQ( "#currentimages" ).sortable(
			{
			 placeholder: "ui-state-highlight",
			 stop: function(event, ui) { 
				 jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
			 },
			 create:function(event,ui) {
				 jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
			}
			}
			 );
		
		jQ( "#currentimages" ).disableSelection();
		jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
	}
	function setTotalFileCount(number) {
		jQ('#totalcount').html(number);
	}
	setCurrentFileCount();
	// Convert divs to queue widgets when the DOM is ready
	jQ(function() {
		jQ("#imagesupload").pluploadQueue({
			// General settings
			runtimes : 'html5,flash,html4',
			url : '<?php echo '../index.php?option=com_adsmanager&task=upload&tmpl=component'; ?>',
			max_file_size : '10mb',
			chunk_size : '1mb',
			unique_names : true,
	
			// Resize images on clientside if we can
			resize : {width : <?php echo $this->conf->max_width?>, height : <?php echo $this->conf->max_height?>, quality : 100},
	
			// Specify what files to browse for
			filters : [
				{title : "Image files", extensions : "jpeg,jpg,gif,png"}
			],
	
			// Flash settings
			flash_swf_url : '<?php echo $this->baseurl?>components/com_adsmanager/js/plupload/plupload.flash.swf',

			init : {
	            FilesAdded: function(up, files) {
					maxnewimages = max_total_file_count - current_uploaded_files_count;
					// Check if the size of the queue is bigger than max_file_count
				    if(up.files.length > maxnewimages)
				    {
				        // Removing the extra files
				        while(up.files.length > maxnewimages)
				        {
				            if(up.files.length > maxnewimages)
				            	up.removeFile(up.files[maxnewimages]);
				        }
				        alert('<?php echo JText::_(sprintf("Max %s Files",$this->conf->nb_images))?>');
				    }

				    if (typeof updatePaidCurrentFileCount != "undefined") {
				    	updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
				    							   current_uploaded_files_count+up.files.length);
				    }
				    nb_files_in_queue = up.files.length;
			        setCurrentFileCount();
				},
				FilesRemoved: function(up, files) {
					if (typeof updatePaidCurrentFileCount != "undefined") {
						updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
    							   				   current_uploaded_files_count+up.files.length);
				    }
					nb_files_in_queue = up.files.length;
			        setCurrentFileCount();
				},
				FileUploaded: function(up, file,info) {
					maxheight = <?php echo $this->conf->max_height_t ?>;
					name = '<?php echo JURI::root() ?>images/com_adsmanager/ads/uploaded/'+file.target_name;
					html = "<li class='ui-state-default' id='li_img_"+file.id+"'><img height='"+maxheight+"' src='"+name+"' align='top' border='0' alt='' />";
					html += "<br/><input type='checkbox' onClick='removeTmpImage(\""+file.id+"\")' value='' /><?php echo JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE')?></li>";
					jQ('#currentimages').append(html);
					setCurrentFileCount();
				}
			}
		});
	});
	</script>
	</td></tr>
	<?php } ?>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_PUBLISH'); ?></td>
<td>
<select name="published" id="published">
<option value="1" <?php if (@$this->content->published == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_PUBLISH'); ?></option>
<option value="0" <?php if (@$this->content->published == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO_PUBLISH'); ?></option>
</select>
</td>
<td>&nbsp;

</td>
</tr>


<?php if (($this->conf->metadata_mode != 'nometadata')&&
		  ($this->conf->metadata_mode != 'automatic')) { ?>
<tr><td colspan='2'><strong><?php echo JText::_('ADSMANAGER_METADATA')?></strong></td></tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_METADATA_DESCRIPTION'); ?></td>
<td>
<textarea cols="50" rows="10" name="metadata_description"><?php echo htmlspecialchars(@$this->content->metadata_description)?></textarea>			
</td>
</tr>

<tr>
<td><?php echo JText::_('ADSMANAGER_METADATA_KEYWORDS'); ?></td>
<td>
<textarea cols="50" rows="10" name="metadata_keywords"><?php echo htmlspecialchars(@$this->content->metadata_keywords)?></textarea>			
</td>
</tr>

<?php } ?>

<?php 
if (function_exists("editAdminPaidAd")){
	editAdminPaidAd($this->content,$this->isUpdateMode,$this->conf);
}?>
<?php
    if(isset($this->conf->publication_date) && $this->conf->publication_date == 1) {
?>
<tr>
<td><?php echo JText::_('ADSMANAGER_PUBLICATION_DATE'); ?></td>
<td>
    <?php 
        if (isset($this->content->publication_date) && $this->content->publication_date != '0000-00-00 00:00:00'){
            $publication_date = $this->content->publication_date;
        }else{
            $publication_date = "";
        }
    
        $options = array();
        $options['size'] = 25;
        $options['maxlength'] = 19;
        $options['class'] = 'adsmanager_required';
        $options['mosReq'] = '1';
        $options['mosLabel'] = htmlspecialchars(JText::_('ADSMANAGER_PUBLICATION_DATE'),ENT_QUOTES);
        $return = JHTML::_('behavior.calendar');
        $return .= JHTML::_('calendar', $publication_date, "publication_date", "publication_date", '%Y-%m-%d', $options);
//      $return .= "<script type='text/javascript'>jQ(document).ready(function() {jQ('#publication_date').val(".json_encode($publication_date).");});</script>";

        echo $return;
    ?>
</td>
</tr>
<?php } ?>
</table>
<input type="hidden" name="id" value="<?php echo @$this->content->id; ?>" />
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="c" value="contents" />
<input type="hidden" name="task" value="" />
</form>
<script type="text/javascript">
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
				jQ('#adminForm #'+child).val = '';
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
				jQ('#adminForm #'+child).val = '';
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
			jQ('#adminForm #'+child).val = '';
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
	<?php foreach($this->fields as $field) { 
		if (@$field->options->is_conditional_field == 1) { ?>
	dependency('<?php echo $field->name?>',
			   '<?php echo $field->options->conditional_parent_name?>',
			   '<?php echo $field->options->conditional_parent_value?>');
		<?php } 
	}?>
	updateFields();
});
</script>