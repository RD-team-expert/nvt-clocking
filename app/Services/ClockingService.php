<?php

namespace App\Services;


use Carbon\Carbon;
use \ConvertApi\ConvertApi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

use PhpOffice\PhpSpreadsheet\IOFactory;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpDate;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

use App\Models\ClockingDataTable;

class ClockingService
{
    public function index($filePath,$entryNumber){
        //get the jason data
        // $jsonData = $request->json()->all();
        // Log::info('Received JSON data', ['data' => $jsonData]);

        //calling the proocessFile function to get back the CSV file path
        // $fileUrl= ['HookDataExcelFile'][0]['File'];
        $dataRows=$this->processFile($filePath);
        Log::info('Received XLS data', ['dataRows' => $dataRows]);

        //Entry Number Value
        // $entryNumber = $jsonData['Entry']['Number'] ?? null;

        // use the processClockInOut to store the data
        $this->processClockInOut($dataRows, $entryNumber);
        Log::info('Data Stored in thee Clocking table');
    }

    public function update($filePath,$entryNumber){
        //get the jason data
        // $jsonData = $request->json()->all();
        // Log::info('Received JSON data', ['data' => $jsonData]);

        //calling the proocessFile function to get back the CSV file path
         $dataRows=$this->processFile($filePath);
         Log::info('Received XLS data', ['dataRows' => $dataRows]);

        //Entry Number Value
        // $entryNumber = $jsonData['Entry']['Number'] ?? null;

        //delete the records with the same entry number
        ClockingDataTable::where('Entry_Number', $entryNumber)->delete();
        Log::info("Deleted existing clocking_data rows for Entry_Number = {$entryNumber}");

        // use the processClockInOut to store the updated data
        $this->processClockInOut($dataRows, $entryNumber);
        Log::info('Data Stored in thee Clocking table');
    }
    public function delete($entryNumber){
        //get the jason data
        // $jsonData = $request->json()->all();
        // Log::info('Received JSON data', ['data' => $jsonData]);

        // get entry number
        // $entryNumber = $jsonData['Entry']['Number'] ?? null;

        //delete the records with the same entry number
        ClockingDataTable::where('Entry_Number', $entryNumber)->delete();
        Log::info("Deleted existing clocking_data rows for Entry_Number = {$entryNumber}");

    }

