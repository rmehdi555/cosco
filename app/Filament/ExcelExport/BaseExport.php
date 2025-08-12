<?php

namespace App\Filament\ExcelExport;

use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class BaseExport
{
    /**
     * Generate CSV response with proper headers and content
     */
    protected static function generateCsvResponse(Collection $data, string $filename): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 to ensure proper Persian text rendering in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write CSV headers
            fputcsv($file, static::getCsvHeaders());
            
            // Write data rows
            foreach ($data as $record) {
                fputcsv($file, static::formatRecordData($record));
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get the filename for export
     */
    protected static function getFilename(string $prefix = 'export', bool $isSelected = false): string
    {
        $suffix = $isSelected ? '-selected' : '';
        return $prefix . $suffix . '-' . now()->format('Y-m-d-H-i-s') . '.csv';
    }

    /**
     * Get CSV column headers - must be implemented by child classes
     */
    abstract protected static function getCsvHeaders(): array;

    /**
     * Format record data for CSV export - must be implemented by child classes
     */
    abstract protected static function formatRecordData($record): array;
}
