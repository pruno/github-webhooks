<?php

namespace GithubHooks;

/**
 * Class Repository
 * @package GithubHooks
 */
class Repository
{
    /**
     * @const string
     */
    const GITHUB_URL_FORMAT = 'https://github.com/%s/%s';

    /**
     * @var string
     */
    protected $owner;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $hooks = array();

    /**
     * @param string $owner
     * @param string $name
     */
    public function __construct($owner, $name)
    {
        $this->owner = $owner;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return sprintf(self::GITHUB_URL_FORMAT, $this->owner, $this->name);
    }

    /**
     * @param $branches
     * @param HookInterface $hook
     * @throws \InvalidArgumentException
     */
    public function addHook($branches, HookInterface $hook)
    {
        if (is_string($branches)) {
            $branches = array($branches);
        }

        if (!is_array($branches)) {
            throw \InvalidArgumentException("First argument supplied for addHook must be a string or an array of strings");
        }

        foreach ($branches as $branch) {
            if (!isset($this->hooks[$branch])) {
                $this->hooks[$branch] = array();
            }

            $this->hooks[$branch][] = $hook;
        }
    }

    /**
     * @param \StdClass $payload
     */
    public function resolveHooks(\StdClass $payload)
    {
        $aref = explode('/', $payload->ref);
        $branch = $aref[count($aref) - 1];

        if (isset($this->hooks[$branch])) {
            /* @var $hook HookInterface */
            foreach ($this->hooks[$branch] as $hook) {
                try {
                    $hook->resolve($payload);
                } catch (\Exception $e) {
                    // Add error handling controll?
                }
            }
        }
    }
}