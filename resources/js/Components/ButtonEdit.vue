<template>
    <Link :href="url" class="transition-all inline-flex cursor-pointer items-center justify-center rounded-md font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-300 focus-visible:ring-offset-2 border border-transparent text-gray-500 bg-transparent hover:bg-gray-50 active:bg-gray-100 px-4 py-2 text-sm !px-2 !py-2">
        <PencilSquareIcon class="h-5 w-5" aria-hidden="true" />
        <slot></slot>
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import { PencilSquareIcon } from '@heroicons/vue/24/outline';

export default {
    props: {
        url: {
            type: String,
            required: true,
        }
    },
    components: {
        Link,
        PencilSquareIcon,
    }
}
</script>
