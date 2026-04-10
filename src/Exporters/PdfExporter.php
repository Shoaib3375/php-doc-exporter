<?php

namespace Shoaib3375\PhpDocExporter\Exporters;

use Dompdf\Dompdf;
use Dompdf\Options;
use Shoaib3375\PhpDocExporter\ExporterInterface;

/**
 * PdfExporter
 *
 * Converts array data or raw HTML into a PDF binary string using Dompdf.
 *
 * Key improvements over the original:
 *  - Auto-detects Bangla Unicode in data/HTML and switches font automatically.
 *  - Bundles NotoSansBengali font from the package's own fonts/ directory,
 *    so users never have to install fonts manually.
 *  - Falls back to DejaVu Sans for non-Bangla content (Latin, numbers, etc.).
 *  - Registers the bundled font with Dompdf at runtime using font metrics.
 *  - Exposes a static helper isBangla() for use by DocumentExporter too.
 */
class PdfExporter implements ExporterInterface
{
    // -----------------------------------------------------------------------
    // Constants
    // -----------------------------------------------------------------------

    /** Unicode block for Bangla: U+0980 – U+09FF */
    private const BANGLA_REGEX = '/[\x{0980}-\x{09FF}]/u';

    /** Default font when no Bangla content is detected */
    private const DEFAULT_FONT = 'DejaVu Sans';

    /** Font name registered with Dompdf for Bangla */
    private const BANGLA_FONT_NAME = 'NotoSansBengali';

    /**
     * Path to the bundled fonts directory inside this package.
     * Composer installs the package at vendor/shoaib3375/php-doc-exporter,
     * so __DIR__ points to src/Exporters/ → go one level up → fonts/.
     */
    private const FONTS_DIR = __DIR__ . '/../fonts/';

    // -----------------------------------------------------------------------
    // Public API
    // -----------------------------------------------------------------------

    /**
     * Export an associative-array dataset as a PDF.
     *
     * @param  array  $data     Rows: each row is an associative array (keys → column headers).
     * @param  array  $options  Optional: title, paper, orientation, font.
     * @return string           Raw PDF binary content.
     */
    public function export(array $data, array $options = []): string
    {
        $html = $this->generateHtml($data, $options);
        return $this->renderPdf($html, $data, $options);
    }

    /**
     * Export a pre-rendered HTML string as a PDF (used by Blade-template flow).
     *
     * @param  string $html     Fully rendered HTML.
     * @param  array  $options  Optional: paper, orientation, font.
     * @return string           Raw PDF binary content.
     */
    public function exportFromHtml(string $html, array $options = []): string
    {
        // For Blade exports we detect Bangla directly in the HTML string.
        return $this->renderPdf($html, [], $options, $html);
    }

    /**
     * Utility: check whether a string (or JSON-encoded array) contains
     * any Bangla Unicode codepoints.
     *
     * @param  string|array $content
     * @return bool
     */
    public static function isBangla(string|array $content): bool
    {
        $text = is_array($content) ? json_encode($content) : $content;
        return (bool) preg_match(self::BANGLA_REGEX, $text);
    }

    // -----------------------------------------------------------------------
    // Internal helpers
    // -----------------------------------------------------------------------

