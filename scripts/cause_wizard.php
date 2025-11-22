#!/usr/bin/env php
<?php
/**
 * Cause configuration wizard (skeleton).
 */

const USAGE = <<<TXT
Cause Wizard
============
Usage:
  php scripts/cause_wizard.php [command] [options]

Commands:
  create             Interactively create a new cause config (default)
  update <slug>      Update an existing cause config
  --help, -h         Show this message
TXT;

function main(array $argv): int {
    $script = array_shift($argv);
    $command = $argv[0] ?? 'create';
    $projectRoot = dirname(__DIR__);
    $causesDir = $projectRoot . '/config/causes';

    if (in_array($command, ['--help', '-h'], true)) {
        fwrite(STDOUT, USAGE . PHP_EOL);
        return 0;
    }

    if ($command === 'update') {
        $slug = $argv[1] ?? null;
        if (!$slug) {
            fwrite(STDERR, "Missing slug for update command." . PHP_EOL);
            fwrite(STDOUT, USAGE . PHP_EOL);
            return 1;
        }
        $existing = loadExistingConfig($causesDir, $slug);
        if ($existing === null) {
            fwrite(STDERR, "No config found for slug '{$slug}'." . PHP_EOL);
            return 1;
        }
        $data = collectCauseData('update', $projectRoot, $causesDir, $existing, $slug);
        displaySummary($data, $existing);
        showConfigPreview($data);
        finalizeWizard($data, 'update', $projectRoot, $causesDir);
        return 0;
    }

    if ($command !== 'create') {
        fwrite(STDERR, "Unknown command: {$command}" . PHP_EOL);
        fwrite(STDOUT, USAGE . PHP_EOL);
        return 1;
    }

    $data = collectCauseData('create', $projectRoot, $causesDir, null, null);
    displaySummary($data, null);
    showConfigPreview($data);
    finalizeWizard($data, 'create', $projectRoot, $causesDir);
    return 0;
}

function loadExistingConfig(string $causesDir, string $slug): ?array {
    $file = rtrim($causesDir, '/\n') . '/' . $slug . '.php';
    if (!is_file($file)) {
        return null;
    }
    $config = require $file;
    return is_array($config) ? $config : null;
}

function collectCauseData(string $mode, string $projectRoot, string $causesDir, ?array $existing, ?string $slugOverride): array {
    fwrite(STDOUT, PHP_EOL . "--- Cause Wizard ({$mode}) ---" . PHP_EOL);
    $data = [];

    $data['slug'] = $slugOverride ?? promptSlug($existing['slug'] ?? null);
    $data['display_name'] = promptString('Display name', $existing['display_name'] ?? null, true);

    $defaultDb = $existing['db_path'] ?? ($projectRoot . '/data/' . $data['slug'] . '.db');
    $data['db_path'] = promptString('SQLite path', $defaultDb, true, function ($value) use ($projectRoot) {
        return validateDbPath($value, $projectRoot);
    });

    $price = $existing['price_range'] ?? [];
    $minPrice = promptNumber('Estimated min price', $price['min'] ?? null, 1);
    $maxPrice = promptNumber('Estimated max price', $price['max'] ?? null, $minPrice);
    $priceLabel = promptString('Price range description', $price['description'] ?? 'Estimated cost', true);
    $data['price_range'] = [
        'min' => $minPrice,
        'max' => $maxPrice,
        'description' => $priceLabel,
    ];

    $data['deadline'] = promptDate('Deadline (YYYY-MM-DD)', $existing['deadline'] ?? null);
    $data['goal_banner'] = promptString('Goal banner text', $existing['goal_banner'] ?? '', true);

    $hero = $existing['hero'] ?? [];
    $data['hero'] = [
        'headline' => promptString('Hero headline', $hero['headline'] ?? '', true),
        'tagline' => promptString('Hero tagline (HTML allowed)', $hero['tagline'] ?? '', true),
        'subtext' => promptString('Hero subtext', $hero['subtext'] ?? '', true),
        'avatar' => [
            'src' => promptString('Avatar image path/URL', $hero['avatar']['src'] ?? 'image/1530699.jpeg', true),
            'alt' => promptString('Avatar alt text', $hero['avatar']['alt'] ?? '', true),
            'link' => promptString('Avatar link URL', $hero['avatar']['link'] ?? '#', false),
        ],
    ];

    $story = $existing['story'] ?? [];
    $data['story'] = [
        'why_it_matters' => promptList("Story paragraphs (one per line, '.' to finish)", $story['why_it_matters'] ?? []),
        'explore' => [
            'heading' => promptString('Explore heading', $story['explore']['heading'] ?? 'What we are exploring', true),
            'items' => promptList("Explore bullets (one per line, '.' to finish)", $story['explore']['items'] ?? []),
        ],
    ];

    $data['research_projects'] = promptResearchProjects($existing['research_projects'] ?? []);

    return $data;
}

