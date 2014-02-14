<?php

namespace GithubHooks\Hooks;

use GithubHooks\HookEventListenerInterface;
use GithubHooks\Payload;

/**
 * Class PullOriginHooks
 * @package GithubHooks\Hooks
 */
class Pull implements HookEventListenerInterface
{
    /**
     * @var string
     */
    protected $path;

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
    public function __construct($path, $remote = 'origin', $branch = 'master', $pathToSshKey = null)
    {
        $this->path = $path;
        $this->pathToSshKey = $pathToSshKey;
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

        $aref = explode('/', $payload->ref);
        $branch = $aref[count($aref) - 1];

        $cmd = '';

        if ($this->pathToSshKey) {
            if (!$this->isKeyReadable($this->pathToSshKey)) {
                throw new \RuntimeException("SSH key {$this->pathToSshKey} is not readable");
            }

            $realKeyPath = realpath($this->pathToSshKey);
            $realBinPath = realpath(__DIR__.'/../../../bin/git_ssh.sh');

            $cmd .= "PATH_TO_PRIVATE_KEY=".escapeshellarg($realKeyPath)." GIT_SSH=".escapeshellarg($realBinPath)." ";
        }

        $cmd .= "git pull origin {$branch}";

        $cwd = getcwd();
        chdir($this->path);
        exec($cmd);
        chdir($cwd);
    }
}