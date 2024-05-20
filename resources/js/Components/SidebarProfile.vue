<template>
    <li class="-mx-6 mt-auto">
        <Link @click="handleClick" :href="route('admin.admins.edit', user.id)" class="flex items-center gap-x-3 px-6 py-3 text-sm text-indigo-200 font-semibold leading-6 bg-indigo-600 hover:bg-indigo-700">
            <img v-if="avatar" :src="avatar" :alt="user.name" class="h-8 w-8 rounded-full" />
            <UserCircleIcon v-else class="h-8 w-8 rounded-full" />
            <span class="sr-only">Your profile</span>
            <span aria-hidden="true">{{ user.name }}</span>
        </Link>
    </li>
</template>

<script>
import {
    Link,
} from '@inertiajs/vue3';

import {
    UserCircleIcon,
} from '@heroicons/vue/24/solid';

export default {
    emits: [
        'click',
    ],
    components: {
        Link,
        UserCircleIcon,
    },
    data() {
        return {
            user: this.$page.props.auth.data ?? null,
        }
    },
    methods: {
        handleClick($event) {
            this.$emit('click', $event);
        }
    },
    computed: {
        avatar() {
            if (this.user && this.user.avatars.length) {
                return this.user.avatars[0].preview_url ?? null;
            }

            return null;
        },
    }
}
</script>
