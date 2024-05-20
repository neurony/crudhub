<!-- Generated with Crudhub -->

<template>
@if($updatedField)
    <PageHeader title="Edit {{ $titleName }}" :subtitle="`Last updated on ${lastUpdatedDate}`">
@else
    <PageHeader title="Edit {{ $titleName }}">
@endif
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

import ButtonSave from "crudhub/Components/ButtonSave.vue";
import ButtonSaveStay from "crudhub/Components/ButtonSaveStay.vue";
import Form from "./Form.vue";
import PageContent from "crudhub/Components/PageContent.vue";
import PageHeader from "crudhub/Components/PageHeader.vue";

export default {
    props: {
        item: Object,
        errors: Object
    },
    components: {
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
@foreach($formFields as $field => $data)
@if(($data['relation_type'] ?? null) == 'belongsToMany')
                {{ $field }}: _.map(this.item.data?.{{ $data['input_options'] }} ?? [], '{{ $data['relation_primary_key'] ?? 'id' }}'),
@elseif($data['input_type'] == 'multiselect')
                {{ $field }}: this.item.data?.{!! $field !!} ?? [],
@elseif($data['input_type'] == 'media')
                {{ $field }}: this.item.data?.{!! $field !!} ?? [],
@else
@if($data['data_type'] == 'boolean')
                {{ $field }}: this.item.data?.{!! $field !!} ?? 0,
@elseif($data['data_type'] == 'json' || (bool)($data['translatable'] ?? false) === true)
                {{ $field }}: this.item.data?.{!! $field !!} ?? {},
@else
                {{ $field }}: this.item.data?.{!! $field !!} ?? null,
@endif
@endif
@endforeach
            }),
        }
    },
    methods: {
        updateRecord(saveStay = false) {
            this.form.save_stay = saveStay;

            this.form.put(route('{{ $routeNames['update'] }}', this.item.data.id));
        },
    },
    computed: {
@if($updatedField)
        lastUpdatedDate() {
            return moment(this.item.data.{{ $updatedField }}).format('DD MMMM, YYYY [at] HH:mm');
        },
    }
@endif
}
</script>
