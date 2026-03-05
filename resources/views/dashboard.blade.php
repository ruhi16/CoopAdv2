<x-app-layout>
    {{-- <x-slot name="myheader">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight bg-red-400">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="py-12">
        <div class="flex flex-col max-w-8xl mx-auto sm:px-6 lg:px-8">
            {{-- <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
            </div> --}}
            
            @livewire('lc-main-layout')
            


        </div>
    </div>

   
</x-app-layout>
