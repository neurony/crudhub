<template>
    <div>
        <label v-if="label" class="block text-sm font-medium leading-6 text-gray-900">
            {{ label }}
        </label>

        <div class="relative mt-2 rounded-md shadow-sm">
            <Multiselect
                v-model="selectValue"
                mode="single"
                :name="name"
                :options="options"
                :searchable="true"
                :label="trackBy"
                :value-prop="valueProp"
                :track-by="trackBy"
                :disabled="disabled"
                :placeholder="placeholder"
                :classes="selectClasses"
                :close-on-select="true"
                :hide-selected="false"
                :native-support="nativeSupport"
            >

                <template #caret>
                    <span :class="{'bg-white' : !disabled}" class="absolute inset-y-1 right-0 flex items-center rounded-r-md">
                        <ChevronUpDownIcon class="text-gray-600 h-5 w-5" />
                    </span>
                </template>

                <template #clear="{ clear }">
                    <button @click="clear" type="button" class="z-10 absolute inset-y-1 right-5 flex items-center bg-white pr-0.5 focus:outline-none">
                        <XMarkIcon class="text-gray-400 h-4 w-4" />
                    </button>
                </template>

                <template #singlelabel="{ value }">
                    <div class="multiselect-single-label text-gray-900 text-sm">
                        {{ trackBy && value[trackBy] ? value[trackBy] : value }}
                    </div>
                </template>

            </Multiselect>

            <div v-if="errorMessage" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
            </div>
        </div>

        <p v-if="errorMessage" class="mt-2 text-sm text-red-600" :id="`${name}-error`">
            {{ errorMessage }}
        </p>
    </div>
</template>

<script>
import Multiselect from '@vueform/multiselect';

import {
    ChevronUpDownIcon,
    XMarkIcon,
    ExclamationCircleIcon,
} from '@heroicons/vue/24/solid';

