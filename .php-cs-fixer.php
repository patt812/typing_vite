<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude(['vendor'])
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/database/factories',
        __DIR__ . '/database/seeders',
        __DIR__ . '/config',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ]);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'no_empty_comment' => false,
        'array_syntax' => ['syntax' => 'short'],
        'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
        'function_typehint_space' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'no_empty_phpdoc' => true,
        'no_whitespace_before_comma_in_array' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'ternary_operator_spaces' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'method_chaining_indentation' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'arrays', 'match']],
        'trim_array_spaces' => true,
        'array_indentation' => true,
        'lowercase_keywords' => true,
        'lowercase_cast' => true,
        'no_spaces_after_function_name' => true,
        'align_multiline_comment' => true,
        'class_attributes_separation' => true,
        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'no_extra_blank_lines' => true,
        'concat_space' => ['spacing' => 'one'],
        // risky options
        'array_push' => true,
        'ereg_to_preg' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(true);
