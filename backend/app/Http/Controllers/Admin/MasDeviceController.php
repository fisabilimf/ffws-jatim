<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasDevice;
use App\Models\MasRiverBasin;

class MasDeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = MasDevice::with('riverBasin');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('riverBasin', function ($rbQ) use ($search) {
                      $rbQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $devices = $query->orderBy('name')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        $tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'river_basin_name', 'label' => 'DAS', 'sortable' => false],
            ['key' => 'name', 'label' => 'Nama Device', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'latitude', 'label' => 'Latitude', 'sortable' => false],
            ['key' => 'longitude', 'label' => 'Longitude', 'sortable' => false],
            ['key' => 'elevation_m', 'label' => 'Elevasi (m)', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'format' => 'status', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        // Transform devices data for table
        $devices->getCollection()->transform(function ($device) {
            $detailData = [
                'id' => $device->id,
                'name' => addslashes($device->name),
                'code' => addslashes($device->code),
                'mas_river_basin_id' => $device->mas_river_basin_id,
                'latitude' => $device->latitude,
                'longitude' => $device->longitude,
                'elevation_m' => $device->elevation_m,
                'status' => $device->status
            ];
            $detailJson = json_encode($detailData);

            $device->river_basin_name = $device->riverBasin->name ?? '-';
            $device->latitude = number_format($device->latitude, 6);
            $device->longitude = number_format($device->longitude, 6);
            $device->elevation_m = $device->elevation_m ? number_format($device->elevation_m, 2) . ' m' : '-';
            
            $device->actions = [
                [
                    'label' => 'Edit',
                    'title' => 'Edit Device',
                    'url' => '#',
                    'onclick' => "window.dispatchEvent(new CustomEvent('open-edit-device', { detail: {$detailJson} }))",
                    'icon' => 'pen',
                    'color' => 'blue'
                ],
                [
                    'label' => 'Hapus',
                    'title' => 'Hapus Device',
                    'url' => route('admin.devices.destroy', $device->id),
                    'color' => 'red',
                    'method' => 'DELETE',
                    'icon' => 'trash',
                    'confirm' => 'Apakah Anda yakin ingin menghapus device ini?'
                ]
            ];
            
            return $device;
        });
        
        // Get river basins for form select options
        $riverBasins = MasRiverBasin::all()->map(function ($rb) {
            return [
                'value' => $rb->id,
                'label' => $rb->name
            ];
        });
        
        return view('admin.mas_devices.index', compact('devices', 'tableHeaders', 'riverBasins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:mas_devices,code',
            'mas_river_basin_id' => 'required|exists:mas_river_basins,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'elevation_m' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        MasDevice::create($validated);

        return redirect()->route('admin.devices.index')
            ->with('success', 'Device berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $device = MasDevice::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:mas_devices,code,' . $id,
            'mas_river_basin_id' => 'required|exists:mas_river_basins,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'elevation_m' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $device->update($validated);

        return redirect()->route('admin.devices.index')
            ->with('success', 'Device berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $device = MasDevice::findOrFail($id);
        $device->delete();

        return redirect()->route('admin.devices.index')
            ->with('success', 'Device berhasil dihapus.');
    }
}
