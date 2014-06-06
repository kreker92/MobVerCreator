<?php
/**
 * Popup page
 * Displays the Sourcerer Code Helper
 *
 * @package         Sourcerer
 * @version         4.3.3
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
if ($user->get('guest')
	|| (
		!$user->authorise('core.edit', 'com_content')
		&& !$user->authorise('core.create', $asset)
	)
)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
$parameters = NNParameters::getInstance();
$params = $parameters->getPluginParams('sourcerer');

if (JFactory::getApplication()->isSite())
{
	if (!$params->enable_frontend)
	{
		JError::raiseError(403, JText::_("ALERTNOTAUTH"));
	}
}

$class = new plgButtonSourcererPopup;
$class->render($params);

class plgButtonSourcererPopup
{
	function render(&$params)
	{
		jimport('joomla.filesystem.file');

		// Load plugin language
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_nnframework', JPATH_ADMINISTRATOR);
		$lang->load('plg_editors-xtd_sourcerer', JPATH_ADMINISTRATOR);
		$lang->load('plg_system_sourcerer', JPATH_ADMINISTRATOR);

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		JHtml::script('nnframework/script.min.js', false, true);

		$script = "
			var sourcerer_syntax_word = '" . $params->syntax_word . "';
			var sourcerer_editorname = '" . JFactory::getApplication()->input->getString('name', 'text') . "';
			var sourcerer_default_addsourcetags = " . (int) $params->addsourcetags . ";
			var sourcerer_root = '" . JURI::root(true) . "';
		";
		JFactory::getDocument()->addScriptDeclaration($script);
		JHtml::stylesheet('sourcerer/popup.min.css', false, true);

		if (version_compare(JVERSION, '3.2', 'l'))
		{
			JHtml::script('sourcerer/script.31.min.js', false, true);
		}
		else
		{
			JHtml::script('sourcerer/script.min.js', false, true);
		}

		$params->code = '<!-- You can place html anywhere within the source tags --><br /><br /><br /><script language=&quot;javascript&quot; type=&quot;text/javascript&quot;><br />    // You can place JavaScript like this<br />    <br /></script><br /><?php<br />    // You can place PHP like this<br />    <br />?>';
		$params->code = str_replace('<br />', "\n", $params->code);

		echo $this->getHTML($params);
	}

	function getHTML(&$params)
	{
		JFactory::getApplication()->setUserState('editor.source.syntax', 'php');

		$editor = JEditor::getInstance('codemirror');

		ob_start();
		?>
		<div class="header">
			<div class="container-fluid">
				<h1 CLASS="page-title"><?php echo JText::_('SRC_SOURCERER_CODE_HELPER'); ?></h1>
			</div>
		</div>

		<div class="subhead-collapse">
			<div class="subhead">
				<div class="container-fluid">
					<div id="container-collapse" class="container-collapse"></div>
					<div class="row-fluid">
						<div class="span12">
							<div class="btn-toolbar" id="toolbar">
								<div class="btn-group" id="toolbar-apply">
									<button href="#" onclick="sourcerer_insertText();window.parent.SqueezeBox.close();" class="btn btn-small btn-success">
										<span class="icon-apply icon-white"></span> <?php echo JText::_('SRC_INSERT') ?>
									</button>
								</div>
								<div class="btn-group">
									<button class="btn btn-small hasTip" id="btn-sourcetags" onclick="sourcerer_toggleSourceTags();return false;" title="<?php echo JText::_('SRC_TOGGLE_SOURCE_TAGS_DESC'); ?>">
										<span class="icon-nonumber icon-src-sourcetags"></span> <?php echo JText::_('SRC_TOGGLE_SOURCE_TAGS') ?>
									</button>
								</div>
								<div class="btn-group">
									<button class="btn btn-small hasTip" id="btn-tagstyle" onclick="sourcerer_toggleTagStyle();return false;" title="<?php echo JText::_('SRC_TOGGLE_TAG_STYLE_DESC'); ?>">
										<span class="icon-nonumber icon-src-tagstyle"></span> <?php echo JText::_('SRC_TOGGLE_TAG_STYLE') ?>
									</button>
								</div>
								<div class="btn-group" id="toolbar-cancel">
									<button href="#" onclick="if ( confirm( '<?php echo JText::_('NN_ARE_YOU_SURE'); ?>' ) ) { window.parent.SqueezeBox.close(); }" class="btn btn-small">
										<span class="icon-cancel "></span> <?php echo JText::_('JCANCEL') ?>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid container-main">
			<form action="index.php" id="sourceForm" method="post">

				<div class="control-group form-inline">
				</div>

				<div class="well well-small src_editor">
					<?php echo $editor->display('source', $params->code, '100%', '100%', 10, 10, 0, null, null, null, array('linenumbers' => 1, 'tabmode' => 'shift')); ?>
				</div>

				<script type="text/javascript">
					sourcerer_init();
				</script>
			</form>
			<?php
			if (JFactory::getApplication()->isAdmin())
			{
				$user = JFactory::getUser();
				if ($user->authorise('core.admin', 1))
				{
					echo '<em>' . str_replace('<a ', '<a target="_blank" ', html_entity_decode(JText::_('SRC_SETTINGS'))) . '</em>';
				}
			}
			?>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
