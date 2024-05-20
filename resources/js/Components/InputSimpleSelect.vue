<template>
    <label v-if="label" :for="`input-select-${name}`" class="block text-sm font-medium leading-6 text-gray-900">
        {{ label }}
    </label>

    <div :class="{'mt-2': label}" class="relative rounded-md shadow-sm">
        <select
            @input="handleInputChanged"
            @change="onChange"
            :value="modelValue"
            :name="name"
            :id="`input-select-${name}`"
            :class="[errorMessage ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-300 focus:ring-indigo-600']"
            class="text-gray-900 block w-full rounded-md border-0 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
        >
            <option
                v-if="placeholder"
                v-text="placeholder"
                disabled
            ></option>

            <option
                v-for="(option, index) in options"
                :key="`${name}-${index}-select-option`"
                :value="typeof option === 'object' ? option[valueProp] : option"
                v-text="typeof option === 'object' ? option[trackBy] : option"
            ></option>
        </select>

        <div v-if="errorMessage" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
        </div>
    </div>

    <p v-if="errorMessage" class="mt-2 text-sm text-red-600" :id="`${name}-error`">
        {{ errorMessage }}
    </p>
</template>

<script>
import {
    ExclamationCircleIcon
} from '@heroicons/vue/24/solid';

export default {
    emits: [
        'update:modelValue',
        'update:error',
        'change',
    ],
    props: {
        modelValue: {
            type: String,
            required: false,
            default: null,
        },
        name: {
            type: String,
            required: true,
        },
        options: {
            type: Array,
            required: true,
        },
        valueProp: {
            type: String,
            required: false,
            default: 'id'
        },
        trackBy: {
            type: String,
            required: false,
            default: 'text'
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
        onChange($event) {
            this.$emit('change', $event.target.value);
            this.$emit('update:error', null);
        }
    },
    watch: {
        modelValue(newVal) {
            this.$emit('update:modelValue', newVal);
            this.$emit('update:error', null);
        },
        error: function(newVal, oldVal) {
            this.errorMessage = newVal;
        }
    }
}
</script>
