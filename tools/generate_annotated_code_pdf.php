<?php

$root = realpath(dirname(__DIR__)) ?: dirname(__DIR__);
$output = $root . DIRECTORY_SEPARATOR . 'TaskFlow_Annotated_Code_Guide.pdf';

$includeRoots = [
    'app',
    'routes',
    'database',
    'resources',
    'config',
    'bootstrap',
    'tests',
];

$includeFiles = [
    'artisan',
    'composer.json',
    'package.json',
    'phpunit.xml',
    'tailwind.config.js',
    'vite.config.js',
];

$allowedExtensions = [
    'php',
    'blade.php',
    'js',
    'css',
    'json',
    'xml',
];

function relative_path(string $root, string $path): string
{
    return str_replace('\\', '/', ltrim(substr($path, strlen($root)), DIRECTORY_SEPARATOR));
}

function should_include_file(string $root, string $path, array $allowedExtensions, array $includeFiles): bool
{
    $relative = relative_path($root, $path);

    if (in_array($relative, $includeFiles, true)) {
        return true;
    }

    foreach ($allowedExtensions as $extension) {
        if (str_ends_with($relative, '.' . $extension)) {
            return true;
        }
    }

    return false;
}

function line_note(string $line): string
{
    $trimmed = trim($line);

    if ($trimmed === '') {
        return 'Blank line used to separate code sections.';
    }

    if (str_starts_with($trimmed, '<?php')) {
        return 'Starts a PHP file.';
    }

    if (str_starts_with($trimmed, 'namespace ')) {
        return 'Defines where this PHP class belongs.';
    }

    if (str_starts_with($trimmed, 'use ')) {
        return 'Imports another class or helper for this file.';
    }

    if (preg_match('/^class\s+/', $trimmed)) {
        return 'Declares a class that holds related behavior.';
    }

    if (preg_match('/^public function\s+([A-Za-z0-9_]+)/', $trimmed, $matches)) {
        return 'Defines the "' . $matches[1] . '" method.';
    }

    if (preg_match('/^protected function\s+([A-Za-z0-9_]+)/', $trimmed, $matches)) {
        return 'Defines the protected "' . $matches[1] . '" method.';
    }

    if (preg_match('/^\$[A-Za-z0-9_]+/', $trimmed) || str_contains($trimmed, ' = ')) {
        return 'Sets or updates a value used by the application.';
    }

    if (str_contains($trimmed, 'Route::')) {
        return 'Registers a web route for the application.';
    }

    if (str_contains($trimmed, 'return view')) {
        return 'Returns a Blade view to display UI.';
    }

    if (str_contains($trimmed, 'return redirect')) {
        return 'Redirects the browser to another route or page.';
    }

    if (str_contains($trimmed, 'validate(')) {
        return 'Validates user input before saving or using it.';
    }

    if (str_contains($trimmed, 'create([')) {
        return 'Creates a new database record.';
    }

    if (str_contains($trimmed, 'update([')) {
        return 'Updates an existing database record.';
    }

    if (str_contains($trimmed, 'delete(')) {
        return 'Deletes a database record or item.';
    }

    if (str_contains($trimmed, 'auth()->user()') || str_contains($trimmed, 'auth()->id()')) {
        return 'Uses the currently logged-in user.';
    }

    if (str_contains($trimmed, 'wire:')) {
        return 'Livewire directive that connects UI to component behavior.';
    }

    if (str_contains($trimmed, '@if') || str_contains($trimmed, 'if (')) {
        return 'Conditional logic that runs only when the condition is true.';
    }

    if (str_contains($trimmed, '@foreach') || str_contains($trimmed, 'foreach')) {
        return 'Loops through a collection of items.';
    }

    if (str_contains($trimmed, '@forelse')) {
        return 'Loops through items and handles the empty state.';
    }

    if (str_contains($trimmed, '@empty')) {
        return 'Displays fallback content when no items exist.';
    }

    if (str_contains($trimmed, '@endif') || $trimmed === '}') {
        return 'Closes the current block of code.';
    }

    if (str_starts_with($trimmed, '<')) {
        return 'Blade/HTML markup that renders part of the page.';
    }

    if (str_contains($trimmed, 'class=')) {
        return 'Tailwind classes control layout, spacing, colors, and state.';
    }

    if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*')) {
        return 'Developer comment explaining nearby code.';
    }

    return 'Part of this file\'s application behavior or presentation.';
}

function pdf_escape(string $text): string
{
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}

class SimplePdf
{
    private array $pages = [];
    private array $current = [];
    private int $line = 0;
    private int $maxLines = 46;

    public function addPage(): void
    {
        if ($this->current !== []) {
            $this->pages[] = $this->current;
        }

        $this->current = [];
        $this->line = 0;
    }

    public function text(string $text, int $size = 8, string $font = 'F1'): void
    {
        if ($this->current === [] || $this->line >= $this->maxLines) {
            $this->addPage();
        }

        $y = 790 - ($this->line * 16);
        $this->current[] = sprintf('BT /%s %d Tf 36 %d Td (%s) Tj ET', $font, $size, $y, pdf_escape($text));
        $this->line++;
    }

    public function blank(): void
    {
        $this->text('', 8);
    }

    public function save(string $path): void
    {
        if ($this->current !== []) {
            $this->pages[] = $this->current;
            $this->current = [];
        }

        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $kids = [];
        $pageObjectNumbers = [];

        foreach ($this->pages as $index => $pageLines) {
            $pageObjectNumbers[] = 3 + ($index * 2);
            $kids[] = (3 + ($index * 2)) . ' 0 R';
        }

        $objects[] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($this->pages) . ' >>';

        foreach ($this->pages as $index => $pageLines) {
            $content = implode("\n", $pageLines);
            $contentNumber = 4 + ($index * 2);
            $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 842] /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> /F2 << /Type /Font /Subtype /Type1 /BaseFont /Courier >> >> >> /Contents ' . $contentNumber . ' 0 R >>';
            $objects[] = '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        }

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($number + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";

        file_put_contents($path, $pdf);
    }
}

$files = [];

foreach ($includeRoots as $directory) {
    $fullDirectory = $root . DIRECTORY_SEPARATOR . $directory;

    if (! is_dir($fullDirectory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fullDirectory, FilesystemIterator::SKIP_DOTS));

    foreach ($iterator as $file) {
        if ($file->isFile() && should_include_file($root, $file->getPathname(), $allowedExtensions, $includeFiles)) {
            $files[] = $file->getPathname();
        }
    }
}

foreach ($includeFiles as $file) {
    $path = $root . DIRECTORY_SEPARATOR . $file;

    if (is_file($path)) {
        $files[] = $path;
    }
}

$files = array_values(array_unique($files));
sort($files);

$pdf = new SimplePdf();
$pdf->text('TaskFlow Annotated Code Guide', 18);
$pdf->text('Generated from first-party project files. Dependencies and build artifacts are excluded.', 10);
$pdf->blank();

foreach ($files as $file) {
    $relative = relative_path($root, $file);
    $pdf->text('FILE: ' . $relative, 12);

    $lines = file($file, FILE_IGNORE_NEW_LINES);

    foreach ($lines as $index => $line) {
        $number = str_pad((string) ($index + 1), 4, ' ', STR_PAD_LEFT);
        $code = mb_substr(str_replace("\t", '    ', $line), 0, 86);
        $note = line_note($line);
        $pdf->text($number . ' | ' . $code, 7, 'F2');
        $pdf->text('     -> ' . $note, 7);
    }

    $pdf->blank();
}

$pdf->save($output);

echo $output . PHP_EOL;
