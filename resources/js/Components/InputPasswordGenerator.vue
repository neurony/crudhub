<template>
    <label v-if="label" :for="`input-text-${name}`" class="block text-sm font-medium leading-6 text-gray-900">
        {{ label }}
    </label>

    <div class="flex mt-2 rounded-md shadow-sm">
        <div class="relative flex flex-grow items-stretch focus-within:z-10">
            <input @input="handleInputChanged"
                   type="password"
                   :value="modelValue"
                   :name="name"
                   :id="`input-text-${name}`"
                   :placeholder="placeholder"
                   :class="[errorMessage ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-300 focus:ring-indigo-600']"
                   class="text-gray-900 block w-full rounded-md rounded-r-none border-0 pr-10 ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6"
            />

            <div v-if="errorMessage" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
            </div>
        </div>
        <button @click.prevent="generatePassword(12)" type="button" class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-gray-600 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            Generate
        </button>
    </div>

    <p v-if="errorMessage" class="mt-2 text-sm text-red-600" :id="`${name}-error`">
        {{ errorMessage }}
    </p>

    <DialogInfo @acknowledge="acknowledgePasswordGeneration" :is-opened="generatedPassword.openDialog">
        <template #title>
            Password generated
        </template>
        <template #description>
            A password has been generated and copied to your clipboard<br/>
            <code v-if="generatedPassword.currentValue" class="inline-block mt-1 py-1 px-2 bg-indigo-100 text-indigo-600 rounded-md font-semibold">
                {{ generatedPassword.currentValue }}
            </code>
        </template>
    </DialogInfo>
</template>

<script>
import {
    ExclamationCircleIcon,
} from '@heroicons/vue/24/solid';

import {
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';

import CopiesToClipboard from "../Mixins/CopiesToClipboard.js";
import DialogInfo from "./DialogInfo.vue";

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
    },
    mixins: [
        CopiesToClipboard,
    ],
    components: {
        DialogInfo,
        CheckCircleIcon,
        ExclamationCircleIcon,
    },
    data() {
        return {
            errorMessage: this.error,
            generatedPassword: {
                currentValue: null,
                openDialog: false,
            },
        }
    },
    methods: {
        handleInputChanged($event) {
            this.$emit('update:modelValue', $event.target.value)
            this.$emit('update:error', null);
        },
        generatePassword(length = 16) {
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=<>?";

            let password = '';

            for (let i = 0; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }

            this.copyToClipboard(password);

            this.generatedPassword.currentValue = password;
            this.generatedPassword.openDialog = true;

            this.$emit('update:modelValue', password);
            this.errorMessage = null;
        },
        acknowledgePasswordGeneration() {
            this.generatedPassword.openDialog = false;

            setTimeout(function () {
                this.generatedPassword.currentValue = null;
            }.bind(this), 300);
        }
    },
    watch: {
        error: function(newVal, oldVal) {
            this.errorMessage = newVal;
        }
    },
}
</script>
