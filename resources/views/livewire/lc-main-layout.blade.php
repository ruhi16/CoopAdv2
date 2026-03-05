<div>
    <div class="flex h-screen bg-gray-50">
    <!-- Left Sidebar Menu -->
    <div class="w-64 bg-white shadow-md">
        <div class="p-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">Menu</h1>
        </div>
        
        <nav class="p-2">
            <ul class="space-y-1">
                @foreach($menuItems as $key => $item)
                    <li>
                        <button 
                            wire:click="setActiveMenu('{{ $key }}')"
                            class="w-full flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-100 
                                   {{ $activeMenu === $key ? 'bg-blue-50 text-blue-600' : '' }}"
                        >
                            <!-- Heroicon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                            </svg>
                            <span class="ml-3">{{ $item['label'] }}</span>
                            
                            @if(isset($item['submenu']))
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-auto h-4 w-4 transform transition-transform duration-200 
                                    {{ $activeMenu === $key ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            @endif
                        </button>
                        
                        <!-- Submenu items -->
                        @if(isset($item['submenu']) && $activeMenu === $key)
                            <ul class="ml-8 mt-1 space-y-1">
                                @foreach($item['submenu'] as $subKey => $subItem)
                                    <li>
                                        <button 
                                            wire:click="setActiveMenu('{{ $key }}', '{{ $subKey }}')"
                                            class="w-full flex items-center p-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 
                                                   {{ $activeSubMenu === $subKey ? 'bg-blue-50 text-blue-600' : '' }}"
                                        >
                                            <span>{{ $subItem }}</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>

    <!-- Right Content Area -->
    <div class="flex-1 overflow-auto p-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                @if($activeSubMenu)
                    {{ $menuItems[$activeMenu]['submenu'][$activeSubMenu] }}
                @else
                    {{ $menuItems[$activeMenu]['label'] }}
                @endif
            </h2>
            
            <div class="text-gray-600">
                @if($activeMenu === 'dashboard')
                    <p>Welcome to your dashboard. Here you can see an overview of your application.</p>
                    {{-- @livewire('bs-school-comp')
                    @livewire('bs02-financialyear-comp') --}}
                    {{-- @livewire('bs05-member-type-comp') --}}
                    @livewire('bs06-member-db-comp')
                    {{-- @livewire('bs07-member-cr-comp')  --}}
                    {{-- @livewire('ec01-loan-scheme-comp')
                    @livewire('ec02-loan-scheme-feature-comp')
                    @livewire('ec02-loan-scheme-detail-comp') --}}

                    @livewire('ec03-loan-request-comp')
                    @livewire('ec05-loan-assign-comp')

                @elseif($activeMenu === 'products')
                    @if($activeSubMenu === 'all-products')
                        <p>Displaying all products...</p>

                    @elseif($activeSubMenu === 'categories')
                        <p>Product categories management...</p>

                    @elseif($activeSubMenu === 'inventory')
                        <p>Inventory tracking...</p>

                    @else
                        <p>Products management section. Select a submenu option.</p>

                    @endif


                @elseif($activeMenu === 'orders')
                    <p>Order management section. View and process customer orders.</p>
                @elseif($activeMenu === 'customers')
                    <p>Customer management section. View and edit customer information.</p>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
