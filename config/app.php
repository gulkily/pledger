<?php

$projectRoot = dirname(__DIR__);
$causeDir = __DIR__ . '/causes';
$defaultSlug = 'github-universe-trip';

$envSlug = getenv('PLEDGER_CAUSE');
$selectorFile = __DIR__ . '/current_cause.php';
$fileSlug = null;
if (file_exists($selectorFile)) {
    $selected = require $selectorFile;
    if (is_string($selected) && trim($selected) !== '') {
        $fileSlug = trim($selected);
    }
}

$causeSlug = $envSlug ?: $fileSlug ?: $defaultSlug;
$causeFile = $causeDir . '/' . $causeSlug . '.php';

if (!file_exists($causeFile)) {
    $causeSlug = $defaultSlug;
    $causeFile = $causeDir . '/' . $defaultSlug . '.php';
}

if (!file_exists($causeFile)) {
    throw new RuntimeException("Cause config not found: {$causeFile}");
}

$causeConfig = require $causeFile;
if (!is_array($causeConfig)) {
    throw new RuntimeException("Cause config must return an array: {$causeFile}");
}

if (!isset($causeConfig['db_path'])) {
    $causeConfig['db_path'] = $projectRoot . '/pledges.db';
}

return array_merge([
    'project_root' => $projectRoot,
    'cause_slug' => $causeConfig['slug'] ?? $causeSlug,
    'cause_file' => $causeFile,
    'selector' => [
        'env' => $envSlug,
        'file' => $fileSlug,
    ],
], $causeConfig);
