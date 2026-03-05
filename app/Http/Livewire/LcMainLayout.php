<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LcMainLayout extends Component
{
    public $activeMenu = 'dashboard'; // Default active menu item
    public $activeSubMenu = ''; // Active submenu item
    
    // Menu items structure
    public $menuItems = [
        'dashboard' => [
            'label' => 'Dashboard',
            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        'products' => [
            'label' => 'Products',
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'submenu' => [
                'all-products' => 'All Products',
                'categories' => 'Categories',
                'inventory' => 'Inventory',
            ],
        ],
        'orders' => [
            'label' => 'Orders',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        ],
        'customers' => [
            'label' => 'Customers',
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        ],
    ];

    // Set active menu
    public function setActiveMenu($menu, $subMenu = null)
    {
        $this->activeMenu = $menu;
        $this->activeSubMenu = $subMenu;
        // $this->dispatch('menu-selected', menu: $menu);
        // dd($menu, $subMenu);
    }
    public function render()
    {
        return view('livewire.lc-main-layout');
    }
}
