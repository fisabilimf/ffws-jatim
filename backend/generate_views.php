<?php

/**
 * Generate Modal-based View Files for Remaining Controllers
 * This script creates standardized modal-based views for CRUD operations
 */

class ViewGenerator {
    
    private $basePath = 'resources/views/admin/';
    
    private $templates = [
        'mas_upts' => [
            'title' => 'UPT',
            'description' => 'Kelola data Unit Pelaksana Teknis',
            'fields' => [
                ['name' => 'upt_name', 'label' => 'Nama UPT', 'type' => 'text', 'placeholder' => 'Contoh: UPT Pengairan Kota Malang'],
                ['name' => 'upt_code', 'label' => 'Kode UPT', 'type' => 'text', 'placeholder' => 'Contoh: UPT001'],
                ['name' => 'upt_address', 'label' => 'Alamat UPT', 'type' => 'textarea', 'placeholder' => 'Alamat lengkap UPT']
            ],
            'parent' => null
        ],
        'mas_uptds' => [
            'title' => 'UPTD',
            'description' => 'Kelola data Unit Pelaksana Teknis Daerah',
            'fields' => [
                ['name' => 'uptd_name', 'label' => 'Nama UPTD', 'type' => 'text', 'placeholder' => 'Contoh: UPTD Pengairan Wilayah Malang'],
                ['name' => 'uptd_code', 'label' => 'Kode UPTD', 'type' => 'text', 'placeholder' => 'Contoh: UPTD001'],
                ['name' => 'uptd_address', 'label' => 'Alamat UPTD', 'type' => 'textarea', 'placeholder' => 'Alamat lengkap UPTD'],
                ['name' => 'upt_code', 'label' => 'UPT', 'type' => 'select', 'parent' => 'upts', 'empty_option' => 'Pilih UPT']
            ],
            'parent' => 'mas_upts'
        ],
        'mas_device_parameters' => [
            'title' => 'Parameter Perangkat',
            'description' => 'Kelola data parameter perangkat monitoring',
            'fields' => [
                ['name' => 'device_parameter_name', 'label' => 'Nama Parameter', 'type' => 'text', 'placeholder' => 'Contoh: Tinggi Muka Air'],
                ['name' => 'device_parameter_code', 'label' => 'Kode Parameter', 'type' => 'text', 'placeholder' => 'Contoh: TMA'],
                ['name' => 'device_parameter_unit', 'label' => 'Satuan', 'type' => 'text', 'placeholder' => 'Contoh: cm'],
                ['name' => 'device_parameter_description', 'label' => 'Deskripsi', 'type' => 'textarea', 'placeholder' => 'Deskripsi parameter']
            ],
            'parent' => null
        ],
        'mas_sensor_parameters' => [
            'title' => 'Parameter Sensor',
            'description' => 'Kelola data parameter sensor monitoring',
            'fields' => [
                ['name' => 'sensor_parameter_name', 'label' => 'Nama Parameter', 'type' => 'text', 'placeholder' => 'Contoh: Suhu Air'],
                ['name' => 'sensor_parameter_code', 'label' => 'Kode Parameter', 'type' => 'text', 'placeholder' => 'Contoh: TEMP'],
                ['name' => 'sensor_parameter_unit', 'label' => 'Satuan', 'type' => 'text', 'placeholder' => 'Contoh: °C'],
                ['name' => 'sensor_parameter_description', 'label' => 'Deskripsi', 'type' => 'textarea', 'placeholder' => 'Deskripsi parameter sensor']
            ],
            'parent' => null
        ]
    ];
    
    public function generateViews() {
        foreach ($this->templates as $module => $config) {
            $this->generateModuleView($module, $config);
        }
    }
    
    private function generateModuleView($module, $config) {
        $path = $this->basePath . $module . '/';
        $filename = $path . 'index.blade.php';
        
        // Create directory if not exists
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        $content = $this->generateViewContent($module, $config);
        file_put_contents($filename, $content);
        
        echo "Generated: {$filename}\n";
    }
    
