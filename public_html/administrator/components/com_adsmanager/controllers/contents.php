<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2012 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'tables');
jimport('joomla.application.component.controller');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class AdsmanagerControllerContents extends TController
{
	function __construct($config= array()) {
		parent::__construct($config);
		
		// Apply, Save & New
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}
	
	function init()
	{
		// Set the default view name from the Request
		$this->_view = $this->getView("admin",'html');

		// Push a model into the view
		$this->_model = $this->getModel( "content");
		if (!JError::isError( $this->_model )) {
			$this->_view->setModel( $this->_model, true );
		}
		
		$uri = JFactory::getURI();
		$baseurl = JURI::base()."../";

		$this->_view->assign("baseurl",$baseurl);
		$this->_view->assignRef("baseurl",$baseurl);
	}
	
	function display($cachable = false, $urlparams = false)
	{
		$this->init();
		
		$confmodel	  = $this->getModel("configuration");
		$catmodel	  = $this->getModel("category");
		$this->_view->setModel( $confmodel );
		$this->_view->setModel( $catmodel );
		
		$this->_view->setLayout("listcontents");
		$this->_view->display();
	}
	
	function edit()
	{
		$this->init();
		$confmodel	  = $this->getModel("configuration");
		$catmodel	  = $this->getModel("category");
		$usermodel	  = $this->getModel("user");
		$fieldmodel	  = $this->getModel("field");

		$this->_view->setModel( $confmodel );
		$this->_view->setModel( $catmodel );
		$this->_view->setModel( $usermodel );
		$this->_view->setModel( $fieldmodel );

		$this->_view->setLayout("editcontent");
		$this->_view->display();
	}
	
	function add()
	{
		$this->init();
		
		$confmodel	  = $this->getModel("configuration");
		$catmodel	  = $this->getModel("category");
		$usermodel	  = $this->getModel("user");
		$fieldmodel	  = $this->getModel("field");
		
		$this->_view->setModel( $confmodel );
		$this->_view->setModel( $catmodel );
		$this->_view->setModel( $usermodel );
		$this->_view->setModel( $fieldmodel );

		$this->_view->setLayout("editcontent");
		$this->_view->display();
	}
	
	function remove()
	{
		$app = JFactory::getApplication();
		
		$content = JTable::getInstance('contents', 'AdsmanagerTable');
		
		$ids = JRequest::getVar( 'cid', array(0));
		if (!is_array($ids)) {
			$table = array();
			$table[0] = $ids;
			$ids = $table;
		}
		
		$model = $this->getModel( "configuration");
		$conf = $model->getConfiguration();
		
		$model = $this->getModel( "field");
		$plugins = $model->getPlugins();
		
		foreach($ids as $id){
			$content->deleteContent($id,$conf,$plugins);
		}
		
		$cache = JFactory::getCache( 'com_adsmanager');
		$cache->clean();
		
		$app->redirect( 'index.php?option=com_adsmanager&c=contents', JText::_('ADSMANAGER_CONTENT_REMOVED'),'message' );
	}
	
	function unpublish()
	{
		$this->_changeState();
	}
	
	function publish()
	{
		$model = $this->getModel( "configuration");
		$conf = $model->getConfiguration();
		
		if ($conf->auto_publish == 0)
		{
			if ($conf->send_email_on_validation_to_user == 1) {	
				$cid = JRequest::getVar( 'cid', array(), '', 'array' );		
				foreach($cid as $id) {
					$model = $this->getModel( "content");
					$c = $model->getContent($id,false);
					$usermodel = $this->getModel("user");
					$user = $usermodel->getUser($c->userid);
					$model->sendMailToUser($conf->validation_subject,$conf->validation_text,$user,$c,$conf,"validation");
					$model->updateContentDate($id);
				}
			}
		}
		
		$this->_changeState();
	}
	
	function save($src=null, $orderingFilter = '', $ignore = '')
	{
		$app = JFactory::getApplication();
		
		$conf = JTable::getInstance('adsconfiguration', 'AdsmanagerTable');
		$conf->load(1);
		
		$contentid = JRequest::getInt('id', 0);
		
		// New or Update
		if ($contentid != 0)
			$isUpdateMode = 1;
		else
			$isUpdateMode = 0;
		
		$content = JTable::getInstance('contents', 'AdsmanagerTable');
		if ($contentid != 0) {
			$content->load($contentid);
			$previouspublished = $content->published;
		}

		$model = $this->getModel("configuration");
		$conf = $model->getConfiguration();
		
		$model = $this->getModel("field");
		$plugins = $model->getPlugins();
		
		// bind it to the table
		$content->bindContent(JRequest::get( 'post' ),JRequest::get( 'files' ),
							  $conf,$this->getModel("adsmanager"),$plugins);
							 	   
		if (function_exists('bindPaidSystemContent')) {
			bindPaidSystemContent($content,
								  JRequest::get( 'post' ),JRequest::get( 'files' ),
								  $conf,$this->getModel("adsmanager"));
		}
		
		$errors = $content->getErrors();
		if (count($errors) > 0) {
			return JError::raiseWarning( 500, $content->getError() );
		}
	
		$content->saveContent();
		
		if (($contentid != 0)&&($conf->auto_publish == 0)) {
			if (($previouspublished == 0)&&(JRequest::getInt('published', 1))) {
				$usermodel = $this->getModel("user");
				$user = $usermodel->getUser($content->userid);
				$cmodel = $this->getModel("content");
				$cmodel->sendMailToUser($conf->validation_subject,$conf->validation_text,$user,$content,$conf,"validation");
				$cmodel->updateContentDate($id);
			}
		}
		
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();
		
		$category = JRequest::getInt('category', 0);
		
		if ($category != 0) {
			$extra = "&catid=$category";
		} else {
			$extra= "";
		}
		
		$task = JRequest::getCmd('task');
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				$app->redirect( 'index.php?option=com_adsmanager&c=contents&task=edit&id='.$content->id, JText::_('ADSMANAGER_CONTENT_SAVED') ,'message');
				break;
		
			case 'save2new':
				$app->redirect( "index.php?option=com_adsmanager&c=contents{$extra}&task=add", JText::_('ADSMANAGER_CONTENT_SAVED'),'message' );
				break;
		
			default:
				$app->redirect( 'index.php?option=com_adsmanager&c=contents', JText::_('ADSMANAGER_CONTENT_SAVED') ,'message');
				break;
		}
	}
	
	function _changeState()
	{
		$app = JFactory::getApplication();

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid		= JRequest::getVar( 'cid', array(), '', 'array' );
		$publish	= ( $this->getTask() == 'publish' ? 1 : 0 );

		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1)
		{
			$action = $publish ? 'publish' : 'unpublish';
			JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
		}
		
		$model = $this->getModel( "adsmanager");
		$model->changeState("#__adsmanager_ads","id","published",$publish,$cid);
		
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();

		$app->redirect( 'index.php?option=com_adsmanager&c=contents' );
	}	
}
