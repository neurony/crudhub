<template>
    <div class="overflow-x-auto">
        <table :class="[request.ongoing ? 'opacity-50 pointer-events-none' : '']" class="table-fixed min-w-full divide-y divide-gray-200 transition-opacity">
            <thead class="bg-gray-50">
                <tr v-if="items.length" class="py-4">
                    <TableTh v-if="bulkDelete" class="max-w-[60px]">
                        <input v-model="selectAll" @change="toggleSelectAllItems" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                    </TableTh>
                    <template v-if="bulkDelete && selectedItems.length > 0">
                        <TableTh class="relative w-full" colspan="50">
                            <button @click.prevent="confirmMultipleDelete" type="button" class="absolute top-1/2 -translate-y-1/2 inline-flex items-center gap-x-3 bg-transparent text-sm font-semibold underline text-indigo-600 hover:text-indigo-500 focus-visible:outline-none">
                                <TrashIcon class="h-4 w-4 stroke-2" aria-hidden="true" />
                                Delete {{ selectedItems.length }} records
                            </button>
                        </TableTh>
                    </template>
                    <template v-else>
                        <TableTh v-if="draggable && !hasQueryFilled(this.$page.props?.query ?? {}, ['page'])" class="max-w-[60px]">
                            <ArrowsPointingOutIcon class="h-5 w-5 text-gray-400" />
                        </TableTh>
                        <slot name="head"></slot>
                    </template>
                </tr>
                <tr v-else class="py-4">
                    <TableTh colspan="50" class="py-[26px]"></TableTh>
                </tr>
            </thead>

            <Draggable
                v-if="draggable"
                :list="items"
                @change="updateItemsOrder"
                :item-key="`draggable-table`"
                direction="vertical"
                handle=".drag-item"
                tag="tbody"
                class="divide-y divide-gray-200"
                ghost-class="opacity-0"
            >
                <template #item="{element, index}">
                    <tr :key="index">
                        <TableTd v-if="bulkDelete" class="max-w-[60px]">
                            <div v-if="isSelectedItem(element[itemKey])" class="bg-indigo-600 absolute inset-y-0 left-0 w-0.5"></div>
                            <input v-model="selectedItems" :value="element[this.itemKey]" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                        </TableTd>
                        <TableTd v-if="draggable && !hasQueryFilled(this.$page.props?.query ?? {}, ['page'])" class="drag-item max-w-[60px] cursor-move">
                            <ArrowsPointingOutIcon class="h-5 w-5 text-gray-400" />
                        </TableTd>
                        <slot name="row" v-bind="{element, index}"></slot>
                    </tr>
                </template>

                <template #footer v-if="!items.length">
                    <tr>
                        <TableTd colspan="50">
                    <span class="text-gray-500">
                        {{ noItemsText }}
                    </span>
                        </TableTd>
                    </tr>
                </template>
            </Draggable>

            <tbody v-else class="divide-y divide-gray-200">
                <tr v-if="items.length" v-for="(element, index) in items" :key="index">
                    <TableTd v-if="bulkDelete" class="max-w-[60px]">
                        <div v-if="isSelectedItem(element[itemKey])" class="bg-indigo-600 absolute inset-y-0 left-0 w-0.5"></div>
                        <input v-model="selectedItems" :value="element[this.itemKey]" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                    </TableTd>
                    <TableTd v-if="draggable && !hasQueryFilled(this.$page.props?.query ?? {}, ['page'])" class="drag-item max-w-[60px] cursor-move">
                        <ArrowsPointingOutIcon class="h-5 w-5 text-gray-400" />
                    </TableTd>
                    <slot name="row" v-bind="{element, index}"></slot>
                </tr>
                <tr v-else>
                    <TableTd colspan="50">
                        <span class="text-gray-500">
                            {{ noItemsText }}
                        </span>
                    </TableTd>
                </tr>
            </tbody>
        </table>
    </div>

    <DialogConfirm :is-opened="confirmOpened" @confirmed="deleteSelectedItems" @declined="declineMultipleDelete">
        <template #title>
            Delete {{ selectedItems.length }} records?
        </template>
        <template #description>
            Are you sure you want to delete these records?<br />This action cannot be undone.
        </template>
        <template #confirmbutton>
            Delete
        </template>
    </DialogConfirm>
</template>

<script>
import {
    request,
} from "../constants.js";

import {
    ArrowsPointingOutIcon,
} from '@heroicons/vue/24/solid';

import {
    TrashIcon
} from '@heroicons/vue/24/outline';

import Draggable from "vuedraggable";

import ChecksFilledQuery from "../Mixins/ChecksFilledQuery.js";
import TableTd from "./TableTd.vue";
import TableTh from "./TableTh.vue";
import DialogConfirm from "./DialogConfirm.vue";

export default {
    emits: [
        'dragged',
        'deleteMultiple',
    ],
    mixins: [
        ChecksFilledQuery,
    ],
    props: {
        items: {
            type: [Array, Object],
            required: true,
        },
        draggable: {
            type: Boolean,
            required: false,
            default: false,
        },
        itemKey: {
            type: String,
            required: false,
            default: 'id',
        },
        bulkDelete: {
            type: Boolean,
            required: false,
            default: true,
        },
        noItemsText: {
            type: String,
            required: false,
            default: 'No records found',
        },
    },
    components: {
        ArrowsPointingOutIcon,
        TrashIcon,
        Draggable,
        TableTd,
        TableTh,
        DialogConfirm,
    },
    data() {
        return {
            request: request,
            selectedItems: [],
            selectAll: false,
            confirmOpened: false,
        }
    },
    methods: {
        isSelectedItem(key) {
            return this.selectedItems.includes(key);
        },
        toggleSelectAllItems() {
            this.selectedItems = this.selectAll ? this.items.map(item => item[this.itemKey]) : [];
        },
        deleteSelectedItems() {
            this.$emit('deleteMultiple', this.selectedItems);

            this.confirmOpened = false;
            this.selectedItems = [];
        },
        confirmMultipleDelete() {
            this.confirmOpened = true;
        },
        declineMultipleDelete() {
            this.selectedItems = [];
            this.confirmOpened = false;
        },
        updateItemsOrder($event) {
            let data = [];

            this.items.forEach((item, index) => {
                data.push({
                    key: item[this.itemKey],
                    order: index + 1,
                })
            });

            this.$emit('dragged', data);
        }
    },
    watch: {
        selectedItems: {
            handler() {
                this.selectAll = this.selectedItems.length === this.items.length;
            },
            deep: true,
        }
    }
}
</script>