    private function generateViewContent($module, $config) {
        $title = $config['title'];
        $description = $config['description'];
        $fields = $config['fields'];
        $parent = $config['parent'];
        
        // Generate route names
        $routePrefix = 'admin.' . str_replace('_', '-', $module);
        $singularName = rtrim($module, 's');
        $camelCase = $this->toCamelCase($module);
        $kebabCase = str_replace('_', '-', $module);
        
        $content = "@extends('layouts.admin')

@section('title', '{$title}')
@section('page-title', '{$title}')
@section('page-description', '{$description}')
@section('breadcrumb', '{$title}')

@section('content')
<div class=\"space-y-6\" x-data=\"{$camelCase}Page()\" x-init=\"init()\">

    <!-- Filter Section -->
    @php
        \$filterConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Cari {$title}',
                'placeholder' => 'Cari berdasarkan nama atau kode...'
            ],";
        
        // Add parent filter if exists
        if ($parent) {
            $parentVariable = '$' . str_replace('mas_', '', $parent);
            $parentLabel = $this->templates[$parent]['title'];
            $content .= "
            [
                'type' => 'select',
                'name' => '" . $this->getParentField($fields) . "',
                'label' => '{$parentLabel}',
                'empty_option' => 'Semua {$parentLabel}',
                'options' => {$parentVariable}->toArray()
            ],";
        }
        
        $content .= "
            [
                'type' => 'select',
                'name' => 'per_page',
                'label' => 'Per Halaman',
                'options' => [
                    ['value' => '10', 'label' => '10'],
                    ['value' => '15', 'label' => '15'],
                    ['value' => '25', 'label' => '25'],
                    ['value' => '50', 'label' => '50'],
                    ['value' => '100', 'label' => '100']
                ]
            ]
        ];
    @endphp

    <x-filter-bar 
        title=\"Filter & Pencarian {$title}\"
        :\$filters=\"\$filterConfig\"
        :action=\"route('{$routePrefix}.index')\"
        gridCols=\"md:grid-cols-3\"
    />

    <x-table
        title=\"Daftar {$title}\"
        :headers=\"\$tableHeaders\"
        :rows=\"\${$this->getVariableName($module)}\"
        searchable
        searchPlaceholder=\"Cari {$title}...\"
    >
        <x-slot:actions>
            <x-admin.button type=\"button\" variant=\"primary\" @click=\"openCreate()\">
                <i class=\"fa-solid fa-plus -ml-1 mr-2\"></i>
                Tambah {$title}
            </x-admin.button>
        </x-slot:actions>
    </x-table>

    <!-- Modal Create -->
    <x-admin.modal :show=\"false\" name=\"{$kebabCase}-create\" title=\"Tambah {$title}\" size=\"lg\" :close-on-backdrop=\"true\">
        <form id=\"{$camelCase}CreateForm\" x-ref=\"createForm\" action=\"{{ route('{$routePrefix}.store') }}\" method=\"POST\" class=\"space-y-6\">
            @csrf
            <input type=\"hidden\" name=\"context\" value=\"create\" />
            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-6\">";
        
        // Generate form fields
        foreach ($fields as $field) {
            $content .= $this->generateFormField($field, 'create');
        }
        
        $content .= "
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type=\"button\" variant=\"secondary\" @click=\"closeModal('{$kebabCase}-create')\">
                Batal
            </x-admin.button>
            <x-admin.button type=\"submit\" variant=\"primary\" form=\"{$camelCase}CreateForm\">
                <i class=\"fa-solid fa-save -ml-1 mr-2\"></i>
                Simpan
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

    <!-- Modal Edit -->  
    <x-admin.modal :show=\"false\" name=\"{$kebabCase}-edit\" title=\"Edit {$title}\" size=\"lg\" :close-on-backdrop=\"true\">
        <form id=\"{$camelCase}EditForm\" x-ref=\"editForm\" :action=\"editAction\" method=\"POST\" class=\"space-y-6\">
            @csrf
            @method('PUT')
            <input type=\"hidden\" name=\"context\" value=\"edit\" />
            <div class=\"grid grid-cols-1 md:grid-cols-2 gap-6\">";
        
        // Generate edit form fields
        foreach ($fields as $field) {
            $content .= $this->generateFormField($field, 'edit');
        }
        
        $content .= "
            </div>
        </form>
        
        <x-slot:footer>
            <x-admin.button type=\"button\" variant=\"secondary\" @click=\"closeModal('{$kebabCase}-edit')\">
                Batal
            </x-admin.button>
            <x-admin.button type=\"submit\" variant=\"primary\" form=\"{$camelCase}EditForm\">
                <i class=\"fa-solid fa-save -ml-1 mr-2\"></i>
                Perbarui
            </x-admin.button>
        </x-slot:footer>
    </x-admin.modal>

