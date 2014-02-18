<?php

namespace GithubWebhooksTest;

use GithubWebhooks\HookManager;
use GithubWebhooks\Payload;

class PayloadTest extends AbstractTestCase
{
    /**
     * @var Payload
     */
    protected $payload;

    public function setUp()
    {
        parent::setUp();

        $this->payload = new Payload(array('dummy' => 1), HookManager::EVENT_PING, '_ID_');
    }

    public function tearDown()
    {
        $this->payload = null;

        parent::tearDown();
    }

    public function testCreate()
    {
        new Payload(array(), HookManager::EVENT_PING, '_ID_');
    }

    public function testGetters()
    {
        $this->assertEquals($this->payload->getContent(), array('dummy' => 1));
        $this->assertEquals($this->payload->getEvent(), HookManager::EVENT_PING);
        $this->assertEquals($this->payload->getHookId(), '_ID_');
    }
}