export default {
    emits: [
        'update:modelValue',
        'update:error',
    ],
    props: {
        modelValue: {
            type: [String, Number, Boolean],
            required: false,
            default: null,
        },
        name: {
            type: String,
            required: true,
        },
        options: {
            type: [Array, Object],
            required: true,
        },
        error: {
            type: String,
            required: false,
            default: null,
        },
        label: {
            type: String,
            required: false,
        },
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
        placeholder: {
            type: String,
            required: false,
        },
        valueProp: {
            type: String,
            required: false,
            default: 'value'
        },
        trackBy: {
            type: String,
            required: false,
            default: 'label'
        },
        nativeSupport: {
            type: Boolean,
            required: false,
            default: false,
        }
    },
    components: {
        Multiselect,
        ChevronUpDownIcon,
        XMarkIcon,
        ExclamationCircleIcon,
    },
    data() {
        return {
            errorMessage: this.error,
        }
    },
    computed: {
        selectValue: {
            get() {
                return typeof this.modelValue === 'boolean' ? +this.modelValue : this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        selectClasses() {
            return {
                container: `text-gray-900 mx-auto w-full flex items-center justify-end rounded-md ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6 min-h-[36px] ${
                    this.errorMessage ? 'ring-red-300 focus:ring-red-500 pr-9' : 'ring-gray-300 focus:ring-indigo-600 pr-3'
                }`,
                containerDisabled: "cursor-not-allowed bg-gray-100",
                containerOpen: "ring-2 ring-inset ring-indigo-600",
                containerOpenTop: "",
                containerActive: "ring-2 ring-inset ring-indigo-600",
                singleLabel: "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent pl-3 pr-10 max-w-full text-ellipsis overflow-hidden whitespace-nowrap text-sm h-[36px] leading-[36px]",
                multipleLabel: "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent pl-3 text-sm h-[36px] leading-[36px]",
                search: "my-0.5 ml-[2px] w-full absolute inset-0 outline-none appearance-none box-border border-0 font-sans bg-white pl-3 block pr-10 focus:outline-none ring-0 sm:text-sm rounded-md focus:ring-transparent focus:border-0 focus:border-transparent",
                tags: "flex-grow flex-shrink flex-wrap flex items-center gap-1 py-1 pl-2 pr-9",
                tag: "bg-indigo-500 text-white text-sm font-semibold py-0.5 pl-2 rounded mr-1 mb-1 flex items-center whitespace-nowrap",
                tagDisabled: "pr-2 opacity-50",
                tagRemove: "flex items-center justify-center p-1 mx-0.5 rounded-sm hover:bg-black hover:bg-opacity-10 group",
                tagRemoveIcon: "bg-center bg-no-repeat opacity-30 inline-block w-3 h-3 group-hover:opacity-60",
                tagsSearchWrapper: "inline-block relative mx-1 flex-grow flex-shrink h-full",
                tagsSearch: "absolute inset-0 border-0 outline-none appearance-none p-0 text-base font-sans box-border w-full !ring-0",
                tagsSearchCopy: "invisible whitespace-pre-wrap inline-block h-px",
                placeholder: "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3 text-gray-600 text-sm",
                caret: "bg-center bg-no-repeat w-2.5 h-4 py-px box-content mr-3.5 relative z-10 opacity-40 flex-shrink-0 flex-grow-0 transition-transform transform pointer-events-auto",
                caretOpen: "pointer-events-auto",
                clear: "pr-3.5 relative z-10 opacity-40 transition duration-300 flex-shrink-0 flex-grow-0 flex hover:opacity-80",
                clearIcon: "bg-center bg-no-repeat w-2.5 h-4 py-px box-content inline-block",
                spinner: "bg-multiselect-spinner bg-center bg-no-repeat w-4 h-4 z-10 mr-3.5 animate-spin flex-shrink-0 flex-grow-0",
                dropdown: "max-h-60 absolute -left-px -right-px -bottom-2 transform translate-y-full -mt-px overflow-y-scroll z-40 bg-white flex flex-col rounded-md shadow-lg z-10 bg-white max-h-60 py-1 text-base border border-gray-300 overflow-auto focus:outline-none sm:text-sm",
                dropdownTop: "-translate-y-full top-px bottom-auto flex-col-reverse rounded-b-none rounded-t",
                dropdownHidden: "hidden",
                options: "flex flex-col p-0 m-0 list-none text-gray-700",
                optionsTop: "flex-col-reverse",
                group: "p-0 m-0",
                groupLabel: "cursor-default flex text-sm box-border items-center justify-start text-left py-1 px-3 font-semibold bg-gray-200 leading-normal",
                groupLabelPointable: "cursor-default",
                groupLabelPointed: "bg-gray-300 text-gray-700",
                groupLabelSelected: "bg-indigo-500 text-white",
                groupLabelDisabled: "bg-gray-100 text-gray-300 cursor-not-allowed",
                groupLabelSelectedPointed: "bg-indigo-500 text-white opacity-90",
                groupLabelSelectedDisabled: "text-indigo-100 bg-indigo-500 bg-opacity-50 cursor-not-allowed",
                groupOptions: "p-0 m-0",
                option: "cursor-pointer flex items-center justify-start box-border text-left block px-4 py-2 text-sm",
                optionPointed: "text-gray-900 bg-gray-100",
                optionSelected: "text-white bg-indigo-500",
                optionDisabled: "!text-gray-300 cursor-not-allowed",
                optionSelectedPointed: "text-white bg-indigo-500 opacity-90",
                optionSelectedDisabled: "text-white bg-indigo-500 bg-opacity-50 cursor-not-allowed",
                noOptions: "py-2 px-3 text-gray-600 bg-white",
                noResults: "py-2 px-3 text-gray-600 bg-white",
                fakeInput: "bg-transparent absolute left-0 right-0 -bottom-px w-full border-0 p-0 appearance-none outline-none text-transparent -z-50",
                spacer: "h-8 py-0.5 box-content",
            }
        },
    },
    watch: {
        modelValue(newVal) {
            this.$emit('update:modelValue', newVal);
            this.$emit('update:error', null);
        },
        error: function(newVal, oldVal) {
            this.errorMessage = newVal;
        }
    }
}
</script>
