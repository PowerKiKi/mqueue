<?php

namespace Application\Handler\User;

use Application\Form\Login;
use Application\Handler\PageHandler;
use Application\Model\User;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new Login();

        if ($request->getMethod() === 'POST') {

            $form->setData($request->getParsedBody());
            if ($form->isValid()) {
                $values = $form->getData();

                /** @var null|User $user */
                $user = _em()->getRepository(User::class)->getByEmailAndPassword($values['email'], $values['password']);
                if ($user) {
                    User::setCurrent($user);
                    /** @var \Mezzio\Session\SessionInterface $session */
                    $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
                    $session->set('user', $user->id);

                    $this->flashMessages($request)->flash('notice', _tr('Logged in.'));

                    // If we have a valid referer to one page of ours (except login or logout), redirect to it
                    $referrer = $values['referrer'] ?? '';
                    $loginUrl = ($this->serverUrl)(($this->url)('user.login'));
                    $logoutUrl = ($this->serverUrl)(($this->url)('user.logout'));
                    if (str_starts_with($referrer, ($this->serverUrl)('/'))
                        && !str_starts_with($referrer, $loginUrl)
                        && !str_starts_with($referrer, $logoutUrl)) {

                        return new RedirectResponse($referrer);
                    }

                    return new RedirectResponse('/movie');
                }

                $this->flashMessages($request)->flash('error', _tr('Login failed.'));
                $currentUrl = (string) $request->getUri();

                return new RedirectResponse($currentUrl);
            }
        } else {
            $form->setData([
                'referrer' => $request->getServerParams()['HTTP_REFERER'] ?? '',
            ]);
        }

        $data = [
            'form' => $form,
        ];

        return new HtmlResponse($this->template->render('app::user/login', $data));
    }
}
