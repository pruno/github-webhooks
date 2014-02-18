<?php

namespace GithubWebhooksTest;

use GithubWebhooks\HookManager;
use GithubWebhooks\HookEventListener\Pull;
use GithubWebhooks\Payload;
use GithubWebhooksTest\HookEventListenerTest\AbstractHookEventListenerTest;

/**
 * Class PullTest
 * @package GithubWebhooksTest
 *
 * @property \GithubWebhooks\HookEventListener\Pull $listener
 */
class PullTest extends AbstractHookEventListenerTest
{
    /**
     * @var string
     */
    const CLONE_DIRNAME_TEMPLATE = '__pruno_githubwebhooks_%d';

    /**
     * @var string
     */
    const GIT_REMOTE_URL = 'https://github.com/pruno/github-webhooks.git';

    /**
     * @var string
     */
    protected $testDirname;

    /**
     * @var string
     */
    protected $testPath;

    public function setUp()
    {
        $config = $this->getConfig();

        $this->testDirname = sprintf(self::CLONE_DIRNAME_TEMPLATE, ((int) rand(10000, 99999)));
        $this->testPath = $config['listeners']['Pull']['path'].'/'.$this->testDirname;

        // ^ those property are needed by createListener() ^

        parent::setUp();

        if (PHP_OS != 'Linux') {
            $this->markTestSkipped("Pull is currently supported only on Linux platforms.");
        }

        if (!$config['listeners']['Pull']['path'] || !is_dir($config['listeners']['Pull']['path']) || !is_writable($config['listeners']['Pull']['path'])) {
            $this->markTestSkipped("{$config['listeners']['Pull']['path']} is not writable.");
        }
    }

    public function tearDown()
    {
        if (is_dir($this->testPath)) {
            exec("rm -rf ".escapeshellarg($this->testPath));
        }

        $this->testPath = null;
        $this->testDirname = null;

        parent::tearDown();
    }

    /**
     * @return string
     */
    protected function getDummyKeyPath()
    {
        return __DIR__.'/../../misc/id_rsa.dummy';
    }

    public function createListener()
    {
        return new Pull($this->testPath);
    }

    public function testGetters()
    {
        $this->assertEquals($this->listener->getPath(), $this->testPath);
        $this->assertEquals($this->listener->getRemote(), Pull::DEFAULT_REMOTE);
        $this->assertEquals($this->listener->getBranch(), Pull::DEFAULT_BRANCH);
        $this->assertNull($this->listener->getPathToSshKey());
    }

    public function testInvoke()
    {
        $config = $this->getConfig();

        if ($config['listeners']['Pull']['skip_invoke_test']) {
            $this->markTestSkipped();
        }

        $payload = new Payload(array(), HookManager::EVENT_PING, '_ID_');

        $cwd = getcwd();
        chdir($config['listeners']['Pull']['path']);
        $output = null;
        $exitCode = 0;
        exec("git clone ".escapeshellarg(self::GIT_REMOTE_URL)." ".escapeshellarg($this->testDirname)." 2> /dev/null", $output, $exitCode);
        chdir($cwd);

        if ($exitCode) {
            $this->fail("git clone exit with code {$exitCode}.");
        }

        // valid path
        call_user_func($this->listener, $payload);

        // valid path with valid key
        $listener = new Pull($this->listener->getPath(), $this->listener->getRemote(), $this->listener->getBranch(), $this->getDummyKeyPath());
        call_user_func($listener, $payload);

        $hasThrownAnException = false;

        try {
            // invalid path
            $listener = new Pull($this->listener->getPath().'_NON_EXISTING_', $this->listener->getRemote(), $this->listener->getBranch(), $this->getDummyKeyPath());
            call_user_func($listener, $payload);
        } catch (\Exception $e) {
            $hasThrownAnException = true;
        }

        $this->assertTrue($hasThrownAnException);

        try {
            // valid path with invalid key
            $listener = new Pull($this->listener->getPath(), $this->listener->getRemote(), $this->listener->getBranch(), $this->getDummyKeyPath().'_NON_EXISTING_');
            call_user_func($listener, $payload);
        } catch (\Exception $e) {
            $hasThrownAnException = true;
        }

        $this->assertTrue($hasThrownAnException);

        // ATTENTION: from this point on the clone is corrupted!
        exec("rm -rf ".escapeshellarg($this->testPath.'/.git'));

        try {
            // forcing git command error
            call_user_func($this->listener, $payload);
        } catch (\Exception $e) {
            $hasThrownAnException = true;
        }

        $this->assertTrue($hasThrownAnException);
    }
}