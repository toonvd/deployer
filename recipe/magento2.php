<?php

/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/common.php';

/**
 * Magento 2 Configuration
 */

// Magento shared dirs
set('shared_dirs', ['var', 'pub']);

// Magento shared files
set('shared_files', ['app/etc/env.php']);

// Magento writable dirs
set('writable_dirs', ['var', 'pub']);

/**
 * Start DB upgrade
 */
task('deploy:setup:upgrade', function () {
    run("cd {{release_path}} && php bin/magento setup:upgrade");
})->desc('Start DB upgrade');

/**
 * Start di compilation
 */
task('deploy:setup:di:compile', function() {
    run("cd {{release_path}} && php bin/magento setup:di:compile");
})->desc('Start di compilation');

/**
 * Deploy static content
 */
task('deploy:setup:static-content:deploy', function () {
    run("cd {{release_path}} && php bin/magento setup:static-content:deploy");
})->desc('Deploy static content');

/**
 * Flush cache
 */
task('deploy:cache:flush', function () {
    run("cd {{release_path}} && php bin/magento cache:flush");
})->desc('Flush cache');

/**
 * Remove files that can be used to compromise Magento
 */
task('deploy:clear_version', function () {
    run("rm -f {{release_path}}/LICENSE.txt");
    run("rm -f {{release_path}}/LICENSE_AFL.txt");
    run("rm -f {{release_path}}/RELEASE_NOTES.txt");
    run("rm -f {{release_path}}/CHANGELOG.md");
    run("rm -f {{release_path}}/.htaccess.sample");
    run("rm -f {{release_path}}/.php_cs");
    run("rm -f {{release_path}}/CONTRIBUTING.md");
    run("rm -f {{release_path}}/CONTRIBUTOR_LICENSE_AGREEMENT.html");
    run("rm -f {{release_path}}/COPYING.txt");
    run("rm -f {{release_path}}/nginx.conf.sample");
    run("rm -f {{release_path}}/php.ini.sample");
    run("rm -f {{release_path}}/.travis.yml");
})->setPrivate();

after('deploy:update_code', 'deploy:clear_version');

/**
 * Main task
 */
task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:setup:upgrade',
    'deploy:setup:di:compile',
    'deploy:setup:static-content:deploy',
    'deploy:cache:flush',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');
