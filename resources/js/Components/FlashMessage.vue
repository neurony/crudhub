<template>
    <div v-if="traditional" aria-live="assertive" class="z-50 pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="show.success" class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg border border-2 border-dashed border-green-500 border-opacity-75">
                    <div class="p-4 ">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <CheckCircleIcon class="h-6 w-6 text-green-500" aria-hidden="true" />
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-700" v-text="successMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="show.error" class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg border border-2 border-dashed border-red-500 border-opacity-75">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <ExclamationCircleIcon class="h-6 w-6 text-red-400" aria-hidden="true" />
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900" v-text="errorMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
import {
    router,
} from '@inertiajs/vue3';

import {
    CheckCircleIcon,
    ExclamationCircleIcon,
} from '@heroicons/vue/24/outline';

import Flash from "../Classes/Flash.js";

export default {
    props: {
        traditional: {
            type: Boolean,
            required: false,
            default: false,
        }
    },
    components: {
        CheckCircleIcon,
        ExclamationCircleIcon,
    },
    mounted() {
        this.displayMessages();

        router.on('finish', () => {
            this.displayMessages();
        });
    },
    data() {
        return {
            show: {
                success: false,
                error: false,
            }
        }
    },
    methods: {
        displayMessages() {
            this.displaySuccessMessage();
            this.displayErrorMessage();
        },
        displaySuccessMessage() {
            if (!this.successMessage) {
                return;
            }

            if (this.traditional) {
                this.show.success = true;

                setTimeout(() => {
                    this.show.success = false;
                }, 2500);
            } else {
                Flash.success(this.successMessage);
            }
        },
        displayErrorMessage() {
            if (!this.errorMessage) {
                return;
            }

            if (this.traditional) {
                this.show.error = true;

                setTimeout(() => {
                    this.show.error = false;
                }, 2500);
            } else {
                Flash.error(this.errorMessage);
            }
        },
    },
    computed: {
        successMessage() {
            return this.$page.props.flash.success ?? null;
        },
        errorMessage() {
            return this.$page.props.flash.error ?? null;
        },
    },
}
</script>
