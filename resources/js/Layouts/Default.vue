<template>
    <div>
        <Sidebar />

        <div class="lg:pl-72">
            <main>
                <slot />
            </main>
        </div>

        <FlashMessage />
    </div>
</template>

<script>
import Sidebar from "../Components/Sidebar.vue";
import FlashMessage from "../Components/FlashMessage.vue";

export default {
    components: {
        FlashMessage,
        Sidebar,
    },
}
</script>
