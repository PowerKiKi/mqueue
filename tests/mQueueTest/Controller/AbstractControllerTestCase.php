<?php

namespace mQueueTest\Controller;

abstract class AbstractControllerTestCase extends \Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var \mQueue\Model\User
     */
    protected $testUser;
    protected $userData = [
        'nickname' => 'test user',
        'email' => 'valid@email.org',
        'password' => 'superpassword',
    ];
    protected $movieData = [
        'id' => '0096446',
        'title' => 'Willow (1988)',
    ];

    public function setUp(): void
    {
        $this->bootstrap = new \Zend_Application(
            APPLICATION_ENV, [
                'config' => [
                    APPLICATION_PATH . '/configs/application.ini',
                ],
            ]
        );

        $this->testUser = \mQueue\Model\UserMapper::findEmailPassword($this->userData['email'], $this->userData['password']);
        if (!$this->testUser) {
            $this->testUser = \mQueue\Model\UserMapper::insertUser($this->userData);
        }

        $movie = \mQueue\Model\MovieMapper::find($this->movieData['id']);
        if (!$movie) {
            \mQueue\Model\MovieMapper::getDbTable()->createRow($this->movieData)->save();
        }

        parent::setUp();
    }

    public function loginUser($login, $password): void
    {
        $this->request->setMethod('POST')
            ->setPost([
                'login' => $login,
                'password' => $password,
            ]);
        $this->dispatch('/user/login');

        $this->resetRequest()
            ->resetResponse();

        $this->request->setPost([]);
    }

    /**
     * Assert against plain text search; content should contain needle
     *
     * @param string $needle needle that should be contained in content
     * @param string $message
     */
    public function assertContentContains($needle, $message = ''): void
    {
        $this->_incrementAssertionCount();
        $content = $this->response->outputBody();
        if (mb_strpos($content, $needle) === false) {
            $failure = sprintf('Failed asserting needle DENOTED BY %s DOES NOT EXIST', $needle);
            if (!empty($message)) {
                $failure = $message . "\n" . $failure;
            }

            throw new \Zend_Test_PHPUnit_Constraint_Exception($failure);
        }
    }
}
