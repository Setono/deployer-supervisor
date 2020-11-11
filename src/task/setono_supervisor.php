<?php

declare(strict_types=1);

namespace Setono\Deployer\Supervisor;

use function Deployer\get;
use function Deployer\locateBinaryPath;
use function Deployer\run;
use function Deployer\set;
use function Deployer\task;
use function Safe\file_get_contents;
use Symfony\Component\Finder\Finder;

/**
 * The supervisor(ctl) binary
 */
set('bin/supervisor', static function () {
    return locateBinaryPath('supervisorctl');
});

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

/**
 * Contains an array of config files to exclude.
 * You can use this to exclude files based on stage for example
 */
set('supervisor_excluded_files', []);

task('supervisor:stop', static function (): void {
    run('{{bin/supervisor}} stop all');
})->desc('Stops all services managed by Supervisor');

task('supervisor:upload', static function (): void {
    $finder = new Finder();
    $finder->files()->in(get('supervisor_source_dir'));

    $mergedConfigs = '';
    foreach ($finder as $file) {
        if (in_array($file->getFilename(), get('supervisor_excluded_files'), true)) {
            continue;
        }

        $mergedConfigs .= trim(file_get_contents($file->getRealPath())) . "\n\n";
    }

    if ('' === $mergedConfigs) {
        run('rm -rf {{supervisor_remote_dir}}/{{supervisor_config_filename}}');

        return;
    }

    /**
     * This 'hack' will save a multiline text string into a file
     * See https://stackoverflow.com/questions/10969953/how-to-output-a-multiline-string-in-bash
     */
    run("cat <<EOT > {{supervisor_remote_dir}}/{{supervisor_config_filename}}\n{$mergedConfigs}EOT"); // todo create a test that checks for multiline replacements
})->desc('This task uploads your processed supervisor configs to the specified directory on your server');

task('supervisor:start', static function (): void {
    run('{{bin/supervisor}} start all');
})->desc('Starts all services managed by Supervisor');
