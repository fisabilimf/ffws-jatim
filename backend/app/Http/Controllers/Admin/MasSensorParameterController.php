<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasSensorParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasSensorParameterController extends Controller
{
    public function index(Request $request)
    {
        $query = MasSensorParameter::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $sensorParameters = $query->orderBy('name')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'name', 'label' => 'Nama Parameter', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode Parameter', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform sensor parameters data for table
        $sensorParameters->getCollection()->transform(function ($parameter) {
            $detailData = [
                'id' => $parameter->id,
                'name' => addslashes($parameter->name),
                'code' => addslashes($parameter->code ?? '')
            ];
            $detailJson = json_encode($detailData);

            $parameter->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit Parameter Sensor',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-sensor-parameter', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus Parameter Sensor',
                    'url' => route('admin.mas-sensor-parameters.destroy', $parameter->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus parameter sensor ini?'
                ]
            ];
            
            return $parameter;
        });
        
        // Use 'sensor_parameters' to match the view expectation
        $sensor_parameters = $sensorParameters;
        
        return view('admin.mas_sensor_parameters.index', compact('sensor_parameters', 'tableHeaders'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:mas_sensor_parameters,name',
                'code' => 'required|string|max:50|unique:mas_sensor_parameters,code'
            ]);

            $item = MasSensorParameter::create($validated);

            return redirect()->route('admin.mas-sensor-parameters.index')
                ->with('success', "Parameter sensor '{$item->name}' berhasil ditambahkan.");
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Data yang diinput tidak valid. Silakan periksa kembali.');
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when creating parameter sensor: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = MasSensorParameter::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:mas_sensor_parameters,name,' . $id,
                'code' => 'required|string|max:50|unique:mas_sensor_parameters,code,' . $id
            ]);

            $oldName = $item->name;
            $item->update($validated);

            return redirect()->route('admin.mas-sensor-parameters.index')
                ->with('success', "Parameter sensor '{$oldName}' berhasil diperbarui.");
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when updating parameter sensor: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $item = MasSensorParameter::findOrFail($id);
            $itemName = $item->name;
            $item->delete();

            return redirect()->route('admin.mas-sensor-parameters.index')
                ->with('success', "Parameter sensor '{$itemName}' berhasil dihapus.");
                
        } catch (\Exception $e) {
            Log::error('Unexpected error when deleting parameter sensor: ' . $e->getMessage());
            
            return redirect()->route('admin.mas-sensor-parameters.index')
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }
}