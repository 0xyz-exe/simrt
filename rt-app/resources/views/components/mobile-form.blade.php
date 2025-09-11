@props(['fields', 'action', 'method' => 'POST', 'submitText' => 'Simpan'])

<form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}"
      class="mobile-form space-y-6" x-data="mobileForm()">

    @if($method !== 'GET' && $method !== 'POST')
        @method($method)
    @endif

    @if($method !== 'GET')
        @csrf
    @endif

    @foreach($fields as $field)
        <div class="form-group">
            <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $field['label'] }}
                @if($field['required'] ?? false)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            @switch($field['type'])
                @case('text')
                @case('email')
                @case('password')
                @case('number')
                @case('tel')
                    <input type="{{ $field['type'] }}"
                           id="{{ $field['name'] }}"
                           name="{{ $field['name'] }}"
                           value="{{ old($field['name'], $field['value'] ?? '') }}"
                           placeholder="{{ $field['placeholder'] ?? '' }}"
                           class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           {{ ($field['required'] ?? false) ? 'required' : '' }}
                           {{ isset($field['readonly']) && $field['readonly'] ? 'readonly' : '' }}>
                    @break

                @case('textarea')
                    <textarea id="{{ $field['name'] }}"
                              name="{{ $field['name'] }}"
                              rows="{{ $field['rows'] ?? 4 }}"
                              placeholder="{{ $field['placeholder'] ?? '' }}"
                              class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                              {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ old($field['name'], $field['value'] ?? '') }}</textarea>
                    @break

                @case('select')
                    <select id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @if($field['placeholder'] ?? false)
                            <option value="">{{ $field['placeholder'] }}</option>
                        @endif
                        @foreach($field['options'] as $value => $label)
                            <option value="{{ $value }}"
                                    {{ old($field['name'], $field['value'] ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @break

                @case('radio')
                    <div class="space-y-3">
                        @foreach($field['options'] as $value => $label)
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio"
                                       name="{{ $field['name'] }}"
                                       value="{{ $value }}"
                                       class="form-radio h-5 w-5 text-green-600 focus:ring-green-500"
                                       {{ old($field['name'], $field['value'] ?? '') == $value ? 'checked' : '' }}
                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                <span class="text-sm text-gray-900">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @break

                @case('checkbox')
                    @if(isset($field['options']))
                        <div class="space-y-3">
                            @foreach($field['options'] as $value => $label)
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox"
                                           name="{{ $field['name'] }}[]"
                                           value="{{ $value }}"
                                           class="form-checkbox h-5 w-5 text-green-600 focus:ring-green-500 rounded"
                                           {{ in_array($value, old($field['name'], $field['value'] ?? [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-900">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox"
                                   name="{{ $field['name'] }}"
                                   value="1"
                                   class="form-checkbox h-5 w-5 text-green-600 focus:ring-green-500 rounded"
                                   {{ old($field['name'], $field['value'] ?? false) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-900">{{ $field['checkbox_label'] ?? $field['label'] }}</span>
                        </label>
                    @endif
                    @break

                @case('file')
                    <div class="file-upload" x-data="fileUpload()">
                        <input type="file"
                               id="{{ $field['name'] }}"
                               name="{{ $field['name'] }}"
                               class="hidden"
                               {{ isset($field['accept']) ? 'accept=' . $field['accept'] : '' }}
                               {{ isset($field['multiple']) && $field['multiple'] ? 'multiple' : '' }}
                               @change="handleFileChange($event)"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>

                        <label for="{{ $field['name'] }}"
                               class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                                </p>
                                <p class="text-xs text-gray-500">{{ $field['help'] ?? 'SVG, PNG, JPG atau GIF' }}</p>
                            </div>
                        </label>

                        <div x-show="files.length > 0" class="mt-4 space-y-2">
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-900" x-text="file.name"></span>
                                    </div>
                                    <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    @break

                @case('date')
                    <input type="date"
                           id="{{ $field['name'] }}"
                           name="{{ $field['name'] }}"
                           value="{{ old($field['name'], $field['value'] ?? '') }}"
                           class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           {{ ($field['required'] ?? false) ? 'required' : '' }}>
                    @break

                @default
                    <input type="text"
                           id="{{ $field['name'] }}"
                           name="{{ $field['name'] }}"
                           value="{{ old($field['name'], $field['value'] ?? '') }}"
                           placeholder="{{ $field['placeholder'] ?? '' }}"
                           class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           {{ ($field['required'] ?? false) ? 'required' : '' }}>
            @endswitch

            @if($field['help'] ?? false)
                <p class="mt-2 text-sm text-gray-600">{{ $field['help'] }}</p>
            @endif

            @error($field['name'])
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endforeach

    <!-- Form Actions -->
    <div class="flex flex-col space-y-3 pt-6">
        <button type="submit"
                class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors btn-touch font-medium"
                :disabled="submitting"
                @click="handleSubmit()">
            <span x-show="!submitting">{{ $submitText }}</span>
            <span x-show="submitting" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            </span>
        </button>

        @if(isset($cancelUrl))
            <a href="{{ $cancelUrl }}"
               class="w-full bg-gray-200 text-gray-800 py-3 px-4 rounded-lg hover:bg-gray-300 transition-colors btn-touch font-medium text-center">
                Batal
            </a>
        @endif
    </div>
</form>

<script>
function mobileForm() {
    return {
        submitting: false,

        handleSubmit() {
            this.submitting = true;
            // Form will submit normally, this just shows loading state
        }
    }
}

function fileUpload() {
    return {
        files: [],

        handleFileChange(event) {
            this.files = Array.from(event.target.files);
        },

        removeFile(index) {
            this.files.splice(index, 1);
            // Reset the input
            const input = event.target.closest('.file-upload').querySelector('input[type="file"]');
            input.value = '';
        }
    }
}
</script>

<style>
.form-input {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.form-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-radio:focus,
.form-checkbox:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
