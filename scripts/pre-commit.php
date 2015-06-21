#!/usr/bin/php
<?php
/**
 * .git/hooks/pre-commit.
 *
 * This pre-commit hooks will check for PHP error (lint), and make sure the code
 * is PSR compliant.
 *
 * Dependecy: PHP-CS-Fixer (https://github.com/fabpot/PHP-CS-Fixer)
 *
 * @author  Mardix  http://github.com/mardix
 *
 * @since   Sept 4 2012
 */

// collect all files which have been added, copied or modified
exec('git diff --cached --name-only --diff-filter=ACM', $output);

foreach ($output as $fileName) {
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileName = escapeshellarg($fileName);
    if ($ext === 'php') {
        runCheck("php -l $fileName");
        if (runCheck("vendor/bin/php-cs-fixer fix $fileName --level=psr2 --fixers=-psr0")) {
            exec("git add $fileName");
        }
    } elseif ($ext === 'js') {
        runCheck("./node_modules/.bin/jshint {$fileName}");
        if (runCheck("./node_modules/.bin/jscs -x {$fileName}")) {
            exec("git add $fileName");
        }
    }
}

function runCheck($command)
{
    $output = [];
    exec($command, $output, $return);
    $changed = count($output);
    if ($changed) {
        echo implode("\n", $output) . "\n";
    }
    if ($return !== 0) {
        exit($return);
    }
    return $changed;
}

exit(0);
