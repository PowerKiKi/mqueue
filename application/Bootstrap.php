<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public static $translator = null;

    protected function _initAutoload(): void
    {
        // Add our own action helpers
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/mQueue/Controller/ActionHelper', 'mQueue\\Controller\\ActionHelper\\');
    }

    protected function _initDoctype(): void
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        // Enable our own View Helpers
        $view->addHelperPath(APPLICATION_PATH . '/mQueue/View/Helper', 'mQueue\\View\\Helper');
    }

    protected function _initNavigation(): void
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $navigation = new Zend_Navigation([
            [
                'label' => $view->translate('Movies'),
                'controller' => 'movie',
                'route' => 'default',
                'pages' => [
                    [
                        'label' => $view->translate('Add movie'),
                        'controller' => 'movie',
                        'action' => 'add',
                        'route' => 'default',
                    ],
                    [
                        'label' => $view->translate('Import votes from IMDb'),
                        'controller' => 'movie',
                        'action' => 'import',
                        'route' => 'default',
                    ],
                ],
            ],
            [
                'label' => $view->translate('Activity'),
                'controller' => 'activity',
                'route' => 'default',
            ],
            [
                'label' => $view->translate('Users'),
                'controller' => 'user',
                'route' => 'default',
            ],
            [
                'label' => $view->translate('FAQ'),
                'controller' => 'faq',
                'route' => 'default',
            ],
        ]);

        $view->navigation($navigation);
    }

    protected function _initSession(): void
    {
        // We need to receive cookie from any third party sites, so we inject SameSite
        // into path, because PHP 7.2 does not have a way to do it properly
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params(
            $cookieParams['lifetime'],
            $cookieParams['path'] . '; SameSite=None',
            $cookieParams['domain'],
            true,
            true
        );

        Zend_Session::setOptions(['name' => 'mqueue']);

        if (!Zend_Session::sessionExists()) {
            Zend_Session::rememberMe(1 * 60 * 60 * 24 * 31 * 12); // Cookie for 1 year
        }
    }

    protected function _initLanguage(): void
    {
        $session = new Zend_Session_Namespace();

        // handle language switch
        if (isset($_GET['lang'])) {
            $session->locale = $_GET['lang'];
        }

        if (isset($session->locale)) {
            $locale = new Zend_Locale($session->locale);
        } else {
            $locale = new Zend_Locale(); // autodetect browser
        }
        Zend_Registry::set(Zend_Locale::class, $locale);

        $adapter = new Zend_Translate('gettext', APPLICATION_PATH . '/localization', $locale, ['scan' => Zend_Translate::LOCALE_FILENAME, 'disableNotices' => true]);
        Zend_Registry::set(Zend_Translate::class, $adapter);
        Zend_Form::setDefaultTranslator($adapter);
        self::$translator = $adapter;
    }

    protected function _initPagination(): void
    {
        Zend_Paginator::setDefaultScrollingStyle('Elastic');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
    }

    protected function _initRoutes(): void
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        // Required for unit tests
        $router->addDefaultRoutes();

        // A route for single id (typically view a single movie/user)
        $router->addRoute('singleid', new Zend_Controller_Router_Route(':controller/:action/:id', ['action' => 'view']));

        // A route for activities
        $router->addRoute('activity', new Zend_Controller_Router_Route('activity/*', ['controller' => 'activity', 'action' => 'index']));
        $router->addRoute('activityMovie', new Zend_Controller_Router_Route('activity/movie/:movie/*', ['controller' => 'activity', 'action' => 'index']));
        $router->addRoute('activityUser', new Zend_Controller_Router_Route('activity/user/:user/*', ['controller' => 'activity', 'action' => 'index']));

        // For backward compatibility with RSS readers we keep the old route
        $router->addRoute('activityOld', new Zend_Controller_Router_Route('activity/index/*', ['controller' => 'activity', 'action' => 'index']));
        $router->addRoute('activityMovieOld', new Zend_Controller_Router_Route('activity/index/movie/:movie/*', ['controller' => 'activity', 'action' => 'index']));
        $router->addRoute('activityUserOld', new Zend_Controller_Router_Route('activity/index/user/:user/*', ['controller' => 'activity', 'action' => 'index']));

        // Routes to define and view statuses
        $router->addRoute('status', new Zend_Controller_Router_Route_Regex('status/(\d+)/(\d)', ['controller' => 'status', 'action' => 'index'], [1 => 'movie', 2 => 'rating'], 'status/%s/%s'));
        $router->addRoute('statusView', new Zend_Controller_Router_Route_Regex('status/(\d+)', ['controller' => 'status', 'action' => 'index'], [1 => 'movie'], 'status/%s'));
    }

    /**
     * Add the Zend_Db_Adapter to the registry if we need to call it outside of the modules.
     *
     * @return Zend_Db_Adapter_Abstract
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
 *
 * @return string the translated string
 */
function _tr($msgId)
{
    return Bootstrap::$translator->translate($msgId);
}
