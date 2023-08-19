<?php
/*
 * This file is part of the Calculation package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    // --------------------------------------------------------------
    //  Rule sets
    // --------------------------------------------------------------
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PHP80Migration' => true,
    '@PHP80Migration:risky' => true,
    '@PHP81Migration' => true,
    '@DoctrineAnnotation' => true,
    '@PHPUnit100Migration:risky' => true,

    // --------------------------------------------------------------
    //  Rules override
    // --------------------------------------------------------------
    'method_chaining_indentation' => true,
    'native_function_invocation' => ['include' => ['@internal', 'all']],
    'final_internal_class' => true,
    'blank_line_before_statement' => ['statements' => ['declare', 'try', 'return']],
    'no_unused_imports' => true,
    'strict_comparison' => true,
    'strict_param' => true,
    'ordered_imports' => true,
    'ordered_class_elements' => ['sort_algorithm' => 'alpha'],
    'concat_space' => ['spacing' => 'one'],
    'array_syntax' => ['syntax' => 'short'],
    'list_syntax' => ['syntax' => 'short'],
    'doctrine_annotation_array_assignment' => ['operator' => '='],
    'ordered_interfaces' => true,
];

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$config = new Config();

return $config
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules($rules);
