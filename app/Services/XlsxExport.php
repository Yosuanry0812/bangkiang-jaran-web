<?php

namespace App\Services;

use DateTimeInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/**
 * Penulis XLSX minimal tanpa dependensi: satu sheet, data apa adanya (angka, teks, tanggal).
 * Tanpa tabel, grafik, atau warna; hanya header tebal, lebar kolom otomatis, dan baris judul dibekukan.
 */
class XlsxExport
{
    private const S_DEFAULT = 0;

    private const S_HEAD = 1;

    private const S_NUM = 2;

    private const S_DATE = 3;

    private const S_BOLD_NUM = 4;

    private const S_BOLD = 5;

    /**
     * @param  array<int,string>  $headers
     * @param  iterable<int,array<int,mixed>>  $rows  nilai int/float = angka, DateTimeInterface = tanggal, lainnya teks
     * @param  bool  $boldLast  tebalkan baris terakhir (baris TOTAL)
     */
    public function download(string $filename, array $headers, iterable $rows, bool $boldLast = true): BinaryFileResponse
    {
        $all = [];
        foreach ($rows as $row) {
            $all[] = array_values($row);
        }

        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat berkas XLSX.');
        }

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Laporan" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>');
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode="dd/mm/yyyy"/></numFmts>'
            .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            .'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="6">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyAlignment="1"><alignment horizontal="center"/></xf>'
            .'<xf numFmtId="3" fontId="1" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'</cellXfs></styleSheet>');

        $widths = array_map(fn ($h) => $this->len((string) $h) + 2, $headers);
        $xml = '';
        $r = 1;
        $xml .= '<row r="1" ht="32" customHeight="1">';
        foreach ($headers as $i => $h) {
            $xml .= $this->cell($i, 1, (string) $h, self::S_HEAD);
        }
        $xml .= '</row>';

        $last = count($all) - 1;
        foreach ($all as $k => $row) {
            $r++;
            $bold = $boldLast && $k === $last;
            $xml .= '<row r="'.$r.'">';
            foreach ($row as $i => $v) {
                if ($v === '' || $v === null) {
                    continue;
                }
                if ($v instanceof DateTimeInterface) {
                    $serial = $this->serial($v);
                    $xml .= '<c r="'.$this->ref($i, $r).'" s="'.self::S_DATE.'"><v>'.$serial.'</v></c>';
                    $widths[$i] = max($widths[$i] ?? 0, 12);
                } elseif (is_int($v) || is_float($v)) {
                    $xml .= '<c r="'.$this->ref($i, $r).'" s="'.($bold ? self::S_BOLD_NUM : self::S_NUM).'"><v>'.$v.'</v></c>';
                    $widths[$i] = max($widths[$i] ?? 0, $this->len(number_format((float) $v, 0, ',', '.')) + 2);
                } else {
                    $xml .= $this->cell($i, $r, (string) $v, $bold ? self::S_BOLD : self::S_DEFAULT);
                    $widths[$i] = max($widths[$i] ?? 0, $this->len((string) $v) + 2);
                }
            }
            $xml .= '</row>';
        }

        $cols = '';
        foreach ($headers as $i => $_) {
            $w = min(max($widths[$i] ?? 10, 10), 45);
            $cols .= '<col min="'.($i + 1).'" max="'.($i + 1).'" width="'.$w.'" customWidth="1"/>';
        }

        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            .'<cols>'.$cols.'</cols><sheetData>'.$xml.'</sheetData></worksheet>');
        $zip->close();

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function cell(int $col, int $row, string $text, int $style): string
    {
        return '<c r="'.$this->ref($col, $row).'" t="inlineStr" s="'.$style.'"><is><t xml:space="preserve">'
            .htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</t></is></c>';
    }

    private function ref(int $col, int $row): string
    {
        $n = $col + 1;
        $s = '';
        while ($n > 0) {
            $m = ($n - 1) % 26;
            $s = chr(65 + $m).$s;
            $n = intdiv($n - 1, 26);
        }

        return $s.$row;
    }

    private function serial(DateTimeInterface $d): int
    {
        return intdiv(gmmktime(0, 0, 0, (int) $d->format('n'), (int) $d->format('j'), (int) $d->format('Y')), 86400) + 25569;
    }

    private function len(string $s): int
    {
        return function_exists('mb_strlen') ? mb_strlen($s) : strlen($s);
    }
}
