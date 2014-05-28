<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="AjaxUploadForm" id="AjaxUploadForm" enctype="multipart/form-data" class="form-validate form-horizontal" onsubmit="">
  <div class="control-group">
    <div class="control-label">
    </div>
    <div class="controls">
      <div id="triggerClearUploadList" class="btn btn-info pull-right hidden">
        <i class="icon-list icon-black"></i> <?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_CLEAR_UPLOAD_LIST'); ?>
      </div>
      <div id="fine-uploader"></div>
      <script>
        jQuery(document).ready(function() {
          var uploader = new qq.FineUploader({
            element: jQuery('#fine-uploader')[0],
            request: {
              endpoint: 'index.php?option=<?php echo _JOOM_OPTION; ?>&task=ajaxupload.upload&format=raw',
              paramsInBody: true
            },
            chunking: {
              enabled: true,
              partSize: <?php echo $this->chunkSize; ?>
            },
            autoUpload: false,
            display: {
              fileSizeOnSubmit: true
            },
            text: {
              uploadButton: '<i class="icon-plus icon-plus"></i> <?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_SELECT_IMAGES', true); ?>',
              cancelButton: '<?php echo JText::_('COM_JOOMGALLERY_COMMON_CANCEL', true); ?>',
              retryButton: '<?php echo JText::_('COM_JOOMGALLERY_COMMON_RETRY', true); ?>',
              failUpload: '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_UPLOAD_FAILED', true); ?>',
              dragZone: '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_DRAGZONETEXT', true); ?>',
              dropProcessing:'<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_DROPPROCESSINGTEXT', true); ?>',
              formatProgress: '{percent}% ' + '<?php echo JText::_('COM_JOOMGALLERY_COMMON_OF', true); ?>' +'  {total_size}',
              waitingForResponse: '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_PROCESSING', true); ?>'
            },
            failedUploadTextDisplay: {
              mode: 'custom'
            },
            dragAndDrop: {
              extraDropzones: [],
              hideDropzones: true,
              disableDefaultDropzone: false
            },
            fileTemplate: '<li class="alert">' +
                            '<div class="qq-progress-bar"></div>' +
                            '<span class="qq-upload-spinner"></span>' +
                            '<span class="qq-upload-finished"></span>' +
                            '<span class="qq-upload-file"></span>' +
                            '<span class="qq-upload-size badge"></span>' +
                            '<a class="qq-upload-cancel btn btn-mini" href="#">{cancelButtonText}</a>' +
                            '<a class="qq-upload-retry" href="#">{retryButtonText}</a>' +
                            '<span class="qq-upload-status-text">{statusText}</span>' +
                            '<span class="qq-upload-debug-text"></span>' +
                          '</li>',
            template: '<div class="qq-uploader span12">' +
                        '<div class="qq-upload-drop-area span12"><span>{dragZoneText}</span></div>' +
                        '<div class="qq-upload-button btn btn-large btn-success">{uploadButtonText}</div>' +
                        '<div class="small">' + '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_DRAGNDROPHINT'); ?>' + '</div>'+
                        '<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
                        '<ul class="qq-upload-list"></ul>' +
                      '</div>',
            classes: {
                success: 'alert-success',
                fail: 'alert-error',
                debugText: 'qq-upload-debug-text'
            },
            validation: {
              allowedExtensions: ['jpg', 'jpeg', 'jpe', 'gif', 'png'],
              acceptFiles: 'image/*',
              sizeLimit: <?php echo $this->fileSizeLimit; ?>
            },
            messages: {
              typeError: '{file}: ' + '<?php echo JText::_('COM_JOOMGALLERY_COMMON_ALERT_WRONG_EXTENSION', true); ?>',
              sizeError: '{file}: ' + '<?php echo JText::sprintf('COM_JOOMGALLERY_UPLOAD_OUTPUT_MAX_ALLOWED_FILESIZE', $this->fileSizeLimit, array(true)) ?>',
              fileNameError: '{file}: ' + '<?php echo JText::_('COM_JOOMGALLERY_COMMON_ALERT_WRONG_FILENAME', true); ?>',
              fileNameDouble: '{file}: ' + '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_ALERT_FILENAME_DOUBLE', true); ?>',
              minSizeError: '{file}: ' + '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_ALERT_FILE_TOO_SMALL', true); ?>' + ' {minSizeLimit}.',
              emptyError: '{file} : '  + '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_ALERT_FILE_EMPTY', true); ?>',
              noFilesError: '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_ALERT_NO_FILES', true); ?>',
              onLeave: '<?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_ALERT_ON_LEAVE', true); ?>'
            },
            debug: true,
            maxConnections: 1,
            disableCancelForFormUploads: true,
            callbacks: {
              onComplete: function(id, fileName, responseJSON) {
                if(responseJSON.debug_output) {
                  var item = this.getItemByFileId(id);
                  var element = this._find(item, 'debugText');
                  element.innerHTML = responseJSON.debug_output;
                }
                if(this.requestParams.hasOwnProperty("filecounter")) {
                  this.requestParams.filecounter =  this.requestParams.filecounter + 1;
                  this.setParams(this.requestParams);
                }
                if(responseJSON.success) {
                	uploader.fileCount--;
                	var ajax_redirect = '<?php echo $this->ajax_redirect; ?>';
                	if(uploader.fileCount == 0 && ajax_redirect != '') {
                    // redirect only if all file upload were successfull
                    location.href = ajax_redirect;
                	}
                }
              },
              onValidate: function(fileData) {
                if(!jg_filenamewithjs) {
                  var searchwrongchars = /[^a-zA-Z0-9_-]/;
                  if(searchwrongchars.test(fileData.name)) {
                    this._itemError('fileNameError', fileData.name);
                    return false;
                  }
                }
                for (var i = 0; i < this._storedIds.length; i++) {
                  var fileName = this._handler.getName(this._storedIds[i]);
                  if(fileName && fileName == fileData.name) {
                    this._itemError('fileNameDouble', fileData.name);
                    return false;
                  }
                }
              }
            }
          });
          jQuery('#triggerClearUploadList').click(function() {
            uploader.reset();
            jQuery('#triggerClearUploadList').addClass('hidden');
          });
          jQuery('#triggerUpload').click(function() {
            if(uploader._storedIds.length == 0) {
              alert('<?php echo JText::_('COM_JOOMGALLERY_COMMON_ALERT_YOU_MUST_SELECT_ONE_IMAGE', true); ?>');
              return false;
            }
            var form = document.id('AjaxUploadForm');
            if(!document.formvalidator.isValid(form)) {
              var msg = new Array();
              msg.push('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>');
              if(form.imgtitle.hasClass('invalid')) {
                  msg.push('<?php echo JText::_("COM_JOOMGALLERY_COMMON_ALERT_IMAGE_MUST_HAVE_TITLE", true);?>');
              }
              if(form.catid.hasClass('invalid')) {
                msg.push('<?php echo JText::_("COM_JOOMGALLERY_COMMON_ALERT_YOU_MUST_SELECT_CATEGORY", true);?>');
              }
              alert(msg.join('\n'));
              return false;
            }

            // Prepare request parameters
            uploader.requestParams = new Object();
            uploader.requestParams.catid = jQuery('#ajax_catid').val();
            if(jQuery('#ajax_imgtitle').length > 0) {
              uploader.requestParams.imgtitle = jQuery('#ajax_imgtitle').val();
            }
            if(jQuery('#ajax_filecounter').length > 0) {
              var filecounter = parseInt(jQuery('#ajax_filecounter').val());
              if(!isNaN(filecounter)) {
                uploader.requestParams.filecounter = filecounter;
              }
            }
            uploader.requestParams.imgtext = jQuery('#ajax_imgtext').val();
            uploader.requestParams.published = jQuery('#ajax_published').val();
            if(jQuery('#ajax_original_delete').length > 0) {
              uploader.requestParams.original_delete = jQuery('#ajax_original_delete').prop('checked') ? 1 : 0;
            }
            uploader.requestParams.create_special_gif = jQuery('#ajax_create_special_gif').prop('checked') ? 1 : 0;
            uploader.requestParams.debug = jQuery('#ajax_debug').prop('checked') ? 1 : 0;
            uploader.setParams(uploader.requestParams);
            uploader.fileCount = uploader._storedIds.length;
            uploader.uploadStoredFiles();
            jQuery('#triggerClearUploadList').removeClass('hidden');
          });
        });
      </script>
    </div>
  </div>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('catid'); ?>
    </div>
    <div class="controls">
      <?php echo $this->ajax_form->getInput('catid'); ?>
    </div>
  </div>
      <?php if(!$this->_config->get('jg_useruseorigfilename')): ?>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('imgtitle'); ?>
    </div>
    <div class="controls">
      <?php echo $this->ajax_form->getInput('imgtitle'); ?>
    </div>
  </div>
      <?php endif;
            if(!$this->_config->get('jg_useruseorigfilename') && $this->_config->get('jg_useruploadnumber')): ?>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('filecounter'); ?>
    </div>
    <div class="controls">
      <?php echo $this->ajax_form->getInput('filecounter'); ?>
    </div>
  </div>
      <?php endif; ?>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('imgtext'); ?>
    </div>
    <div class="controls">
      <?php echo $this->ajax_form->getInput('imgtext'); ?>
    </div>
  </div>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('imgauthor'); ?>
    </div>
    <div class="controls">
      <div class="jg-uploader"><?php echo JHtml::_('joomgallery.displayname', $this->_user->get('id'), 'upload'); ?></div>
    </div>
  </div>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('published'); ?>
    </div>
    <div class="controls">
      <?php echo $this->ajax_form->getInput('published'); ?>
    </div>
  </div>
    <?php /*
      <?php echo $this->ajax_form->getLabel('access'); ?>
      <?php echo $this->ajax_form->getInput('access'); ?>
          */ ?>
      <?php if($this->_config->get('jg_delete_original_user') == 2): ?>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('original_delete'); ?>
    </div>
    <div class="controls">
      <?php echo $this->ajax_form->getInput('original_delete'); ?>
    </div>
  </div>
      <?php endif;
            if($this->_config->get('jg_special_gif_upload') == 1): ?>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('create_special_gif'); ?>
    </div>
    <div class="controls">
      <?php echo $this->ajax_form->getInput('create_special_gif'); ?>
    </div>
  </div>
      <?php endif;
            if($this->_config->get('jg_redirect_after_upload')): ?>
  <div class="control-group">
    <div class="control-label">
      <?php echo $this->ajax_form->getLabel('debug'); ?>
    </div>
    <div class="controls">
      <?php echo $this->ajax_form->getInput('debug'); ?>
    </div>
  </div>
      <?php endif; ?>
  <div class="control-group">
    <div class="control-label">
      <label for="button"></label>
    </div>
    <div class="controls">
      <div id="triggerUpload" class="btn btn-primary">
        <i class="icon-upload icon-white"></i> <?php echo JText::_('COM_JOOMGALLERY_UPLOAD_UPLOAD'); ?>
      </div>
      <button type="button" class="btn" onclick="javascript:location.href='<?php echo JRoute::_('index.php?view=userpanel', false); ?>';return false;"><i class="icon-cancel"></i> <?php echo JText::_('COM_JOOMGALLERY_COMMON_CANCEL'); ?></button>
    </div>
  </div>
</form>