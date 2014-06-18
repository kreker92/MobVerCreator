<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php 
$app        = JFactory::getApplication();
$template   = $app->getTemplate(true)->template;
$doc        = JFactory::getDocument();
$doc->addScript('templates/' .$template. '/js/site_list.js'); 
$user = JFactory::getUser();

$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query
	->select(array('a.idsite', 'a.idusers', 'a.datecreate', 'a.dateedit', 'c.siteurl', 'c.mobsiteurl', 'c.uridir', 'c.publish', 'c.showonsitepanel', 'c.multilang', 'c.callbtn', 'c.synchronization'))
	->from('`#__sites_users` AS a')
	->join('LEFT', '`#__sites_data` AS c ON a.idsite = c.idsite')
	->where('c.showonsitepanel = 1 AND a.idusers = '. $user->id .'');
$db->setQuery($query);

if ($db->getErrorNum()) {
	echo $db->getErrorMsg();
	exit;
} else {
	$sites = $db->loadObjectList();
	// print_r($data);
}
?>

<div class="profile <?php echo $this->pageclass_sfx?>">
<ul class="nav nav-tabs">
	<li class="active"><a href="#sites" data-toggle="tab" >Сайты</a></li>
	<li><a href="#profile" data-toggle="tab">Профиль</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane active" id="sites">
		<a href="#chronoform-addSite" class="btn btn-success btn-default"><span class="icon-new icon-white"></span> Добавить новый сайт</a>
		<table width="100%" class="table table-striped" id="site-list">
			<thead>
				<tr>
					<th class="nowrap center">
						
					</th>
					<th width="1%" class="nowrap center">
						<p>Опубликовано</p>
					</th>
					<th width="1%" class="nowrap center">
						<p>Установить свой URL</p>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 0;
				foreach($sites as $site) { ?>
				<tr>
					<td class="left tools">
						<a href="<?php echo $site->siteurl; ?>" class="grey" target="_blank"><?php echo str_replace('http://', '', $site->siteurl); ?></a>
						<a href="<?php echo $site->mobsiteurl; ?>" class="grey"><?php echo $site->mobsiteurl; ?></a>
						<div class="btn-group">
							<a class="btn hasTooltip" href="/view?site=<?php echo $site->mobsiteurl; ?>" target="_blank" data-original-title="Просмотр"><span class="icon-eye-open"></span></a>
							<a class="btn hasTooltip" href="/editor?<?php echo $site->idsite; ?>" data-original-title="Изменить"><span class="icon-edit"></span></a>
							<a class="btn hasTooltip" href="#duplicate" data-id="<?php echo $site->idsite; ?>" data-original-title="Дублировать"><span class="icon-duplicate"></span></a>
							<a class="btn hasTooltip" href="#remove" data-id="<?php echo $site->idsite; ?>" data-original-title="Удалить"><span class="icon-trash"></span></a>
						</div>
					</td>
					<td class="center middle">
						<a class="btn btn-large" href="#publish" data-id="<?php echo $site->idsite; ?>" data-cond="<?php echo ($site->publish == true) ? '1' : '0'; ?>"><span class="icon-<?php echo ($site->publish == true) ? 'publish' : 'unpublish'; ?>"></span></a>
					</td>
					<td class="center middle">
						<a class="btn btn-large" href="javascript://"><span class="icon-wrench"></span></a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="tab-pane" id="profile">
		<?php if (JFactory::getUser()->id == $this->data->id) : ?>
		<ul class="btn-toolbar pull-right">
			<li class="btn-group">
				<a class="btn" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">
					<span class="icon-user"></span> <?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?></a>
			</li>
		</ul>
		<?php endif; ?>
		<?php echo $this->loadTemplate('core'); ?>

		<?php echo $this->loadTemplate('params'); ?>

		<?php echo $this->loadTemplate('custom'); ?>
	</div>
</div>
</div>