<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

/*
 * laminas-router route configuration
 *
 * @see https://docs.laminas.dev/laminas-router/
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/', \Application\Handler\HomePageHandler::class, 'home');
    $app->get('/faq', \Application\Handler\FaqPageHandler::class, 'faq');
    $app->get('/activity', \Application\Handler\ActivityHandler::class, 'activity.index');
    $app->get('/activity/user/:user', \Application\Handler\ActivityHandler::class, 'activity.user');
    $app->get('/activity/movie/:movie', \Application\Handler\ActivityHandler::class, 'activity.movie');
    $app->get('/movie', \Application\Handler\Movie\IndexHandler::class, 'movie.index');
    $app->get('/movie/add', \Application\Handler\Movie\AddHandler::class, 'movie.add');
    $app->get('/movie/view/:id', \Application\Handler\Movie\ViewHandler::class, 'movie.view');
    $app->get('/user', \Application\Handler\User\IndexHandler::class, 'user.index');
    $app->get('/user/new', \Application\Handler\User\NewHandler::class, 'user.new');
    $app->post('/user/new', \Application\Handler\User\NewHandler::class, 'user.new.post');
    $app->get('/user/view/:id', \Application\Handler\User\ViewHandler::class, 'user.view');
    $app->get('/user/login', \Application\Handler\User\LoginHandler::class, 'user.login');
    $app->post('/user/login', \Application\Handler\User\LoginHandler::class, 'user.login.post');
    $app->get('/user/logout', \Application\Handler\User\LogoutHandler::class, 'user.logout');
    $app->get('/status/list/movies/:movies', \Application\Handler\Status\ListHandler::class, 'status.list');
    $app->get('/status/:movie', \Application\Handler\Status\IndexHandler::class, 'status');
    $app->get('/status/:movie/:rating', \Application\Handler\Status\IndexHandler::class, 'status.rating');
    $app->get('/status/graph', \Application\Handler\Status\GraphHandler::class, 'status.graph');
    $app->get('/status/graph/:user', \Application\Handler\Status\GraphHandler::class, 'status.graph.user');
    $app->get('/about', \Application\Handler\AboutPageHandler::class, 'about');
    $app->get('/css/gravatar.css', \Application\Handler\GravatarPageHandler::class, 'css.gravatar');
    $app->get('/js/mqueue-user.js', \Application\Handler\JsMqueueUserHandler::class, 'js.mqueue-user');
    $app->get('/js/remote.js', \Application\Handler\JsRemoteHandler::class, 'js.remote');
};
