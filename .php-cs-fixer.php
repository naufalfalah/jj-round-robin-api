<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->exclude('vendor')
    ->exclude('storage')
    ->exclude('bootstrap/cache')
    // Prevent break laravel generated file
    ->notPath('database/migrations')
    ->notPath('config')
    ->notPath('resources/lang');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'align_multiline_comment' => true,
        'binary_operator_spaces' => [
            'default' => 'single_space',
            // Don't align array keys/values
            'operators' => ['=>' => 'single_space']
        ],
        'array_indentation' => true, // Prevent array indentation changes
        'single_quote' => true,
        'single_space_after_construct' => true,
        'class_definition' => [
            'single_line' => false, // Ensure class definitions are multiline
            'inline_constructor_arguments' => false, // Ensure constructors are multiline
            'space_before_parenthesis' => true, // Ensure space before parenthesis
        ],
        'no_extra_blank_lines' => [
            'tokens' => [
                'return',
            ],
        ],
    ])
    ->setFinder($finder);
