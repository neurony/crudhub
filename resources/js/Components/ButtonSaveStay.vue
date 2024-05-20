<template>
    <button type="button" :disabled="request.ongoing" :class="[request.ongoing ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer']" class="transition-all inline-flex items-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
        <slot>Save & Stay</slot>
    </button>
</template>

<script>
import {
    request,
} from "../constants.js";

export default {
    data() {
        return {
            request: request,
        }
    },
}
</script>
