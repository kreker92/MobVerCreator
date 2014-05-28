<?php defined('_JEXEC') or die('Restricted access');
if($this->_mainframe->isSite())
{
  $url = JRoute::_('index.php?option='._JOOM_OPTION.'&task=ajaxupload.upload', false);
}
else
{
  $url = JRoute::_('index.php?option='._JOOM_OPTION.'&controller=ajaxupload&task=upload', false);
} ?>
  <form action="index.php" method="post" class="form-validate form-horizontal" id="SingleUploadForm" name="SingleUploadForm">
    <div class="control-group">
      <?php echo $this->upload_form->getLabel('catid'); ?>
      <div class="controls">
        <?php echo $this->upload_categories ? $this->upload_categories : $this->upload_form->getInput('catid'); ?>
      </div>
    </div>
    <?php if(!$this->_config->get('jg_useorigfilename')): ?>
    <div class="control-group">
      <?php echo $this->upload_form->getLabel('imgtitle'); ?>
      <div class="controls">
        <?php echo $this->upload_form->getInput('imgtitle'); ?>
      </div>
    </div>
    <?php endif; ?>
    <div class="control-group">
      <?php echo $this->upload_form->getLabel('published'); ?>
      <div class="controls">
        <?php echo $this->upload_form->getInput('published'); ?>
      </div>
    </div>
    <?php if($this->_config->get('jg_delete_original') == 2): ?>
    <div class="control-group">
      <?php echo $this->upload_form->getLabel('original_delete'); ?>
      <div class="controls">
        <?php echo $this->upload_form->getInput('original_delete'); ?>
      </div>
    </div>
    <?php endif; ?>
    <div class="control-group">
      <?php echo $this->upload_form->getLabel('create_special_gif'); ?>
      <div class="controls">
        <?php echo $this->upload_form->getInput('create_special_gif'); ?>
      </div>
    </div>
  </form>
  <div>
    <div id="fine-uploader"></div>
    <script>
      jQuery(document).ready(function() {
        var uploader = new qq.FineUploader({
          element: jQuery('#fine-uploader')[0],
          request: {
            endpoint: '<?php echo $url.'&format=raw'; ?>',
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
                          '<span class="qq-upload-thumb"></span>' +
                          '<span class="qq-upload-file"></span>' +
                          '<span class="qq-upload-size badge"></span>' +
                          '<a class="qq-upload-cancel btn btn-mini" href="#">{cancelButtonText}</a>' +
                          '<a class="qq-upload-retry" href="#">{retryButtonText}</a>' +
                          '<span class="qq-upload-status-text">{statusText}</span>' +
                          '<span class="qq-upload-debug-text"></span>' +
                          '<div class="qq-upload-options hide form-horizontal"></div>' +
                          '<div class="qq-upload-note hide small center"><?php echo JText::_('COM_JOOMGALLERY_MINI_AJAX_UPLOAD_NOTE', true); ?></div>' +
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
              debugText: 'qq-upload-debug-text',
              thumb: 'qq-upload-thumb',
              options: 'qq-upload-options',
              note: 'qq-upload-note'
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
            onComplete: function(id, fileName, r) {
              var item = this.getItemByFileId(id);

<?php if(!$this->upload_catid): ?>
              // Display options if the upload was successful and if the image is published
              if(r.success && !document.id('published0').checked)
              {
                displayInsertOptions(this, item, fileName, r);
              }
<?php else: ?>
              // Reload the page if published images were uploaded (so the user can see them instantly on the page)
              if(r.success && !document.id('published0').checked)
              {
                window.parent.SqueezeBox.addEvent('onClose', function(){window.parent.location.reload();});
              }
<?php endif; ?>

              if(r.debug_output) {
                var element = this._find(item, 'debugText');
                element.innerHTML = r.debug_output;
              }
              if(this.requestParams.hasOwnProperty("filecounter")) {
                this.requestParams.filecounter =  this.requestParams.filecounter + 1;
                this.setParams(this.requestParams);
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
          Joomla.removeMessages();

          if(uploader._storedIds.length == 0) {
            alert('<?php echo JText::_('COM_JOOMGALLERY_COMMON_ALERT_YOU_MUST_SELECT_ONE_IMAGE', true); ?>');
            return false;
          }
          var form = document.id('SingleUploadForm');
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

          // For new upload procedure scrolling is allowed for the first image
          jg_scrolled = false;

          // Prepare request parameters
          uploader.requestParams = new Object();
          uploader.requestParams.catid = jQuery('#upload_categories').val();
          if(jQuery('#imgtitle').length > 0) {
            uploader.requestParams.imgtitle = jQuery('#imgtitle').val();
          }
          if(jQuery('#filecounter').length > 0) {
            var filecounter = parseInt(jQuery('#filecounter').val());
            if(!isNaN(filecounter)) {
              uploader.requestParams.filecounter = filecounter;
            }
          }
          //uploader.requestParams.imgtext = jQuery('#imgtext').val();
          //uploader.requestParams.imgauthor = jQuery('#imgauthor').val();
          uploader.requestParams.published = jQuery('#published0').prop('checked') ? 0 : 1;
          //uploader.requestParams.access = jQuery('#access').val();
          if(jQuery('#original_delete').length > 0) {
            uploader.requestParams.original_delete = jQuery('#original_delete').prop('checked') ? 1 : 0;
          }
          uploader.requestParams.create_special_gif = jQuery('#create_special_gif').prop('checked') ? 1 : 0;
          //uploader.requestParams.debug = jQuery('#debug').prop('checked') ? 1 : 0;
          uploader.setParams(uploader.requestParams);
          uploader.uploadStoredFiles();
          jQuery('#triggerClearUploadList').removeClass('hidden');
        });
      });
    </script>
  </div>
  <div class="buttons">
    <button id="triggerUpload" class="btn btn-large btn-primary pull-left">
      <i class="icon-upload icon-white"></i>
      <?php echo JText::_('COM_JOOMGALLERY_UPLOAD_UPLOAD'); ?>
    </button>
    <button id="triggerClearUploadList" class="btn btn-info pull-right hidden">
      <i class="icon-list icon-black"></i> <?php echo JText::_('COM_JOOMGALLERY_AJAXUPLOAD_CLEAR_UPLOAD_LIST'); ?>
    </button>
  </div>