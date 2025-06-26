<?php

namespace App\Services;
use App\Models\ClockingDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ExportingService{

    /*Export any Eloquent model as CSV.*/
    public function exportCSV(Request $request, string $modelClass, $startDateParam = null, $endDateParam = null): StreamedResponse
    {
        Log::info("{$modelClass} CSV export requested", [
            'ip'         => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // Parse filters
        $startDate       = $startDateParam ?? $request->query('start_date');
        $endDate         = $endDateParam   ?? $request->query('end_date');




        // Build and execute query
        $query = $modelClass::query();
        if ($startDate && $endDate) {
            $query->whereBetween('Date', [$startDate, $endDate]);
        }

        $data = $query->get();
        $count = $data->count();

        Log::info("Data retrieved for {$modelClass}", compact('count'));

        // Column names: override in controller or extend this service if needed
        $columns = property_exists($modelClass, 'exportColumns')
            ? $modelClass::$exportColumns
            : array_keys($modelClass::first()->getAttributes());

        // Stream callback
        $callback = function() use ($data, $columns) {
            $handle = fopen('php://output','w');
            fputcsv($handle, $columns);
            foreach ($data as $item) {
                $row = array_map(fn($col) => $item->{$col}, $columns);
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        $filename = $this->makeFilename((new \ReflectionClass($modelClass))->getShortName(), $startDate, $endDate, 'csv');

        return response()->streamDownload($callback, $filename, ['Content-Type'=>'text/csv']);
    }

    /*Export any Eloquent model as JSON.*/
    public function exportJson(Request $request, string $modelClass, $startDateParam = null, $endDateParam = null)
    {
        Log::info("{$modelClass} JSON export requested", [
            'ip'         => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        $startDate = $startDateParam ?? $request->query('start_date');
        $endDate   = $endDateParam   ?? $request->query('end_date');


        $query = $modelClass::query();
        if ($startDate && $endDate) {
            $query->whereBetween('Date', [$startDate, $endDate]);
        }

        $data  = $query->get();
        $count = $data->count();

        return response()->json([
            'success'      => true,
            'record_count' => $count,
            'data'         => $data,
        ]);
    }
    /** Build filename */
    protected function makeFilename(string $base, $start, $end, string $ext): string
    {
        $name = strtolower($base) . '_' . ($start && $end ? "{$start}_to_{$end}" : 'all_dates');

        return "{$name}.{$ext}";
    }

}
