<template>
    <Link :href="url" class="transition-all cursor-pointer inline-flex items-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        <PlusCircleIcon class="-ml-0.5 mr-2 h-5 w-5" aria-hidden="true" />
        <slot>Add new</slot>
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import { PlusCircleIcon } from '@heroicons/vue/20/solid';

export default {
    props: {
        url: {
            type: String,
            required: true,
        }
    },
    components: {
        Link,
        PlusCircleIcon,
    }
}
</script>
