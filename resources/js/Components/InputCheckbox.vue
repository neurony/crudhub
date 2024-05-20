<template>
    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input
                @change="handleInputChanged"
                :value="modelValue"
                :checked="isChecked"
                :name="name"
                :id="`input-checkbox-${name}`"
                :class="[errorMessage ? 'border-red-300 text-red-500 focus:ring-red-500' : 'border-gray-300 text-indigo-600 focus:ring-indigo-600']"
                class="h-4 w-4 rounded"
                type="checkbox"
            />
        </div>

        <div v-if="label" class="ml-2.5 leading-5">
            <label :for="`input-checkbox-${name}`" class="font-medium text-sm text-gray-900">
                {{ label }}
            </label>
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
    ],
    props: {
        modelValue: {
            type: [Boolean, Number],
            required: false,
            default: false,
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
            let value = $event.target.checked;

            value = value === true || value === 1 ? 1 : 0;

            this.$emit('update:modelValue', value);
            this.$emit('update:error', null);
        }
    },
    computed: {
        isChecked() {
            return this.modelValue === true || this.modelValue === 1;
        }
    },
    watch: {
        error: function(newVal, oldVal) {
            this.errorMessage = newVal;
        }
    },
}
</script>
