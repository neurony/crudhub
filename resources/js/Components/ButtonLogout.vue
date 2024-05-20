<template>
    <Link as="button" :href="route('admin.login.destroy')" method="post" :disabled="request.ongoing" :class="[request.ongoing ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer']" class="transition-all inline-flex items-center rounded-md bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 ring-1 ring-inset ring-red-600 hover:ring-red-500">
        <ArrowPathIcon v-if="request.ongoing" class="-ml-0.5 mr-2.5 h-5 w-5 animate animate-spin" aria-hidden="true" />
        <ArrowLeftOnRectangleIcon v-else class="-ml-0.5 mr-2.5 h-5 w-5" aria-hidden="true" />
        <slot>Logout</slot>
    </Link>
</template>

<script>
import {
    Link,
} from '@inertiajs/vue3';

import {
    request,
} from "../constants.js";

import {
    ArrowLeftOnRectangleIcon,
    ArrowPathIcon,
} from '@heroicons/vue/20/solid';

export default {
    components: {
        Link,
        ArrowLeftOnRectangleIcon,
        ArrowPathIcon,
    },
    data() {
        return {
            request: request,
        }
    },
}
</script>
