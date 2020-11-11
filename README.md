# Install the Symfony binary with Deployer

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]

## Installation

```bash
$ composer require setono/deployer-symfony
```

## Usage

In your `deploy.php` file require the recipe:

```php
<?php

namespace Deployer;

require_once 'recipe/setono_symfony.php';

// ...
```

This will automatically hook into the default flow of Deployer.

[ico-version]: https://poser.pugx.org/setono/deployer-symfony/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/deployer-symfony/v/unstable
[ico-license]: https://poser.pugx.org/setono/deployer-symfony/license
[ico-github-actions]: https://github.com/Setono/deployer-symfony/workflows/build/badge.svg

[link-packagist]: https://packagist.org/packages/setono/deployer-symfony
[link-github-actions]: https://github.com/Setono/deployer-symfony/actions
