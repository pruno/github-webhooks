<?php

namespace GithubHooks\Hooks;

use GithubHooks\HookInterface;

/**
 * Class PullOriginHooks
 * @package GithubHooks\Hooks
 */
class PullOriginHooks implements HookInterface
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
     * @param string $pathToSshKey Path to private SSH key
     */
    public function __construct($path, $pathToSshKey = null)
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
     * @param \StdClass $payload
     * @throws \RuntimeException
     */
    public function resolve(\StdClass $payload)
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

            $cmd .= "GIT_SSH='ssh -i {$realKeyPath}' ";
        }

        $cmd .= "git pull origin {$branch}";

        $cwd = getcwd();
        chdir($this->path);
        exec($cmd);
        chdir($cwd);
    }
}