<!-- Flash Messages -->
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4" x-data="{ show: true }" x-show="show">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4" x-data="{ show: true }" x-show="show">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4" x-data="{ show: true }" x-show="show">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle h-5 w-5 text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex bg-yellow-50 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4" x-data="{ show: true }" x-show="show">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle h-5 w-5 text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex bg-blue-50 rounded-md p-1.5 text-blue-500 hover:bg-blue-100">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Validation Errors -->
@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4" x-data="{ show: true }" x-show="show">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Ada kesalahan dalam input:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif


