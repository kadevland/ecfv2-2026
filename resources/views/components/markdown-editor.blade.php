@props([
    'name',
    'value' => '',
    'label' => null,
    'placeholder' => 'Écrivez votre contenu en Markdown...',
    'required' => false,
    'minHeight' => '300px',
    'help' => null,
    'autosave' => false,
    'toolbar' => null,
])

@php
    $id = $name . '_' . Str::random(8);
@endphp

<div class="markdown-editor-wrapper">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium mb-2 dark:text-white">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        class="easymde"
        data-placeholder="{{ $placeholder }}"
        data-autosave="{{ $autosave ? 'true' : 'false' }}"
        data-min-height="{{ $minHeight }}"
        @if($required) required @endif
    >{{ old($name, $value) }}</textarea>

    @if($help)
        <p class="text-xs text-gray-500 mt-2 dark:text-neutral-400">
            {!! $help !!}
        </p>
    @endif

    @error($name)
        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
    @enderror
</div>