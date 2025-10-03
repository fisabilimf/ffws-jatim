<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasWhatsappNumber;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class MasWhatsappNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MasWhatsappNumber::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->search($search);
        }

        $whatsappNumbers = $query->orderBy('name')
                                ->paginate(15)
                                ->withQueryString();

        return view('admin.mas_whatsapp_numbers.index', compact('whatsappNumbers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.mas_whatsapp_numbers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number' => [
                'required',
                'string',
                'max:20',
                'unique:mas_whatsapp_numbers,number',
                function ($attribute, $value, $fail) {
                    if (!MasWhatsappNumber::validateIndonesianNumber($value)) {
                        $fail('The ' . $attribute . ' must be a valid Indonesian phone number.');
                    }
                },
            ],
        ]);

        // Normalize the number
        $validated['number'] = $this->normalizePhoneNumber($validated['number']);

        MasWhatsappNumber::create($validated);

        return redirect()->route('admin.mas-whatsapp-numbers.index')
                        ->with('success', 'WhatsApp number created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasWhatsappNumber $masWhatsappNumber): View
    {
        return view('admin.mas_whatsapp_numbers.show', compact('masWhatsappNumber'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasWhatsappNumber $masWhatsappNumber): View
    {
        return view('admin.mas_whatsapp_numbers.edit', compact('masWhatsappNumber'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasWhatsappNumber $masWhatsappNumber): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mas_whatsapp_numbers', 'number')->ignore($masWhatsappNumber->id),
                function ($attribute, $value, $fail) {
                    if (!MasWhatsappNumber::validateIndonesianNumber($value)) {
                        $fail('The ' . $attribute . ' must be a valid Indonesian phone number.');
                    }
                },
            ],
        ]);

        // Normalize the number
        $validated['number'] = $this->normalizePhoneNumber($validated['number']);

        $masWhatsappNumber->update($validated);

        return redirect()->route('admin.mas-whatsapp-numbers.index')
                        ->with('success', 'WhatsApp number updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasWhatsappNumber $masWhatsappNumber): RedirectResponse
    {
        $masWhatsappNumber->delete();

        return redirect()->route('admin.mas-whatsapp-numbers.index')
                        ->with('success', 'WhatsApp number deleted successfully.');
    }

    /**
     * Send test message to WhatsApp number.
     */
    public function testMessage(MasWhatsappNumber $masWhatsappNumber): RedirectResponse
    {
        // This would integrate with WhatsApp API in real implementation
        // For now, just redirect to WhatsApp web
        $url = $masWhatsappNumber->whatsapp_url . '?text=' . urlencode('Test message from FFWS System');
        
        return redirect()->away($url);
    }

    /**
     * Export WhatsApp numbers to CSV.
     */
    public function export()
    {
        $whatsappNumbers = MasWhatsappNumber::orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="whatsapp_numbers_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($whatsappNumbers) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Name', 'Number', 'Formatted Number', 'Created At']);
            
            // Add data rows
            foreach ($whatsappNumbers as $number) {
                fputcsv($file, [
                    $number->name,
                    $number->number,
                    $number->formatted_number,
                    $number->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Normalize phone number format.
     */
    private function normalizePhoneNumber(string $number): string
    {
        // Remove non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Convert to standard format starting with 08
        if (str_starts_with($number, '62')) {
            $number = '0' . substr($number, 2);
        } elseif (str_starts_with($number, '+62')) {
            $number = '0' . substr($number, 3);
        }
        
        return $number;
    }
}