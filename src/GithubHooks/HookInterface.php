<?php

namespace GithubHooks;

/**
 * Interface HookInterface
 * @package GithubHooks
 */
interface HookInterface
{
    /**
     * @param \StdClass $payload
     * @return void
     */
    public function resolve(\StdClass $payload);
}
