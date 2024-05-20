<template>
    <PageHeader title="Edit admin" :subtitle="`Last updated on ${lastUpdatedDate}`">
        <ButtonLogout v-if="auth.data.id === item.data.id" />
        <ButtonSaveStay @click="updateRecord(true)" />
        <ButtonSave @click="updateRecord()" />
    </PageHeader>

    <PageContent>
        <Form :form="form" :errors="errors" />
    </PageContent>
</template>

<script>
import moment from "moment";

import {
    useForm,
} from '@inertiajs/vue3';

import ButtonLogout from "crudhub/Components/ButtonLogout.vue";
import ButtonSave from "crudhub/Components/ButtonSave.vue";
import ButtonSaveStay from "crudhub/Components/ButtonSaveStay.vue";
import Form from "./Form.vue";
import PageContent from "crudhub/Components/PageContent.vue";
import PageHeader from "crudhub/Components/PageHeader.vue";

export default {
    props: {
        auth: Object,
        item: Object,
        errors: Object
    },
    components: {
        ButtonLogout,
        ButtonSave,
        ButtonSaveStay,
        Form,
        PageContent,
        PageHeader,
    },
    data() {
        return {
            form: useForm({
                save_stay: false,
                name: this.item.data?.name ?? null,
                email: this.item.data?.email ?? null,
                password: null,
                password_confirmation: null,
                active: this.item.data?.active ?? 0,
                role: this.item.data?.role?.id ?? null,
                avatars: this.item.data?.avatars ?? [],
            }),
        }
    },
    methods: {
        updateRecord(saveStay = false) {
            this.form.save_stay = saveStay;

            this.form.put(route('admin.admins.update', this.item.data.id));
        },
    },
    computed: {
        lastUpdatedDate() {
            return moment(this.item.data.updated_at).format('DD MMMM, YYYY [at] HH:mm');
        },
    }
}
</script>