function promptSlug(?string $default): string {
    return promptString('Cause slug (letters, numbers, -)', $default, true, function ($value) {
        return (bool) preg_match('/^[a-z0-9-]+$/', $value);
    });
}

function promptDate(string $label, ?string $default): string {
    return promptString($label, $default, true, function ($value) {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    });
}

function promptNumber(string $label, ?int $default, ?int $min = null): int {
    while (true) {
        $input = ask("{$label}" . formatDefault($default) . ': ');
        if ($input === '' && $default !== null) {
            return (int) $default;
        }
        if (!is_numeric($input)) {
            fwrite(STDOUT, "Please enter a number." . PHP_EOL);
            continue;
        }
        $value = (int) $input;
        if ($min !== null && $value < $min) {
            fwrite(STDOUT, "Value must be at least {$min}." . PHP_EOL);
            continue;
        }
        return $value;
    }
}

function promptList(string $label, array $default): array {
    fwrite(STDOUT, PHP_EOL . $label . PHP_EOL);
    if (!empty($default) && promptYesNo('Keep existing values?', true)) {
        return $default;
    }

    fwrite(STDOUT, "Enter one entry per line. Type a single '.' to finish." . PHP_EOL);
    $items = [];
    while (true) {
        $line = ask('> ');
        if ($line === '.') {
            break;
        }
        if ($line === '' && empty($items)) {
            fwrite(STDOUT, "Please enter at least one item." . PHP_EOL);
            continue;
        }
        if ($line !== '') {
            $items[] = $line;
        }
    }
    return $items;
}

function promptResearchProjects(array $existing): array {
    fwrite(STDOUT, PHP_EOL . "Research projects" . PHP_EOL);
    if (!empty($existing) && promptYesNo('Keep existing projects?', true)) {
        return $existing;
    }

    $projects = [];
    while (promptYesNo('Add a research project?', empty($projects))) {
        $title = promptString('  Project title', null, true);
        $tags = promptCommaList('  Tags (comma separated)', []);
        $description = promptString('  Description', null, true);
        $projects[] = [
            'title' => $title,
            'tags' => $tags,
            'description' => $description,
        ];
    }

    return $projects;
}

function promptCommaList(string $label, array $default): array {
    $raw = promptString($label, empty($default) ? null : implode(', ', $default), false);
    if ($raw === '') {
        return $default;
    }
    return array_values(array_filter(array_map('trim', explode(',', $raw)), fn($value) => $value !== ''));
}

function promptString(string $label, ?string $default, bool $required, callable $validator = null): string {
    while (true) {
        $input = ask("{$label}" . formatDefault($default) . ': ');
        if ($input === '' && $default !== null) {
            return $default;
        }
        if ($input === '' && !$required) {
            return '';
        }
        if ($input === '' && $required) {
            fwrite(STDOUT, "This field is required." . PHP_EOL);
            continue;
        }
        if ($validator && !$validator($input)) {
            fwrite(STDOUT, "Invalid value, please try again." . PHP_EOL);
            continue;
        }
        return $input;
    }
}

function promptYesNo(string $label, bool $default): bool {
    $suffix = $default ? ' [Y/n]' : ' [y/N]';
    while (true) {
        $input = strtolower(ask($label . $suffix . ': '));
        if ($input === '' && $default !== null) {
            return $default;
        }
        if (in_array($input, ['y', 'yes'], true)) {
            return true;
        }
        if (in_array($input, ['n', 'no'], true)) {
            return false;
        }
        fwrite(STDOUT, "Please answer yes or no." . PHP_EOL);
    }
}

function ask(string $prompt): string {
    if (function_exists('readline')) {
        $input = readline($prompt);
        if ($input === false) {
            return '';
        }
        if ($input !== '') {
            readline_add_history($input);
        }
        return trim($input);
    }

    fwrite(STDOUT, $prompt);
    $line = fgets(STDIN);
    return $line === false ? '' : trim($line);
}

function formatDefault($value): string {
    if ($value === null || $value === '') {
        return '';
    }
    return " [{$value}]";
}

function validateDbPath(string $value, string $projectRoot): bool {
    return trim($value) !== '';
}

