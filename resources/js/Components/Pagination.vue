<template>
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-5 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <Component
                :is="previousPage.url ? 'Link' : 'span'"
                :href="previousPage.url"
                v-text="`Previous`"
                preserve-state
                preserve-scroll
                :class="[previousPage.url ? 'hover:bg-gray-50' : 'cursor-not-allowed opacity-50']"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700"
            />
            <Component
                :is="nextPage.url ? 'Link' : 'span'"
                :href="nextPage.url"
                v-text="`Next`"
                preserve-state
                preserve-scroll
                :class="[previousPage.url ? 'hover:bg-gray-50' : 'cursor-not-allowed opacity-50']"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700"
            />
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div class="flex items-center gap-x-4">
                <div class="flex gap-1 flex-col rounded-md shadow-sm relative">
                    <div class="flex flex-col gap-2">
                        <div class="relative flex rounded-md text-gray-600 shadow-sm">
                            <InputSimpleSelect
                                v-model="perPageValue"
                                @change="updatePerPage"
                                :options="perPageOptions"
                                name="per_page"
                            />
                        </div>
                    </div>
                </div>

                <p class="text-sm text-gray-500">
                    Showing {{ fromRecord }} to {{ toRecord }} of {{ totalRecords }} results
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <Component
                        :is="previousPage.url ? 'Link' : 'span'"
                        :href="previousPage.url"
                        preserve-state
                        preserve-scroll
                        :class="[previousPage.url ? 'hover:bg-gray-50' : 'cursor-not-allowed opacity-50']"
                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-700 ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0"
                    >
                        <span class="sr-only">Previous</span>
                        <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                    </Component>

                    <Component
                        v-for="page in numberPages"
                        :is="page.url ? 'Link' : 'span'"
                        :href="page.url"
                        v-text="page.label"
                        preserve-state
                        preserve-scroll
                        :aria-current="page.active ? 'page' : null"
                        :class="[page.active ? 'z-10 bg-indigo-600 font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0']"
                        class="relative inline-flex items-center px-4 py-2 text-sm"
                    />

                    <Component
                        :is="nextPage.url ? 'Link' : 'span'"
                        :href="nextPage.url"
                        preserve-state
                        preserve-scroll
                        :class="[nextPage.url ? 'hover:bg-gray-50' : 'cursor-not-allowed opacity-50']"
                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-700 ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0"
                    >
                        <span class="sr-only">Previous</span>
                        <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                    </Component>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import {
    Link,
    router,
} from '@inertiajs/vue3';

import {
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@heroicons/vue/20/solid';
import InputSimpleSelect from "./InputSimpleSelect.vue";

export default {
    props: {
        perPage: {
            type: [Number, String],
            required: true,
        },
        totalRecords: {
            type: Number,
            required: true,
        },
        fromRecord: {
            type: Number,
            required: true,
        },
        toRecord: {
            type: Number,
            required: true,
        },
        pageLinks: {
            type: Array,
            required: true,
        }
    },
    components: {
        InputSimpleSelect,
        Link,
        ChevronLeftIcon,
        ChevronRightIcon,
    },
    data() {
        return {
            perPageValue: (this.$page.props.query.per_page ?? 10).toString(),
            perPageOptions: [10, 20, 50, 100],
        }
    },
    methods: {
        updatePerPage() {
            const url = this.$page.url.split(/[?#]/)[0];

            router.get(url, {
                ...this.$page.props.query,
                ...{page: 1},
                ...{per_page: this.perPageValue},
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        }
    },
    computed: {
        currentPage() {
            return this.pageLinks.find(item => item.active === true);
        },
        previousPage() {
            const activeIndex = this.pageLinks.findIndex(item => item.active === true);

            return this.pageLinks[activeIndex - 1] ?? null;
        },
        nextPage() {
            const activeIndex = this.pageLinks.findIndex(item => item.active === true);

            return this.pageLinks[activeIndex + 1];
        },
        numberPages() {
            return this.pageLinks.slice(1, -1);
        }
    }
}
</script>
