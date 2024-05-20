<template>
    <PageHeader title="Users" subtitle="Manage your users">
        <ButtonAdd :url="route('admin.users.create')" />
    </PageHeader>

    <PageContent>
        <div class="overflow-hidden rounded-md shadow ring-1 ring-gray-600/5 ring-opacity-5">
            <Filter />

            <Table :items="items.data" :bulk-delete="true" @delete-multiple="bulkDeleteRecords">
                <template #head>
                    <TableTh sort-by="name" :sticky-start="true">User</TableTh>
                    <TableTh sort-by="active">Active</TableTh>
                    <TableTh sort-by="created_at">Joined at</TableTh>
                    <TableTh :sticky-end="true"></TableTh>
                </template>

                <template #row="{element, index}">
                    <TableTd :sticky-start="true">
                        <div class="flex items-center">
                            <AvatarIcon :src="element.avatar?.preview_url || ''" :alt="element.name" />

                            <div class="ml-4">
                                <div class="font-medium text-gray-900">{{ element.name }}</div>
                                <div class="text-gray-500">{{ element.email }}</div>
                            </div>
                        </div>
                    </TableTd>
                    <TableTd>
                        <InputToggle v-model="element.active" @toggled="updateActive($event, element.id)"/>
                    </TableTd>
                    <TableTd>
                        <div>
                            <div class="font-medium text-gray-900">{{ moment(element.created_at).format('DD MMMM, YYYY') }}</div>
                            <div class="text-gray-500">at {{ moment(element.created_at).format('HH:mm') }}</div>
                        </div>
                    </TableTd>
                    <TableTd :sticky-end="true">
                        <div class="flex items-center justify-end gap-3">
                            <ButtonEdit :url="route('admin.users.edit', element.id)" />
                            <ButtonDelete @click.prevent="confirmDeleteRecord(element.id)" />
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
</template>

<script>
import {
    router,
} from '@inertiajs/vue3';

import moment from "moment";

import AvatarIcon from "crudhub/Components/AvatarIcon.vue";
import ButtonAdd from "crudhub/Components/ButtonAdd.vue";
import ButtonDelete from "crudhub/Components/ButtonDelete.vue";
import ButtonEdit from "crudhub/Components/ButtonEdit.vue";
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
    components: {
        AvatarIcon,
        ButtonAdd,
        ButtonDelete,
        ButtonEdit,
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
            },
        }
    },
    methods: {
        moment,
        deleteRecord() {
            router.delete(route('admin.users.destroy', this.deletion.id), {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    this.deletion.confirm = false;
                },
            });
        },
        bulkDeleteRecords(ids) {
            router.post(route('admin.users.bulk_destroy'), {
                ids: ids,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
        },
        confirmDeleteRecord(id) {
            this.deletion.id = id;
            this.deletion.confirm = true;
        },
        declineDeleteRecord() {
            this.deletion.id = null;
            this.deletion.confirm = false;
        },
        updateActive(value, id) {
            router.patch(route('admin.users.partial_update', id), {
                active: value,
            }, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            });
        },
    },
}
</script>
