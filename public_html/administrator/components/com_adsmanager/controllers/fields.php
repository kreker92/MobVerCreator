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
class AdsmanagerControllerFields extends TController
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
		$this->_model = $this->getModel( "field");
		if (!JError::isError( $this->_model )) {
			$this->_view->setModel( $this->_model, true );
		}
	}
	
	function display($cachable = false, $urlparams = false)
	{
		$this->init();
		
		$this->_view->setLayout("listfields");
		$this->_view->display();
	}
	
	function edit()
	{
		$this->init();
		$catmodel		= $this->getModel("category");
		$positionmodel	= $this->getModel("position");
		$columnmodel	= $this->getModel("column");
		$this->_view->setModel( $catmodel );
		$this->_view->setModel( $positionmodel );
		$this->_view->setModel( $columnmodel );
		
		$this->_view->setLayout("editfield");
		$this->_view->display();
	}
	
	function add()
	{
		$this->init();
		$catmodel		= $this->getModel("category");
		$positionmodel	= $this->getModel("position");
		$columnmodel	= $this->getModel("column");
		$this->_view->setModel( $catmodel );
		$this->_view->setModel( $positionmodel );
		$this->_view->setModel( $columnmodel );
		
		$this->_view->setLayout("editfield");
		$this->_view->display();
	}
	
	function save()
	{
		$app = JFactory::getApplication();
		
		$field = JTable::getInstance('field', 'AdsmanagerTable');

		// bind it to the table
		if (!$field -> bind(JRequest::get( 'post' ))) {
			return JError::raiseWarning( 500, $field->getError() );
		}
		// store it in the db
		if (!$field -> store()) {
			return JError::raiseWarning( 500, $field->getError() );
		}	
		
		$this->_model = $this->getModel( "field");
		$plugins = $this->_model->getPlugins();
		$field->saveContent(JRequest::get( 'post' ),$plugins);
	
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();
	
		// Redirect the user and adjust session state based on the chosen task.
		$task = JRequest::getCmd('task');
		switch ($task)
		{
			case 'apply':
				$app->redirect( 'index.php?option=com_adsmanager&c=fields&task=edit&cid='.$field->fieldid, JText::_('ADSMANAGER_FIELD_SAVED'),'message' );
				break;
		
			case 'save2new':
				$app->redirect( 'index.php?option=com_adsmanager&c=fields&task=add', JText::_('ADSMANAGER_FIELD_SAVED'),'message' );
				break;
		
			default:
				$app->redirect( 'index.php?option=com_adsmanager&c=fields', JText::_('ADSMANAGER_FIELD_SAVED'),'message' );
			break;
		}
		
	}
	
	function remove()
	{
		$app = JFactory::getApplication();
		
		$field = JTable::getInstance('field', 'AdsmanagerTable');
		
		$ids = JRequest::getVar( 'cid', array(0));
		if (!is_array($ids)) {
			$table = array();
			$table[0] = $ids;
			$ids = $table;
		}
		
		foreach($ids as $id){
			$field->deleteContent($id);
		}
		
		$cache = JFactory::getCache( 'com_adsmanager');
		$cache->clean();
		
		$app->redirect( 'index.php?option=com_adsmanager&c=fields', JText::_('ADSMANAGER_FIELD_REMOVED') ,'message');
	}
	
	function unpublish()
	{
		$this->_changeState();
	}
	
	function publish()
	{
		$this->_changeState();
	}
	
	function orderdown()
	{
		$this->_changeOrder(1);
	}
	
	function orderup()
	{
		$this->_changeOrder(-1);
	}
	
	function _changeOrder($inc)
	{
		$app = JFactory::getApplication();
		
		$cid		= JRequest::getVar( 'cid', array(), '', 'array' );
		
		if ($cid[0]) {
			$id = $cid[0];
			$row = JTable::getInstance('field', 'AdsmanagerTable');
			$row->load( $id );
			$row->move( $inc, "1" );
		}
		
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();

		$app->redirect( 'index.php?option=com_adsmanager&c=fields' );
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
		$model->changeState("#__adsmanager_fields","fieldid","published",$publish,$cid);
		
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();
		
		$app->redirect( 'index.php?option=com_adsmanager&c=fields' );
	}	
	
	function saveorder()
	{
		$app = JFactory::getApplication();

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
	
		// Initialize variables
		$cid 	= JRequest::getVar('cid', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));
		$total		= count( $cid );
		$order 		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));
	
		$row = JTable::getInstance('field', 'AdsmanagerTable');

		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
			}
		}
	
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();
		
		// Check if i'm using an AJAX call, in this case there is no need to redirect
		$format = JRequest::getVar('format','');
		$status="";
		if ($format == 'json')
		{
			echo json_encode($status);
		
			return;
		}
		
		$msg = JText::_('ADSMANAGER_ORDERING_SAVED');
		$app->redirect( 'index.php?option=com_adsmanager&c=fields', $msg,'message' );
	}
	
	function required()
	{
		$app = JFactory::getApplication();

		$cid		= JRequest::getVar( 'cid', array(), '', 'array' );
		$required	= JRequest::getInt( 'required', 0 );

		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1)
		{
			$action = $required ? 'required' : 'unrequired';
			JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
		}
		
		$model = $this->getModel( "adsmanager");
		$model->changeState("#__adsmanager_fields","fieldid","required",$required,$cid);
		
		$cache = JFactory::getCache('com_adsmanager');
		$cache->clean();
		
		$app->redirect( 'index.php?option=com_adsmanager&c=fields' );
	}
}
