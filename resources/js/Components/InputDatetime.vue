<template>
    <label v-if="label" :for="`input-datetime-${name}`" class="block text-sm font-medium leading-6 text-gray-900">
        {{ label }}
    </label>

    <div class="relative mt-2 rounded-md shadow-sm">
        <input @input="handleInputChanged"
               type="text"
               :value="modelValue"
               :name="name"
               :id="`input-datetime-${name}`"
               :ref="`input-datetime-${name}`"
               :placeholder="placeholder"
               :class="defaultClasses"
               readonly
        />

        <div v-if="errorMessage" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
        </div>
    </div>

    <p v-if="errorMessage" class="mt-2 text-sm text-red-600" :id="`${name}-error`">
        {{ errorMessage }}
    </p>
</template>

<script>
import flatpickr from 'flatpickr';

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
            type: String,
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
        format: {
            type: String,
            required: false,
            default: 'Y-m-d H:i'
        },
        minDate: {
            type: String,
            required: false,
        },
        maxDate: {
            type: String,
            required: false,
        },
        minTime: {
            type: String,
            required: false,
        },
        maxTime: {
            type: String,
            required: false,
        },
    },
    components: {
        ExclamationCircleIcon,
    },
    mounted() {
        this.createFlatpickrInstance();
    },
    data() {
        return {
            defaultClasses: 'text-gray-900 block w-full rounded-md border-0 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6',
            errorMessage: this.error,
            flatpickrInstance: false,
        }
    },
    methods: {
        handleInputChanged($event) {
            this.$emit('update:modelValue', $event.target.value)
            this.$emit('update:error', null);
        },
        createFlatpickrInstance() {
            this.flatpickrInstance = flatpickr(this.$refs[`input-datetime-${this.name}`], {
                dateFormat: 'Y-m-d H:i',
                altInput: true,
                altFormat: this.format,
                minDate: this.minDate,
                maxDate: this.maxDate,
                enableTime: true,
                minTime: this.minTime,
                maxTime: this.maxTime,
                onReady: (selectedDates, dateStr, instance) => {
                    this.updateFlatpickrInputClasses(instance);
                }
            });
        },
        updateFlatpickrInputClasses(instance) {
            if (!instance || !instance.altInput) {
                return;
            }

            let newClasses = this.defaultClasses;

            if (this.errorMessage) {
                newClasses += ' ring-red-300 focus:ring-red-500';
            } else {
                newClasses += ' ring-gray-300 focus:ring-indigo-600';
            }

            if (this.disabled) {
                newClasses += ' bg-gray-100 cursor-not-allowed';
            }

            instance.altInput.className = newClasses;
        },
    },
    watch: {
        error: function(newVal, oldVal) {
            this.errorMessage = newVal;
        },
        errorMessage(newVal) {
            if (this.flatpickrInstance) {
                this.updateFlatpickrInputClasses(this.flatpickrInstance);
            }
        },
        disabled(newVal) {
            if (this.flatpickrInstance) {
                this.updateFlatpickrInputClasses(this.flatpickrInstance);
            }
        },
    },
}
</script>
