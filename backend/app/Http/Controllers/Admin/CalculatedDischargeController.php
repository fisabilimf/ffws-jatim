<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalculatedDischarge;
use App\Models\MasSensor;
use App\Models\SensorValue;
use App\Models\RatingCurve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalculatedDischargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CalculatedDischarge::with(['sensor', 'ratingCurve']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mas_sensor_code', 'like', "%{$search}%")
                  ->orWhereIn('mas_sensor_code', function ($subQuery) use ($search) {
                      $subQuery->select('mas_sensor_code')
                               ->from('sensor_values')
                               ->where('sensor_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ratingCurve', function ($curve) use ($search) {
                      $curve->where('formula_type', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by sensor
        if ($request->filled('sensor_code')) {
            $query->where('mas_sensor_code', $request->sensor_code);
        }

        // Filter by rating curve
        if ($request->filled('rating_curve_id')) {
            $query->where('rating_curve_id', $request->rating_curve_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('calculated_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('calculated_at', '<=', $request->date_to . ' 23:59:59');
        }

        $calculatedDischarges = $query->orderBy('calculated_at', 'desc')->paginate(15);

        // Get filter options
        $sensors = SensorValue::select('mas_sensor_code', 'sensor_name')
                             ->distinct()
                             ->whereNotNull('sensor_name')
                             ->where('sensor_name', '!=', '')
                             ->orderBy('sensor_name')
                             ->get();
        $ratingCurves = RatingCurve::orderBy('effective_date', 'desc')->get();

        return view('admin.calculated_discharges.index', compact(
            'calculatedDischarges', 
            'sensors', 
            'ratingCurves'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sensors = SensorValue::select('mas_sensor_code', 'sensor_name')
                             ->distinct()
                             ->whereNotNull('sensor_name')
                             ->where('sensor_name', '!=', '')
                             ->orderBy('sensor_name')
                             ->get();
        $ratingCurves = RatingCurve::orderBy('effective_date', 'desc')->get();

        return view('admin.calculated_discharges.create', compact('sensors', 'ratingCurves'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mas_sensor_code' => [
                'required',
                'string',
                'max:50',
                'exists:sensor_values,mas_sensor_code'
            ],
            'sensor_value' => [
                'required',
                'numeric',
                'min:0'
            ],
            'sensor_discharge' => [
                'required',
                'numeric',
                'min:0'
            ],
            'rating_curve_id' => [
                'required',
                'exists:rating_curves,id'
            ],
            'calculated_at' => [
                'required',
                'date'
            ],
        ]);

        CalculatedDischarge::create($request->all());

        return redirect()->route('admin.calculated-discharges.index')
                        ->with('success', 'Calculated discharge created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalculatedDischarge $calculatedDischarge)
    {
        $calculatedDischarge->load(['sensor', 'ratingCurve']);

        return view('admin.calculated_discharges.show', compact('calculatedDischarge'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalculatedDischarge $calculatedDischarge)
    {
        $sensors = SensorValue::select('mas_sensor_code', 'sensor_name')
                             ->distinct()
                             ->whereNotNull('sensor_name')
                             ->where('sensor_name', '!=', '')
                             ->orderBy('sensor_name')
                             ->get();
        $ratingCurves = RatingCurve::orderBy('effective_date', 'desc')->get();

        return view('admin.calculated_discharges.edit', compact(
            'calculatedDischarge', 
            'sensors', 
            'ratingCurves'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalculatedDischarge $calculatedDischarge)
    {
        $request->validate([
            'mas_sensor_code' => [
                'required',
                'string',
                'max:50',
                'exists:sensor_values,mas_sensor_code'
            ],
            'sensor_value' => [
                'required',
                'numeric',
                'min:0'
            ],
            'sensor_discharge' => [
                'required',
                'numeric',
                'min:0'
            ],
            'rating_curve_id' => [
                'required',
                'exists:rating_curves,id'
            ],
            'calculated_at' => [
                'required',
                'date'
            ],
        ]);

        $calculatedDischarge->update($request->all());

        return redirect()->route('admin.calculated-discharges.index')
                        ->with('success', 'Calculated discharge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalculatedDischarge $calculatedDischarge)
    {
        $calculatedDischarge->delete();

        return redirect()->route('admin.calculated-discharges.index')
                        ->with('success', 'Calculated discharge deleted successfully.');
    }

    /**
     * Export calculated discharges data
     */
    public function export(Request $request)
    {
        $query = CalculatedDischarge::with(['sensor', 'ratingCurve']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mas_sensor_code', 'like', "%{$search}%")
                  ->orWhereIn('mas_sensor_code', function ($subQuery) use ($search) {
                      $subQuery->select('mas_sensor_code')
                               ->from('sensor_values')
                               ->where('sensor_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ratingCurve', function ($curve) use ($search) {
                      $curve->where('formula_type', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('sensor_code')) {
            $query->where('mas_sensor_code', $request->sensor_code);
        }

        if ($request->filled('rating_curve_id')) {
            $query->where('rating_curve_id', $request->rating_curve_id);
        }

        if ($request->filled('date_from')) {
            $query->where('calculated_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('calculated_at', '<=', $request->date_to . ' 23:59:59');
        }

        $calculatedDischarges = $query->orderBy('calculated_at', 'desc')->get();

        $filename = 'calculated_discharges_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($calculatedDischarges) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Sensor Code',
                'Sensor Name',
                'Sensor Value',
                'Sensor Discharge',
                'Rating Curve',
                'Calculated At',
                'Created At',
                'Updated At'
            ]);

            foreach ($calculatedDischarges as $discharge) {
                // Get sensor name from sensor_values table
                $sensorName = SensorValue::where('mas_sensor_code', $discharge->mas_sensor_code)
                                        ->value('sensor_name') ?? 'N/A';
                
                fputcsv($file, [
                    $discharge->id,
                    $discharge->mas_sensor_code,
                    $sensorName,
                    $discharge->sensor_value,
                    $discharge->sensor_discharge,
                    ($discharge->ratingCurve->formula_type ?? 'N/A') . ' (ID: ' . ($discharge->ratingCurve->id ?? 'N/A') . ')',
                    $discharge->calculated_at?->format('Y-m-d H:i:s'),
                    $discharge->created_at?->format('Y-m-d H:i:s'),
                    $discharge->updated_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}