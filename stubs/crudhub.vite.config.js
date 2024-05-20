import {defineConfig, splitVendorChunkPlugin} from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import {resolve} from "path";
import tailwindcss from "tailwindcss";

export default defineConfig({
    plugins: [
        splitVendorChunkPlugin(),
        laravel({
            input: [
                "resources/js/crudhub/app.js",
                "resources/css/crudhub/app.scss",
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss({
                    config: "./crudhub.tailwind.config.js",
                }),
            ],
        },
    },
    resolve: {
        alias: {
            ziggy: resolve(__dirname, "./vendor/tightenco/ziggy"),
            "@": resolve(__dirname, "./resources/js"),
            "@heroicons/vue": resolve(__dirname, "./node_modules/@heroicons/vue"),
            "@headlessui/vue": resolve(__dirname, "./node_modules/@headlessui/vue"),
            "@inertiajs/vue3": resolve(__dirname, "./node_modules/@inertiajs/vue3"),
            "@vueform/multiselect": resolve(__dirname, "./node_modules/@vueform/multiselect"),
            "axios": resolve(__dirname, "./node_modules/axios"),
            "flatpickr": resolve(__dirname, "./node_modules/flatpickr"),
            "vuedraggable": resolve(__dirname, "./node_modules/vuedraggable"),
            "sweetalert2": resolve(__dirname, "./node_modules/sweetalert2"),
            "crudhub": resolve(__dirname, "./vendor/neurony/crudhub/resources/js"),
        },
    },
});
