<template>
    <button type="button" :disabled="request.ongoing" :class="[request.ongoing ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer']" class="transition-all inline-flex items-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-indigo-600 hover:bg-indigo-500 hover:ring-indigo-500">
        <ArrowPathIcon v-if="request.ongoing" class="-ml-0.5 mr-2.5 h-5 w-5 animate animate-spin" aria-hidden="true" />
        <ArrowDownTrayIcon v-else class="-ml-0.5 mr-2.5 h-5 w-5" aria-hidden="true" />
        <slot>Save</slot>
    </button>
</template>

<script>
import {
    request,
} from "../constants.js";

import {
    ArrowDownTrayIcon,
    ArrowPathIcon,
} from '@heroicons/vue/20/solid';

export default {
    components: {
        ArrowDownTrayIcon,
        ArrowPathIcon,
    },
    data() {
        return {
            request: request,
        }
    },
}
</script>
