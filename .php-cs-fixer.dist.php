<?php

$finder = PhpCsFixer\Finder::create()
    ->name('*.php')
    ->in(['./']);

$config = new PhpCsFixer\Config();
$config->setFinder($finder)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_after_namespace' => true,
        'blank_line_before_statement' => true,
        'concat_space' => ['spacing' => 'one'],
        'function_typehint_space' => true,
        'include' => true,
        'line_ending' => true,
        'new_with_braces' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'multiline_whitespace_before_semicolons' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'phpdoc_align' => true,
        'phpdoc_separation' => true,
        'phpdoc_tag_type' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => true,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => true,
    ]);

return $config;
