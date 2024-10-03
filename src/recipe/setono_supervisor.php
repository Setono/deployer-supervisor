<?php

declare(strict_types=1);

namespace Setono\Deployer\Supervisor;

use function Deployer\after;
use function Deployer\before;

require_once 'task/setono_supervisor.php';

before('deploy:symlink', [
    'supervisor:stop',
    'supervisor:upload',
]);

after('success', 'supervisor:start');
after('deploy:failed', 'supervisor:start');
after('rollback', 'supervisor:start');
