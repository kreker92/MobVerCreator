<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');

// Create shortcut to parameters.
$params = $this->state->get('params');
//$images = json_decode($this->item->images);
//$urls = json_decode($this->item->urls);

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);
if (!$editoroptions)
{
	$params->show_urls_images_frontend = '0';
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task);
		}
	}
</script>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
		<fieldset>
			<!--<ul class="nav nav-tabs">
				<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('COM_CONTENT_ARTICLE_CONTENT') ?></a></li>
				<?php if ($params->get('show_urls_images_frontend') ) : ?>
				<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
				<?php endif; ?>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_CONTENT_PUBLISHING') ?></a></li>
				<li><a href="#language" data-toggle="tab"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></a></li>
				<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_CONTENT_METADATA') ?></a></li>
			</ul>-->

			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<?php echo $this->form->renderField('title'); ?>
					<div id="leftCol">
						<div class="btn-toolbar">
							<div class="btn-group">
								<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
									<span class="icon-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
								</button>
								<button type="button" class="btn" onclick="Joomla.submitbutton('article.cancel')">
									<span class="icon-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
								</button>
								<button type="button" class="btn btn-success" onclick="Joomla.submitbutton('article.cancel')">
									<span class="icon-ok"></span>&#160;Опубликовать
								</button>
								<button type="button" class="btn" onclick="Joomla.submitbutton('article.cancel')">
									<span class="icon-cancel"></span>&#160;Снять с публикации
								</button>
							</div>
						</div>
						
						<!--<?php if (is_null($this->item->id)) : ?>
							<?php echo $this->form->renderField('alias'); ?>
						<?php endif; ?>-->
						
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th width="1%" class="nowrap center hidden-phone">
										<a href="#" onclick="return false;" class="js-stools-column-order hasTooltip" data-order="a.ordering" data-direction="ASC" data-name="" title="" data-original-title="Меняйте порядок пунктов меню">
											<i class="icon-menu-2"></i>
										</a>
									</th>
									<th class="center">
										<input type="checkbox" name="cid[]" value="">
									</th>
									<td></td>
									<td class="center">
										<p>Опубликовать</p>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr class="info">
									<td>
										<span class="sortable-handler active tip-top hasTooltip" title="" data-original-title="">
											<i class="icon-menu"></i>
										</span>
									</td>
									<td class="center">
										<input type="checkbox" name="cid[]" value="">
									</td>
									<td colspan="2">Меню 1</td>
								</tr>
								<tr>
									<td class="order nowrap center hidden-phone">
										<span class="sortable-handler active tip-top hasTooltip" title="" data-original-title="">
											<i class="icon-menu"></i>
										</span>
									</td>
									<td class="center">
										<input type="checkbox" name="cid[]" value="">
									</td>
									<td>Главная</td>
									<td class="center">
										<div class="btn-group">
											<a href="#" class="btn"><span class="icon-publish"></span></a>
										</div>
									</td>
								</tr>
								<tr>
									<td class="order nowrap center hidden-phone">
										<span class="sortable-handler active tip-top hasTooltip" title="" data-original-title="Меняйте порядок пунктов меню">
											<i class="icon-menu"></i>
										</span>
									</td>
									<td class="center">
										<input type="checkbox" name="cid[]" value="">
									</td>
									<td>Полезные статьи</td>
									<td class="center">
										<a href="#" class="btn"><span class="icon-unpublish"></span></a>
									</td>
								</tr>
								<tr>
									<td class="order nowrap center hidden-phone">
										<span class="sortable-handler active tip-top hasTooltip" title="" data-original-title="Меняйте порядок пунктов меню">
											<i class="icon-menu"></i>
										</span>
									</td>
									<td class="center">
										<input type="checkbox" name="cid[]" value="">
									</td>
									<td>О компании</td>
									<td class="center">
										<div class="btn-group">
											<a href="#" class="btn"><span class="icon-publish"></span></a>
										</div>
									</td>
								</tr>
								<tr>
									<td class="order nowrap center hidden-phone">
										<span class="sortable-handler active tip-top hasTooltip" title="" data-original-title="Меняйте порядок пунктов меню">
											<i class="icon-menu"></i>
										</span>
									</td>
									<td class="center">
										<input type="checkbox" name="cid[]" value="">
									</td>
									<td>Контакты</td>
									<td class="center">
										<div class="btn-group">
											<a href="#" class="btn"><span class="icon-publish"></span></a>
										</div>
									</td>
								</tr>
								<tr class="info">
									<td>
										<span class="sortable-handler active tip-top hasTooltip" title="" data-original-title="">
											<i class="icon-menu"></i>
										</span>
									</td>
									<td class="center">
										<input type="checkbox" name="cid[]" value="">
									</td>
									<td colspan="2">Меню 2</td>
								</tr>
								<tr>
									<td class="order nowrap center hidden-phone">
										<span class="sortable-handler active tip-top hasTooltip" title="" data-original-title="Меняйте порядок пунктов меню">
											<i class="icon-menu"></i>
										</span>
									</td>
									<td class="center">
										<input type="checkbox" name="cid[]" value="">
									</td>
									<td>Второе меню</td>
									<td class="center">
										<div class="btn-group">
											<a href="#" class="btn"><span class="icon-publish"></span></a>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						
						<div id="tryit">
							<table class="table table-striped table-hover">
								<tr>
									<td style="width: 10%"><img src="/images/vk_icon.jpg" /></td>
									<td class="left"><input style="width: 95%" type="text" value="http://vk.com/site_group" /></td>
								</tr>
							</table>
							<select>
								<option>twitter</option>
								<option></option>
							</select>
						</div>
						
						<label>E-mail для формы обратной связи:</label>
						<input type="text" value="info@ursite.ru" />
						<label>Ваш телефон:</label>
						<input type="text" value="8 (999) 777-66-55" />
						<p>Мультиязычность сайта:</p>
						<div class="btn-group btn-toggle"> 
							<a class="btn btn-default active">On</a>
							<a class="btn btn-danger">Off</a>
						</div>
					</div>

					<div id="rightCol">
						<?php echo $this->form->getInput('articletext'); ?>
					</div>
				</div>
				<?php if ($params->get('show_urls_images_frontend')): ?>
				<div class="tab-pane" id="images">
					<?php echo $this->form->renderField('image_intro', 'images'); ?>
					<?php echo $this->form->renderField('image_intro_alt', 'images'); ?>
					<?php echo $this->form->renderField('image_intro_caption', 'images'); ?>
					<?php echo $this->form->renderField('float_intro', 'images'); ?>
					<?php echo $this->form->renderField('image_fulltext', 'images'); ?>
					<?php echo $this->form->renderField('image_fulltext_alt', 'images'); ?>
					<?php echo $this->form->renderField('image_fulltext_caption', 'images'); ?>
					<?php echo $this->form->renderField('float_fulltext', 'images'); ?>
					<?php echo $this->form->renderField('urla', 'urls'); ?>
					<?php echo $this->form->renderField('urlatext', 'urls'); ?>
					<div class="control-group">
						<div class="controls">
							<?php echo $this->form->getInput('targeta', 'urls'); ?>
						</div>
					</div>
					<?php echo $this->form->renderField('urlb', 'urls'); ?>
					<?php echo $this->form->renderField('urlbtext', 'urls'); ?>
					<div class="control-group">
						<div class="controls">
							<?php echo $this->form->getInput('targetb', 'urls'); ?>
						</div>
					</div>
					<?php echo $this->form->renderField('urlc', 'urls'); ?>
					<?php echo $this->form->renderField('urlctext', 'urls'); ?>
					<div class="control-group">
						<div class="controls">
							<?php echo $this->form->getInput('targetc', 'urls'); ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<div class="tab-pane" id="publishing">
					<?php echo $this->form->renderField('catid'); ?>
					<?php echo $this->form->renderField('tags'); ?>
					<?php if ($params->get('save_history', 0)) : ?>
						<?php echo $this->form->renderField('version_note'); ?>
					<?php endif; ?>
					<?php echo $this->form->renderField('created_by_alias'); ?>
					<?php if ($this->item->params->get('access-change')) : ?>
						<?php echo $this->form->renderField('state'); ?>
						<?php echo $this->form->renderField('featured'); ?>
						<?php echo $this->form->renderField('publish_up'); ?>
						<?php echo $this->form->renderField('publish_down'); ?>
					<?php endif; ?>
					<?php echo $this->form->renderField('access'); ?>
					<?php if (is_null($this->item->id)):?>
						<div class="control-group">
							<div class="control-label">
							</div>
							<div class="controls">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="language">
					<?php echo $this->form->renderField('language'); ?>
				</div>
				<div class="tab-pane" id="metadata">
					<?php echo $this->form->renderField('metadesc'); ?>
					<?php echo $this->form->renderField('metakey'); ?>

					<input type="hidden" name="task" value="" />
					<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
					<?php if ($this->params->get('enable_category', 0) == 1) :?>
					<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
					<?php endif; ?>
				</div>
			</div>
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