    /**
     * Build a simple HTML table from array data, embedding the correct font.
     */
    private function generateHtml(array $data, array $options): string
    {
        if (empty($data)) {
            return '<html><body><p>No data available.</p></body></html>';
        }

        $title      = htmlspecialchars($options['title'] ?? 'Report', ENT_QUOTES, 'UTF-8');
        $fontFamily = $this->resolveFontFamily($data, $options);
        $headers    = array_keys($data[0]);

        // Table header cells
        $headerHtml = '';
        foreach ($headers as $header) {
            $headerHtml .= '<th>' . htmlspecialchars((string) $header, ENT_QUOTES, 'UTF-8') . '</th>';
        }

        // Table body rows
        $rowsHtml = '';
        foreach ($data as $row) {
            $rowsHtml .= '<tr>';
            foreach ($row as $cell) {
                $rowsHtml .= '<td>' . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $rowsHtml .= '</tr>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: '{$fontFamily}', DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 16px;
            color: #111;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #4a90d9;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 12px;
        }
        td {
            border: 1px solid #ddd;
            padding: 7px 10px;
            font-size: 11px;
        }
        tr:nth-child(even) td {
            background-color: #f7f7f7;
        }
    </style>
</head>
<body>
    <h1>{$title}</h1>
    <table>
        <thead><tr>{$headerHtml}</tr></thead>
        <tbody>{$rowsHtml}</tbody>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Core render method: configures Dompdf, registers the Bangla font if
     * needed, loads the HTML, and returns the PDF binary.
     *
     * @param  string       $html         HTML to render.
     * @param  array        $dataForSniff Array data used for Bangla sniffing
     *                                    (empty when $htmlForSniff is provided).
     * @param  array        $options      paper / orientation / font options.
     * @param  string|null  $htmlForSniff When set, sniff Bangla from HTML instead of data.
     * @return string Raw PDF binary.
     */
    private function renderPdf(
        string  $html,
        array   $dataForSniff,
        array   $options      = [],
        ?string $htmlForSniff = null
    ): string {
        $fontFamily = $this->resolveFontFamily(
            $dataForSniff,
            $options,
            $htmlForSniff
        );

        $isBanglaFont  = $this->isBanglaFont($fontFamily);
        $fontsDir      = realpath(self::FONTS_DIR) ?: sys_get_temp_dir();
        $paper         = $options['paper']       ?? 'A4';
        $orientation   = $options['orientation'] ?? 'portrait';

        // --- Dompdf options ------------------------------------------------
        $dompdfOptions = new Options();
        $dompdfOptions->set('isRemoteEnabled', true);
        $dompdfOptions->set('isHtml5ParserEnabled', true);
        $dompdfOptions->set('defaultFont', $isBanglaFont ? self::BANGLA_FONT_NAME : self::DEFAULT_FONT);

        // Point Dompdf at our bundled fonts directory so it can find the
        // NotoSansBengali .ttf files without any user configuration.
        if ($isBanglaFont && is_dir($fontsDir)) {
            $dompdfOptions->set('fontDir', $fontsDir);
            $dompdfOptions->set('fontCache', $fontsDir);
        }

        // --- Dompdf instance -----------------------------------------------
        $dompdf = new Dompdf($dompdfOptions);

        // Register the bundled Bangla font with Dompdf's font metrics so it
        // can be referenced by name inside CSS font-family declarations.
        if ($isBanglaFont && is_dir($fontsDir)) {
            $this->registerBanglaFont($dompdf, $fontsDir);

            // Inject @font-face into the HTML if it isn't already present.
            if (!str_contains($html, 'NotoSansBengali') && !str_contains($html, 'Noto Sans Bengali')) {
                $html = $this->injectFontFace($html, $fontsDir, $fontFamily);
            }
        }

        // --- Render --------------------------------------------------------
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Determine which CSS font-family string to use.
     *
     * Priority:
     *  1. Explicit $options['font'] provided by the caller.
     *  2. Auto-detected Bangla in data or HTML  → NotoSansBengali.
     *  3. Default                                → DejaVu Sans.
     *
     * @param  array       $data
     * @param  array       $options
     * @param  string|null $htmlContent  HTML to sniff when data array is empty.
     * @return string  Font-family name.
     */
    private function resolveFontFamily(
        array   $data,
        array   $options,
        ?string $htmlContent = null
    ): string {
        // 1. Explicit override from caller
        if (!empty($options['font'])) {
            return $options['font'];
        }

        // 2. Auto-detect Bangla
        $sniffTarget = $htmlContent ?? (!empty($data) ? json_encode($data) : '');
        if ($sniffTarget !== '' && self::isBangla($sniffTarget)) {
            return self::BANGLA_FONT_NAME;
        }

        // 3. Fallback
        return self::DEFAULT_FONT;
    }

    /**
     * Returns true if the resolved font name is a known Bangla font.
     */
    private function isBanglaFont(string $font): bool
    {
        $banglaFonts = [
            'noto sans bengali',
            'notosansbengali',
            'kalpurush',
            'solaimanlipi',
            'nikosh',
            'siyamrupali',
        ];
        return in_array(strtolower($font), $banglaFonts, true);
    }

    /**
     * Register NotoSansBengali with Dompdf's font metrics so Dompdf
     * recognises the font-family name when it appears in CSS.
     *
     * This is a lightweight registration — it only needs to happen once per
     * request and is skipped gracefully if the .ttf files are missing.
     */
    private function registerBanglaFont(Dompdf $dompdf, string $fontsDir): void
    {
        $regularTtf = $fontsDir . '/NotoSansBengali-Regular.ttf';
        $boldTtf    = $fontsDir . '/NotoSansBengali-Bold.ttf';

        if (!file_exists($regularTtf)) {
            // Font files not bundled yet — silently skip registration.
            // Dompdf will fall back to DejaVu Sans.
            return;
        }

        $fontMetrics = $dompdf->getFontMetrics();

        // Register regular weight
        $fontMetrics->registerFont(
            ['family' => self::BANGLA_FONT_NAME, 'weight' => 'normal', 'style' => 'normal'],
            $regularTtf
        );

        // Register bold weight (fall back to regular if bold file is absent)
        $fontMetrics->registerFont(
            ['family' => self::BANGLA_FONT_NAME, 'weight' => 'bold', 'style' => 'normal'],
            file_exists($boldTtf) ? $boldTtf : $regularTtf
        );
    }

    /**
     * Inject a @font-face block into the <head> of the HTML so the CSS
     * font-family declaration resolves to the bundled .ttf file.
     *
     * This is needed when the HTML was generated by generateHtml() with a
     * Bangla font family but doesn't yet have an explicit @font-face rule.
     */
    private function injectFontFace(string $html, string $fontsDir, string $fontFamily): string
    {
        $regularTtf = $fontsDir . '/NotoSansBengali-Regular.ttf';

        if (!file_exists($regularTtf)) {
            return $html; // Nothing to inject if font file is missing
        }

        // Use the absolute path so Dompdf can resolve it regardless of CWD.
        $fontPath = realpath($regularTtf);

        $fontFace = <<<CSS
<style>
    @font-face {
        font-family: '{$fontFamily}';
        src: url('{$fontPath}');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: '{$fontFamily}';
        src: url('{$fontPath}');
        font-weight: bold;
        font-style: normal;
    }
</style>
CSS;

        // Insert before </head> if it exists, otherwise prepend to the document.
        if (stripos($html, '</head>') !== false) {
            return str_ireplace('</head>', $fontFace . '</head>', $html);
        }

        return $fontFace . $html;
    }
}
