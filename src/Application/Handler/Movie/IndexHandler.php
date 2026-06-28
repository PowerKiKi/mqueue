<?php

namespace Application\Handler\Movie;

use Application\Enum\Rating;
use Application\Form\Filters;
use Application\Handler\PageHandler;
use Application\Model\Movie;
use Application\Model\User;
use Application\Paginator\PaginatorFactory;
use Application\Service\Sorting;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexHandler extends PageHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Check there is at least one user, otherwise the whole page will crash
        if (!User::getCurrent() && !_em()->getRepository(User::class)->count()) {
            $this->error('At least one user must exist to access this page');
        }

        $form = new Filters();

        // Detect if at least one filter was submitted
        $submitted = false;
        foreach ($request->getQueryParams() as $key => $filter) {
            if (preg_match('/^filter\d+$/', $key)) {
                $submitted = true;
            }
        }

        $search = $request->getQueryParams()['search'] ?? null;

        // If was submitted, try to validate values
        if ($submitted) {
            $form->setData($request->getQueryParams());
            if (!$form->isValid()) {
                $this->flashMessages($request)->flash('warning', _tr('Filter is invalid.'));
                $form->setData([]);
            }
        } // If we submitted a quicksearch, set default values to search with any status
        elseif ($search) {
            $form->setData([
                'filter1' => [
                    'user' => User::getCurrent() ? 0 : _em()->getRepository(User::class)->getAll()[0]->id,
                    'condition' => 'is',
                    'status' => Rating::possibleValues(true),
                    'title' => $search,
                ],
            ]);
        } // Otherwise clear the filter
        else {
            $form->setData([]);
        }

        // Gather users selected in filters
        $users = [];
        $form->isValid();
        $filters = $form->getData();
        foreach ($filters as $key => $filter) {
            if (!preg_match('/^filter\d+$/', $key)) {
                continue;
            }

            $id = $filter['user'];
            if ($id) {
                $users[$id] = _em()->getRepository(User::class)->findOneById($id);
            }
        }

        // If we output rss, we force sorting by date
        if (($request->getQueryParams()['format'] ?? null) === 'rss') {
            $queryParamsForSorting = [
                'sort' => $filters['filter1']['withSource'] ? 'date_search' : 'date',
                'sortOrder' => 'desc',
            ];
        } else {
            $queryParamsForSorting = $request->getQueryParams();
        }

        $allowedSortingKey = ['movie.title', 'date', 'movie.dateSearch'];
        $usersCount = count($users);
        for ($i = 0; $i < $usersCount; ++$i) {
            $allowedSortingKey[] = "status$i.rating";
        }
        $sorting = new Sorting($queryParamsForSorting, $allowedSortingKey);

        $permanentParams = $form->getData();
        unset($permanentParams['addFilter']);
        $permanentParams[$sorting->keyParamName] = $sorting->selectedKey;
        $permanentParams[$sorting->orderParamName] = $sorting->selectedOrder;

        // Set up the paginator: Apply pagination only if there is no special context (so it is normal html rendering)
        $paginator = PaginatorFactory::create(
            $request,
            _em()->getRepository(Movie::class)->getFilteredQuery($filters, $sorting),
        );

        $data = [
            'formFilter' => $form,
            'users' => $users,
            'paginator' => $paginator,
            'permanentParams' => $permanentParams,
            'filterName' => $form->getValuesText(),
            'sorting' => $sorting,
        ];

        return $this->htmlOrRss($request, 'app::movie/index', $data);
    }
}
