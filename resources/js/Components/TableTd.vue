<template>
    <td
        :class="[stickyStart ? 'sticky left-0 z-20' : '', stickyEnd ? 'sticky right-0 z-20' : '']"
        class="relative z-10 whitespace-nowrap px-5 py-5 text-sm text-gray-900 first-of-type:pl-4 first-of-type:sm:pl-6 bg-white"
    >
        <slot></slot>
    </td>
</template>

<script>

export default {
    props: {
        stickyStart: {
            type: Boolean,
            default: false,
        },
        stickyEnd: {
            type: Boolean,
            default: false,
        },
    },
    components: {

    }
}
</script>
