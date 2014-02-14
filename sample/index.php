<?php

namespace GithubHooks;

use GithubHooks\Hooks\Pull;

include '../vendor/autoload.php';

$webhook = new Hook(1111111, 'pruno', 'github-hooks');

$server = new Server();
$server->getHookManager()->attach($webhook, HookManager::EVENT_PING, new Pull('/path/to/clone'));

$server->resolve();