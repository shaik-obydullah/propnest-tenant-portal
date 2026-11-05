<?php

namespace App\Support;

/**
 * A tiny dependency-free PDF writer used for the demo document library.
 * It produces a single A4 page with a heading and wrapped lines of text.
 */
class SimplePdf
{
    public static function make(string $title, string $subtitle, array $lines): string
    {
        $content = "BT\n/F1 22 Tf\n54 790 Td\n(" . self::escape($title) . ") Tj\nET\n";
        $content .= "BT\n/F1 10 Tf\n54 766 Td\n(" . self::escape($subtitle) . ") Tj\nET\n";

        $y = 730;
        foreach ($lines as $line) {
            foreach (self::wrap($line, 92) as $wrapped) {
                if ($y < 60) {
                    break 2;
                }
                $content .= "BT\n/F1 11 Tf\n54 {$y} Td\n(" . self::escape($wrapped) . ") Tj\nET\n";
                $y -= 18;
            }
            $y -= 6;
        }

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "endstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    private static function escape(string $text): string
    {
        $text = str_replace(["\r", "\n"], ' ', $text);

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    /**
     * @return array<int, string>
     */
    private static function wrap(string $text, int $width): array
    {
        return explode("\n", wordwrap($text, $width, "\n", true));
    }
}
