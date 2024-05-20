<template>
    <div class="h-11 w-11 flex-shrink-0">
        <img v-if="src.length" :src="src" :alt="alt" class="h-11 w-11 rounded-full" />
        <span v-else v-text="initials" class="ring-1 ring-inset ring-gray-200 flex items-center justify-center font-semibold text-gray-500 rounded-full h-11 w-11 bg-gray-50" />
    </div>
</template>

<script>
export default {
    props: {
        src: {
            type: String,
            required: true,
            default: '',
        },
        alt: {
            type: String,
            required: false,
            default: '',
        },
    },
    computed: {
        initials() {
            const words = this.alt.split(" ").filter(word => word.trim() !== '');
            let initials = '';

            for (let index = 0; index < words.length && index < 2; index++) {
                initials += words[index][0].toUpperCase();
            }

            return initials;
        }
    }
}
</script>
