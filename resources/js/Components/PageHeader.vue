<template>
    <div class="sticky top-0 z-40 flex h-20 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 pl-20 shadow-sm sm:gap-x-6 lg:px-6 xs:pl-20 lg:px-8">
        <div class="sm:flex-auto">
            <h1 v-if="title" class="text-xl font-semibold text-gray-900">
                {{ title }}
            </h1>
            <p v-if="subtitle" class="mt-0.5 text-sm text-gray-500">
                {{ subtitle }}
            </p>
        </div>
        <div class="mt-4 ml-auto sm:mt-0 sm:flex-none flex gap-4 lg:gap-5">
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
        },
        subtitle: {
            type: String,
            required: false,
        },
    }
}
</script>
