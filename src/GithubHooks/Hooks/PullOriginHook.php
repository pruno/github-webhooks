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
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
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

        $cwd = getcwd();
        chdir($this->path);
        exec("git pull origin {$branch}");
        chdir($cwd);
    }
}