</div>

<script>
function {$camelCase}Page() {
    return {
        editData: {},
        editAction: '',
        
        init() {
            // Listen for custom events to open modals
            window.addEventListener('open-edit-{$kebabCase}', (e) => {
                this.openEdit(e.detail);
            });
        },
        
        openCreate() {
            this.\$refs.createForm.reset();
            this.openModal('{$kebabCase}-create');
        },
        
        openEdit(data) {
            this.editData = { ...data };
            this.editAction = `{{ route('{$routePrefix}.index') }}/\${data.id}`;
            this.openModal('{$kebabCase}-edit');
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
        
        return $content;
    }
    
    private function generateFormField($field, $context) {
        $name = $field['name'];
        $label = $field['label'];
        $type = $field['type'];
        $placeholder = $field['placeholder'] ?? '';
        $xModel = $context === 'edit' ? "x-model=\"editData.{$name}\"" : '';
        
        $content = "\n                <x-admin.form-input \n";
        
        if ($type === 'select') {
            $parent = $field['parent'] ?? null;
            $emptyOption = $field['empty_option'] ?? 'Pilih ' . $label;
            $options = $parent ? '$' . str_replace('mas_', '', $parent) : '$options';
            
            $content .= "                    type=\"select\"\n";
            $content .= "                    name=\"{$name}\" \n";
            $content .= "                    label=\"{$label}\" \n";
            $content .= "                    required=\"true\" \n";
            if ($context === 'create') {
                $content .= "                    :error=\"\$errors->first('{$name}')\"\n";
            }
            $content .= "                    :options=\"{$options}\"\n";
            $content .= "                    empty-option=\"{$emptyOption}\"\n";
            if ($xModel) $content .= "                    {$xModel}\n";
            
        } elseif ($type === 'textarea') {
            $content .= "                    type=\"textarea\" \n";
            $content .= "                    name=\"{$name}\" \n";
            $content .= "                    label=\"{$label}\" \n";
            $content .= "                    placeholder=\"{$placeholder}\" \n";
            if ($context === 'create') {
                $content .= "                    :error=\"\$errors->first('{$name}')\"\n";
            }
            if ($xModel) $content .= "                    {$xModel}\n";
            
        } else {
            $content .= "                    type=\"text\" \n";
            $content .= "                    name=\"{$name}\" \n";
            $content .= "                    label=\"{$label}\" \n";
            $content .= "                    placeholder=\"{$placeholder}\" \n";
            $content .= "                    required=\"true\" \n";
            if ($context === 'create') {
                $content .= "                    :error=\"\$errors->first('{$name}')\"\n";
            }
            if ($xModel) $content .= "                    {$xModel}\n";
        }
        
        $content .= "                />";
        
        return $content;
    }
    
    private function getParentField($fields) {
        foreach ($fields as $field) {
            if ($field['type'] === 'select' && isset($field['parent'])) {
                return $field['name'];
            }
        }
        return '';
    }
    
    private function getVariableName($module) {
        return str_replace('mas_', '', $module);
    }
    
    private function toCamelCase($string) {
        $parts = explode('_', $string);
        $camelCase = $parts[0];
        for ($i = 1; $i < count($parts); $i++) {
            $camelCase .= ucfirst($parts[$i]);
        }
        return $camelCase;
    }
}

// Run the generator
$generator = new ViewGenerator();
$generator->generateViews();

echo "\nAll view files generated successfully!\n";