<?php

abstract class AbstractControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    /**
     * @var Default_Model_User
     */
    protected $testUser;
    protected $userData = array(
        'nickname' => 'test user',
        'email' => 'valid@email.org',
        'password' => 'superpassword',
    );
    protected $movieData = array(
        'id' => '0096446',
        'title' => 'Willow (1988)',
    );

    public function setUp()
    {

        $this->bootstrap = new Zend_Application(
                APPLICATION_ENV, array(
            'config' => array(
                APPLICATION_PATH . '/configs/application.ini',
            ))
        );

        $this->testUser = Default_Model_UserMapper::findEmailPassword($this->userData['email'], $this->userData['password']);
        if (!$this->testUser) {
            $this->testUser = Default_Model_UserMapper::insertUser($this->userData);
        }

        $movie = Default_Model_MovieMapper::find($this->movieData['id']);
        if (!$movie) {
            Default_Model_MovieMapper::getDbTable()->createRow($this->movieData)->save();
        }

        parent::setUp();
    }

    public function loginUser($login, $password)
    {
        $this->request->setMethod('POST')
                ->setPost(array(
                    'login' => $login,
                    'password' => $password,
        ));
        $this->dispatch('/user/login');

        $this->resetRequest()
                ->resetResponse();

        $this->request->setPost(array());
    }

    /**
     * Assert against plain text search; content should contain needle
     *
     * @param  string $needle needle that should be contained in content
     * @param  string $message
     * @return void
     */
    public function assertContentContains($needle, $message = '')
    {
        $this->_incrementAssertionCount();
        $content = $this->response->outputBody();
        if (strpos($content, $needle) === false) {

            $failure = sprintf('Failed asserting needle DENOTED BY %s DOES NOT EXIST', $needle);
            if (!empty($message)) {
                $failure = $message . "\n" . $failure;
            }
            throw new Zend_Test_PHPUnit_Constraint_Exception($failure);
        }
    }

}
