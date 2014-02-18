<?php

namespace GithubWebhooksTest;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class AbstractTestCase
 * @package GithubWebhooksTest
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return Bootstrap::getConfig();
    }

    /**
     * @param mixed $subject
     * @param string $message
     */
    public function assertString($subject, $message = '')
    {
        $this->assertTrue(
            is_string($subject) && $subject,
            $message
        );
    }
}