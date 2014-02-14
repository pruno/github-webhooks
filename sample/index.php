<?php

namespace GithubHooks;

use GithubHooks\Hooks\Pull;

include '../vendor/autoload.php';

//
//
//$repository = new Repository('OwnerName', 'ProjectName');
//$repository->addHook(array('master', 'develop'), new PullOriginHooks('/path/to/working/copy'));
//
//$privateRepository = new Repository('OwnerName', 'ProjectName2');
//$privateRepository->addHook('develop', new PullOriginHooks('/path/to/working/copy2', '/path/to/deploy/key'));

$webhook = new WebHook(1111111, 'pruno', 'github-hooks-test');

$server = new Server();
$server->getHookManager()->attach($webhook, HookManager::EVENT_PING, new Pull());

$server->resolve();