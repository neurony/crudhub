<!-- Generated with Crudhub -->

<template>
@if($mainField)
    <PageHeader title="Create {{ $titleName }}" :subtitle="newRecord">
@else
    <PageHeader title="Create {{ $titleName }}">
@endif
        <ButtonSaveContinue @click.prevent="storeRecord(true)" />
        <ButtonSave @click.prevent="storeRecord()" />
    </PageHeader>

    <PageContent>
        <Form :form="form" :errors="errors" />
    </PageContent>
</template>

<script>
import {
    useForm,
} from '@inertiajs/vue3';

@if((bool)($mainField['translatable'] ?? false) === true)
import SwitchesLocale from "crudhub-lang/Mixins/SwitchesLocale.js";

@endif
import ButtonSave from "crudhub/Components/ButtonSave.vue";
import ButtonSaveContinue from "crudhub/Components/ButtonSaveContinue.vue";
import Form from "./Form.vue";
import PageContent from "crudhub/Components/PageContent.vue";
import PageHeader from "crudhub/Components/PageHeader.vue";

export default {
    props: {
        item: Object,
        errors: Object
    },
@if((bool)($mainField['translatable'] ?? false) === true)
    mixins: [
        SwitchesLocale,
    ],
@endif
    components: {
        ButtonSave,
        ButtonSaveContinue,
        Form,
        PageContent,
        PageHeader,
    },
    data() {
        return {
            form: useForm({
                save_continue: false,
@foreach($formFields as $field => $data)
@if($data['input_type'] == 'number')
                {{ $field }}: 0,
@elseif($data['input_type'] == 'multiselect')
                {{ $field }}: [],
@elseif($data['input_type'] == 'media')
                {{ $field }}: [],
@else
@if($data['data_type'] == 'boolean')
                {{ $field }}: 0,
@elseif($data['data_type'] == 'json' || (bool)($data['translatable'] ?? false) === true)
                {{ $field }}: {},
@else
                {{ $field }}: null,
@endif
@endif
@endforeach
            }),
        }
    },
    methods: {
        storeRecord(saveContinue) {
            this.form.save_continue = saveContinue;

            this.form.post(route('{{ $routeNames['store'] }}'));
        },
    },
    computed: {
@if(!empty($mainField['name']))
        newRecord() {
@if((bool)($mainField['translatable'] ?? false) === true)
            return this.form.{{ $mainField['name'] }}?.[this.currentLocale] ?? 'Undefined';
@else
            return this.form.{{ $mainField['name'] }} ?? 'Undefined';
@endif
        },
@endif
    }
}
</script>