function displaySummary(array $data, ?array $existing): void {
    fwrite(STDOUT, PHP_EOL . "Summary" . PHP_EOL);
    fwrite(STDOUT, str_repeat('-', 20) . PHP_EOL);
    fwrite(STDOUT, "Slug: {$data['slug']}" . PHP_EOL);
    fwrite(STDOUT, "Name: {$data['display_name']}" . PHP_EOL);
    fwrite(STDOUT, "Database: {$data['db_path']}" . PHP_EOL);
    fwrite(STDOUT, "Price range: {$data['price_range']['min']} - {$data['price_range']['max']} ({$data['price_range']['description']})" . PHP_EOL);
    fwrite(STDOUT, "Deadline: {$data['deadline']}" . PHP_EOL);
    fwrite(STDOUT, "Story paragraphs: " . count($data['story']['why_it_matters']) . PHP_EOL);
    fwrite(STDOUT, "Research projects: " . count($data['research_projects']) . PHP_EOL);

    if ($existing) {
        $changed = [];
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $existing) || serialize($existing[$key]) !== serialize($value)) {
                $changed[] = $key;
            }
        }
        fwrite(STDOUT, "Updated fields: " . (empty($changed) ? 'None' : implode(', ', $changed)) . PHP_EOL);
    }
}

function showConfigPreview(array $data): void {
    fwrite(STDOUT, PHP_EOL . "Config preview" . PHP_EOL);
    fwrite(STDOUT, str_repeat('-', 20) . PHP_EOL);
    fwrite(STDOUT, renderConfigPhp($data) . PHP_EOL);
}

function finalizeWizard(array $data, string $mode, string $projectRoot, string $causesDir): void {
    if (!promptYesNo('Write configuration file now?', true)) {
        fwrite(STDOUT, "Skipped writing files. Preview only." . PHP_EOL);
        return;
    }

    $configPath = rtrim($causesDir, '\\/') . '/' . $data['slug'] . '.php';
    $writtenPath = writeConfigFile($configPath, renderConfigPhp($data));
    fwrite(STDOUT, "Config saved to {$writtenPath}." . PHP_EOL);

    $selectorPath = $projectRoot . '/config/current_cause.php';
    if (promptYesNo('Set this cause as the current selector?', $mode === 'create')) {
        updateCurrentCause($selectorPath, $data['slug']);
        fwrite(STDOUT, "Updated selector file." . PHP_EOL);
    }

    if (promptYesNo('Ensure SQLite path exists?', true)) {
        ensureDatabasePath($data['db_path'], $projectRoot);
    }
}

function renderConfigPhp(array $data): string {
    $export = exportArray($data, 0);
    return "<?php\n\nreturn {$export};";
}

function exportArray(array $data, int $indent): string {
    $lines = ['['];
    $pad = str_repeat('    ', $indent + 1);
    foreach ($data as $key => $value) {
        $keyExport = is_int($key) ? $key : var_export($key, true);
        $valueExport = exportValue($value, $indent + 1);
        $lines[] = sprintf('%s%s => %s,', $pad, $keyExport, $valueExport);
    }
    $lines[] = str_repeat('    ', $indent) . ']';
    return implode(PHP_EOL, $lines);
}

function exportValue(mixed $value, int $indent): string {
    if (is_array($value)) {
        return exportArray($value, $indent);
    }
    return var_export($value, true);
}

function writeConfigFile(string $path, string $contents): string {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    if (is_file($path)) {
        $backup = $path . '.' . date('YmdHis') . '.bak';
        copy($path, $backup);
        fwrite(STDOUT, "Existing config backed up to {$backup}." . PHP_EOL);
    }
    file_put_contents($path, rtrim($contents) . PHP_EOL);
    return $path;
}

function updateCurrentCause(string $file, string $slug): void {
    $contents = <<<PHP
<?php
// Returns the slug for the currently active cause. Override via env `PLEDGER_CAUSE` when needed.
return '{$slug}';
PHP;
    file_put_contents($file, $contents . PHP_EOL);
}

function ensureDatabasePath(string $dbPath, string $projectRoot): void {
    $absolute = resolvePath($dbPath, $projectRoot);
    $dir = dirname($absolute);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
        fwrite(STDOUT, "Created directory {$dir}." . PHP_EOL);
    }
    if (!file_exists($absolute)) {
        touch($absolute);
        fwrite(STDOUT, "Created placeholder database file at {$absolute}." . PHP_EOL);
    } else {
        fwrite(STDOUT, "Database file already exists at {$absolute}." . PHP_EOL);
    }
}

function resolvePath(string $path, string $projectRoot): string {
    if ($path === '') {
        return $projectRoot;
    }
    if (preg_match('/^([A-Za-z]:\\\\|\\\\)/', $path) || stringStartsWith($path, '/')) {
        return $path;
    }
    return rtrim($projectRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}

function stringStartsWith(string $haystack, string $needle): bool {
    if ($needle === '') {
        return true;
    }
    return strncmp($haystack, $needle, strlen($needle)) === 0;
}

exit(main($argv));