    public function processFile(string $tempFilePath): array
    {
        // The try-catch block is a good practice, let's keep it.
        try {
            // 1. Figure out its extension (xls, xlsx, or csv) from the file path itself.
            // The URL-related code has been removed.
            $extension = strtolower(pathinfo($tempFilePath, PATHINFO_EXTENSION));

            if (! in_array($extension, ['xls', 'xlsx', 'csv'])) {
                // Throw an exception for an invalid file type.
                Log::error('Unsupported file extension', ['extension' => $extension, 'path' => $tempFilePath]);
                throw new \InvalidArgumentException('Unsupported file extension: ' . $extension . '. Only XLS, XLSX, and CSV are allowed.');
            }

            // 2. Prepare paths for temporary files.
            // We don't need to "save downloaded bytes" here, because the file is already on the filesystem at $tempFilePath.
            $baseName = 'hookdata_' . uniqid();
            $csvFilePath = storage_path("app/{$baseName}.csv");
            $csvToReadPath = null; // This will hold the final path of the CSV to be read.

            // 3. If it’s already a CSV, we use its path; otherwise, convert it.
            if ($extension === 'csv') {
                // If the uploaded file is already a CSV, use its path directly.
                $csvToReadPath = $tempFilePath;
            } else {
                // Load XLS or XLSX from the temporary file.
                $spreadsheet = IOFactory::load($tempFilePath);

                // Write out to a new CSV file.
                $csvWriter = new CsvWriter($spreadsheet);
                $csvWriter->save($csvFilePath);
                Log::info('Converted spreadsheet to CSV', ['path' => $csvFilePath]);

                // Free memory from the loaded spreadsheet.
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);

                // Set the path to the newly created CSV for reading.
                $csvToReadPath = $csvFilePath;
            }

            // 4. Now read the CSV row by row using native PHP (fgetcsv), skipping the header.
            $rows = [];
            $rowNum = 0;

            // Check if the file can be opened.
            if (($handle = fopen($csvToReadPath, 'r')) === false) {
                Log::error('Unable to open CSV for reading', ['path' => $csvToReadPath]);
                // Throw a runtime exception if the file cannot be opened.
                throw new \RuntimeException('Cannot read the converted CSV file.');
            }

            while (($columns = fgetcsv($handle, 0, ',')) !== false) {
                // First line is header → skip it
                if ($rowNum === 0) {
                    $rowNum++;
                    continue;
                }

                // ... (Your column C date processing logic, which looks correct)
                if (isset($columns[2]) && $columns[2] !== null && $columns[2] !== '') {
                    $rawValue = $columns[2];
                    try {
                        if (is_numeric($rawValue)) {
                            $dt = PhpDate::excelToDateTimeObject((float) $rawValue);
                            $columns[2] = Carbon::instance($dt)->toDateTimeString();
                        } else {
                            $columns[2] = Carbon::parse($rawValue)->toDateTimeString();
                        }
                    } catch (\Exception $e) {
                        Log::warning('Invalid datetime in column C', ['value' => $rawValue, 'exception' => $e->getMessage()]);
                        $columns[2] = null; // Set to null if parsing fails
                    }
                }

                $rows[] = $columns;
                $rowNum++;
            }
            fclose($handle);

            // 5. Clean up the temporary CSV file if it was created during conversion.
            if ($csvToReadPath !== $tempFilePath && File::exists($csvToReadPath)) {
                File::delete($csvToReadPath);
                Log::info('Deleted temp CSV', ['path' => $csvToReadPath]);
            }

            // The original $tempFilePath should be deleted by the controller after the whole process is done.

            // 6. Return the array of rows.
            return $rows;

        } catch (\Exception $e) {
            // This catch block will handle any exceptions thrown inside the try block.
            Log::error('Error processing file', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            // Re-throw the exception so the controller can handle it and return a response.
            throw new \RuntimeException('Processing failed: ' . $e->getMessage(), 0, $e);
        }
    }
    public function processClockInOut(array $dataRows, $entryNumber = null): void
    {
        // note the first row is already ignored in the processFile function

        // first group the data by AC_No
        $grouped = [];
        foreach ($dataRows as $row) {
            // see if we have the first 4 items
            if (count($row) < 4) {
                continue;
            }
            //set the values to the var
            $acNo   = $row[0];
            $name   = $row[1];
            $time   = $row[2];
            $state  = $row[3];

            //if any or these acNo,time,state then breack the loop
            if (is_null($acNo) || is_null($time) || is_null($state)) {
                continue;
            }

            // upper case the C/In', 'C/Out
            $stateNorm = strtoupper(trim($state));

            // Only consider "C/IN" or "C/OUT" and breack if other
            if (!in_array($stateNorm, ['C/IN', 'C/OUT'], true)) {
                continue;
            }

            // use carbon to parse the datetime
            try {
                $dt = Carbon::parse($time);
            } catch (\Exception $e) {
                Log::warning('Skipping invalid datetime row', [
                    'AC_No'   => $acNo,
                    'Name'    => $name,
                    'DateTime'=> $time,
                    'State'   => $state,
                ]);
                continue;
            }

            $grouped[$acNo][] = [
                'AC_No'    => $acNo,
                'Name'     => $name,
                'DateTime' => $dt,
                'State'    => $stateNorm, // "C/IN" or "C/OUT"
            ];
        }

        // second sorting and pairing
        foreach ($grouped as $acNo => $events) {
            // sort ascending by datetime
            usort($events, function ($a, $b) {
                return $a['DateTime']->timestamp <=> $b['DateTime']->timestamp;
            });

            $pendingIn = null;

            foreach ($events as $event) {
                $dt    = $event['DateTime'];
                $state = $event['State'];
                $name  = $event['Name'];

                if ($state === 'C/IN') {
                    if ($pendingIn !== null) {
                        // We already had an unmatched "In" → save it with null Clock_Out
                        ClockingDataTable::create([
                            'AC_No'        => $pendingIn['AC_No'],
                            'Name'         => $pendingIn['Name'],
                            'Date'         => $pendingIn['DateTime']->toDateString(),
                            'Clock_In'     => $pendingIn['DateTime']->toTimeString(),
                            'Clock_Out'    => null,
                            'Entry_ID' => $entryNumber,
                        ]);
                    }
                    // Now set this as the new pending "In"
                    $pendingIn = [
                        'AC_No'    => $acNo,
                        'Name'     => $name,
                        'DateTime' => $dt,
                    ];
                }
                else if ($state === 'C/OUT') {
                    if ($pendingIn !== null) {
                        // We have a pending "In" → pair them
                        ClockingDataTable::create([
                            'AC_No'        => $pendingIn['AC_No'],
                            'Name'         => $pendingIn['Name'],
                            'Date'         => $pendingIn['DateTime']->toDateString(),
                            'Clock_In'     => $pendingIn['DateTime']->toTimeString(),
                            'Clock_Out'    => $dt->toTimeString(),
                            'Entry_ID' => $entryNumber,
                        ]);
                        // Clear pendingIn
                        $pendingIn = null;
                    }
                    // If pendingIn is null, this is a "C/Out" without "C/In" → skip
                }
            }
             // third After iterating, if there’s still an unmatched "In" → store with null Clock_Out
             if ($pendingIn !== null) {
                ClockingDataTable::create([
                    'AC_No'        => $pendingIn['AC_No'],
                    'Name'         => $pendingIn['Name'],
                    'Date'         => $pendingIn['DateTime']->toDateString(),
                    'Clock_In'     => $pendingIn['DateTime']->toTimeString(),
                    'Clock_Out'    => null,
                    'Entry_ID' => $entryNumber,
                ]);
                $pendingIn = null;
            }
        }

    }

