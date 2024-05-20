<template>
    <div class="border-b border-b-gray-200 shadow ring-1 ring-gray-600/5 ring-opacity-5 bg-white px-4 py-3 text-gray-900 sm:rounded-t-md sm:px-6 sm:py-4">
        <div class="relative flex items-center">
            <div class="h-[34px]"></div>
            <div class="flex w-full items-center justify-between gap-3">
                <div class="w-3/6 md:w-2/6">
                    <div v-if="showSearch" class="flex gap-1 flex-col w-full relative">
                        <div class="flex flex-col gap-2">
                            <div class="relative flex rounded-md shadow-sm">
                                <div class="relative w-full">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <MagnifyingGlassIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                                    </div>
                                    <input v-model="keyword" type="text" placeholder="Search" autocomplete="" class="pl-10 border-gray-300 focus:border-primary-500 focus:ring-primary-500 py-1.5 px-3 block w-full rounded-md text-sm text-gray-800 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="inline-flex rounded-md">
                    <div class="relative flex text-left">
                        <div aria-expanded="false" data-headlessui-state="" class="flex">
                            <slot name="extra-items"></slot>
                            <button v-if="$slots.default" @click.prevent="openFilters" type="button" :class="[hasFiltersApplied ? 'rounded-l-md' : 'rounded-md']" class="shadow-sm inline-flex cursor-pointer items-center justify-center font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-300 focus-visible:ring-offset-2 border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 active:bg-gray-100 shadow-sm px-3 py-1.5 text-sm">
                                <AdjustmentsHorizontalIcon class="-ml-0.5 mr-2 h-4 w-4" aria-hidden="true" />
                                Filters
                                <ExclamationCircleIcon v-if="hasFiltersApplied" class="ml-2 h-5 w-5 text-indigo-600" aria-hidden="true" />
                            </button>
                            <button v-if="hasFiltersApplied" @click.prevent="clearFilters" type="button" :class="[$slots.default ? 'rounded-r-md border-l-0' : 'rounded-md']" class="shadow-sm inline-flex cursor-pointer items-center justify-center font-medium focus:outline-none border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 active:bg-gray-100 shadow-sm px-2 py-1.5 text-sm">
                                <XMarkIconSolid class="h-5 w-5" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <TransitionRoot :show="opened" as="template" key="filter-slide-over">
        <Dialog as="div" class="relative z-40" @close="closeFilters">
            <div class="fixed inset-0" />
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <TransitionChild as="template" enter="transform transition ease-in-out duration-300 sm:duration-400" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-300 sm:duration-400" leave-from="translate-x-0" leave-to="translate-x-full">
                            <DialogPanel class="pointer-events-auto w-screen max-w-md">
                                <form @submit.prevent="applyFilters" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                                    <div class="h-0 flex-1 overflow-y-auto">
                                        <div class="bg-indigo-600 px-4 py-6 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-base font-medium leading-6 text-indigo-100">
                                                    Filter records
                                                </DialogTitle>
                                                <div class="ml-3 flex h-7 items-center">
                                                    <button @click.prevent="closeFilters" type="button" class="relative rounded-md bg-indigo-600 text-indigo-200 hover:text-white focus:outline-none">
                                                        <span class="absolute -inset-2.5" />
                                                        <span class="sr-only">Close panel</span>
                                                        <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-6">
                                            <div class="mt-1 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                                <slot></slot>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-shrink-0 justify-end px-4 py-4">
                                        <button @click.prevent="closeFilters" type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="submitting" :class="[submitting ? 'cursor-not-allowed opacity-50' : '']" class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                            Filter
                                        </button>
                                    </div>
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {
    router,
} from '@inertiajs/vue3';

import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';

import {
    MagnifyingGlassIcon,
    AdjustmentsHorizontalIcon,
    XCircleIcon,
    ExclamationCircleIcon,
    XMarkIcon as XMarkIconSolid,
} from '@heroicons/vue/20/solid';

import {
    XMarkIcon,
} from '@heroicons/vue/24/outline'

export default {
    emits: [
        'apply',
        'clear',
    ],
    props: {
        showSearch: {
            type: Boolean,
            required: false,
            default: true,
        },
        excludedFromQuery: {
            type: Array,
            required: false,
            default: ['page', 'per_page'],
        }
    },
    components: {
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        MagnifyingGlassIcon,
        AdjustmentsHorizontalIcon,
        XMarkIcon,
        XCircleIcon,
        ExclamationCircleIcon,
        XMarkIconSolid,
    },
    data() {
        return {
            opened: false,
            submitting: false,
            keyword: this.$page.props.query.keyword ?? null,
        }
    },
    methods: {
        openFilters() {
            this.opened = true;
        },
        closeFilters() {
            this.opened = false;
        },
        applyFilters($event) {
            this.submitting = true;
            this.opened = false;

            this.$emit('apply');
        },
        clearFilters() {
            this.$emit('clear');
        }
    },
    mounted() {
        this.hasFiltersApplied;
    },
    computed: {
        hasFiltersApplied() {
            const query = this.$page.props.query;

            for (const key in query) {
                if (this.excludedFromQuery.includes(key)) {
                    continue;
                }

                if (query[key]) {
                    return true;
                }
            }

            return false;
        }
    },
    watch: {
        keyword: _.debounce(function (value){
            const url = this.$page.url.split(/[?#]/)[0];

            router.get(url, {
                ...this.$page.props.query,
                ...{page: 1},
                ...{keyword: value},
            }, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            });
        }, 350),
    }
}
</script>
