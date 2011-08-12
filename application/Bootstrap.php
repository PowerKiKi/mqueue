<?php
require_once('debug.php');

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public static $translator = null;
	
	protected function _initAutoload()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default',
            'basePath'  => dirname(__FILE__),
		));
		return $autoloader;
	}

	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);

		$path = dirname(__FILE__) . '/views/helpers';
		$prefix = 'Default_View_Helper_';
		$view->addHelperPath($path, $prefix);

	}
	
	protected function _initNavigation()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		
		$navigation = new Zend_Navigation(array(
			array(
				'label' => $view->translate('Movies'),
				'controller'=>'movie',
				'route' => 'default',
				'pages' => array(
					array(
						'label' => $view->translate('Add movie'),
						'controller'=>'movie',
						'action' => 'add',
						'route' => 'default',
					),
					array(
						'label' => $view->translate('Import votes from IMDb'),
						'controller'=>'movie',
						'action' => 'import',
						'route' => 'default',
					),
				)
			),
			array(
				'label' => $view->translate('Activity'),
				'controller' => 'activity',				
				'route' => 'default',
			),
			array(
				'label' => $view->translate('Users'),
				'controller' => 'user',
				'route' => 'default',
			),
			array(
				'label' => $view->translate('FAQ'),
				'controller' => 'faq',
				'route' => 'default',
			),
		));
		
		$view->navigation($navigation);
	}
	
	protected function _initSession()
	{
		Zend_Session::setOptions(array('name' => 'mqueue'));
	}

	protected function _initLanguage()
	{
		$session = new Zend_Session_Namespace();
		
		// handle language switch
		if (isset($_GET['lang']))
		{
			$session->locale = $_GET['lang'];
		}

		if (isset($session->locale))
		{
			$locale = new Zend_Locale($session->locale);
		}
		else
		{
			$locale = new Zend_Locale(); // autodetect browser
		}
		Zend_Registry::set('Zend_Locale', $locale);

		$adapter = new Zend_Translate('gettext', APPLICATION_PATH . '/localization', $locale, array('scan' => Zend_Translate::LOCALE_FILENAME, 'disableNotices' => true));
		Zend_Registry::set('Zend_Translate', $adapter);
		Zend_Form::setDefaultTranslator($adapter);
		self::$translator = $adapter;
	}
	
	protected function _initPagination()
	{
		Zend_Paginator::setDefaultScrollingStyle('Elastic');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
	}
	
	protected function _initRoutes()
	{
		$front  = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();
		
		// A route for single id (typically view a single movie/user)
		$router->addRoute('singleid',
			new Zend_Controller_Router_Route(':controller/:action/:id', array('action' => 'view'))
		);
		
		// A route for activities
		$router->addRoute('activity',
			new Zend_Controller_Router_Route('activity/*', array('controller' => 'activity', 'action' => 'index'))
		);
		$router->addRoute('activityMovie',
			new Zend_Controller_Router_Route('activity/movie/:movie/*', array('controller' => 'activity', 'action' => 'index'))
		);
		$router->addRoute('activityUser',
			new Zend_Controller_Router_Route('activity/user/:user/*', array('controller' => 'activity', 'action' => 'index'))
		);
		
		// For backward compatibility with RSS readers we keep the old route
		$router->addRoute('activityOld',
			new Zend_Controller_Router_Route('activity/index/*', array('controller' => 'activity', 'action' => 'index'))
		);
		$router->addRoute('activityMovieOld',
			new Zend_Controller_Router_Route('activity/index/movie/:movie/*', array('controller' => 'activity', 'action' => 'index'))
		);
		$router->addRoute('activityUserOld',
			new Zend_Controller_Router_Route('activity/index/user/:user/*', array('controller' => 'activity', 'action' => 'index'))
		);
		
		// Routes to define and view statuses
		$router->addRoute('status',
			new Zend_Controller_Router_Route('status/:movie/:rating', array('controller' => 'status', 'action' => 'index'))
		);
		$router->addRoute('statusView',
			new Zend_Controller_Router_Route('status/:movie', array('controller' => 'status', 'action' => 'index'))
		);	
	}
	
	/**
	 * Add the Zend_Db_Adapter to the registry if we need to call it outside of the modules.
	 * @return Zend_Db_Adapter
	 */
	protected function _initMyDb()
	{
		$db = $this->getPluginResource('db')->getDbAdapter();
		Zend_Registry::set('db', $db);

		return $db;
	}
}

/**
 * Global shortcut method that returns localized strings 
 * 
 * @param string $msgId the original string to translate
 * @return string the translated string
 */
function _tr($msgId)
{
	return Bootstrap::$translator->translate($msgId);
}