    /***************************** Exporting *********************************/


    public function exportcsv()
    {
        // 1. Fetch all records. If you want to filter (e.g. by Entry_Number), replace with a where() clause.
        $records = ClockingDataTable::all();

        // 2. Define a filename that includes a timestamp
        $fileName = 'clocking_data_' . now()->format('Ymd_His') . '.csv';

        // 3. Prepare HTTP headers for CSV download
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        // 4. Callback to stream CSV content row by row
        $callback = function() use ($records) {
            // Open output stream
            $handle = fopen('php://output', 'w');

            // Write the column headers as the first row
            fputcsv($handle, [
                'ID',
                'AC_No',
                'Name',
                'Date',
                'Clock_In',
                'Clock_Out',
                'Entry_Number',
                'Created_At',
                'Updated_At',
            ]);

            // Write each record’s data
            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->AC_No,
                    $row->Name,
                    $row->Date,
                    $row->Clock_In,
                    $row->Clock_Out,
                    $row->Entry_Number,
                    $row->created_at,
                    $row->updated_at,
                ]);
            }

            // Close the output stream
            fclose($handle);
        };

        // 5. Return a streamed download response
        return Response::stream($callback, 200, $headers);
    }

    public function exportjson(){
         // 1. Fetch all records (or add a where clause if needed)
        $records = ClockingDataTable::all();

        // 2. Return as JSON. Laravel automatically sets application/json header.
        return response()->json([
            'status'  => 'success',
            'count'   => $records->count(),
            'results' => $records,
        ], 200);
    }



}
