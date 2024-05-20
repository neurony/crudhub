<!-- Generated with Crudhub -->

<template>
@if($withTranslatable)
    <LanguageSelector
        :locale="currentLocale"
        @language-selected="switchLocale"
    />
@endif

    <Form columns="5">
        <FormSection title="Primary info" col-span="3">
@foreach($formFields as $field => $data)
@if((bool)($data['translatable'] ?? false) === true)
            <div class="sm:col-span-6">
                <InputText
                    v-for="locale in activeLocales"
                    v-show="locale === currentLocale"
                    v-model="form.{{ $field }}[locale]"
                    v-model:error="errors[`{{ $field }}.${locale}`]"
                    :name="`{{ $field }}_${locale}`"
                    label="{{ $data['label'] }}"
                />
            </div>
@elseif($data['input_type'] == 'text')
            <div class="sm:col-span-6">
                <InputText
                    v-model="form.{{ $field }}"
                    v-model:error="errors.{{ $field }}"
                    name="{{ $field }}"
                    label="{{ $data['label'] }}"
                    :error="errors.{{ $field }} ?? null"
                />
            </div>
@elseif($data['input_type'] == 'textarea')
            <div class="sm:col-span-6">
                <InputTextarea
                    v-model="form.{{ $field }}"
                    v-model:error="errors.{{ $field }}"
                    name="{{ $field }}"
                    label="{{ $data['label'] }}"
                />
            </div>
@elseif($data['input_type'] == 'number')
            <div class="sm:col-span-6">
                <InputNumber
                    v-model="form.{{ $field }}"
                    v-model:error="errors.{{ $field }}"
                    name="{{ $field }}"
                    label="{{ $data['label'] }}"
                />
            </div>
@elseif($data['input_type'] == 'date')
            <div class="sm:col-span-6">
                <InputDate
                    v-model="form.{{ $field }}"
                    v-model:error="errors.{{ $field }}"
                    name="{{ $field }}"
                    label="{{ $data['label'] }}"
                />
            </div>
@elseif($data['input_type'] == 'datetime')
            <div class="sm:col-span-6">
                <InputDatetime
                    v-model="form.{{ $field }}"
                    v-model:error="errors.{{ $field }}"
                    name="{{ $field }}"
                    label="{{ $data['label'] }}"
                />
            </div>
@elseif($data['input_type'] == 'select')
@if($data['data_type'] == 'boolean')
            <div class="sm:col-span-6">
                <InputSelect
                    v-model="form.{{ $field }}"
                    v-model:error="errors.{{ $field }}"
                    :options="{1: 'Yes', 0: 'No'}"
                    name="{{ $field }}"
                    label="{{ $data['label'] }}"
                />
            </div>
@elseif($data['relation_type'] == 'belongsTo')
            <div class="sm:col-span-6">
                <InputSelect
                    v-model="form.{{ $field }}"
                    v-model:error="errors.{{ $field }}"
                    :options="$page.props.options.{{ $data['input_options'] }}"
                    name="{{ $field }}"
                    label="{{ $data['label'] }}"
                    placeholder="Please choose"
                />
            </div>
@endif
@elseif($data['input_type'] == 'multiselect')
            <div class="sm:col-span-6">
                <InputMultiselect
                    v-model="form.{{ $field }}"
                    v-model:error="errors.{{ $field }}"
                    :options="$page.props.options.{{ $data['input_options'] }}"
                    name="{{ $field }}"
                    label="{{ $data['label'] }}"
                    placeholder="Please choose"
                />
            </div>
@endif
@endforeach
        </FormSection>

@if(count($mediaCollections))
        <FormSection title="Media info" col-span="2">
@foreach($formFields as $field => $data)
@if($data['input_type'] == 'media')
            <div class="sm:col-span-6">
                <InputMedia
                    v-model="form.{{ $field }}"
                    label="{{ $data['label'] }}"
                    collection-name="{{ $field }}"
@if($data['image_media'])
                    :accepted-mime-types="['image/*']"
@else
                    :accepted-mime-types="'*'"
@endif
@if($data['single_media'])
                    :multiple-files="false"
@else
                    :multiple-files="true"
@endif
                />
            </div>
@endif
@endforeach
        </FormSection>
@else
        <FormSection title="Secondary info" col-span="2">
            <div class="sm:col-span-6">
                Crudhub generated the base CRUD.<br />
                Now it's time for you to make it pretty and complex.
            </div>
        </FormSection>
@endif
    </Form>
</template>

<script>
@if($withTranslatable)
import SwitchesLocale from "crudhub-lang/Mixins/SwitchesLocale.js";
@endif

import Form from "crudhub/Components/Form.vue";
import FormDivider from "crudhub/Components/FormDivider.vue";
import FormSection from "crudhub/Components/FormSection.vue";
import InputDate from "crudhub/Components/InputDate.vue";
import InputDatetime from "crudhub/Components/InputDatetime.vue";
import InputNumber from "crudhub/Components/InputNumber.vue";
import InputMedia from "crudhub/Components/InputMedia.vue";
import InputMultiselect from "crudhub/Components/InputMultiselect.vue";
import InputSelect from "crudhub/Components/InputSelect.vue";
import InputText from "crudhub/Components/InputText.vue";
import InputTextarea from "crudhub/Components/InputTextarea.vue";
@if($withTranslatable)
import LanguageSelector from "crudhub-lang/Components/LanguageSelector.vue";
@endif

export default {
    props: {
        form: {
            type: Object,
            required: true,
        },
        errors: {
            type: Object,
            required: false,
            default: {},
        }
    },
@if($withTranslatable)
    mixins: [
        SwitchesLocale,
    ],
@endif
    components: {
        Form,
        FormDivider,
        FormSection,
        InputDate,
        InputDatetime,
        InputNumber,
        InputMedia,
        InputMultiselect,
        InputSelect,
        InputText,
        InputTextarea,
@if($withTranslatable)
        LanguageSelector,
@endif
    },
}
</script>
