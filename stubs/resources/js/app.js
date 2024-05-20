import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from "ziggy/dist/vue.es.js";

import _ from 'lodash';

import Default from "crudhub/Layouts/Default.vue";
import OngoingRequest from "crudhub/Mixins/OngoingRequest.js";

window._ = _;

createInertiaApp({
    progress: {
        delay: 0,
        color: '#4F45E4',
        includeCSS: true,
        showSpinner: false,
    },
    resolve: async (name) => {
        let page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        if (page.default.layout === undefined) {
            page.default.layout ??= Default;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mixin(OngoingRequest)
            .mount(el)
    },
});

