<template>
    <TransitionRoot as="template" :show="isOpened">
        <Dialog @close="acknowledge" as="div" class="relative z-50">
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
                                        <div v-if="showIcon" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <CheckCircleIcon class="h-6 w-6 text-indigo-600" aria-hidden="true" />
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
                                <button @click="acknowledge" type="button" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto" :class="acknowledgeButtonClasses">
                                    <slot name="acknowledgebutton">
                                        Ok
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
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';

export default {
    emits: [
        'acknowledge',
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
        acknowledgeButtonClasses: {
            type: Array,
            required: false,
            default: [],
        }
    },
    components: {
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        CheckCircleIcon,
    },
    methods: {
        acknowledge() {
            this.$emit('acknowledge');
        },
    }
}
</script>
