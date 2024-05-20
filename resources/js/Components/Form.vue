<template>
    <div :class="[columnsClass, request.ongoing ? 'opacity-50 cursor-not-allowed pointer-events-none' : '']" class="w-auto grid items-start gap-9 transition-opacity">
        <slot></slot>
    </div>
</template>

<script>
import {
    request,
} from "../constants.js";

export default {
    props: {
        columns: {
            type: [Number, String],
            required: false,
            default: 1,
        },
    },
    data() {
        return {
            request: request,
        }
    },
    computed: {
        columnsClass() {
            switch (parseInt(this.columns)) {
                case 2:
                    return 'two-column';
                case 3:
                    return 'three-column';
                case 4:
                    return 'four-column';
                case 5:
                    return 'five-column';
                case 6:
                    return 'six-column';
                case 7:
                    return 'seven-column';
                case 8:
                    return 'eight-column';
                case 9:
                    return 'nine-column';
                case 10:
                    return 'ten-column';
                case 11:
                    return 'eleven-column';
                case 12:
                    return 'twelve-column';
                default:
                    return 'one-column';
            }
        }
    }
}
</script>

<style scoped>
@media (min-width: 1280px) {
    .one-column {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
    }

    .two-column {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }

    .three-column {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
    }

    .four-column {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
    }

    .five-column {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
    }

    .six-column {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
    }

    .seven-column {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }

    .eight-column {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
    }

    .nine-column {
        display: grid;
        grid-template-columns: repeat(9, 1fr);
    }

    .ten-column {
        display: grid;
        grid-template-columns: repeat(10, 1fr);
    }

    .eleven-column {
        display: grid;
        grid-template-columns: repeat(11, 1fr);
    }

    .twelve-column {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
    }
}
</style>
