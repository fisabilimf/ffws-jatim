<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasDeviceParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasDeviceParameterController extends Controller
{
    public function index(Request $request)
    {
        $query = MasDeviceParameter::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $deviceParameters = $query->orderBy('name')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'name', 'label' => 'Nama Parameter', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode Parameter', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform device parameters data for table
        $deviceParameters->getCollection()->transform(function ($parameter) {
            $detailData = [
                'id' => $parameter->id,
                'name' => addslashes($parameter->name),
                'code' => addslashes($parameter->code ?? '')
            ];
            $detailJson = json_encode($detailData);

            $parameter->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit Parameter Perangkat',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-device-parameter', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus Parameter Perangkat',
                    'url' => route('admin.mas-device-parameters.destroy', $parameter->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus parameter perangkat ini?'
                ]
            ];
            
            return $parameter;
        });
        
        // Use 'device_parameters' to match the view expectation
        $device_parameters = $deviceParameters;
        
        return view('admin.mas_device_parameters.index', compact('device_parameters', 'tableHeaders'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:mas_device_parameters,name',
                'code' => 'required|string|max:50|unique:mas_device_parameters,code'
            ]);

            $parameter = MasDeviceParameter::create($validated);

            return redirect()->route('admin.mas-device-parameters.index')
                ->with('success', "Parameter Perangkat '{$parameter->name}' berhasil ditambahkan.");
                
        } catch (\Exception $e) {
            Log::error('Error creating device parameter: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-device-parameters.index')
                ->with('error', 'Terjadi kesalahan saat menambahkan parameter perangkat. Silakan coba lagi.');
        }
    }

    public function update(Request $request, MasDeviceParameter $masDeviceParameter)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:mas_device_parameters,name,' . $masDeviceParameter->id,
                'code' => 'required|string|max:50|unique:mas_device_parameters,code,' . $masDeviceParameter->id
            ]);

            $masDeviceParameter->update($validated);

            return redirect()->route('admin.mas-device-parameters.index')
                ->with('success', "Parameter Perangkat '{$masDeviceParameter->name}' berhasil diperbarui.");
                
        } catch (\Exception $e) {
            Log::error('Error updating device parameter: ' . $e->getMessage(), [
                'parameter_id' => $masDeviceParameter->id,
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-device-parameters.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui parameter perangkat. Silakan coba lagi.');
        }
    }

    public function destroy(MasDeviceParameter $masDeviceParameter)
    {
        try {
            DB::beginTransaction();

            $parameterName = $masDeviceParameter->name;
            $masDeviceParameter->delete();

            DB::commit();

            return redirect()->route('admin.mas-device-parameters.index')
                ->with('success', "Parameter Perangkat '{$parameterName}' berhasil dihapus.");
                
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error deleting device parameter: ' . $e->getMessage(), [
                'parameter_id' => $masDeviceParameter->id,
                'parameter_name' => $masDeviceParameter->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.mas-device-parameters.index')
                ->with('error', 'Parameter Perangkat tidak dapat dihapus karena masih memiliki data terkait atau terjadi kesalahan sistem.');
        }
    }
}