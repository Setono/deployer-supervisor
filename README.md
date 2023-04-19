# Handle Supervisor during your deployment

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]

## Installation

```bash
composer require setono/deployer-supervisor
```

## Usage

In your `deploy.php` file require the recipe:

```php
<?php

namespace Deployer;

require_once 'recipe/setono_supervisor.php';

// ...
```

This will automatically hook into the default flow of Deployer.

Then create a config file in `etc/supervisor` (in your repository) like so:

```ini
;etc/supervisor/service1.conf
[program:service1]
command={{bin/php}} {{bin/console}} messenger:consume async
user={{http_user}}
autostart=true
autorestart=true
```

**Notice** that you can use Deployer's parameters within your config file!

### Add delay before (re)starting a service
```ini
;etc/supervisor/service1.conf
[program:service1]
command=bash -c "sleep 10 && exec {{bin/php}} {{bin/console}} messenger:consume async"
user={{http_user}}
autostart=true
autorestart=true
startsecs=15
```

In this example we insert a delay of 10 seconds before running the actual command and we changed the `startsecs`
to 15 (5 seconds more than the delay) so that supervisor doesn't count the process as running before after 15 seconds.

[ico-version]: https://poser.pugx.org/setono/deployer-supervisor/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/deployer-supervisor/v/unstable
[ico-license]: https://poser.pugx.org/setono/deployer-supervisor/license
[ico-github-actions]: https://github.com/Setono/deployer-supervisor/workflows/build/badge.svg

[link-packagist]: https://packagist.org/packages/setono/deployer-supervisor
[link-github-actions]: https://github.com/Setono/deployer-supervisor/actions
