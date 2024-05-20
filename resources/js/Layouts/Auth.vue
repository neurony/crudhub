<template>
    <div class="flex min-h-screen flex-1 flex-col justify-center sm:px-6 lg:px-8">
        <div class="sm:mx-auto bg-indigo-600 px-4 rounded-lg mb-10">
            <Logo />
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12 ring-1 ring-inset ring-opacity-50 ring-gray-200">
                <slot></slot>
            </div>
        </div>

        <FlashMessage />
    </div>
</template>

<script>
import FlashMessage from "../Components/FlashMessage.vue";
import Logo from "@/crudhub/Components/Logo.vue";

export default {
    components: {
        FlashMessage,
        Logo,
    },
}
</script>
