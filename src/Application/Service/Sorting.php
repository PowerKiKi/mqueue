<?php

namespace Application\Service;

readonly class Sorting
{
    public string $keyParamName;

    public string $orderParamName;

    public string $selectedKey;

    public string $selectedOrder;

    public string $validKey;

    public string $validOrder;

    /**
     * It will look for parameter "sort" and "sortOrder" in request to build allowed SQL sorting.
     *
     * @param string[] $allowedKeys
     */
    public function __construct(array $queryParams, array $allowedKeys)
    {
        $this->keyParamName = 'sort';
        $this->orderParamName = $this->keyParamName . 'Order';
        $key = $queryParams[$this->keyParamName] ?? '';
        $order = $queryParams[$this->orderParamName] ?? '';

        $this->selectedKey = $key;
        $this->selectedOrder = $order;

        $validKey = $this->selectedKey;
        if (!in_array($validKey, $allowedKeys, true)) {
            $validKey = reset($allowedKeys);
        }

        $this->validKey = $validKey;
        $this->validOrder = strcasecmp($order, 'desc') === 0 ? 'DESC' : 'ASC';
    }
}
