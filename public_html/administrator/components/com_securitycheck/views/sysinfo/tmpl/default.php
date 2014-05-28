<?php 

/*
* @ author Jose A. Luque
* @ Copyright (c) 2011 - Jose A. Luque
* @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted access');
JRequest::checkToken( 'get' ) or die( 'Invalid Token' );

// Add style declaration
$media_url = "media/com_securitycheck/stylesheets/cpanelui.css";
JHTML::stylesheet($media_url);

$bootstrap_css = "media/com_securitycheck/stylesheets/bootstrap.min.css";
JHTML::stylesheet($bootstrap_css);

JHTML::_( 'behavior.framework', true );
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="securitycheck-bootstrap">

	<div id="editcell">
		<div class="accordion-group">
	</div>
	
	<div>
		<span class="badge" style="background-color: #C993FF; padding: 10px 10px 10px 10px; float:right;"><?php echo JText::_( 'COM_SECURITYCHECK_SYSTEM_INFORMATION' ); ?></span>
	</div>

	<table class="table table-bordered table-striped">
		<tbody>
			<th class="sysinfo-global-header" colspan="2"><?php echo JText::_( 'COM_SECURITYCHECK_GLOBAL_CONFIGURATION' ); ?></th>
			<tr>
				<td><strong><?php echo JText::_( 'COM_SECURITYCHECK_SYSINFO_JOOMLAVERSION' ); ?></strong></td>
				<td><?php echo $this->system_info['version']; ?></td>
			</tr>
			<tr>
				<td><strong><?php echo JText::_( 'COM_SECURITYCHECK_SYSINFO_JOOMLAPLATFORM' ); ?></strong></td>
				<td><?php echo $this->system_info['platform']; ?></td>
			</tr>
			<th class="sysinfo-mysql-header" colspan="2"><?php echo JText::_( 'COM_SECURITYCHECK_MYSQL_CONFIGURATION' ); ?></th>
			<tr>
				<td><strong><?php echo JText::_( 'COM_SECURITYCHECK_SYSINFO_MAX_ALLOWED_PACKET' ); ?></strong></td>
				<td><?php echo $this->system_info['max_allowed_packet']; ?>M</td>
			</tr>
			<th class="sysinfo-php-header" colspan="2"><?php echo JText::_( 'COM_SECURITYCHECK_PHP_CONFIGURATION' ); ?></th>
			<tr>
				<td><strong><?php echo JText::_( 'COM_SECURITYCHECK_SYSINFO_PHPVERSION' ); ?></strong></td>
				<td><?php echo $this->system_info['phpversion']; ?></td>
			</tr>
			<tr>
				<td><strong><?php echo JText::_( 'COM_SECURITYCHECK_SYSINFO_MEMORY_LIMIT' ); ?></strong></td>
				<td><?php echo $this->system_info['memory_limit']; ?></td>
			</tr>
		</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_securitycheck" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="1" />
</form>