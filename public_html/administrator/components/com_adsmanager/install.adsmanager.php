<?php
/**
 *  @package	AdsManager
 *  @copyright	Copyright (c)2010-2012 Thomas Papin / JoomPROD.com
 *  @license	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 *  @version 	$Id$
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport( 'joomla.error.error' );

class Com_AdsmanagerInstallerScript
{
	/** @var string The component's name */
	protected $_adsmanager_extension = 'com_adsmanager';
	
	/** @var array The list of extra modules and plugins to install */
	private $installation_queue = array(
		// modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules' => array(
			'admin' => array(
			),
			'site' => array(
				'adsmanager_ads' => array('left', 0),
				'adsmanager_menu' => array('left', 0),
				'adsmanager_search' => array('left', 0),
				'adsmanager_table' => array('left', 0)
			)
		),
		'plugins' => array(
			'adsmanagercontent' => array(
				'jcomments'	=> 0,
				'captcha'	=> 0,
				'recaptcha'	=> 0,
				'social'	=> 0
			)
		)
		,'adsmanagerfields' => array( 
		)
	);
	
	private $adsmanagerRemovePlugins = array(
		'' => array(
		)
	);
	
	/** @var array Obsolete files and folders to remove */
	private $adsmanagerRemoveFiles = array(
		'files'	=> array(
			'administrator/components/com_adsmanager/admin.adsmanager.php',
			'administrator/components/com_adsmanager/admin.adsmanager.html.php'
		),
		'folders' => array(
			/*'administrator/components/com_adsmanager/commands',*/
		)
	);
	
	private $adsmanagerCliScripts = array();
	
	/**
	 * Joomla! pre-flight event
	 * 
	 * @param string $type Installation type (install, update, discover_install)
	 * @param JInstaller $parent Parent object
	 */
	public function preflight($type, $parent)
	{
		// Bugfix for "Can not build admin menus"
		if(in_array($type, array('install','discover_install'))) {
			$this->_bugfixDBFunctionReturnedNoError();
		} else {
			$this->_bugfixCantBuildAdminMenus();
		}
		
		// Only allow to install on Joomla! 2.5.1 or later
		if(!version_compare(JVERSION, '2.5.1', 'ge')) {
			echo "<h1>Unsupported Joomla! version</h1>";
			echo "<p>This component can only be installed on Joomla! 2.5.1 or later</p>";
			return false;
		}
		return true;
	}
	
	function install($parent) { $this->_updateDatabase(); }
	function update($parent) { $this->_updateDatabase(); }
	

	
	/**
	 * Runs after install, update or discover_update
	 * @param string $type install, update or discover_update
	 * @param JInstaller $parent 
	 */
	function postflight( $type, $parent )
	{
		// Install subextension
		$status = $this->_installSubextensions($parent);
		
		// Remove obsolete files and folders
		$adsmanagerRemoveFiles = $this->adsmanagerRemoveFiles;
		$this->_removeObsoleteFilesAndFolders($adsmanagerRemoveFiles);
		
		$this->_copyCliFiles($parent);
		
		// Remove Professional version plugins from Akeeba Backup Core
		$this->_removeObsoletePlugins($parent);
		
		$straperStatus = 0;
		$fofStatus = 0;
		
		// Show the post-installation page
		$this->_renderPostInstallation($status, $fofStatus, $straperStatus, $parent);
		
		// Kill update site
		//$this->_killUpdateSite();
	}
	
	/**
	 * Runs on uninstallation
	 * 
	 * @param JInstaller $parent 
	 */
	function uninstall($parent)
	{
		// Uninstall subextensions
		$status = $this->_uninstallSubextensions($parent);
		
		// Show the post-uninstallation page
		$this->_renderPostUninstallation($status, $parent);
	}
	
	/**
	 * Removes the plugins which have been discontinued
	 * 
	 * @param JInstaller $parent 
	 */
	private function _removeObsoletePlugins($parent)
	{
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}
		$db = JFactory::getDbo();
		
		foreach($this->adsmanagerRemovePlugins as $folder => $plugins) {
			foreach($plugins as $plugin) {
				$sql = $db->getQuery(true)
					->select($db->qn('extension_id'))
					->from($db->qn('#__extensions'))
					->where($db->qn('type').' = '.$db->q('plugin'))
					->where($db->qn('element').' = '.$db->q($plugin))
					->where($db->qn('folder').' = '.$db->q($folder));
				$db->setQuery($sql);
				$id = $db->loadResult();
				if($id)
				{
					$installer = new JInstaller;
					$result = $installer->uninstall('plugin',$id,1);
				}
			}
		}
	}
	
	/**
	 * Copies the CLI scripts into Joomla!'s cli directory
	 * 
	 * @param JInstaller $parent 
	 */
	private function _copyCliFiles($parent)
	{
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}
		
		jimport("joomla.filesystem.file");
		jimport("joomla.filesystem.folder");
		
		if(empty($this->adsmanagerCliScripts)) {
			return;
		}
		
		foreach($this->adsmanagerCliScripts as $script) {
			if(JFile::exists(JPATH_ROOT.'/cli/'.$script)) {
				JFile::delete(JPATH_ROOT.'/cli/'.$script);
			}
			if(JFile::exists($src.'/cli/'.$script)) {
				JFile::move($src.'/cli/'.$script, JPATH_ROOT.'/cli/'.$script);
			}
		}
	}
	
	/**
	 * Renders the post-installation message 
	 */
	private function _renderPostInstallation($status, $fofStatus, $straperStatus, $parent)
	{
?>
<?php if (!version_compare(PHP_VERSION, '5.3.0', 'ge')): ?>
	<div style="margin: 1em; padding: 1em; background: #ffff00; border: thick solid red; color: black; font-size: 14pt;" id="notfixedperms">
		<h1 style="margin: 1em 0; color: red; font-size: 22pt;">OUTDATED PHP VERSION</h1>
		<p>You are using an outdated version of PHP which is not properly supported by JoomPROD.com. Please upgrade to PHP 5.3 or later as soon as possible. Future versions of our software will not work at all on PHP 5.2.</p>
	</div>
<?php endif; ?>
<h1>AdsManager</h1>

<?php $rows = 1;?>
<img src="../components/com_adsmanager/images/logofull.png" alt="Adsmanager" align="left" />
<h2 style="font-size: 14pt; font-weight: black; padding: 0; margin: 0 0 0.5em;">Welcome to Adsmanager!</h2>
<span>The classified component for Joomla!</span>

<table class="adminlist table table-striped" width="100%" style="width:100%">
	<thead>
		<tr>
			<th class="title" colspan="2">Extension</th>
			<th width="30%">Status</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2">
				<img src="../components/com_adsmanager/images/logo.png" width="16" height="16" alt="Adsmanager" align="left" />
				&nbsp;
				Adsmanager component
			</td>
			<td><strong style="color: green">Installed</strong></td>
		</tr>
		<?php if (count($status->modules)) : ?>
		<tr>
			<th>Module</th>
			<th>Client</th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo ($rows++ % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($module['result'])?'Installed':'Not installed'; ?></strong></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		<?php if (count($status->plugins)) : ?>
		<tr>
			<th>Plugin</th>
			<th>Group</th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo ($rows++ % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?'Installed':'Not installed'; ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (count($status->adsmanagerfields)) : ?>
		<tr>
			<th><?php echo "AdsManager Fields"; ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->adsmanagerfields as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?JText::_('Installed'):JText::_('Not installed'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<?php
	}
	
	private function _renderPostUninstallation($status, $parent) {
?>
<?php $rows = 0;?>
<h2 style="font-size: 14pt; font-weight: black; padding: 0; margin: 0 0 0.5em;">&nbsp;AdsManager Uninstallation</h2>
<p>We are sorry that you decided to uninstall AdsManager. Please let us know why by using the Contact Us form on our site. We appreciate your feedback; it helps us develop better software!</p>

<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'AdsManager '.JText::_('Component'); ?></td>
			<td><strong style="color: green"><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($module['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		<?php if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<?php
	}
	
	/**
	 * Joomla! 1.6+ bugfix for "DB function returned no error"
	 */
	private function _bugfixDBFunctionReturnedNoError()
	{
		$db = JFactory::getDbo();
			
		// Fix broken #__assets records
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__assets')
			->where($db->qn('name').' = '.$db->q($this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__assets')
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__extensions records
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where($db->qn('element').' = '.$db->q($this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__extensions')
				->where($db->qn('extension_id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__menu records
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('menutype').' = '.$db->q('main'))
			->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}
	}
	
	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 */
	private function _bugfixCantBuildAdminMenus()
	{
		$db = JFactory::getDbo();
		
		// If there are multiple #__extensions record, keep one of them
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where($db->qn('element').' = '.$db->q($this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(count($ids) > 1) {
			asort($ids);
			$extension_id = array_shift($ids); // Keep the oldest id
			
			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__extensions')
					->where($db->qn('extension_id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->query();
			}
		}
		
		// If there are multiple assets records, delete all except the oldest one
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__assets')
			->where($db->qn('name').' = '.$db->q($this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadObjectList();
		if(count($ids) > 1) {
			asort($ids);
			$asset_id = array_shift($ids); // Keep the oldest id
			
			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__assets')
					->where($db->qn('id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Remove #__menu records for good measure!
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('menutype').' = '.$db->q('main'))
			->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_adsmanager_extension));
		$db->setQuery($query);
		$ids1 = $db->loadColumn();
		if(empty($ids1)) $ids1 = array();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('menutype').' = '.$db->q('main'))
			->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_adsmanager_extension.'&%'));
		$db->setQuery($query);
		$ids2 = $db->loadColumn();
		if(empty($ids2)) $ids2 = array();
		$ids = array_merge($ids1, $ids2);
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->query();
		}
	}
	
	function _updateDatabase() {
		if(version_compare(JVERSION, '2.5', '>=') && version_compare(JVERSION, '3.0', '<') ) {
			if (JError::$legacy)
				$tmp_legacy = true;
			else
				$tmp_legacy = false;
		
			JError::$legacy = false;
		}
	
		jimport( 'joomla.filesystem.file' );
	
	if(!file_exists(JPATH_ROOT . "/images/com_adsmanager/")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/categories/")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/categories/");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/ads/")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/ads/");
	};
	
	if(file_exists(JPATH_ROOT. "/images/com_adsmanager/ads/tmp")){
		JFolder::move(JPATH_ROOT. "/images/com_adsmanager/ads/tmp",JPATH_ROOT. "/images/com_adsmanager/ads/waiting");
	}
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/ads/waiting")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/ads/waiting");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/ads/uploaded")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/ads/uploaded");
	};
	
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/ads/tmp")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/ads/tmp");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/email/")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/email/");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/files/")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/files/");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/files/.htaccess")){
		$content = "ForceType application/octet-stream";
		$content .= "\nHeader set Content-Disposition attachment";
		JFile::write(JPATH_ROOT. "/images/com_adsmanager/files/.htaccess",$content);
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/fields/")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/fields/");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/plugins/")){
		JFolder::create(JPATH_ROOT. "/images/com_adsmanager/plugins/");
	};
	
	
	if(!file_exists(JPATH_ROOT . "/images/com_adsmanager/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/index.html");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/categories/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/categories/index.html");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/ads/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/ads/index.html");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/ads/waiting/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/ads/waiting/index.html");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/ads/uploaded/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/ads/uploaded/index.html");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/email/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/email/index.html");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/files/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/files/index.html");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/fields/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/fields/index.html");
	};
	
	if(!file_exists(JPATH_ROOT. "/images/com_adsmanager/plugins/index.html")){
		JFile::copy(JPATH_ROOT."/components/com_adsmanager/index.html",JPATH_ROOT. "/images/com_adsmanager/plugins/index.html");
	};
	
	$lang = JFactory::getLanguage();
	$lang->load("com_adsmanager");
	
	// Schema modification -- BEGIN
	
	$db = JFactory::getDBO();
	
	$db->setQuery("CREATE TABLE IF NOT EXISTS `#__adsmanager_searchmodule_config` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`params` text NOT NULL,
			PRIMARY KEY  (`id`)
	);");
	try {
		$result = $db->query();
	} catch(Exception $e) {
		
	}
	
	$db->setQuery("INSERT IGNORE INTO `#__adsmanager_searchmodule_config` (`params`) VALUES ('')");
	try {
		$result = $db->query();
	} catch(Exception $e) {
		
	}
		   
	$db->setQuery("SELECT count(*) FROM `#__adsmanager_fields` WHERE 1");
	$total = $db->loadResult();
	if ($total == 0)
	{
		$queries = array();
		$queries[] = " INSERT IGNORE INTO `#__adsmanager_columns` (`id`,`name`,`ordering`,`catsid`,`published`) VALUES "
		     . " (2, 'ADSMANAGER_PRICE', 1, 1,1), "
		     . " (3, 'ADSMANAGER_CITY', 2, 1,1), "
		     . " (5, 'ADSMANAGER_STATE', 1, 0,1);";
		
		$queries[] = " UPDATE #__adsmanager_columns SET catsid = ',-1,'";
		
		$queries[] = " INSERT IGNORE INTO `#__adsmanager_field_values` (`fieldvalueid`,`fieldid`,`fieldtitle`,`fieldvalue`,`ordering`,`sys`) VALUES "
		     . " (1, 8, 'ADSMANAGER_KINDOF2', 1, 1, 0), "
		     . " (2, 8, 'ADSMANAGER_KINDOF1', 2, 2, 0), "
		     . " (3, 9, 'ADSMANAGER_STATE_0', 4, 4, 0),"
		     . " (4, 8, 'ADSMANAGER_KINDOFALL', 0, 0, 0),"
			 . " (5, 9, 'ADSMANAGER_STATE_1', 3, 3, 0),"
			 . " (6, 9, 'ADSMANAGER_STATE_3', 1, 1, 0),"
			 . " (7, 9, 'ADSMANAGER_STATE_2', 2, 2, 0),"
			 . " (8, 9, 'ADSMANAGER_STATE_4', 0, 0, 0);";
	
		$queries[] = " INSERT IGNORE INTO `#__adsmanager_fields` (`fieldid`, `name`, `title`, `description`, `type`, `maxlength`, `size`, `required`, `ordering`, `cols`, `rows`, `columnid`, `columnorder`, `pos`, `posorder`, `profile`, `cb_field`, `sort`, `sort_direction`, `published`, `options`) VALUES "
			 . "(1, 'name', 'ADSMANAGER_FORM_NAME', '', 'text', 50, 35, 1, 0, 0, 0, -1, 5, 5, 1, 1, 41, 0, 'DESC', 1, ''),"
			 . "(2, 'email', 'ADSMANAGER_FORM_EMAIL', '', 'emailaddress', 50, 35, 1, 1, 0, 0, -1, 10, 5, 4, 1, 50, 0, 'DESC', 1, ''),"
			 . "(3, 'ad_city', 'ADSMANAGER_FORM_CITY', '', 'text', 50, 35, 0, 4, 0, 0, 3, 1, 5, 3, 1, 59, 1, 'ASC', 1, ''),"
			 . "(4, 'ad_zip', 'ADSMANAGER_FORM_ZIP', '', 'text', 6, 7, 0, 3, 0, 0, -1, 0, 5, 2, 1, -1, 0, 'ASC', 1, ''),"
			 . "(5, 'ad_headline', 'ADSMANAGER_FORM_AD_HEADLINE', '', 'text', 60, 35, 1, 5, 0, 0, -1, 0, 1, 1, 0, -1, 0, 'DESC', 1, ''),"
			 . "(6, 'ad_text', 'ADSMANAGER_FORM_AD_TEXT', '', 'textarea', 500, 0, 1, 6, 40, 20, -1, 0, 3, 1, 0, -1, 0, 'DESC', 1, ''),"
			 . "(7, 'ad_phone', 'ADSMANAGER_FORM_PHONE1', '', 'number', 50, 35, 0, 2, 0, 0, -1, 0, 5, 1, 1, -1, 0, 'DESC', 1, ''),"
			 . "(8, 'ad_kindof', 'ADSMANAGER_FORM_KINDOF', '', 'select', 0, 0, 1, 7, 0, 0, 5, 0, 2, 1, 0, -1, 0, 'DESC', 1, ''),"
			 . "(9, 'ad_state', 'ADSMANAGER_FORM_STATE', '', 'select', 0, 0, 1, 8, 0, 0, 5, 0, 2, 1, 0, -1, 0, 'DESC', 1, ''),"
			 . "(10, 'ad_price', 'ADSMANAGER_FORM_AD_PRICE', '', 'price', 10, 7, 1, 9, 0, 0, 2, 0, 4, 1, 0, -1, 1, 'DESC', 1, '{\"currency_symbol\":\"\\\\u20ac\",\"currency_position\":\"after\",\"currency_number_decimals\":\"2\",\"currency_decimal_separator\":\".\",\"currency_thousands_separator\":\" \"}');";	
		
		
		$queries[] = " UPDATE #__adsmanager_fields SET catsid = ',-1,'";
		$queries[] = " INSERT IGNORE INTO `#__adsmanager_positions` (`id`,`name`,`title`) VALUES "
					. " (1, 'top', 'ADSMANAGER_POSITION_TOP'),"
					. " (2, 'subtitle', 'ADSMANAGER_POSITION_SUBTITLE'),"
						 . " (3, 'description', 'ADSMANAGER_POSITION_DESCRIPTION'),"
						 		. " (4, 'description2', 'ADSMANAGER_POSITION_DESCRIPTION2'),"
						 				. " (5, 'contact', 'ADSMANAGER_POSITION_CONTACT');";
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `name` TEXT DEFAULT NULL;";
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `ad_zip` TEXT DEFAULT NULL;";
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `ad_city` TEXT DEFAULT NULL;";
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `ad_phone` TEXT DEFAULT NULL;";
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `email` TEXT DEFAULT NULL;";	
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `ad_kindof` TEXT DEFAULT NULL;";	
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `ad_headline` TEXT DEFAULT NULL;";	
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `ad_text` TEXT DEFAULT NULL;";	
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` CHANGE `ad_state` `ad_state` TEXT DEFAULT NULL;";	
		$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `ad_price` TEXT DEFAULT NULL;";	  
		$queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` ADD `name` TEXT DEFAULT NULL;";   
		$queries[] = "ALTER IGNORE TABLE  `#__adsmanager_profile` ADD `ad_zip` TEXT DEFAULT NULL;";	
		$queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` ADD `ad_city` TEXT DEFAULT NULL;";	
		$queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` ADD `ad_phone` TEXT NOT NULL;";	
		$queries[] = " INSERT IGNORE INTO `#__adsmanager_categories` (`id`,`parent`,`name`,`published`) VALUES "
			 . " (1, 0,'Category 1', 1),"	
			 . " (2, 0,'Category 2', 1),"
			 . " (3, 1,'SubCat1', 1),"
			 . " (4, 1,'SubCat2', 1),"
			 . " (5, 1,'SubCat3', 1),"
			 . " (6, 2,'SubCat4', 1),"
			 . " (7, 2,'SubCat5', 1),"
			 . " (8, 2,'SubCat6', 1);";
		
		foreach($queries as $query) {
			$db->setQuery($query);
			try {
				$result = $db->query();
			} catch(Exception $e) {
				
			}
		}
	}
	
	$queries = array();
	
	$queries[] = "ALTER IGNORE TABLE `#__adsmanager_fields` CHANGE `catsid` `catsid` TEXT NOT NULL;";	
	$queries[] = "ALTER IGNORE TABLE `#__adsmanager_columns` CHANGE `catsid` `catsid` TEXT NOT NULL;";	
	$queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` CHANGE `date_created` `date_created` DATETIME NOT NULL;";	
	$queries[] = "ALTER IGNORE TABLE  `#__adsmanager_config` ADD `bannedwords` TEXT DEFAULT NULL;";		
	$queries[] = "ALTER IGNORE TABLE  `#__adsmanager_config` ADD `replaceword` TEXT DEFAULT NULL;";		
	$queries[] = "ALTER IGNORE TABLE  `#__adsmanager_config` ADD `after_expiration` TEXT DEFAULT NULL;";			
	$queries[] = "ALTER IGNORE TABLE  `#__adsmanager_config` ADD `archive_catid` INT(10) NOT NULL default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `metadata_description` TEXT DEFAULT NULL;";		
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_ads` ADD `metadata_keywords` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_categories` ADD `metadata_description` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_categories` ADD `metadata_keywords` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `metadata_mode` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `autocomplete` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `jquery` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `jqueryui` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `nb_last_cols` int(10) NOT NULL default '3';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `nb_last_rows` int(10) NOT NULL default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `display_general_menu` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `display_list_sort` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `display_list_search` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `display_inner_pathway` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `display_front` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `send_email_on_new_to_user` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `send_email_on_update_to_user` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `send_email_on_validation_to_user` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `send_email_on_expiration_to_user` tinyint(1) default '1';";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `new_text` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `update_text` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `admin_new_text` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `admin_update_text` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `waiting_validation_text` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `validation_text` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `expiration_text` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `new_subject` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `update_subject` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `admin_new_subject` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `admin_update_subject` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `waiting_validation_subject` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `validation_subject` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `expiration_subject` TEXT DEFAULT NULL;";	
	$queries[] = " ALTER IGNORE TABLE `#__adsmanager_config` ADD `recall_subject` TEXT DEFAULT NULL;";	
	$queries[] = "INSERT IGNORE INTO `#__adsmanager_positions` VALUES (6, 'description3', 'ADSMANAGER_POSITION_DESCRIPTION3');";
	
	$queries[] = " CREATE TABLE IF NOT EXISTS `#__adsmanager_pending_ads` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `userid` int(11) NOT NULL,
	  `date` date NOT NULL,
	  `content` text NOT NULL,
	  `contentid` int(11) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
	
	foreach($queries as $query) {
		$db->setQuery($query);
		try {
			$result = $db->query();
		} catch(Exception $e) {
			
		}
	}
	
	$db->setQuery(" SELECT new_subject FROM `#__adsmanager_config` WHERE id=1;");
	$new_subject = $db->loadResult();
	if ($new_subject == null) {
		$queries = array();
		$queries[] = "UPDATE #__adsmanager_config SET new_subject = ".$db->Quote(JText::_('ADSMANAGER_NEW_SUBJECT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET update_subject = ".$db->Quote(JText::_('ADSMANAGER_UPDATE_SUBJECT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET expiration_subject = ".$db->Quote(JText::_('ADSMANAGER_EXPIRATION_SUBJECT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET recall_subject = ".$db->Quote(JText::_('ADSMANAGER_RECALL_SUBJECT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET waiting_validation_subject = ".$db->Quote(JText::_('ADSMANAGER_WAITING_VALIDATION_SUBJECT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET validation_subject = ".$db->Quote(JText::_('ADSMANAGER_VALIDATION_SUBJECT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET admin_new_subject = ".$db->Quote(JText::_('ADSMANAGER_ADMIN_NEW_SUBJECT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET admin_update_subject = ".$db->Quote(JText::_('ADSMANAGER_ADMIN_UPDATE_SUBJECT_EXAMPLE'));
		
		$queries[] = "UPDATE #__adsmanager_config SET new_text = ".$db->Quote(JText::_('ADSMANAGER_NEW_TEXT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET update_text = ".$db->Quote(JText::_('ADSMANAGER_UPDATE_TEXT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET expiration_text = ".$db->Quote(JText::_('ADSMANAGER_EXPIRATION_TEXT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET recall_text = ".$db->Quote(JText::_('ADSMANAGER_RECALL_TEXT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET waiting_validation_text = ".$db->Quote(JText::_('ADSMANAGER_WAITING_VALIDATION_TEXT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET validation_text = ".$db->Quote(JText::_('ADSMANAGER_VALIDATION_TEXT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET admin_new_text = ".$db->Quote(JText::_('ADSMANAGER_ADMIN_NEW_TEXT_EXAMPLE'));
		$queries[] = "UPDATE #__adsmanager_config SET admin_update_text = ".$db->Quote(JText::_('ADSMANAGER_ADMIN_UPDATE_TEXT_EXAMPLE'));
		foreach($queries as $q) {
			$db->setQuery($q);
			try {
				$result = $db->query();
			} catch(Exception $e) {
				
			}
		}
	}
	$db->setQuery("SELECT * FROM #__adsmanager_config LIMIT 1");
	$config = $db->loadObject();
	
	$db->setQuery("ALTER IGNORE TABLE `#__adsmanager_ads` ADD `images` TEXT;");
	try {
		$result = $db->query();
	} catch(Exception $e) {
		
	}	
	
	if ($config == null) {
		$db->setQuery(" INSERT IGNORE INTO `#__adsmanager_config` (`id`,`version`,`ads_per_page`,`max_image_size`,`max_width`,`max_height`,`max_width_t`,`max_height_t`,`root_allowed`,`nb_images`,"
				." `show_contact`,`send_email_on_new`,`send_email_on_update`,`auto_publish`,`tag`,`fronttext`,`comprofiler`,`email_display`,`rules_text`,"
				." `display_expand`,`display_last`,`display_fullname`,`expiration`,`ad_duration`,`recall`,`recall_time`,`recall_text`,`image_display`,"
				." `cat_max_width`,`cat_max_height`,`cat_max_width_t`,`cat_max_height_t`,`submission_type`,`nb_ads_by_user`,`allow_attachement`,"
				." `allow_contact_by_pms`, `show_rss` ,`nbcats` ,`show_new`,`nbdays_new`,`show_hot`,`nbhits`,`bannedwords`,`replaceword`,`after_expiration`,`archive_catid`,`metadata_mode`) VALUES "
				." (1, '3', 20, 2048000, 400, 300, 150, 100, 1,2,"
				."  1,1,1,1, 'joomprod.com', '<p align=\"center\"><strong>Welcome to Ads Section.</strong></p><p align=\"left\">The better place to sell or buy</p>',0,0,'Inform the users about the rules here...',"
				."  2,1,0,1,30,1,7,'Add This Text to recall message<br />','default',"
				."  150,150,30,30,0,-1,0,"
				."  0,0,1,1,5,1,100,'','****','delete','0','automatic')");
		try {
			$result = $db->query();
		} catch(Exception $e) {
			
		}
	} else {
		$continue = true;
		while($continue) {
			$continue = false;
			switch($config->version) {
                case 10:
					break;
                case 9:
                    $queries = array();
                    $queries[] = "ALTER IGNORE TABLE `#__adsmanager_categories` ADD `limitads` INT(11) default '-1';";
                    $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `publication_date` DATETIME NOT NULL;";
                    foreach($queries as $q) {
                        $db->setQuery($q);
                        try {
                            $result = $db->query();
                        } catch(Exception $e) {

                        }
                    }
                    
                    $obj = new stdClass();
					$obj->id = 1;
					$obj->version = 10;
					$db->updateObject('#__adsmanager_config', $obj,'id');
                    $config->version = 10;
					$continue = true;
					break;
                case 8:
                    $queries = array();
                    $queries[] = "ALTER IGNORE TABLE `#__adsmanager_categories` ADD `limitads` INT(11) default '-1';";
                    $queries[] = "ALTER IGNORE TABLE `#__adsmanager_categories` ADD `usergroupsread` TEXT NOT NULL;";
                    $queries[] = "ALTER IGNORE TABLE `#__adsmanager_categories` ADD `usergroupswrite` TEXT NOT NULL;";
                    foreach($queries as $q) {
                        $db->setQuery($q);
                        try {
                            $result = $db->query();
                        } catch(Exception $e) {

                        }
                    } 
                    $obj = new stdClass();
					$obj->id = 1;
					$obj->version = 9;
					$db->updateObject('#__adsmanager_config', $obj,'id');
                    $config->version = 9;
					$continue = true;
					break;
			    case 7:
                    $q="SELECT params FROM #__adsmanager_config";
                    $db->setQuery($q);
                    $json_params = $db->loadResult();
                    $params = json_decode($json_params);
                    if ($params == null)
                    	$params = new stdClass();
                    if(!isset($params->email_admin) || $params->email_admin == ''){
                        if (version_compare(JVERSION,'3.0.0','>=')) {
                            $versionJoomla = 1;
                        } else {
                            $versionJoomla = 0;
                        }
                        $config	= JFactory::getConfig();
                        $params->email_admin = $versionJoomla ? $config->get('mailfrom') : $config->getValue('config.mailfrom');
                        $params->name_admin = $versionJoomla ? $config->get('fromname') : $config->getValue('config.fromname');
                        
                        $json_params = json_encode($params);
                        
                        $q="UPDATE #__adsmanager_config
                            SET params = ".$db->Quote($json_params);
                        $db->setQuery($q);
                        try {
                            $result = $db->query();
                        } catch(Exception $e) {

                        }
                    }
                    
                    $obj = new stdClass();
					$obj->id = 1;
					$obj->version = 8;
					$db->updateObject('#__adsmanager_config', $obj,'id');
                    $config->version = 8;
					$continue = true;
					break;
                case 6:
					$q= "ALTER IGNORE TABLE `#__adsmanager_categories` MODIFY `description` text;";
					$db->setQuery($q);
					try {
						$result = $db->query();
					} catch(Exception $e) {

					}
                    $obj = new stdClass();
					$obj->id = 1;
					$obj->version = 7;
					$db->updateObject('#__adsmanager_config', $obj,'id');
                    $config->version = 7;
					$continue = true;
					break;
				case 5:
                    $app = JFactory::getApplication();
					$db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$app->getCfg('db')."' AND TABLE_NAME = '".$db->getPrefix()."adsmanager_fieldgmap_conf'");
					$count = $db->loadResult();
                    
					if ($count > 0) {
						$db->setQuery("SELECT fieldid,name FROM #__adsmanager_fields WHERE type='gmap'");
						$fields = $db->loadObjectList("fieldid");
                        $queries = array();
						foreach($fields as $field) {
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."_lat` FLOAT default NULL;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."_lng` FLOAT default NULL;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."_zoom` int(10) unsigned default 8;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."_hide` int(1) unsigned default 0;";
							
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` ADD `".$field->name."_lat` FLOAT default NULL;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` ADD `".$field->name."_lng` FLOAT default NULL;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` ADD `".$field->name."_zoom` int(10) unsigned default 8;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` ADD `".$field->name."_hide` int(1) unsigned default 0;";
                            
                            //Patch for an old error in the update file (TEXT instead of FLOAT)
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` MODIFY `".$field->name."_lat` FLOAT default NULL;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` MODIFY `".$field->name."_lng` FLOAT default NULL;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` MODIFY `".$field->name."_zoom` int(10) unsigned default 8;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_ads` MODIFY `".$field->name."_hide` int(1) unsigned default 0;";
							
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` MODIFY `".$field->name."_lat` FLOAT default NULL;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` MODIFY `".$field->name."_lng` FLOAT default NULL;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` MODIFY `".$field->name."_zoom` int(10) unsigned default 8;";
                            $queries[] = "ALTER IGNORE TABLE `#__adsmanager_profile` MODIFY `".$field->name."_hide` int(1) unsigned default 0;";
						}
                        
                        foreach($queries as $q) {
                            $db->setQuery($q);
                            try {
                                $result = $db->query();
                            } catch(Exception $e) {

                            }
                        }
                    }
					$obj = new stdClass();
					$obj->id = 1;
					$obj->version = 6;
					$db->updateObject('#__adsmanager_config', $obj,'id');
                    
                    $config->version = 6;
					$continue = true;
					break;
				case 4:
					$db->setQuery("SELECT id,images FROM `#__adsmanager_ads`");
					$ads = $db->loadObjectList("id");
					foreach($ads as $ad) {
						$images = json_decode($ad->images);
						$change=0;
						if (count($images) > 1) {
							foreach($images as $key => $image) {
								if (!isset($image->medium)) {
									$images[$key]->medium = $image->thumbnail;
									$change=1;
								}
							}
						}
						if ($change == 1) {
							$obj = new stdClass();
							$obj->id = $ad->id;
							$obj->images = json_encode($images);
							$ret = $db->updateObject('#__adsmanager_ads', $obj,'id');
						}
					}
					
					$db->setQuery(" ALTER IGNORE TABLE `#__adsmanager_config` ADD `special` TEXT DEFAULT NULL;");
					try {
						$result = $db->query();
					} catch(Exception $e) {
						
					}
                    
					$obj = new stdClass();
					$obj->id = 1;
					$obj->version = 5;
					$db->updateObject('#__adsmanager_config', $obj,'id');
                    
                    $config->version = 5;
					$continue = true;
					break;
				case 3:
					$db->setQuery(" ALTER IGNORE TABLE `#__adsmanager_config` ADD `params` TEXT DEFAULT NULL;");
					try {
						$result = $db->query();
					} catch(Exception $e) {
						
					}
					
					$db->setQuery(" ALTER IGNORE TABLE `#__adsmanager_fields` ADD `options` TEXT DEFAULT NULL;");
					try {
						$result = $db->query();
					} catch(Exception $e) {
						
					}
					
					$obj = new stdClass();
					$obj->id = 1;
					$obj->version = 4;
					$db->updateObject('#__adsmanager_config', $obj,'id');
                    
                    $config->version = 4;
					$continue = true;
					break;
				case 2:
					$db->setQuery("ALTER IGNORE TABLE `#__adsmanager_ads` ADD `date_modified` DATETIME;");
					try {
						$result = $db->query();
					} catch(Exception $e) {
						
					}
					
					$db->setQuery("UPDATE `#__adsmanager_ads` SET `date_modified` = `date_created`");
					try {
						$result = $db->query();
					} catch(Exception $e) {
						
					}
					
					$obj = new stdClass();
					$obj->id = 1;
					$obj->version = 3;
					$db->updateObject('#__adsmanager_config', $obj,'id');
					
					$config->version = 3;
					$continue = true;
					break;
				default:
                    
					$db->setQuery("SELECT id,images FROM `#__adsmanager_ads`");
					$ads = $db->loadObjectList("id");
					
					$imagefiles = scandir(JPATH_ROOT."/images/com_adsmanager/ads/");
					$lists = array();
					sort($imagefiles);
					foreach($imagefiles as $imagefile) {
						if (preg_match("/([0-9]*)[a-z ]*_t.jpg/",$imagefile,$matches)) {
							$id = $matches[1];
							if (!isset($lists[$id]))
								$lists[$id] = array();
							$newimg = new stdClass();
							$newimg->index = count($lists[$id])+1;
							$newimg->image = str_replace('_t.jpg','.jpg',$imagefile);
							$newimg->thumbnail = $imagefile;
							$newimg->medium = str_replace('_t.jpg','.jpg',$imagefile);
							$lists[$id][] = $newimg;
						}
					}
					
					foreach($lists as $id => $list) {
						$obj = new stdClass();
						$obj->id = $id;
						$obj->images = json_encode($list);
						$ret = $db->updateObject('#__adsmanager_ads', $obj,'id');
					}
					
					$app = JFactory::getApplication();
					$db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$app->getCfg('db')."' AND TABLE_NAME = '".$db->getPrefix()."adsmanager_fieldgmap'");
					$count = $db->loadResult();
					if ($count > 0) {
						$db->setQuery("SELECT fieldid,name FROM #__adsmanager_fields WHERE type='gmap'");
						$fields = $db->loadObjectList("fieldid");
					
						foreach($fields as $field) {
							$query = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."_lat` FLOAT default NULL;";
							$db->setQuery($query);
							try {
								$result = $db->query();
							} catch(Exception $e) {
								
							}
					
							$query = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."_lng` FLOAT default NULL;";
							$db->setQuery($query);
							try {
								$result = $db->query();
							} catch(Exception $e) {
								
							}
						}
					
						$db->setQuery("SELECT * FROM #__adsmanager_fieldgmap");
						$list = $db->loadObjectList();
						$fields = array();
						if ($list != null) {
							foreach($list as $item) {
								$name = $fields[$item->fieldid];
								$obj = new stdClass();
								$obj->id = $item->contentid;
								$lat = $name."_lat";
								$obj->$lat = $item->lat;
								$lng = $name."_lng";
								$obj->$lng = $item->lng;
								$db->updateObject('#__adsmanager_ads', $obj,'id');
							}
						}
						$db->setQuery("DROP TABLE #__adsmanager_fieldgmap");
						try {
							$result = $db->query();
						} catch(Exception $e) {
							
						}
					}
					
					$app = JFactory::getApplication();
					$db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$app->getCfg('db')."' AND TABLE_NAME = '".$db->getPrefix()."adsmanager_youtube'");
					$count = $db->loadResult();
					if ($count > 0) {
						$db->setQuery("SELECT fieldid,name FROM #__adsmanager_fields WHERE type='youtube'");
						$fields = $db->loadObjectList("fieldid");
					
						foreach($fields as $field) {
							$query = "ALTER IGNORE TABLE `#__adsmanager_ads` ADD `".$field->name."` TEXT default NULL;";
							$db->setQuery($query);
							try {
								$result = $db->query();
							} catch(Exception $e) {
								
							}
						}
					
						$db->setQuery("SELECT * FROM #__adsmanager_youtube");
						$list = $db->loadObjectList();
						if ($list != null) {
							foreach($list as $item) {
								$name = @$fields[$item->fieldid]->name;
								if ($name != null) {
									$obj = new stdClass();
									$obj->id = $item->contentid;
									$obj->$name = $item->key;
									$db->updateObject('#__adsmanager_ads', $obj,'id');
								}
							}
						}
						$db->setQuery("DROP TABLE #__adsmanager_youtube");
						try {
							$result = $db->query();
						} catch(Exception $e) {
							
						}
					}
					
					$obj = new stdClass();
					$obj->id = 1;
					$obj->version = 2;
					$db->updateObject('#__adsmanager_config', $obj,'id');
					
					$config->version = 2;
					$continue = true;
			}	
		}
	}
	?>
	<center>
	<table width="100%" border="0">
	   <tr>
	      <td>
	      Thank you for using AdsManager (joomprod.com)<br/>
		 <p>
			<em>support@juloa.com</em>
		 </p>
	      </td>
	      <td>
	         <p>
	            <br>
	            <br>
	            <br>
	         </p>
	      </td>
	   </tr>
	</table>
	</center>
	
<?php 

	if(version_compare(JVERSION, '2.5', '>=') && version_compare(JVERSION, '3.0', '<') ) {
		JError::$legacy = $tmp_legacy;
	}

	return true;
	}
	
	/**
	 * Update XML files only for Joomla1.5
	 */
	public function updateXmlJoomla15($parent) {
		$src = $parent->getPath('source');
        
		$db = JFactory::getDbo();
		
		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();
		
		$src = str_replace('backend','',$src);

		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Install the module
					if(empty($folder)) $folder = 'site';
					$path = "$src/modules/$folder/$module";
					if(!is_dir($path)) {
						$path = "$src/modules/$folder/mod_$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/mod_$module";
					}
					if(!is_dir($path)) continue;
					if(file_exists("$path/mod_".$module."_15.xml")) {
                        JFile::copy("$path/mod_".$module."_15.xml","$path/mod_".$module.".xml");
					}
				}
			}
		}
		
		
		
		// Plugins installation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
                if(count($plugins)) foreach($plugins as $plugin => $published) {
					$path = "$src/plugins/$folder/$plugin";
					//echo $path;
					if(!is_dir($path)) {
						$path = "$src/plugins/$folder/plg_$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/plg_$plugin";
					}
					if(!is_dir($path)) continue;
					if(file_exists("$path/".$plugin."_15.xml")) {
						JFile::copy("$path/".$plugin."_15.xml","$path/".$plugin.".xml");
					}
					
				}
			}
		}
	}

	/**
	 * Installs subextensions (modules, plugins) bundled with the main extension
	 * 
	 * @param JInstaller $parent 
	 * @return JObject The subextension installation status
	 */
	private function _installSubextensions($parent)
	{
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}
		
		$db = JFactory::getDbo();
		
		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();
		$status->adsmanagerfields = array();
		
		$src = str_replace('backend','',$src);
		
		// Modules installation
		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Install the module
					if(empty($folder)) $folder = 'site';
					$path = "$src/modules/$folder/$module";
					if(!is_dir($path)) {
						$path = "$src/modules/$folder/mod_$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/mod_$module";
					}
					if(!is_dir($path)) continue;
					
					// Was the module already installed?
					if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
						$sql = $db->getQuery(true)
						->select('COUNT(*)')
						->from('#__modules')
						->where($db->qn('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($sql);
						$count = $db->loadResult();
					} else {
						$count = 1;
					}
					$installer = new JInstaller;
					$result = $installer->install($path);
					$status->modules[] = array(
						'name'=>'mod_'.$module,
						'client'=>$folder,
						'result'=>$result
					);
					// Modify where it's published and its published state
					if(!$count) {
						// A. Position and state
						list($modulePosition, $modulePublished) = $modulePreferences;
						if($modulePosition == 'cpanel') {
							$modulePosition = 'icon';
						}
						if(version_compare(JVERSION, '3.0.0', 'ge')) {
							if ($modulePosition == "left") {
								$modulePosition = 'position-7';
							}
						}
						
						$sql = $db->getQuery(true)
							->update($db->qn('#__modules'))
							->set($db->qn('position').' = '.$db->q($modulePosition))
							->where($db->qn('module').' = '.$db->q('mod_'.$module));
						if($modulePublished) {
							$sql->set($db->qn('published').' = '.$db->q('1'));
						}
						$db->setQuery($sql);
						$db->query();
						
						// B. Change the ordering of back-end modules to 1 + max ordering
						if($folder == 'admin') {
							$query = $db->getQuery(true);
							$query->select('MAX('.$db->qn('ordering').')')
								->from($db->qn('#__modules'))
								->where($db->qn('position').'='.$db->q($modulePosition));
							$db->setQuery($query);
							$position = $db->loadResult();
							$position++;

							$query = $db->getQuery(true);
							$query->update($db->qn('#__modules'))
								->set($db->qn('ordering').' = '.$db->q($position))
								->where($db->qn('module').' = '.$db->q('mod_'.$module));
							$db->setQuery($query);
							$db->query();
						}
						
						// C. Link to all pages
						$query = $db->getQuery(true);
						$query->select('id')->from($db->qn('#__modules'))
							->where($db->qn('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($query);
						$moduleid = $db->loadResult();

						$query = $db->getQuery(true);
						$query->select('*')->from($db->qn('#__modules_menu'))
							->where($db->qn('moduleid').' = '.$db->q($moduleid));
						$db->setQuery($query);
						$assignments = $db->loadObjectList();
						$isAssigned = !empty($assignments);
						if(!$isAssigned) {
							$o = (object)array(
								'moduleid'	=> $moduleid,
								'menuid'	=> 0
							);
							$db->insertObject('#__modules_menu', $o);
						}
					}
				}
			}
		}
		
        if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$this->installation_queue['plugins']['sh404sefextplugins'] = array(
                                                                        'sh404sefextplugincom_adsmanager' => 1
                                                                    );
		} else {
			$this->installation_queue['plugins']['sh404sefextplugins'] = array(
                                                                        'com_adsmanager' => 1
                                                                    );
            unset($this->installation_queue['plugins']['xmap']);
		}
        
		// Plugins installation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) foreach($plugins as $plugin => $published) {
					$path = "$src/plugins/$folder/$plugin";
					
					if(!is_dir($path)) {
						$path = "$src/plugins/$folder/plg_$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/plg_$plugin";
					}
					if(!is_dir($path)) continue;

					// Was the plugin already installed?
					if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
						$query = $db->getQuery(true)
							->select('COUNT(*)')
							->from($db->qn('#__extensions'))
							->where($db->qn('element').' = '.$db->q($plugin))
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$count = $db->loadResult();
					} else {
						$count = 1;
					}
					
					
					$installer = new JInstaller;
					$result = $installer->install($path);
					
					$status->plugins[] = array('name'=>'plg_'.$plugin,'group'=>$folder, 'result'=>$result);

					if($published && !$count) {
						$query = $db->getQuery(true)
							->update($db->qn('#__extensions'))
							->set($db->qn('enabled').' = '.$db->q('1'))
							->where($db->qn('element').' = '.$db->q($plugin))
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}

		// External Plugins installation
		if(count($this->installation_queue['adsmanagerfields'])) {
			
			$path = JPATH_SITE."/images/com_adsmanager/plugins";	
			if( file_exists ($path)) {		
                foreach($this->installation_queue['adsmanagerfields'] as $folder => $plugins) {
 					if(count($plugins)) foreach($plugins as $plugin) {
						$destPath = $path."/".$folder;
						$sourcePath = $src."/adsmanagerfields/".$folder;
						
						if (is_dir($destPath)){
							//echo "ok";
							$this->rmdir_recurse($destPath);
							//if (is_dir($destPath))
								//echo "ok";
						}
                        
						@mkdir($path);
                        
						$this->recurse_copy($sourcePath,$destPath);
                        
                        if(file_exists($destPath.'/plug.php')) {
                            include_once($destPath.'/plug.php');
                            if(isset($plugins[$folder]))
                                   $plugins[$folder]->install();
                        }
					} 
					$status->adsmanagerfields[] = array('name'=>$folder,'group'=>'','result'=>'Installed');
				}
			}
		}
        
		return $status;
	}
	
	function rmdir_recurse($path,$onlycontent = false) {
		if (is_dir($path)){
			$path= rtrim($path, '/').'/';
			$handle = opendir($path);
			for (;false !== ($file = readdir($handle));)
				if($file != "." and $file != ".." ) {
				$fullpath= $path.$file;
				if( is_dir($fullpath) ) {
					self::rmdir_recurse($fullpath);
				} else {
					@unlink($fullpath);
				}
			}
			closedir($handle);
			if  ($onlycontent == false)
				rmdir($path);
		}
	}
	
	function recurse_copy($src,$dst) {
		$dir = opendir($src);
		if ($dir == false) {
			return false;
		}
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
    
	/**
	 * Uninstalls subextensions (modules, plugins) bundled with the main extension
	 * 
	 * @param JInstaller $parent 
	 * @return JObject The subextension uninstallation status
	 */
	private function _uninstallSubextensions($parent)
	{
		jimport('joomla.installer.installer');
		
		$db = JFactory::getDBO();
		
		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}

		// Modules uninstallation
		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Find the module ID
					$sql = $db->getQuery(true)
						->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('element').' = '.$db->q('mod_'.$module))
						->where($db->qn('type').' = '.$db->q('module'));
					$db->setQuery($sql);
					$id = $db->loadResult();
					// Uninstall the module
					if($id) {
						$installer = new JInstaller;
						$result = $installer->uninstall('module',$id,1);
						$status->modules[] = array(
							'name'=>'mod_'.$module,
							'client'=>$folder,
							'result'=>$result
						);
					}
				}
			}
		}

		// Plugins uninstallation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) foreach($plugins as $plugin => $published) {
					$sql = $db->getQuery(true)
						->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('type').' = '.$db->q('plugin'))
						->where($db->qn('element').' = '.$db->q($plugin))
						->where($db->qn('folder').' = '.$db->q($folder));
					$db->setQuery($sql);

					$id = $db->loadResult();
					if($id)
					{
						$installer = new JInstaller;
						$result = $installer->uninstall('plugin',$id,1);
						$status->plugins[] = array(
							'name'=>'plg_'.$plugin,
							'group'=>$folder,
							'result'=>$result
						);
					}			
				}
			}
		}
		
		return $status;
	}
	
	/**
	 * Removes obsolete files and folders
	 * 
	 * @param array $adsmanagerRemoveFiles 
	 */
	private function _removeObsoleteFilesAndFolders($adsmanagerRemoveFiles)
	{
		// Remove files
		jimport('joomla.filesystem.file');
		if(!empty($adsmanagerRemoveFiles['files'])) foreach($adsmanagerRemoveFiles['files'] as $file) {
			$f = JPATH_ROOT.'/'.$file;
			if(!JFile::exists($f)) continue;
			JFile::delete($f);
		}

		// Remove folders
		jimport('joomla.filesystem.file');
		if(!empty($adsmanagerRemoveFiles['folders'])) foreach($adsmanagerRemoveFiles['folders'] as $folder) {
			$f = JPATH_ROOT.'/'.$folder;
			if(!JFolder::exists($f)) continue;
			JFolder::delete($f);
		}
	}
	
	/**
	 * Remove the update site specification from Joomla! – we no longer support
	 * that misbehaving crap, thank you very much...
	 */
	private function _killUpdateSite()
	{
		// Get some info on all the stuff we've gotta delete
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select(array(
				$db->qn('s').'.'.$db->qn('update_site_id'),
				$db->qn('e').'.'.$db->qn('extension_id'),
				$db->qn('e').'.'.$db->qn('element'),
				$db->qn('s').'.'.$db->qn('location'),
			))
			->from($db->qn('#__update_sites').' AS '.$db->qn('s'))
			->join('INNER',$db->qn('#__update_sites_extensions').' AS '.$db->qn('se').' ON('.
				$db->qn('se').'.'.$db->qn('update_site_id').' = '.
				$db->qn('s').'.'.$db->qn('update_site_id')
				.')')
			->join('INNER',$db->qn('#__extensions').' AS '.$db->qn('e').' ON('.
				$db->qn('e').'.'.$db->qn('extension_id').' = '.
				$db->qn('se').'.'.$db->qn('extension_id')
				.')')
			->where($db->qn('s').'.'.$db->qn('type').' = '.$db->q('extension'))
			->where($db->qn('e').'.'.$db->qn('type').' = '.$db->q('component'))
			->where($db->qn('e').'.'.$db->qn('element').' = '.$db->q($this->_adsmanager_extension))
		;
		$db->setQuery($query);
		$oResult = $db->loadObject();
		
		// If no record is found, do nothing. We've already killed the monster!
		if(is_null($oResult)) return;
		
		// Delete the #__update_sites record
		$query = $db->getQuery(true)
			->delete($db->qn('#__update_sites'))
			->where($db->qn('update_site_id').' = '.$db->q($oResult->update_site_id));
		$db->setQuery($query);
		try {
			$db->query();
		} catch (Exception $exc) {
			// If the query fails, don't sweat about it
		}

		// Delete the #__update_sites_extensions record
		$query = $db->getQuery(true)
			->delete($db->qn('#__update_sites_extensions'))
			->where($db->qn('update_site_id').' = '.$db->q($oResult->update_site_id));
		$db->setQuery($query);
		try {
			$db->query();
		} catch (Exception $exc) {
			// If the query fails, don't sweat about it
		}
		
		// Delete the #__updates records
		$query = $db->getQuery(true)
			->delete($db->qn('#__updates'))
			->where($db->qn('update_site_id').' = '.$db->q($oResult->update_site_id));
		$db->setQuery($query);
		try {
			$db->query();
		} catch (Exception $exc) {
			// If the query fails, don't sweat about it
		}
	}
}

// For Joomla 1.5
global $parentadsmanager;
$parentadsmanager = $this->parent;
// For Joomla 1.5
if(!function_exists('com_install')){
	function com_install(){
		global $parentadsmanager;
		$class = new Com_AdsmanagerInstallerScript();
		$class->updateXmlJoomla15($parentadsmanager);
		$class->postflight("update",$parentadsmanager);
		return $class->install(null);
	}
}
