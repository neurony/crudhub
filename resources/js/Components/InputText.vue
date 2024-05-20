<template>
    <div>
        <label v-if="label" :for="`input-text-${name}`" class="block text-sm font-medium leading-6 text-gray-900">
            {{ label }}
        </label>

        <div class="relative mt-2 rounded-md shadow-sm">
            <input @input="handleInputChanged"
                   type="text"
                   :value="modelValue"
                   :name="name"
                   :id="`input-text-${name}`"
                   :placeholder="placeholder"
                   :disabled="disabled"
                   :class="[errorMessage ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-300 focus:ring-indigo-600']"
                   class="text-gray-900 block w-full rounded-md border-0 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
            />

            <div v-if="errorMessage" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
            </div>
        </div>

        <p v-if="errorMessage" class="mt-2 text-sm text-red-600" :id="`${name}-error`">
            {{ errorMessage }}
        </p>
    </div>
</template>

<script>
import {
    ExclamationCircleIcon
} from '@heroicons/vue/24/solid';

export default {
    emits: [
        'update:modelValue',
        'update:error',
    ],
    props: {
        modelValue: {
            type: [String, Number],
            required: false,
            default: null,
        },
        name: {
            type: String,
            required: true,
        },
        error: {
            type: String,
            required: false,
            default: null,
        },
        label: {
            type: String,
            required: false,
        },
        placeholder: {
            type: String,
            required: false,
        },
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
    },
    components: {
        ExclamationCircleIcon,
    },
    data() {
        return {
            errorMessage: this.error,
        }
    },
    methods: {
        handleInputChanged($event) {
            this.$emit('update:modelValue', $event.target.value);
            this.$emit('update:error', null);
        },
    },
    watch: {
        error: function(newVal, oldVal) {
            this.errorMessage = newVal;
        }
    },
}
</script>
