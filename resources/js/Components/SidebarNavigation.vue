<template>
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <li v-for="menuGroup in menuGroups" :key="menuGroup.heading">
                        <div class="text-xs font-semibold leading-6 text-indigo-200">
                            {{ menuGroup.heading }}
                        </div>
                        <ul role="list" class="-mx-2 space-y-1 mt-1 mb-6">
                            <li v-for="menuItem in menuGroup.items" :key="menuItem.name">
                                <Link @click="handleClick" :href="route(menuItem.route)" :class="[isActiveMenuItem(menuItem) ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-700', 'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold']">
                                    <div v-html="menuItem.icon"></div>
                                    {{ menuItem.name }}
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

            <SidebarProfile @click="handleClick" />
        </ul>
    </nav>
</template>

<script>
import {
    Link,
} from '@inertiajs/vue3';

import {
    UserCircleIcon,
} from '@heroicons/vue/24/solid';

import SidebarProfile from "./SidebarProfile.vue";

export default {
    emits: [
        'click',
    ],
    components: {
        Link,
        UserCircleIcon,
        SidebarProfile,
    },
    data() {
        return {
            menu: this.$page.props.menu,
            prefix: this.$page.props.prefix,
        }
    },
    methods: {
        handleClick($event) {
            this.$emit('click', $event);
        },
        isActiveMenuItem(item) {
            let itemUrl = route(item.route, {}, false),
                pageUrl;

            if (this.$page.url.includes('?')) {
                pageUrl = this.$page.url.replace('?', '/?');
            } else {
                pageUrl = `${this.$page.url}/`;
            }

            if (this.isHomeMenuUrl(itemUrl)) {
                return pageUrl === `${itemUrl}/`;
            }

            return pageUrl.startsWith(`${itemUrl}/`);
        },
        isHomeMenuUrl(itemUrl) {
            return itemUrl === `/${this.prefix}`;
        }
    },
    computed: {
        menuGroups() {
            return this.menu.filter(menuGroup => menuGroup.items && menuGroup.items.length);
        }
    }
}
</script>
