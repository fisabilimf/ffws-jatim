<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PredictedCalculatedDischarge;
use App\Models\MasSensor;
use App\Models\RatingCurve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PredictedCalculatedDischargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PredictedCalculatedDischarge::with(['sensor', 'ratingCurve']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mas_sensor_code', 'like', "%{$search}%")
                  ->orWhereHas('sensor', function ($sensor) use ($search) {
                      $sensor->where('sensor_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ratingCurve', function ($curve) use ($search) {
                      $curve->where('name', 'like', "%{$search}%");
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

        $predictedDischarges = $query->orderBy('calculated_at', 'desc')->paginate(15);

        // Get filter options
        $sensors = MasSensor::orderBy('sensor_name')->get();
        $ratingCurves = RatingCurve::orderBy('name')->get();

        return view('admin.predicted_calculated_discharges.index', compact(
            'predictedDischarges', 
            'sensors', 
            'ratingCurves'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sensors = MasSensor::orderBy('sensor_name')->get();
        $ratingCurves = RatingCurve::orderBy('name')->get();

        return view('admin.predicted_calculated_discharges.create', compact('sensors', 'ratingCurves'));
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
                'exists:mas_sensors,sensor_code'
            ],
            'predicted_value' => [
                'required',
                'numeric',
                'min:0'
            ],
            'predicted_discharge' => [
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

        PredictedCalculatedDischarge::create($request->all());

        return redirect()->route('admin.predicted-calculated-discharges.index')
                        ->with('success', 'Predicted calculated discharge created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PredictedCalculatedDischarge $predictedCalculatedDischarge)
    {
        $predictedCalculatedDischarge->load(['sensor', 'ratingCurve']);

        return view('admin.predicted_calculated_discharges.show', compact('predictedCalculatedDischarge'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PredictedCalculatedDischarge $predictedCalculatedDischarge)
    {
        $sensors = MasSensor::orderBy('sensor_name')->get();
        $ratingCurves = RatingCurve::orderBy('name')->get();

        return view('admin.predicted_calculated_discharges.edit', compact(
            'predictedCalculatedDischarge', 
            'sensors', 
            'ratingCurves'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PredictedCalculatedDischarge $predictedCalculatedDischarge)
    {
        $request->validate([
            'mas_sensor_code' => [
                'required',
                'string',
                'max:50',
                'exists:mas_sensors,sensor_code'
            ],
            'predicted_value' => [
                'required',
                'numeric',
                'min:0'
            ],
            'predicted_discharge' => [
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

        $predictedCalculatedDischarge->update($request->all());

        return redirect()->route('admin.predicted-calculated-discharges.index')
                        ->with('success', 'Predicted calculated discharge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PredictedCalculatedDischarge $predictedCalculatedDischarge)
    {
        $predictedCalculatedDischarge->delete();

        return redirect()->route('admin.predicted-calculated-discharges.index')
                        ->with('success', 'Predicted calculated discharge deleted successfully.');
    }

    /**
     * Export predicted calculated discharges data
     */
    public function export(Request $request)
    {
        $query = PredictedCalculatedDischarge::with(['sensor', 'ratingCurve']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mas_sensor_code', 'like', "%{$search}%")
                  ->orWhereHas('sensor', function ($sensor) use ($search) {
                      $sensor->where('sensor_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ratingCurve', function ($curve) use ($search) {
                      $curve->where('name', 'like', "%{$search}%");
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

        $predictedDischarges = $query->orderBy('calculated_at', 'desc')->get();

        $filename = 'predicted_calculated_discharges_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($predictedDischarges) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Sensor Code',
                'Sensor Name',
                'Predicted Value',
                'Predicted Discharge',
                'Rating Curve',
                'Calculated At',
                'Created At',
                'Updated At'
            ]);

            foreach ($predictedDischarges as $discharge) {
                fputcsv($file, [
                    $discharge->id,
                    $discharge->mas_sensor_code,
                    $discharge->sensor->sensor_name ?? 'N/A',
                    $discharge->predicted_value,
                    $discharge->predicted_discharge,
                    $discharge->ratingCurve->name ?? 'N/A',
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