<x-filament-widgets::widget>
    @php($warnings = $this->getWarnings())

    @if(count($warnings) > 0)
        <x-filament::section>
            <x-slot name="heading">Configuration à finaliser</x-slot>

            <ul class="space-y-2">
                @foreach($warnings as $warning)
                    <li class="flex items-center justify-between gap-4">
                        <span class="text-sm text-warning-600 dark:text-warning-400">{{ $warning['label'] }}</span>
                        <a href="{{ $warning['url'] }}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">
                            Configurer &rarr;
                        </a>
                    </li>
                @endforeach
            </ul>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
