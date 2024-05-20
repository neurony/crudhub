<!-- Generated with Crudhub -->

<template>
    <Filter @apply="applyFilters" @clear="clearFilters">
@foreach($filteringFields as $field => $data)
@if($data['user_provided'])
@if($data['type'] == 'text' || $data['type'] == 'number')
        <div class="sm:col-span-6">
            <InputText
                v-model="form.{{ $field }}"
                name="{{ $field }}"
                label="{{ $data['label'] }}"
            />
        </div>
@elseif($data['type'] == 'date' || $data['type'] == 'datetime')
        <div class="sm:col-span-6">
            <InputDate
                v-model="form.{{ $field }}"
                name="{{ $field }}"
                label="{{ $data['label'] }}"
            />
        </div>
@elseif($data['type'] == 'boolean')
        <div class="sm:col-span-6">
            <InputSelect
                v-model="form.{{ $field }}"
                name="{{ $field }}"
                label="{{ $data['label'] }}"
                placeholder="Any"
                :options="{1: 'Yes', 0: 'No'}"
            />
        </div>
@elseif($data['type'] == 'select' && !empty($data['select_options']) && !empty($data['select_type']))
@if($data['select_type'] == 'multiple')
        <div class="sm:col-span-6">
            <InputMultiselect
                v-model="form.{{ $field }}"
                name="{{ $field }}"
                label="{{ $data['label'] }}"
                placeholder="All"
                :options="$page.props.options.{{ $data['select_options'] }}"
            />
        </div>
@else
        <div class="sm:col-span-6">
            <InputSelect
                v-model="form.{{ $field }}"
                name="{{ $field }}"
                label="{{ $data['label'] }}"
                placeholder="All"
                :options="$page.props.options.{{ $data['select_options'] }}"
            />
        </div>
@endif
@endif
@endif
@endforeach
@if($withSoftDelete)
        <div class="sm:col-span-6">
            <InputCheckbox
                v-model="form.with_trashed"
                name="with_trashed"
                label="Include trashed records"
            />
        </div>
@endif
    </Filter>
</template>

<script>
import {
    router,
    useForm,
} from '@inertiajs/vue3';

import Filter from "crudhub/Components/Filter.vue";
import InputCheckbox from "crudhub/Components/InputCheckbox.vue";
import InputDate from "crudhub/Components/InputDate.vue";
import InputMultiselect from "crudhub/Components/InputMultiselect.vue";
import InputSelect from "crudhub/Components/InputSelect.vue";
import InputText from "crudhub/Components/InputText.vue";

export default {
    components: {
        Filter,
        InputCheckbox,
        InputDate,
        InputMultiselect,
        InputSelect,
        InputText,
    },
    data() {
        return {
            form: useForm({
                sort_by: this.$page.props.query.sort_by ?? null,
                sort_dir: this.$page.props.query.sort_dir ?? null,
@foreach($filteringFields as $field => $data)
                {{ $field }}: this.$page.props.query.{{ $field }} ?? null,
@endforeach
@if($withSoftDelete)
                with_trashed: parseInt(this.$page.props.query.with_trashed ?? 0),
@endif
            }),
        }
    },
    methods: {
        applyFilters() {
            this.form.keyword = this.$page.props.query.keyword ?? null;
            this.form.sort_by = this.$page.props.query.sort_by ?? null;
            this.form.sort_dir = this.$page.props.query.sort_dir ?? null;

            this.form.get(route('{{ $routeNames['index'] }}'));
        },
        clearFilters() {
            router.get(route('{{ $routeNames['index'] }}'));
        },
    },
}
</script>
