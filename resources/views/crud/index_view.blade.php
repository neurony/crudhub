<!-- Generated with Crudhub -->

<template>
    <PageHeader title="{{ $titleName }}" subtitle="Manage your {{ $subtitleName }}">
        <ButtonAdd :url="route('{{ $routeNames['create'] }}')" />
    </PageHeader>

    <PageContent>
        <div class="overflow-hidden rounded-md shadow ring-1 ring-gray-600/5 ring-opacity-5">
            <Filter />

            <Table
                :items="items.data"
                :bulk-delete="true"
@if($withReorderable)
                :draggable="true"
@endif
                @delete-multiple="bulkDeleteRecords"
@if($withReorderable)
                @dragged="reorderRecords"
@endif
            >
                <template #head>
@foreach($tableFields as $index => $field)
@if($index == 0)
                    <TableTh sort-by="{{ $field['name'] }}" :sticky-start="true">
@else
                    <TableTh sort-by="{{ $field['name'] }}">
@endif
                        {{ $field['label'] }}
                    </TableTh>
@endforeach
@if($withSoftDelete)
                    <TableTh v-if="parseInt(query.with_trashed) === 1">
                        Trashed
                    </TableTh>
@endif
                    <TableTh :sticky-end="true"></TableTh>
                </template>

                <template #row="{element, index}">
@foreach($tableFields as $index => $field)
@if($index == 0)
                    <TableTd :sticky-start="true">
@else
                    <TableTd>
@endif
@if($field['type'] == 'boolean' || $field['type'] == 'tinyint')
                        <InputToggle v-model="element.{{ $field['name'] }}" @toggled="update{{ $field['label_studly'] }}($event, element.id)"/>
@elseif(in_array($field['type'], ['int', 'integer', 'float', 'double']))
                        <Badge type="info">
                            @{{ element.{!! $field['name'] !!} }}
                        </Badge>
@elseif((bool)($field['translatable'] ?? false) === true)
                        <div>@{{ element.{!! $field['name'] !!}?.[currentLocale] ?? 'N/A' }}</div>
@else
                        <div>@{{ element.{!! $field['name'] !!} ?? 'N/A' }}</div>
@endif
                    </TableTd>
@endforeach
@if($withSoftDelete)
                    <TableTd v-if="parseInt(query.with_trashed) === 1">
                        <Badge :type="element.deleted_at ? 'danger' : 'success'">
                            @{{ element.deleted_at ? 'Yes' : 'No' }}
                        </Badge>
                    </TableTd>
@endif
                    <TableTd :sticky-end="true">
                        <div class="flex items-center justify-end gap-3">
@if($withSoftDelete)
                            <template v-if="element.deleted_at">
                                <ButtonRestore @click.prevent="confirmRestoreRecord(element.id)" />
                                <ButtonDelete @click.prevent="confirmForceDeleteRecord(element.id)" />
                            </template>
                            <template v-else>
                                <ButtonEdit :url="route('{{ $routeNames['edit'] }}', element.id)" />
                                <ButtonDelete @click.prevent="confirmDeleteRecord(element.id)" />
                            </template>
@else
                            <ButtonEdit :url="route('{{ $routeNames['edit'] }}', element.id)" />
                            <ButtonDelete @click.prevent="confirmDeleteRecord(element.id)" />
@endif
                        </div>
                    </TableTd>
                </template>
            </Table>

            <Pagination
                v-if="items.data.length && items.meta"
                :per-page="items.meta.per_page"
                :total-records="items.meta.total"
                :from-record="items.meta.from"
                :to-record="items.meta.to"
                :page-links="items.meta.links"
            />
        </div>
    </PageContent>

@if($withSoftDelete)
    <DialogConfirm :is-opened="deletion.confirm" @confirmed="deleteRecord" @declined="declineDeleteRecord">
        <template #title>
            Delete record?
        </template>
        <template #description>
            Are you sure you want to soft delete this record?
            <br />
            You can restore it later.
        </template>
        <template #confirmbutton>
            Delete
        </template>
    </DialogConfirm>

    <DialogConfirm :is-opened="deletion.confirm_restore" @confirmed="restoreRecord" @declined="declineRestoreRecord" :confirm-button-classes="['text-white', 'bg-green-600', 'hover:bg-green-500']">
        <template #title>
            Restore record?
        </template>
        <template #description>
            Are you sure you want to restore this record?
            <br />
            This record will become publicly visible.
        </template>
        <template #icon>
            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <ArrowPathIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
            </div>
        </template>
        <template #confirmbutton>
            Restore
        </template>
    </DialogConfirm>

    <DialogConfirm :is-opened="deletion.confirm_force" @confirmed="forceDeleteRecord" @declined="declineForceDeleteRecord">
        <template #title>
            Delete record?
        </template>
        <template #description>
            Are you sure you want to permanently delete this record?
            <br />
            This action cannot be undone.
        </template>
        <template #confirmbutton>
            Delete
        </template>
    </DialogConfirm>
