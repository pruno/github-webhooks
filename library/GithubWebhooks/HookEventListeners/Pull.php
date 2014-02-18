<?php

namespace GithubWebhooks\Hooks;

use GithubWebhooks\HookEventListenerInterface;
use GithubWebhooks\Payload;

/**
 * Class PullOriginHooks
 * @package GithubWebhooks\Hooks
 */
class Pull implements HookEventListenerInterface
{
    /**
     * @var string
     */
    const DEFAULT_REMOTE = 'origin';

    /**
     * @var string
     */
    const DEFAULT_BRANCH = 'master';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $remote;

    /**
     * @var string
     */
    protected $branch;

    /**
     * @var string
     */
    protected $pathToSshKey;

    /**
     * @param string $path Path to working copy
     * @param string $remote
     * @param string $branch
     * @param string $pathToSshKey Path to private SSH key
     */
    public function __construct($path, $remote = null, $branch = null, $pathToSshKey = null)
    {
        $this->path = $path;
        $this->remote = $remote !== null ? $remote : self::DEFAULT_REMOTE;
        $this->branch = $branch !== null ? $branch : self::DEFAULT_BRANCH;
        $this->pathToSshKey = $pathToSshKey;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getPathToSshKey()
    {
        return $this->pathToSshKey;
    }

    /**
     * @return string
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isPathWritable($path)
    {
        return is_dir($path) && is_writable($path);
    }

    /**
     * @param string $pathToSshKey
     * @return bool
     */
    protected function isKeyReadable($pathToSshKey)
    {
        return is_readable($pathToSshKey) && is_file($pathToSshKey);
    }

    /**
     * @param Payload $payload
     * @throws \RuntimeException
     */
    public function __invoke(Payload $payload)
    {
        if (!$this->isPathWritable($this->path)) {
            throw new \RuntimeException("Path {$this->path} is not writable");
        }

        $cmd = '';

        if ($this->pathToSshKey) {
            if (!$this->isKeyReadable($this->pathToSshKey)) {
                throw new \RuntimeException("SSH key {$this->pathToSshKey} is not readable");
            }

            $realKeyPath = realpath($this->pathToSshKey);
            $realBinPath = realpath(__DIR__.'/../../../bin/git-ssh.sh');

            $cmd .= "GIT_KEY=".escapeshellarg($realKeyPath)." GIT_SSH=".escapeshellarg($realBinPath)." ";
        }

        $cmd .= "git pull {$this->remote} {$this->branch} 2> /dev/null";

        $cwd = getcwd();
        chdir($this->path);
        $output = null;
        $exitCode = 0;
        exec($cmd, $output, $exitCode);
        chdir($cwd);

        if ($exitCode) {
            throw new \RuntimeException("git pull exit with code {$exitCode}.");
        }
    }
}