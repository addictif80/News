<x-filament-panels::page>
    @vite(['resources/css/template-builder.css', 'resources/js/template-builder.js'])

    <div
        id="gjs-app"
        wire:ignore
        data-wire-id="{{ $this->getId() }}"
        data-blocks="{{ json_encode($this->blockDefinitions()) }}"
        data-html="{{ e($record->html ?? '') }}"
        data-css="{{ e($record->css ?? '') }}"
        data-grapesjs-data="{{ json_encode($record->grapesjs_data ?: []) }}"
    >
        <div class="mb-3 flex items-center justify-between gap-4">
            <div class="flex gap-2">
                <button type="button" class="gjs-device-btn fi-btn fi-color-gray" data-device="Desktop">Bureau</button>
                <button type="button" class="gjs-device-btn fi-btn fi-color-gray" data-device="Tablet">Tablette</button>
                <button type="button" class="gjs-device-btn fi-btn fi-color-gray" data-device="Mobile portrait">Mobile</button>
            </div>
            <button id="gjs-save" type="button" class="fi-btn fi-color-primary fi-btn-size-md">
                Enregistrer le design
            </button>
        </div>

        <div class="gjs-builder-shell grid grid-cols-[220px_1fr_260px] gap-2 rounded-lg border border-gray-200 dark:border-gray-700">
            <div id="gjs-blocks" class="overflow-y-auto border-r border-gray-200 p-2 dark:border-gray-700"></div>
            <div id="gjs" class="overflow-hidden"></div>
            <div class="overflow-y-auto border-l border-gray-200 dark:border-gray-700">
                <div id="gjs-selectors" class="border-b border-gray-200 p-2 dark:border-gray-700"></div>
                <div id="gjs-styles" class="border-b border-gray-200 p-2 dark:border-gray-700"></div>
                <div id="gjs-layers" class="p-2"></div>
            </div>
        </div>
    </div>

</x-filament-panels::page>
