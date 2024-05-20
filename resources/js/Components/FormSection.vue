<template>
    <div :class="[colSpanClass]" class="bg-white rounded-md shadow ring-1 ring-gray-600/5 ring-opacity-5">
        <div v-if="title" class="flex items-center bg-transparent px-4 pt-5 font-medium sm:px-6">
            <h3 class="inline-block rounded-md leading-6 font-semibold text-xs text-indigo-700 py-1.5 px-2.5 bg-indigo-100">
                {{ title }}
            </h3>
            <div class="flex-grow ml-3 border-t border-gray-200"></div>
        </div>
        <div class="grid w-full grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 px-4 py-6 sm:p-8">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: false,
            default: null
        },
        colSpan: {
            type: [Number, String],
            required: false,
            default: 1,
        }
    },
    computed: {
        colSpanClass() {
            switch (parseInt(this.colSpan)) {
                case 2:
                    return 'two-col-span';
                case 3:
                    return 'three-col-span';
                case 4:
                    return 'four-col-span';
                case 5:
                    return 'five-col-span';
                case 6:
                    return 'six-col-span';
                default:
                    return 'one-col-span';
            }
        }
    }
}
</script>
<style scoped>
@media (min-width: 1280px) {
    .one-col-span {
        grid-column: span 1 / span 1;
    }

    .two-col-span {
        grid-column: span 2 / span 2;
    }

    .three-col-span {
        grid-column: span 3 / span 3;
    }

    .four-col-span {
        grid-column: span 4 / span 4;
    }

    .five-col-span {
        grid-column: span 5 / span 5;
    }

    .six-col-span {
        grid-column: span 6 / span 6;
    }
}
</style>

