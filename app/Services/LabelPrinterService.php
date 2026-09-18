<?php

namespace App\Services;

use App\Models\KitPart;
use App\Models\Setting;
use Throwable;

class LabelPrinterService
{
    public function printPartLabel(KitPart $part): array
    {
        $settings = Setting::query()->pluck('value', 'key')->toArray();

        $ip = $settings['printer_ip'] ?? null;
        $port = (int)($settings['printer_port'] ?? 9100);

        if (!$ip) {
            return [
                'success' => false,
                'message' => 'IP принтера не налаштовано',
            ];
        }

        $barcode = $part->barcode;
        $title = $this->safeText($part->title);

        $tspl = <<<TSPL
SIZE 58 mm,40 mm
GAP 2 mm,0
DENSITY 8
DIRECTION 1
CLS
TEXT 30,25,"3",0,1,1,"{$barcode}"
BARCODE 30,70,"128",90,1,0,2,2,"{$barcode}"
TEXT 30,180,"3",0,1,1,"{$title}"
PRINT 1,1

TSPL;

        try {
            $socket = fsockopen($ip, $port, $errno, $errstr, 3);

            if (!$socket) {
                return [
                    'success' => false,
                    'message' => "Принтер недоступний: {$errstr}",
                ];
            }

            fwrite($socket, $tspl);
            fclose($socket);

            $part->update([
                'printed_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Етикетку відправлено на друк',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function safeText(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;

        return mb_substr(
            preg_replace('/[^A-Za-z0-9\-\s]/', '', $text),
            0,
            28
        );
    }
}
