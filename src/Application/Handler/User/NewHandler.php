<?php

namespace Application\Handler\User;

use Application\Handler\PageHandler;
use Application\Model\User;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NewHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \Mezzio\Session\SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $form = new \Application\Form\User();

        if ($request->getMethod() === 'POST') {
            $form->setData($request->getParsedBody());
            if ($form->isValid()) {
                $values = $form->getData();
                $user = _em()->getRepository(User::class)->insertUser($values);
                User::setCurrent($user);

                $session->set('user', $user->id);

                $this->flashMessages($request)->flash('notice', _tr('Subscription complete.'));

                return new RedirectResponse(($this->url)('movie.index'));
            }
        }

        $data = [
            'form' => $form,
        ];

        return new HtmlResponse($this->template->render('app::user/login', $data));
    }
}
