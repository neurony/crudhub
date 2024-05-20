<template>
    <PageHeader title="Permissions" subtitle="Manage your roles & permissions assignments">
        <ButtonSave @click="savePermissionsForRoles()" />
    </PageHeader>

    <PageContent>
        <div class="overflow-hidden rounded-md shadow ring-1 ring-gray-600/5 ring-opacity-5">
            <div class="overflow-x-auto">
                <table v-if="Object.keys(data).length > 0" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="sticky left-0 z-20 p-3 text-left text-xs font-semibold text-gray-500 first-of-type:pl-4 last-of-type:pr-4 first-of-type:sm:pl-6 last-of-type:sm:pr-6 bg-white border-r">
                                <div class="w-56">
                                    <div class="text-sm font-normal text-slate-900">Permission</div>
                                </div>
                            </th>
                            <th v-for="role in roles" scope="col" class="z-10 p-3 text-left text-xs font-semibold text-gray-500 first-of-type:pl-4 last-of-type:pr-4 first-of-type:sm:pl-6 last-of-type:sm:pr-6 border-l bg-white">
                                <div class="w-52">
                                    <div class="text-xs font-medium text-slate-500">Role</div>
                                    <div class="text-sm font-normal text-slate-900">{{ role.name }}</div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t bg-white">
                            <td class="sticky left-0 z-20 bg-white px-3 text-sm text-gray-600 first-of-type:pl-4 last-of-type:pr-4 first-of-type:sm:pl-6 last-of-type:sm:pr-6 relative py-0 pr-0">
                                <div class="flex items-center group cursor-pointer py-4">
                                    <button @click.prevent="toggleAllCollapsible()" type="button" class="mr-2">
                                        <ChevronDownIcon v-if="!collapse.all" class="h-6 w-6" />
                                        <ChevronUpIcon v-else class="h-6 w-6" />
                                    </button>
                                    <div class="text-sm font-medium">
                                        All
                                    </div>
                                </div>
                            </td>
                            <td v-for="role in roles" class="z-10 px-3 text-sm text-gray-500 first-of-type:pl-4 last-of-type:pr-4 first-of-type:sm:pl-6 last-of-type:sm:pr-6 border-l">
                                <label class="flex items-center justify-center py-4">
                                    <input
                                        @change="togglePermissionsForRole(role.id, allPermissionIds)"
                                        :checked="checkedPermissionsForRole(role.id, allPermissionIds)"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                    />
                                </label>
                            </td>
                        </tr>

                        <template v-for="(permissionCollection, permissionGroup) in permissions">
                            <tr class="border-t bg-gray-50">
                                <td class="sticky left-0 z-20 bg-gray-50 px-3 py-4 text-sm text-gray-500 first-of-type:pl-4 last-of-type:pr-4 first-of-type:sm:pl-6 last-of-type:sm:pr-6 relative py-0 pr-0">
                                    <div class="flex items-center group cursor-pointer">
                                        <button @click.prevent="toggleCollapsible(permissionGroup)" type="button" class="mr-2">
                                            <ChevronDownIcon v-if="!collapse[permissionGroup]" class="h-6 w-6" />
                                            <ChevronUpIcon v-else class="h-6 w-6" />
                                        </button>
                                        <div class="text-sm text-slate-900 font-medium">
                                            {{ permissionGroup }}
                                        </div>
                                    </div>
                                </td>
                                <td v-for="role in roles" class="z-10 bg-gray-50 px-3 text-sm text-gray-500 first-of-type:pl-4 last-of-type:pr-4 first-of-type:sm:pl-6 last-of-type:sm:pr-6 border-l">
                                    <label class="flex items-center justify-center py-4">
                                        <input
                                            @change="togglePermissionsForRole(role.id, map(permissionCollection, 'id'))"
                                            :checked="checkedPermissionsForRole(role.id, map(permissionCollection, 'id'))"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                        />
                                    </label>
                                </td>
                            </tr>
                            <tr v-show="!collapse[permissionGroup]" v-for="permission in permissionCollection" class="border-t bg-white">
                                <td class="sticky left-0 z-20 bg-white px-3 text-sm text-gray-500 first-of-type:pl-4 last-of-type:pr-4 first-of-type:sm:pl-6 last-of-type:sm:pr-6 relative py-0 pr-0">
                                    <div class="flex pl-8 py-4">
                                        <div class="text-sm text-slate-900">
                                            {{ permission.name }}
                                        </div>
                                    </div>
                                </td>
                                <td v-for="role in roles" class="z-10 bg-white px-3 text-sm text-gray-500 first-of-type:pl-4 last-of-type:pr-4 first-of-type:sm:pl-6 last-of-type:sm:pr-6 border-l">
                                    <label class="flex items-center justify-center py-4">
                                        <input
                                            @change="togglePermissionForRole(role.id, permission.id)"
                                            :checked="checkedPermissionForRole(role.id, permission.id)"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                        />
                                    </label>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </PageContent>
