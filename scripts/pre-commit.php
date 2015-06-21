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
exec('git diff --cached --name-status --diff-filter=ACM', $output);

foreach ($output as $file) {
    $fileName = trim(substr($file, 1));
    if (pathinfo($fileName, PATHINFO_EXTENSION) == 'php') {

        // Check for lint errors
        $lint_output = array();
        exec('php -l '.escapeshellarg($fileName), $lint_output, $return);

        if ($return === 0) {
            /*
             * PHP-CS-Fixer && add it back
             */
            exec("vendor/bin/php-cs-fixer fix {$fileName} --level=psr2 --fixers=-psr0; git add {$fileName}");
        } else {
            echo implode("\n", $lint_output), "\n";
            exit(1);
        }
    }
}

exit(0);
