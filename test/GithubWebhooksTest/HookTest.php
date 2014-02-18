<?php

namespace GithubWebhooksTest;

use GithubWebhooks\Hook;

/**
 * Class HookTest
 * @package GithubWebhooksTest
 */
class HookTest extends AbstractTestCase
{
    /**
     * @var Hook
     */
    protected $hook;

    public function setUp()
    {
        parent::setUp();

        $this->hook = new Hook('_ID_', '_OWNER_', '_REPOSITORY_');
    }

    public function tearDown()
    {
        $this->hook = null;

        parent::tearDown();
    }

    public function testCreate()
    {
        $hook = new Hook('_ID_', '_OWNER_', '_REPOSITORY_');
    }

    public function testGetters()
    {
        $this->assertEquals($this->hook->getId(), '_ID_');
        $this->assertEquals($this->hook->getOwner(), '_OWNER_');
        $this->assertEquals($this->hook->getRepository(), '_REPOSITORY_');
    }
}