<?php

namespace GithubWebhooksTest\HookEventListenerTest;

use GithubWebhooks\HookEventListenerInterface;
use GithubWebhooksTest\AbstractTestCase;

/**
 * Class AbstractHookEventListenerTest
 * @package GithubWebhooksTest\HookEventListenerTest
 */
abstract class AbstractHookEventListenerTest extends AbstractTestCase
{
    /**
     * @var HookEventListenerInterface
     */
    protected $listener;

    /**
     * @return HookEventListenerInterface
     */
    abstract public function createListener();

    public function setUp()
    {
        parent::setUp();

        $this->listener = $this->createListener();
    }

    public function tearDown()
    {
        $this->listener = null;

        parent::tearDown();
    }
}