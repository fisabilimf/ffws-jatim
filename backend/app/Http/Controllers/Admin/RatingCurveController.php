<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RatingCurve;
use App\Models\MasSensor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class RatingCurveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = RatingCurve::with(['sensor']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('mas_sensor_code', 'like', "%{$search}%")
                  ->orWhere('formula_type', 'like', "%{$search}%")
                  ->orWhereHas('sensor', function ($q) use ($search) {
                      $q->where('sensor_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by formula type
        if ($request->filled('formula_type')) {
            $query->where('formula_type', $request->get('formula_type'));
        }

        // Filter by sensor
        if ($request->filled('sensor_code')) {
            $query->where('mas_sensor_code', $request->get('sensor_code'));
        }

        $ratingCurves = $query->orderBy('effective_date', 'desc')
                             ->paginate(15)
                             ->withQueryString();

        $formulaTypes = $this->getFormulaTypes();
        $sensors = MasSensor::where('is_active', true)
                           ->orderBy('sensor_name')
                           ->get();

        return view('admin.rating_curves.index', compact('ratingCurves', 'formulaTypes', 'sensors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $sensors = MasSensor::where('is_active', true)
                           ->orderBy('sensor_name')
                           ->get();
        
        $formulaTypes = $this->getFormulaTypes();

        return view('admin.rating_curves.create', compact('sensors', 'formulaTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mas_sensor_code' => 'required|exists:mas_sensors,sensor_code',
            'formula_type' => 'required|in:power,polynomial,exponential,linear',
            'a' => 'required|numeric',
            'b' => 'nullable|numeric',
            'c' => 'nullable|numeric',
            'effective_date' => 'required|date',
        ]);

        RatingCurve::create($validated);

        return redirect()->route('admin.rating-curves.index')
                        ->with('success', 'Rating curve created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RatingCurve $ratingCurve): View
    {
        $ratingCurve->load(['sensor', 'calculatedDischarges', 'predictedCalculatedDischarges']);
        
        return view('admin.rating_curves.show', compact('ratingCurve'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RatingCurve $ratingCurve): View
    {
        $sensors = MasSensor::where('is_active', true)
                           ->orderBy('sensor_name')
                           ->get();
        
        $formulaTypes = $this->getFormulaTypes();

        return view('admin.rating_curves.edit', compact('ratingCurve', 'sensors', 'formulaTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RatingCurve $ratingCurve): RedirectResponse
    {
        $validated = $request->validate([
            'mas_sensor_code' => 'required|exists:mas_sensors,sensor_code',
            'formula_type' => 'required|in:power,polynomial,exponential,linear',
            'a' => 'required|numeric',
            'b' => 'nullable|numeric',
            'c' => 'nullable|numeric',
            'effective_date' => 'required|date',
        ]);

        $ratingCurve->update($validated);

        return redirect()->route('admin.rating-curves.index')
                        ->with('success', 'Rating curve updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RatingCurve $ratingCurve): RedirectResponse
    {
        // Check if rating curve has related calculated discharges
        if ($ratingCurve->calculatedDischarges()->count() > 0 || 
            $ratingCurve->predictedCalculatedDischarges()->count() > 0) {
            return redirect()->route('admin.rating-curves.index')
                           ->with('error', 'Cannot delete rating curve because it has related discharge calculations.');
        }

        $ratingCurve->delete();

        return redirect()->route('admin.rating-curves.index')
                        ->with('success', 'Rating curve deleted successfully.');
    }

    /**
     * Test the rating curve calculation.
     */
    public function testCalculation(Request $request, RatingCurve $ratingCurve)
    {
        $request->validate([
            'test_value' => 'required|numeric',
        ]);

        $testValue = $request->get('test_value');
        $result = $ratingCurve->calculateDischarge($testValue);

        return response()->json([
            'test_value' => $testValue,
            'calculated_discharge' => $result,
            'formula_type' => $ratingCurve->formula_type,
            'formula_display' => $this->getFormulaDisplay($ratingCurve),
        ]);
    }

    /**
     * Generate sample calculations for visualization.
     */
    public function generateSample(RatingCurve $ratingCurve, Request $request)
    {
        $request->validate([
            'min_value' => 'required|numeric',
            'max_value' => 'required|numeric|gt:min_value',
            'steps' => 'required|integer|min:5|max:100',
        ]);

        $minValue = $request->get('min_value');
        $maxValue = $request->get('max_value');
        $steps = $request->get('steps');
        
        $stepSize = ($maxValue - $minValue) / ($steps - 1);
        $samples = [];

        for ($i = 0; $i < $steps; $i++) {
            $value = $minValue + ($i * $stepSize);
            $discharge = $ratingCurve->calculateDischarge($value);
            
            $samples[] = [
                'sensor_value' => round($value, 3),
                'discharge' => round($discharge, 3),
            ];
        }

        return response()->json([
            'samples' => $samples,
            'formula_display' => $this->getFormulaDisplay($ratingCurve),
        ]);
    }

    /**
     * Get available formula types.
     */
    private function getFormulaTypes(): array
    {
        return [
            'power' => 'Power Formula (Q = a × H^b)',
            'polynomial' => 'Polynomial Formula (Q = a + b×H + c×H²)',
            'exponential' => 'Exponential Formula (Q = a × e^(b×H))',
            'linear' => 'Linear Formula (Q = a × H)',
        ];
    }

    /**
     * Get formula display string.
     */
    private function getFormulaDisplay(RatingCurve $ratingCurve): string
    {
        return match($ratingCurve->formula_type) {
            'power' => "Q = {$ratingCurve->a} × H^{$ratingCurve->b}",
            'polynomial' => "Q = {$ratingCurve->a} + {$ratingCurve->b}×H + {$ratingCurve->c}×H²",
            'exponential' => "Q = {$ratingCurve->a} × e^({$ratingCurve->b}×H)",
            'linear' => "Q = {$ratingCurve->a} × H",
            default => "Q = {$ratingCurve->a} × H"
        };
    }
}