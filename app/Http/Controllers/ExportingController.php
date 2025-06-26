<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExportingService;
use Illuminate\Support\Facades\Log;

class ExportingController extends Controller{

    protected ExportingService $exporting;

    public function __construct(ExportingService $exporting)
    {
        $this->exporting = $exporting;
    }

    protected array $restrictedModels = [
        'User','Form'
    ];
    protected function isRestricted(string $model): bool
    {
        return in_array(ucfirst($model), $this->restrictedModels);
    }

    public function ExportClockingCsv(Request $request, string $model, $start = null, $end = null,){

        if ($this->isRestricted($model)) {
            return response()->json(['error' => 'Exporting this model is not allowed.'], 403);
        }
        // Resolve the full Model class (adjust namespace as needed)
        $modelClass = '\\App\\Models\\' . ucfirst($model);

        return $this->exporting->exportCSV($request, $modelClass, $start, $end);
    }

    public function ExportClockingJson(Request $request, string $model, $start = null, $end = null,){

        if ($this->isRestricted($model)) {
            return response()->json(['error' => 'Exporting this model is not allowed.'], 403);
        }
        // Resolve the full Model class (adjust namespace as needed)
        $modelClass = '\\App\\Models\\' . ucfirst($model);

        return $this->exporting->exportJson($request, $modelClass, $start, $end);
    }

}
