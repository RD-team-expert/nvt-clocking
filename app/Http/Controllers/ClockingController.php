<?php

namespace App\Http\Controllers;

use App\Models\ClockingDataTable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
class ClockingController extends Controller
{
    /**
     * Display a listing of clocking data with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = ClockingDataTable::query();

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('Date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('Date', '<=', $request->date_to);
        }

        // Filter by name
        if ($request->filled('name')) {
            $query->where('Name', 'like', '%' . $request->name . '%');
        }

        // Filter by AC_No
        if ($request->filled('ac_no')) {
            $query->where('AC_No', 'like', '%' . $request->ac_no . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'Date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $clockingData = $query->paginate($perPage);

        return response()->json($clockingData);
    }

    /**
     * Store a newly created clocking record
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'AC_No' => 'required|integer',
            'Name' => 'required|string|max:255',
            'Date' => 'required|date',
            'Clock_In' => 'nullable|date_format:H:i:s',
            'Clock_Out' => 'nullable|date_format:H:i:s',
            'Entry_ID' => 'nullable|integer',
        ]);

        $clockingData = ClockingDataTable::create($validated);

        return response()->json([
            'message' => 'Clocking record created successfully',
            'data' => $clockingData
        ], 201);
    }

    /**
     * Update the specified clocking record
     */
    public function update(Request $request, ClockingDataTable $clocking): JsonResponse
    {
        $validated = $request->validate([
            'AC_No' => 'required|integer', // Should be integer, not string
            'Name' => 'required|string|max:255',
            'Date' => 'required|date',
            'Clock_In' => 'nullable|date_format:H:i:s',
            'Clock_Out' => 'nullable|date_format:H:i:s',
            'Entry_ID' => 'nullable|integer', // Should be integer, not string
        ]);

        $clocking->update($validated);

        return response()->json([
            'message' => 'Clocking record updated successfully',
            'data' => $clocking->fresh() // Return fresh data
        ]);
    }

    /**
     * Remove the specified clocking record
     */
    public function destroy(ClockingDataTable $clocking)
    {
        Log::info("destroy record function started");
        ClockingDataTable::where('id', $clocking->id)->delete();
        Log::info("record Deleted by Query");
        // return response()->json([
        //     'message' => 'Clocking record updated successfully',
        //     'data' => $clocking->fresh() // Return fresh data
        // ]);
    }

    /**
     * Get dashboard statistics
     */
   public function getDashboardStats(): JsonResponse
    {
        // Count stats
        $currentlyLoggedIn  = ClockingDataTable::whereNull('Clock_Out')->count();
        $currentlyLoggedOut = ClockingDataTable::whereNull('Clock_In')->count();
        $todayEntries        = ClockingDataTable::whereDate('Date', today())->count();
        $totalRecords        = ClockingDataTable::count();

        // List of employees still logged in
        $loggedInEmployees = ClockingDataTable::whereNull('Clock_Out')
            ->select('AC_No', 'Name', 'Date', 'Clock_In')
            ->orderBy('Clock_In', 'desc')
            ->get();

        // List of employees who have logged out
        $loggedOutEmployees = ClockingDataTable::whereNull('Clock_In')
            ->select('AC_No', 'Name', 'Date', 'Clock_Out')
            ->orderBy('Clock_Out', 'desc')
            ->get();

        return response()->json([
            'currently_logged_in'   => $currentlyLoggedIn,
            'logged_in_employees'   => $loggedInEmployees,
            'currently_logged_out'  => $currentlyLoggedOut,
            'logged_out_employees'  => $loggedOutEmployees,
            'today_entries'         => $todayEntries,
            'total_records'         => $totalRecords,
        ]);
    }
}
