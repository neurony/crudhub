<template>
    <th
        scope="col"
        :class="[stickyStart ? 'sticky left-0 z-20' : '', stickyEnd ? 'sticky right-0 bg-gray-50 z-20' : '']"
        class="z-10 py-4 px-5 text-left text-sm font-semibold text-gray-900 first-of-type:pl-4 first-of-type:sm:pl-6 bg-gray-50"
    >
        <template v-if="sortBy">
            <div class="inline-flex">
                <button @click.prevent="sortRecords" type="button" class="group cursor-pointer">
                    <span class="flex items-center gap-2">
                        <slot></slot>
                        <span :class="{'bg-gray-200 text-gray-700': isSorting}" class="flex-none px-0.5 rounded text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-700">
                            <ChevronUpIcon v-if="isSorting && isSortingAsc" class="h-5 w-5" aria-hidden="true" />
                            <ChevronDownIcon v-else-if="isSorting && isSortingDesc" class="h-5 w-5" aria-hidden="true" />
                            <ChevronUpDownIcon v-else class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </span>
                </button>
            </div>
        </template>
        <template v-else>
            <slot></slot>
        </template>
    </th>
</template>

<script>
import {
    router,
} from '@inertiajs/vue3';

import {
    ChevronDownIcon,
    ChevronUpIcon,
    ChevronUpDownIcon,
} from '@heroicons/vue/20/solid';

export default {
    props: {
        label: {
            type: String,
            default: '',
        },
        sortBy: {
            type: String,
            default: null,
        },
        sortUrl: {
            type: String,
            default: null,
        },
        stickyStart: {
            type: Boolean,
            default: false,
        },
        stickyEnd: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        ChevronDownIcon,
        ChevronUpIcon,
        ChevronUpDownIcon,
    },
    data() {
        return {
            sort: {
                field: this.$page.props.query['sort_by'] ?? null,
                direction: this.$page.props.query['sort_dir'] ?? null,
            }
        }
    },
    methods: {
        sortRecords() {
            const url = this.sortUrl ?? this.$page.url.split(/[?#]/)[0];

            router.get(url, {
                ...this.$page.props.query,
                ...{
                    sort_by: this.sortBy,
                    sort_dir: this.sortDir,
                },
            }, {
                preserveScroll: true,
            });
        },
    },
    computed: {
        sortDir() {
            return this.sort.direction === 'asc' ? 'desc' : 'asc';
        },
        isSorting() {
            return this.sortBy === this.sort.field;
        },
        isSortingAsc() {
            return this.sort.direction === 'asc';
        },
        isSortingDesc() {
            return this.sort.direction === 'desc';
        },
    }
}
</script>
