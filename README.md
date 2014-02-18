Github WebHooks 2.0.0 [![Build Status](https://travis-ci.org/pruno/github-webhooks.png?branch=master)](https://travis-ci.org/pruno/github-webhooks)&nbsp;[![Latest Stable Version](https://poser.pugx.org/pruno/github-webhooks/v/stable.png)](https://packagist.org/packages/ripaclub/sphinxsearch)
===

Github Webhooks Library (with events support).

Introduction
---

This library aims to provide:

 - A simple and embeddable event-based library
 
 - A handy stand-alone server class
 
 - An event listener to perform `git pull`
     
     - Support multiple deploy key
     - _NOTE_: Linux only

Requirements
---

 - php >=5.3.3

Optionally:

 - git (to use the Pull event listener)


Installation
---

Using [composer](http://getcomposer.org/):

Add the following to your `composer.json` file:

    "require": {
        "pruno/github-webhooks": "2.0.*",
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/pruno/github-webhooks.git"
        }
    ]

Alternately with git submodules:

    git submodule add https://github.com/pruno/github-webhooks.git pruno/github-webhooks

 
Quick implementation (Pull on Push event)
---

 1 - Create a [Github Webhook](https://developer.github.com/webhooks/):
     
     - Set an arbitrary URL path (this will be your hook id) 
       (e.g: https://example.com/POSTRECEIVE)
     - Both payload versions are supported.
     - The push event is enough.

 1 - Copy `sample/composer.json` to your project root directory.
 
 2 - Run `composer install`.
 
 3 - Copy `sample/index.php` to your public directory.
 
 4 - Edir `sample/index.php` with:  
 
     - hook information (line 9).
     - git working copy information (line 14).
     
 5 - You may remove line 12 once finished.


Notice:  
Remember that the user who is running your webserver (or fast-cgi process) need to have write permissions on your git working copy.

Embedding
---

Refer to `library/GithubWebhooks/Server.php` as an example implementation.


Contribute
---

Please do. Fork it and send pull requests.


License
---

This software is released under the New-BSD License.