@else
    <DialogConfirm :is-opened="deletion.confirm" @confirmed="deleteRecord" @declined="declineDeleteRecord">
        <template #title>
            Delete record?
        </template>
        <template #description>
            Are you sure you want to delete this record?
            <br />
            This action cannot be undone.
        </template>
        <template #confirmbutton>
            Delete
        </template>
    </DialogConfirm>
@endif
</template>

<script>
import {
    router,
} from '@inertiajs/vue3';
@if($withReorderable)

import axios from "axios";
@endif
@if($withSoftDelete)

import {
    ArrowPathIcon,
} from '@heroicons/vue/24/outline';
@endif
@if($withTranslatable)

import SwitchesLocale from "crudhub-lang/Mixins/SwitchesLocale.js";
@endif

import Badge from "crudhub/Components/Badge.vue";
import ButtonAdd from "crudhub/Components/ButtonAdd.vue";
import ButtonDelete from "crudhub/Components/ButtonDelete.vue";
import ButtonEdit from "crudhub/Components/ButtonEdit.vue";
@if($withSoftDelete)
import ButtonRestore from "crudhub/Components/ButtonRestore.vue";
@endif
import DialogConfirm from "crudhub/Components/DialogConfirm.vue";
import Filter from "./Filter.vue";
import InputToggle from "crudhub/Components/InputToggle.vue";
import PageContent from "crudhub/Components/PageContent.vue";
import PageHeader from "crudhub/Components/PageHeader.vue";
import Pagination from "crudhub/Components/Pagination.vue";
import Table from "crudhub/Components/Table.vue";
import TableTd from "crudhub/Components/TableTd.vue";
import TableTh from "crudhub/Components/TableTh.vue";

export default {
    props: {
        query: Object,
        items: Object,
    },
@if($withTranslatable)
    mixins: [
        SwitchesLocale,
    ],
@endif
    components: {
@if($withSoftDelete)
        ArrowPathIcon,
@endif
        Badge,
        ButtonAdd,
        ButtonDelete,
        ButtonEdit,
@if($withSoftDelete)
        ButtonRestore,
@endif
        DialogConfirm,
        Filter,
        InputToggle,
        PageContent,
        PageHeader,
        Pagination,
        Table,
        TableTd,
        TableTh,
    },
    data() {
        return {
            deletion: {
                id: null,
                confirm: false,
@if($withSoftDelete)
                id_restore: null,
                confirm_restore: false,
                id_force: null,
                confirm_force: false,
@endif
            },
        }
    },
    methods: {
        deleteRecord() {
            router.delete(route('{{ $routeNames['destroy'] }}', this.deletion.id), {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    this.deletion.confirm = false;
                },
            });
        },
@if($withSoftDelete)
        restoreRecord() {
            router.patch(route('{{ $routeNames['restore'] }}', this.deletion.id_restore), {}, {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    this.deletion.confirm_restore = false;
                },
            });
        },
        forceDeleteRecord() {
            router.delete(route('{{ $routeNames['force_destroy'] }}', this.deletion.id_force), {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    this.deletion.confirm_force = false;
                },
            });
        },
@endif
        bulkDeleteRecords(ids) {
            router.post(route('{{ $routeNames['bulk_destroy'] }}'), {
                ids: ids,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        },
@if($withReorderable)
        reorderRecords(data) {
            try {
                const ids = _.map(data, function (item) {
                    return item.key;
                });

                axios.post(route('{{ $routeNames['reorder'] }}'), {
                    ids: ids,
                });
            } catch (error) {
                console.error(error)
            }
        },
@endif
        confirmDeleteRecord(id) {
            this.deletion.id = id;
            this.deletion.confirm = true;
        },
        declineDeleteRecord() {
            this.deletion.id = null;
            this.deletion.confirm = false;
        },
@if($withSoftDelete)
        confirmRestoreRecord(id) {
            this.deletion.id_restore = id;
            this.deletion.confirm_restore = true;
        },
        declineRestoreRecord() {
            this.deletion.id_restore = null;
            this.deletion.confirm_restore = false;
        },
        confirmForceDeleteRecord(id) {
            this.deletion.id_force = id;
            this.deletion.confirm_force = true;
        },
        declineForceDeleteRecord() {
            this.deletion.id_force = null;
            this.deletion.confirm_force = false;
        },
@endif
@foreach($tableFields as $field)
@if($field['type'] == 'boolean' || $field['type'] == 'tinyint')
        update{{ $field['label_studly'] }}(value, id) {
            router.patch(route('{{ $routeNames['partial_update'] }}', id), {
                {!! $field['name'] !!}: value,
            }, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            });
        },
@endif
@endforeach
    },
}
</script>
