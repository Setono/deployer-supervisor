<?php

declare(strict_types=1);

namespace Setono\Deployer\Supervisor;

use function Deployer\after;
use function Deployer\before;

require_once 'task/setono_supervisor.php';

before('supervisor:upload', 'supervisor:stop');
before('deploy:symlink', 'supervisor:upload');

after('success', 'supervisor:start');
after('deploy:failed', 'supervisor:start');
after('rollback', 'supervisor:start');
