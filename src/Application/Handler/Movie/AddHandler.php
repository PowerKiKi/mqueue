<?php

namespace Application\Handler\Movie;

use Application\Handler\PageHandler;
use Application\Model\Movie;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AddHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new \Application\Form\Movie();

        $movies = [];
        if ($request->getQueryParams()['id'] ?? null) {
            $form->setData($request->getQueryParams());
            if ($form->isValid()) {
                $values = $form->getData();
                $movie = _em()->getRepository(Movie::class)->getOrCreate((int) Movie::extractId($values['id']));

                $movies[] = $movie;
            }
        }

        $data = [
            'form' => $form,
            'movies' => $movies,
        ];

        return new HtmlResponse($this->template->render('app::movie/add', $data));
    }
}
