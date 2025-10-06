<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UpdateCrudPattern extends Command
{
    protected $signature = 'crud:update {controller?} {--all} {--dry-run}';
    protected $description = 'Update controllers to modern CRUD pattern with modal-based views';

    protected $controllers = [
        'MasCityController' => [
            'model' => 'MasCity',
            'table' => 'mas_cities',
            'fields' => ['cities_name', 'cities_code', 'regencies_code'],
            'relations' => ['regency'],
            'singular' => 'kecamatan',
            'plural' => 'kecamatan'
        ],
        'MasVillageController' => [
            'model' => 'MasVillage',
            'table' => 'mas_villages',
            'fields' => ['villages_name', 'villages_code', 'cities_code'],
            'relations' => ['city'],
            'singular' => 'desa/kelurahan',
            'plural' => 'desa/kelurahan'
        ],
        'MasUptController' => [
            'model' => 'MasUpt',
            'table' => 'mas_upts',
            'fields' => ['upts_name', 'upts_code', 'river_basin_code', 'cities_code'],
            'relations' => ['riverBasin', 'city'],
            'singular' => 'UPT',
            'plural' => 'UPT'
        ],
        'MasUptdController' => [
            'model' => 'MasUptd',
            'table' => 'mas_uptds',
            'fields' => ['name', 'code', 'upt_code'],
            'relations' => ['upt'],
            'singular' => 'UPTD',
            'plural' => 'UPTD'
        ],
        'MasDeviceParameterController' => [
            'model' => 'MasDeviceParameter',
            'table' => 'mas_device_parameters',
            'fields' => ['name', 'code'],
            'relations' => [],
            'singular' => 'parameter device',
            'plural' => 'parameter device'
        ],
        'MasSensorParameterController' => [
            'model' => 'MasSensorParameter',
            'table' => 'mas_sensor_parameters',
            'fields' => ['name', 'code'],
            'relations' => [],
            'singular' => 'parameter sensor',
            'plural' => 'parameter sensor'
        ]
    ];

    public function handle()
    {
        if ($this->option('all')) {
            foreach ($this->controllers as $controller => $config) {
                $this->updateController($controller, $config);
            }
        } else {
            $controller = $this->argument('controller');
            if (!$controller) {
                $controller = $this->choice('Which controller to update?', array_keys($this->controllers));
            }
            
            if (isset($this->controllers[$controller])) {
                $this->updateController($controller, $this->controllers[$controller]);
            } else {
                $this->error("Controller {$controller} not found in configuration.");
            }
        }
    }

    protected function updateController($controllerName, $config)
    {
        $this->info("Updating {$controllerName}...");
        
        if ($this->option('dry-run')) {
            $this->line("DRY RUN: Would update {$controllerName} with config:");
            $this->line(json_encode($config, JSON_PRETTY_PRINT));
            return;
        }

        // Update controller file
        $this->updateControllerFile($controllerName, $config);
        
        // Update view file
        $this->updateViewFile($controllerName, $config);
        
        $this->info("✅ {$controllerName} updated successfully!");
    }

    protected function updateControllerFile($controllerName, $config)
    {
        $controllerPath = app_path("Http/Controllers/Admin/{$controllerName}.php");
        
        if (!File::exists($controllerPath)) {
            $this->warn("Controller file not found: {$controllerPath}");
            return;
        }

        $template = $this->generateControllerTemplate($config);
        
        // Backup original file
        File::copy($controllerPath, $controllerPath . '.bak');
        
        // Write new content
        File::put($controllerPath, $template);
        
        $this->line("Updated controller: {$controllerPath}");
    }

    protected function updateViewFile($controllerName, $config)
    {
        $viewDir = resource_path('views/admin/' . Str::snake(str_replace('Controller', '', $controllerName)));
        $viewPath = $viewDir . '/index.blade.php';
        
        if (!File::exists($viewPath)) {
            $this->warn("View file not found: {$viewPath}");
            return;
        }

        $template = $this->generateViewTemplate($config);
        
        // Backup original file
        File::copy($viewPath, $viewPath . '.bak');
        
        // Write new content
        File::put($viewPath, $template);
        
        $this->line("Updated view: {$viewPath}");
    }

    protected function generateControllerTemplate($config)
    {
        $model = $config['model'];
        $table = $config['table'];
        $fields = $config['fields'];
        $singular = $config['singular'];
        
        // Generate validation rules
        $validationRules = [];
        foreach ($fields as $field) {
            if (Str::contains($field, 'name')) {
                $validationRules[] = "'{$field}' => 'required|string|max:255|unique:{$table},{$field}'";
            } elseif (Str::contains($field, 'code')) {
                $validationRules[] = "'{$field}' => 'required|string|max:50|unique:{$table},{$field}'";
            } else {
                $validationRules[] = "'{$field}' => 'required'";
            }
        }
        
        $validationString = implode(",\n            ", $validationRules);
        
        return "<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\\{$model};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class " . class_basename($model) . "Controller extends Controller
{
    public function index(Request \$request)
    {
        \$query = {$model}::query();

        if (\$search = \$request->get('search')) {
            \$query->where(function (\$q) use (\$search) {
                \$q->where('{$fields[0]}', 'like', \"%{\$search}%\");
                if (count(\$fields) > 1) {
                    \$q->orWhere('{$fields[1]}', 'like', \"%{\$search}%\");
                }
            });
        }

        \$items = \$query->orderBy('{$fields[0]}')->paginate(10)->withQueryString();
        
        // Prepare data for table component
        \$tableHeaders = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => '{$fields[0]}', 'label' => '" . ucwords(str_replace('_', ' ', $fields[0])) . "', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi', 'format' => 'actions', 'sortable' => false]
        ];
        
        return view('admin." . Str::snake(str_replace('Controller', '', class_basename($model) . "Controller")) . ".index', compact('items', 'tableHeaders'));
    }

    public function store(Request \$request)
    {
        try {
            \$validated = \$request->validate([
                {$validationString}
            ]);

            \$item = {$model}::create(\$validated);

            return redirect()->route('admin." . Str::kebab(str_replace('Controller', '', class_basename($model) . "Controller")) . ".index')
                ->with('success', \"" . ucfirst($singular) . " '{\$item->{$fields[0]}}' berhasil ditambahkan.\");
                
        } catch (\Illuminate\Validation\ValidationException \$e) {
            return redirect()->back()
                ->withErrors(\$e->validator)
                ->withInput()
                ->with('error', 'Data yang diinput tidak valid. Silakan periksa kembali.');
                
        } catch (\Exception \$e) {
            Log::error('Unexpected error when creating {$singular}: ' . \$e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function update(Request \$request, \$id)
    {
        try {
            \$item = {$model}::findOrFail(\$id);
            
            \$validated = \$request->validate([
                {$validationString}
            ]);

            \$oldName = \$item->{$fields[0]};
            \$item->update(\$validated);

            return redirect()->route('admin." . Str::kebab(str_replace('Controller', '', class_basename($model) . "Controller")) . ".index')
                ->with('success', \"" . ucfirst($singular) . " '{\$oldName}' berhasil diperbarui.\");
                
        } catch (\Exception \$e) {
            Log::error('Unexpected error when updating {$singular}: ' . \$e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }

    public function destroy(\$id)
    {
        try {
            \$item = {$model}::findOrFail(\$id);
            \$itemName = \$item->{$fields[0]};
            \$item->delete();

            return redirect()->route('admin." . Str::kebab(str_replace('Controller', '', class_basename($model) . "Controller")) . ".index')
                ->with('success', \"" . ucfirst($singular) . " '{\$itemName}' berhasil dihapus.\");
                
        } catch (\Exception \$e) {
            Log::error('Unexpected error when deleting {$singular}: ' . \$e->getMessage());
            
            return redirect()->route('admin." . Str::kebab(str_replace('Controller', '', class_basename($model) . "Controller")) . ".index')
                ->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.');
        }
    }
}";
    }

    protected function generateViewTemplate($config)
    {
        $model = $config['model'];
        $fields = $config['fields'];
        $singular = $config['singular'];
        $plural = $config['plural'];
        
        $routePrefix = Str::kebab(str_replace('Controller', '', class_basename($model) . "Controller"));
        
        return "@extends('layouts.admin')

@section('title', '" . ucfirst($plural) . "')
@section('page-title', '" . ucfirst($plural) . "')
@section('page-description', 'Kelola data {$plural}')
@section('breadcrumb', '" . ucfirst($plural) . "')

@section('content')
<div class=\"space-y-6\" x-data=\"itemsPage()\" x-init=\"init()\">

    <x-table
        title=\"Daftar " . ucfirst($plural) . "\"
        :headers=\"\$tableHeaders\"
        :rows=\"\$items\"
        searchable
        searchPlaceholder=\"Cari {$plural}...\"
    >
        <x-slot:actions>
            <x-admin.button type=\"button\" variant=\"primary\" @click=\"openCreate()\">
                <i class=\"fa-solid fa-plus -ml-1 mr-2\"></i>
                Tambah " . ucfirst($singular) . "
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show=\"false\" name=\"item-create\" title=\"Tambah " . ucfirst($singular) . "\" size=\"lg\" :close-on-backdrop=\"true\">
        <form id=\"itemCreateForm\" x-ref=\"createForm\" action=\"{{ route('admin.{$routePrefix}.store') }}\" method=\"POST\" class=\"space-y-6\">
            @csrf
            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-6\">
                <x-admin.form-input 
                    type=\"text\" 
                    name=\"{$fields[0]}\" 
                    label=\"" . ucwords(str_replace('_', ' ', $fields[0])) . "\" 
                    placeholder=\"Masukkan " . str_replace('_', ' ', $fields[0]) . "\" 
                    required=\"true\" 
                    :error=\"\$errors->first('{$fields[0]}')\"
                />
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type=\"button\" variant=\"secondary\" @click=\"closeModal('item-create')\">
                Batal
            </x-admin.button>
            <x-admin.button type=\"submit\" variant=\"primary\" form=\"itemCreateForm\">
                <i class=\"fa-solid fa-save -ml-1 mr-2\"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function itemsPage() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            window.addEventListener('open-edit-item', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.\$refs.createForm.reset();
            this.openModal('item-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('admin.{$routePrefix}.index') }}/\${data.id}`;
            this.openModal('item-edit');
        },
        
        openModal(name) {
            this.\$dispatch('open-modal', name);
        },
        
        closeModal(name) {
            this.\$dispatch('close-modal', name);
        }
    }
}
</script>
@endsection";
    }
}