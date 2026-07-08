@props(['menuItems' => [], 'color' => 'blue'])

<aside class="w-64 bg-{{ $color }}-900 text-white min-h-screen fixed left-0 top-0 pt-16">
    <nav class="mt-4">
        <ul class="space-y-1">
            @foreach ($menuItems as $item)
                <li>
                    <a href="{{ $item['link'] }}" 
class="flex items-center gap-3 px-4 py-3 hover:bg-{{ $color }}-800 transition duration-200 {{ request()->is($item['active']) ? 'bg-'.$color.'-800 border-l-4 border-'.$color.'-400' : '' }}"                        @if (isset($item['icon']))
                            <span class="text-lg">{{ $item['icon'] }}</span>
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</aside>