</template>

<script>
import {
    useForm,
} from '@inertiajs/vue3';

import {
    map,
    pull,
    pullAll,
    union,
} from 'lodash';

import {
    ChevronDownIcon,
    ChevronUpIcon,
} from '@heroicons/vue/20/solid';

import ButtonSave from "crudhub/Components/ButtonSave.vue";
import PageContent from "crudhub/Components/PageContent.vue";
import PageHeader from "crudhub/Components/PageHeader.vue";

export default {
    props: {
        roles: Object,
        permissions: Object,
    },
    components: {
        ButtonSave,
        ChevronDownIcon,
        ChevronUpIcon,
        PageContent,
        PageHeader,
    },
    data() {
        return {
            data: {},
            collapse: {
                all: false,
            },
        }
    },
    async created() {
        for (const [index, role] of Object.entries(this.roles)) {
            this.data[role.id] = this.map(role.permissions, 'id');
        }

        for (const [group, permissions] of Object.entries(this.permissions)) {
            this.collapse[group] = false;
        }
    },
    methods: {
        map,
        pull,
        pullAll,
        union,
        savePermissionsForRoles() {
            useForm({
                'roles_permissions': this.data,
            }).post(route('admin.permissions.update'));
        },
        togglePermissionForRole(roleId, permissionId) {
            if (this.checkedPermissionForRole(roleId, permissionId)) {
                this.data[roleId] = this.pull(this.data[roleId], permissionId);
            } else {
                this.data[roleId].push(permissionId);
            }
        },
        togglePermissionsForRole(roleId, permissionIds) {
            if (this.checkedPermissionsForRole(roleId, permissionIds)) {
                this.data[roleId] = this.pullAll(this.data[roleId], permissionIds);
            } else {
                this.data[roleId] = this.union(this.data[roleId], permissionIds);
            }
        },
        checkedPermissionForRole(roleId, permissionId) {
            try {
                return this.data[roleId].includes(permissionId);
            } catch (error) {
                console.error(error);

                return false;
            }
        },
        checkedPermissionsForRole(roleId, permissionIds) {
            return permissionIds.every((permissionId) => {
                return this.checkedPermissionForRole(roleId, permissionId);
            });
        },
        toggleCollapsible(key) {
            this.collapse[key] = !this.collapse[key];

            let allCollapsed = true;

            for (const key of Object.keys(this.collapse)) {
                if (key === 'all') {
                    continue;
                }

                if (this.collapse[key] === false) {
                    allCollapsed = false;

                    break;
                }
            }

            this.collapse.all = allCollapsed;
        },
        toggleAllCollapsible() {
            this.collapse.all = !this.collapse.all;

            for (const key of Object.keys(this.collapse)) {
                if (key === 'all') {
                    continue;
                }

                this.collapse[key] = this.collapse.all;
            }
        },
    },
    computed: {
        allPermissionIds() {
            let ids = [];

            for (const [group, permissions] of Object.entries(this.permissions)) {
                for (const [index, permission] of Object.entries(permissions)) {
                    ids.push(permission.id);
                }
            }

            return ids;
        }
    }
}
</script>
