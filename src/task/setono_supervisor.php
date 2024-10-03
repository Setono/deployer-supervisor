<?php

declare(strict_types=1);

namespace Setono\Deployer\Supervisor;

use function Deployer\commandExist;
use function Deployer\get;
use function Deployer\which;
use function Deployer\run;
use function Deployer\set;
use function Deployer\task;
use function file_get_contents;
use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

/**
 * The supervisor(ctl) binary
 */
set('bin/supervisor', static function (): string {
    if (commandExist('supervisorctl')) {
        return which('supervisorctl');
    }

    throw new \RuntimeException('The supervisorctl binary could not be found');
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
set('supervisor_config_filename', static fn() => sprintf('%s.conf', getStage()));

/**
 * Contains an array of config files to exclude.
 * You can use this to exclude files based on stage for example
 */
set('supervisor_excluded_files', []);

/**
 * An array of supervisor groups to manage
 */
set('supervisor_groups', static fn() => throw new \RuntimeException('You must set the supervisor_groups parameter'));

task('supervisor:stop', static function (): void {
    $groups = getGroups();

    foreach ($groups as $group) {
        run(sprintf('{{bin/supervisor}} stop %s:*', $group));
    }
});

task('supervisor:upload', static function (): void {
    $sourceDir = get('supervisor_source_dir');
    Assert::string($sourceDir);

    $finder = new Finder();
    $finder->files()->in($sourceDir);

    $excludedFiles = get('supervisor_excluded_files');
    Assert::isArray($excludedFiles);

    $mergedConfigs = '';
    foreach ($finder as $file) {
        if (in_array($file->getFilename(), $excludedFiles, true)) {
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
    $groups = getGroups();

    foreach ($groups as $group) {
        run(sprintf('{{bin/supervisor}} update %s', $group));
        run(sprintf('{{bin/supervisor}} start %s:*', $group));
    }
})->desc('Starts all services managed by Supervisor');

/**
 * Returns the current stage or 'prod' if no stage is set
 */
function getStage(): string
{
    $labels = get('labels');
    if (!is_array($labels)) {
        return 'prod';
    }

    if (!isset($labels['stage'])) {
        return 'prod';
    }

    $stage = $labels['stage'];
    Assert::stringNotEmpty($stage);

    return $stage;
}

/**
 * @return list<string>
 */
function getGroups(): array
{
    $groups = get('supervisor_groups');
    Assert::isArray($groups);
    Assert::isList($groups);
    Assert::allString($groups);

    return $groups;
}
