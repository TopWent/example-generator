<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('var')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_var_without_name' => false,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;