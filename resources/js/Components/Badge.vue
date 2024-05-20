<template>
    <span :class="classes" class="cursor-default inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset">
        <slot></slot>
    </span>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            required: false,
            default: 'default',
        },
    },
    computed: {
        classes() {
            switch (this.type) {
                case 'success':
                    return 'bg-green-50 text-green-700 ring-green-600/20';
                case 'danger':
                    return 'bg-red-50 text-red-700 ring-red-600/20';
                case 'info':
                    return 'bg-indigo-50 text-indigo-700 ring-indigo-600/20';
                case 'warning':
                    return 'bg-yellow-50 text-yellow-700 ring-yellow-600/20';
                default:
                    return 'bg-gray-50 text-gray-700 ring-gray-600/20';
            }
        }
    }
}
</script>
