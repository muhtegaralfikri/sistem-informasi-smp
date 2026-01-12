<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100']) }}>
    @if (isset($header))
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            {{ $header }}
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
