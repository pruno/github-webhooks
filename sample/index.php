<?php

use \GithubHooks\Repository;
use \GithubHooks\Server;
use \GithubHooks\Hooks\PullOriginHooks;

include '../vendor/autoload.php';

$repository = new Repository('OwnerName', 'ProjectName');
$repository->addHook(array('master', 'develop'), new PullOriginHooks('/path/to/working/copy'));

$privateRepository = new Repository('OwnerName', 'ProjectName2');
$privateRepository->addHook('develop', new PullOriginHooks('/path/to/working/copy2', '/path/to/deploy/key'));

$server = new Server();
$server->addRepository('ProjectName', $repository);
$server->addRepository('PrivateProjectName', $privateRepository);

$server->resolve();