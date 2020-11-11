<?php

declare(strict_types=1);

namespace Setono\Deployer\Supervisor;

use Symfony\Component\Finder\Finder;
use function Deployer\get;
use function Deployer\parse;
use function Deployer\run;
use function Deployer\set;
use function Deployer\task;
use function Safe\file_get_contents;

/**
 * This is the directory where you have your supervisor configs
 */
set('supervisor_source_dir', 'etc/supervisor');

/**
 * This is the directory on your server where the final config file will be uploaded
 */
set('supervisor_remote_dir', '/etc/supervisor/conf.d');

/**
 * This library will create a single final config file for supervisor. This will be the name of that file
 */
set('supervisor_config_filename', '{{application}}-{{stage}}.conf');

task('supervisor:upload', static function (): void {
    $finder = new Finder();
    $finder->files()->in(get('supervisor_source_dir'));

    /**
     * This will generate a $command variable that will save a multiline text into a file
     * See https://stackoverflow.com/questions/10969953/how-to-output-a-multiline-string-in-bash
     */
    $command = "cat <<EOT > {{supervisor_config_filename}}\n";
    foreach ($finder as $file) {
        $command .= trim(parse(file_get_contents($file->getRelativePathname()))) . "\n\n"; // todo create a test that checks for multiline replacements
    }
    $command .= 'EOT';
    run($command);
})->desc('This task uploads your processed supervisor configs to the specified directory on your server');
