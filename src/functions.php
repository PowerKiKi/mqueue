<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Laminas\Translator\TranslatorInterface;

/**
 * Returns the Entity Manager.
 */
function _em(): EntityManager
{
    global $container;

    return $container->get(EntityManager::class);
}

/**
 * Dump all arguments.
 */
function v(): void
{
    var_dump(func_get_args());
}

/**
 * Dump all arguments and die.
 */
function w(): never
{
    $isHtml = (PHP_SAPI !== 'cli');
    echo "\n_________________________________________________________________________________________________________________________" . ($isHtml ? '</br>' : '') . "\n";
    var_dump(func_get_args());
    echo "\n" . ($isHtml ? '</br>' : '') . '_________________________________________________________________________________________________________________________' . ($isHtml ? '<pre>' : '') . "\n";
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    echo '' . ($isHtml ? '</pre>' : '') . '_________________________________________________________________________________________________________________________' . ($isHtml ? '</br>' : '') . "\n";
    exit("script aborted on purpose.\n");
}

/**
 * Translate given message in current language.
 *
 * If replacements are given, they will be replaced after translation:
 *
 * ```
 * _tr('Hello %my-name%', ['my-name' => 'John']); // Bonjour John
 * ```
 *
 * @param array<string, null|float|int|string> $replacements
 */
function _tr(string $message, array $replacements = []): string
{
    global $container;

    $translator = $container->get(TranslatorInterface::class);
    $translation = $translator->translate($message);
    if (!$replacements) {
        return $translation;
    }

    $finalReplacements = [];
    foreach ($replacements as $key => $value) {
        $finalReplacements['%' . $key . '%'] = $value;
    }

    return strtr($translation, $finalReplacements);
}
