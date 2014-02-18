<?php

namespace GithubWebhooksTest;

use GithubWebhooks\HookManager;
use GithubWebhooks\Payload;
use GithubWebhooks\Server;

/**
 * Class ServerTest
 * @package GithubWebhooksTest
 */
class ServerTest extends AbstractTestCase
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var array
     */
    protected $_SERVER_CPY;

    /**
     * @var array
     */
    protected $_POST_CPY;

    public function setUp()
    {
        parent::setUp();

        $this->_SERVER_CPY = $_SERVER;
        $this->_POST_CPY = $_POST;

        $this->server = new Server();
    }

    public function tearDown()
    {
        $this->server = null;

        $_POST = $this->_POST_CPY;
        $_SERVER = $this->_SERVER_CPY;

        parent::tearDown();
    }

    public function testHookManagerGetterSetter()
    {
        $this->assertTrue($this->server->getHookManager() instanceof HookManager);

        $this->tearDown();
        $this->setUp();

        $customHookManager = new HookManager();

        $this->server->setHookManager($customHookManager);

        $this->assertEquals($this->server->getHookManager(), $customHookManager);
    }

    public function testValidateOriginGetterSetter()
    {
        $this->assertTrue($this->server->getValidateOrigin());

        $this->server->setValidateOrigin(false);

        $this->assertFalse($this->server->getValidateOrigin());
    }

    public function testGetOrigin()
    {
        $_SERVER['REMOTE_ADDR'] = '204.232.175.64';

        $this->assertString($this->server->getOrigin());
        $this->assertEquals(preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/', $this->server->getOrigin()), 1);

        $this->tearDown();
        $this->setUp();

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '204.232.175.64';

        $this->assertEquals(preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/', $this->server->getOrigin()), 1);
    }

    public function testGetEvent()
    {
        $_SERVER[Server::GITHUB_EVENT_HEADER_NAME] = HookManager::EVENT_PING;

        $this->assertString($this->server->getEvent());
        $this->assertEquals($this->server->getEvent(), HookManager::EVENT_PING);
    }

    public function testGetHookId()
    {
        $_SERVER['REQUEST_URI'] = '/_ID_';

        $this->assertString($this->server->getHookId());
        $this->assertEquals($this->server->getHookId(), '_ID_');
    }

    public function testGetPayload()
    {
        $_SERVER['REQUEST_URI'] = '/_ID_';
        $_SERVER[Server::GITHUB_EVENT_HEADER_NAME] = HookManager::EVENT_PING;
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['payload'] = json_encode(array('dummy' => 1));

        $this->assertTrue($this->server->getPayload() instanceof Payload);
        $this->assertEquals($this->server->getPayload()->getContent(), array('dummy' => 1));

        // access to php://input could not be tested
    }

    public function testResolve()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/_ID_';
        $_SERVER[Server::GITHUB_EVENT_HEADER_NAME] = HookManager::EVENT_PING;
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['payload'] = json_encode(array('dummy' => 1));

        $_SERVER['REMOTE_ADDR'] = '204.232.175.64';

        $this->server->resolve(false);

        // invalid origin
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';

        $this->server->resolve(false);
    }
}