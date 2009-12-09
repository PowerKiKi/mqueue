<?php

class Default_Model_AbstractModel
{
	private $mapper;
	
    public function __construct(array $data = null)
    {
        if (is_array($data)) {
            $this->setData($data);
        }
    }
	
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid guestbook property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid guestbook property');
        }
        return $this->$method();
    }

    public function setData(array $data)
    {
        $methods = get_class_methods($this);
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
	
	protected function setMapper($mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    protected function getMapper()
    {
        if (null === $this->mapper) {
            $this->setMapper(new Default_Model_GuestbookMapper());
        }
        return $this->mapper;
    }
}

?>