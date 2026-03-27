<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('migrations') // Très important : ne pas toucher aux fichiers générés par Doctrine
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true, // Ajoute la norme de style PSR-12 (la plus utilisée)
        'array_syntax' => ['syntax' => 'short'], // Force les [] au lieu de array()
        'ordered_imports' => ['sort_algorithm' => 'alpha'], // Trie tes "use" par ordre alphabétique
        'no_unused_imports' => true, // Supprime les imports qui ne servent à rien
        'blank_line_between_import_groups' => true,
    ])
    ->setFinder($finder)
;