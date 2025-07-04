<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
class TemplateController extends Controller{
    public function downloadCsv()
    {
        $filename = 'ClockingTemplate.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $out = fopen('php://output', 'w');

            // first row = header columns
            fputcsv($out, ['AC-No.'	,'Name'	,'Time'	,'State']);

            // if you have default/sample rows, loop and fputcsv…
            // e.g. foreach ($sampleData as $row) { fputcsv($out, $row); }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
