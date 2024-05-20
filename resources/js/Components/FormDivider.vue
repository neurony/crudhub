<template>
    <div v-if="title" class="col-span-full flex items-center bg-transparent pt-5 font-medium">
        <h3 :class="[`text-${typeColor}-700`, `bg-${typeColor}-100`]" class="inline-block rounded-md leading-6 font-semibold text-xs py-1.5 px-2.5">
            {{ title }}
        </h3>
        <div class="flex-grow ml-3 border-t border-gray-200"></div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            required: false,
            default: 'default',
        },
    },
    computed: {
        typeColor() {
            switch (this.type) {
                case 'muted':
                    return 'gray';
                default:
                    return 'indigo';
            }
        },
    }
}
</script>
