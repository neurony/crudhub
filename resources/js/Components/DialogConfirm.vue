<template>
    <TransitionRoot as="template" :show="isOpened">
        <Dialog @close="decline" as="div" class="relative z-50">
            <TransitionChild as="div" enter="ease-in duration-200" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in transition-opacity duration-100" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 opacity-50 transition-opacity bg-gray-500" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-100" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-75 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-75 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <slot name="icon">
                                        <div v-if="showIcon" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <ExclamationTriangleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                                        </div>
                                    </slot>
                                    <div :class="{'sm:ml-4' : showIcon}" class="mt-3 text-center sm:mt-0 sm:text-left">
                                        <DialogTitle v-if="!!$slots.title" as="h3" class="text-base font-semibold leading-6 text-gray-900">
                                            <slot name="title"></slot>
                                        </DialogTitle>
                                        <div v-if="!!$slots.description" class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                <slot name="description"></slot>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button @click="confirm" type="button" class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm sm:ml-3 sm:w-auto" :class="confirmButtonClasses">
                                    <slot name="confirmbutton">
                                        Yes
                                    </slot>
                                </button>
                                <button @click="decline" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset ring-gray-300 sm:mt-0 sm:w-auto" :class="cancelButtonClasses">
                                    <slot name="declinebutton">
                                        Cancel
                                    </slot>
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';

import {
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

export default {
    emits: [
        'confirmed',
        'declined',
    ],
    props: {
        isOpened: {
            type: Boolean,
            required: true,
        },
        showIcon: {
            type: Boolean,
            required: false,
            default: true,
        },
        confirmButtonClasses: {
            type: Array,
            required: false,
            default: [
                'text-white',
                'bg-red-600',
                'hover:bg-red-500',
            ],
        },
        cancelButtonClasses: {
            type: Array,
            required: false,
            default: [
                'text-gray-900',
                'hover:bg-gray-50',
            ],
        },
    },
    components: {
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        ExclamationTriangleIcon,
    },
    methods: {
        confirm() {
            this.$emit('confirmed');
        },
        decline() {
            this.$emit('declined');
        }
    }
}
</script>
