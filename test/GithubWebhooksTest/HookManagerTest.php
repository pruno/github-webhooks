<?php

namespace GithubWebhooksTest;

use GithubWebhooks\Hook;
use GithubWebhooks\HookManager;
use GithubWebhooks\Payload;
use GithubWebhooksTest\TestHookEventListener\Null;
use Zend\EventManager\EventManager;

class HookManagerTest extends AbstractTestCase
{
    /**
     * @var HookManager
     */
    protected $hookManager;

    public function setUp()
    {
        parent::setUp();

        $this->hookManager = new HookManager();
    }

    public function tearDown()
    {
        $this->hookManager = null;

        parent::tearDown();
    }

    public function testCreate()
    {
        new HookManager();
    }

    public function testEventManagerGetterSetter()
    {
        $this->assertTrue($this->hookManager->getEventManager() instanceof EventManager);

        $this->tearDown();
        $this->setUp();

        $customEventManager = new EventManager();

        $this->hookManager->setEventManager($customEventManager);
        $this->assertEquals($customEventManager, $this->hookManager->getEventManager());
    }

    public function testSuppressEventManagerGetterSetter()
    {
        $this->assertTrue(is_bool($this->hookManager->getSuppressListenersExceptions()));

        $this->hookManager->setSuppressListenersExceptions(false);

        $this->assertFalse($this->hookManager->getSuppressListenersExceptions());
    }

    public function testHookGetterSetter()
    {
        $hook = new Hook('_ID_', '_OWNER_', '_REPOSITORY_');

        $this->hookManager->addHook($hook);

        $this->assertTrue($this->hookManager->hasHook('_ID_'));
        $this->assertFalse($this->hookManager->hasHook('_NON_EXISTING_ID'));
        $this->assertEquals($this->hookManager->getHook('_ID_'), $hook);
        $this->assertNull($this->hookManager->getHook('_NON_EXISTING_ID'));

        $hasThrownAnException = false;

        try {
            $this->hookManager->addHook($hook);
        } catch (\Exception $e) {
            $hasThrownAnException = true;
        }

        $this->assertTrue($hasThrownAnException);

        $this->hookManager->removeHook($hook);

        $this->assertFalse($this->hookManager->hasHook('_ID_'));
    }

    /**
     * @param $flag
     * @return callable
     */
    protected function getTestListenerClosure(&$flag)
    {
        return function(Payload $payload) use(&$flag) {
            $flag = true;
        };
    }

    public function testProcess()
    {
        $hook = new Hook('_ID_', '_OWNER_', '_REPOSITORY_');
        $hook2 = new Hook('_ID_2_', '_OWNER_', '_REPOSITORY_');
        $payloadPing = new Payload(array(), HookManager::EVENT_PING, '_ID_');
        $payloadPush = new Payload(array(), HookManager::EVENT_PUSH, '_ID_');
        $missingPayload = new Payload(array(), HookManager::EVENT_PING, '_NON_EXISTING_PAYLOAD_');

        $flag = false;
        $this->hookManager->attach($hook, HookManager::EVENT_PING, new Null($this->getTestListenerClosure($flag)));
        $this->hookManager->attach($hook2, HookManager::EVENT_PING, new Null($this->getTestListenerClosure($flag)));

        $this->assertTrue($this->hookManager->hasHook($hook->getId()));

        $this->hookManager->processPayload($missingPayload);

        $this->assertFalse($flag);

        $this->hookManager->processPayload($payloadPush);

        $this->assertFalse($flag);

        $this->hookManager->processPayload($payloadPing);

        $this->assertTrue($flag);
    }

    public function testSuppressListenerExceptions()
    {
        $hook = new Hook('_ID_', '_OWNER_', '_REPOSITORY_');
        $payload = new Payload(array(), HookManager::EVENT_PING, '_ID_');

        $closure = function(Payload $payload) {
            throw new \Exception();
        };

        $this->hookManager->attach($hook, HookManager::EVENT_PING, new Null($closure));
        $this->hookManager->processPayload($payload);

        $this->hookManager->setSuppressListenersExceptions(false);

        $hasThrowAnException = false;

        try {
            $this->hookManager->processPayload($payload);
        } catch (\Exception $e) {
            $hasThrowAnException = true;
        }

        $this->assertTrue($hasThrowAnException);
    }
}