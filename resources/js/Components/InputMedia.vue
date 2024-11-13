<template>
    <label v-if="label" :for="`input-image-${collectionName}`" class="block text-sm font-medium leading-6 text-gray-900">
        {{ label }}
    </label>

    <label
        @drop.prevent="dropFiles"
        @dragover.prevent="dragging = true"
        @dragenter.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        :for="`input-image-${collectionName}`"
        class="mt-2 relative block shrink-0 cursor-pointer rounded-b-md border border-dashed border-1 shadow-sm p-5 rounded-t-md hover:border-indigo-600 border-gray-400 transition-all"
    >
        <input
            @change="selectFiles"
            :name="collectionName"
            :id="`input-image-${collectionName}`"
            :ref="`input_image_${collectionName}`"
            :accept="acceptAttribute"
            :multiple="multipleFiles"
            class="sr-only absolute"
            type="file"
        />
        <div class="flex items-center gap-3">
            <template v-if="loading">
                <div class="py-3 mx-auto">
                    <ArrowPathIcon class="animate-spin h-6 w-6 text-indigo-600" />
                </div>
            </template>
            <template v-else>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="h-12 w-12 stroke-1 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                </svg>

                <div class="space-y-1">
                    <div class="flex text-sm text-gray-600">
                        <span>Select or drag and drop file</span>
                    </div>
                    <p class="text-xs text-gray-500">
                        <span v-text="acceptText"></span>
                    </p>
                </div>
            </template>
        </div>
    </label>

    <Draggable
        v-model="media"
        @change="updateMediaOrder"
        @start="dragging = true"
        @end="dragging = false"
        :item-key="`draggable-media-item-${collectionName}`"
        :group="`group-media-items-${collectionName}`"
        direction="vertical"
        handle=".draggable-media-item"
    >
        <template #item="{element, index}">
            <div :key="`media-item-${collectionName}-${index}`" class="bg-white mt-5 rounded-md shadow-sm sm:col-span-2 relative">
                <div v-if="element.action !== 'destroy'" class="rounded-md border border-gray-300">
                    <button v-if="multipleFiles" type="button" class="draggable-media-item cursor-move rotate-45 text-gray-400 hover:text-indigo-600 absolute p-1 -left-4 -top-4 rounded-full bg-white">
                        <ArrowsPointingOutIcon class="h-6 w-6" />
                    </button>

                    <button @click.prevent="removeFile(index)" type="button" class="text-red-500 hover:text-red-700 absolute -right-3 -top-3 rounded-full bg-white">
                        <XCircleIcon class="h-8 w-8" />
                    </button>

                    <div class="grid grid-cols-[auto,1fr] gap-4 p-5 divide-x">
                        <a v-if="isImage(element)" :href="element.original_url" target="_blank" class="col-span-1 group relative w-[125px]">
                            <img
                                :src="isSvg(element) ? element.original_url : element.preview_url"
                                class="w-full aspect-square rounded object-cover"
                            />

                            <div class="absolute inset-0 flex cursor-pointer items-center justify-center rounded bg-gray-900/60 opacity-0 transition-opacity group-hover:opacity-100">
                                <ArrowPathRoundedSquareIcon class="h-8 w-8 text-white" />
                            </div>
                        </a>
                        <a v-else-if="isVideo(element)" :href="element.original_url" target="_blank" class="col-span-1 group relative w-[125px]">
                            <VideoCameraIcon class="w-full aspect-square rounded object-cover text-gray-400 bg-gray-100 p-10" />
                            <div class="absolute inset-0 flex cursor-pointer items-center justify-center rounded bg-gray-900/60 opacity-0 transition-opacity group-hover:opacity-100"></div>
                        </a>
                        <a v-else-if="isAudio(element)" :href="element.original_url" target="_blank" class="col-span-1 group relative w-[125px]">
                            <SpeakerWaveIcon class="w-full aspect-square rounded object-cover text-gray-400 bg-gray-100 p-10" />
                            <div class="absolute inset-0 flex cursor-pointer items-center justify-center rounded bg-gray-900/60 opacity-0 transition-opacity group-hover:opacity-100"></div>
                        </a>
                        <a v-else :href="element.original_url" target="_blank" class="col-span-1 group relative w-[125px]">
                            <DocumentIcon class="w-full aspect-square rounded object-cover text-gray-400 bg-gray-100 p-10" />
                            <div class="absolute inset-0 flex cursor-pointer items-center justify-center rounded bg-gray-900/60 opacity-0 transition-opacity group-hover:opacity-100"></div>
                        </a>

                        <div class="col-span-1 pl-4 text-sm text-gray-500 overflow-hidden">
                            <p class="whitespace-nowrap truncate max-w-full mt-0">
                                <span class="font-bold">File location:</span>
                                <a v-if="element.original_url" :href="element.original_url" target="_blank">
                                    <ArrowTopRightOnSquareIcon class="inline-block text-indigo-600 stroke-2 h4 w-4 ml-2 -mt-1" />
                                </a>
                                <span v-else>N/A</span>
                            </p>
                            <p class="whitespace-nowrap truncate max-w-full mt-1.5">
                                <span class="font-bold">File name:</span> {{ element.file_name ?? 'N/A' }}
                            </p>
                            <p class="whitespace-nowrap truncate max-w-full mt-1.5">
                                <span class="font-bold">File size:</span> {{ element.file_size ?? 'N/A' }}
                            </p>
                            <p class="whitespace-nowrap truncate max-w-full mt-1.5">
                                <span class="font-bold">Mime type:</span> {{ element.mime_type ?? 'N/A' }}
                            </p>
                            <p class="whitespace-nowrap truncate max-w-full mt-1.5">
                                <span class="font-bold">Collection name:</span> {{ element.collection_name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div v-if="isImage(element)" class="px-4 sm:divide-y sm:divide-gray-900/10 border-t ">
                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-0 sm:py-4">
                            <label :for="`${collectionName}-filename-${index}`" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                                File title
                            </label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input
                                    @input="updateMediaTitle(index, $event)"
                                    :value="media[index].custom_properties.title"
                                    :id="`${collectionName}-title-${index}`"
                                    type="text"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                />
                            </div>
                        </div>
                        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-0 sm:py-4">
                            <label :for="`${collectionName}-alt-${index}`" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                                Alt tag
                            </label>
                            <div class="mt-2 sm:col-span-2 sm:mt-0">
                                <input
                                    @input="updateMediaAlt(index, $event)"
                                    :value="media[index].custom_properties.alt"
                                    :id="`${collectionName}-alt-${index}`"
                                    type="text"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Draggable>
</template>

<script>
import axios from 'axios';
import Draggable from 'vuedraggable';

import {
    PhotoIcon,
    ArrowPathRoundedSquareIcon,
    XCircleIcon,
    ArrowPathIcon,
    ArrowTopRightOnSquareIcon,
    VideoCameraIcon,
    SpeakerWaveIcon,
    DocumentIcon,
    ArrowsPointingOutIcon,
} from '@heroicons/vue/24/outline';

export default {
    props: {
        modelValue: {
            type: [Array],
            required: false,
            default: null,
        },
        label: {
            type: String,
            required: false,
        },
        collectionName: {
            type: String,
            required: true,
        },
        multipleFiles: {
            type: Boolean,
            required: false,
            default: false,
        },
        uploadRoute: {
            type: String,
            required: false,
            default: () => {
                return 'admin.uploads.store';
            },
        },
        removeRoute: {
            type: String,
            required: false,
            default: () => {
                return 'admin.uploads.destroy';
            },
        },
        acceptedMimeTypes: {
            type: [String, Array],
            required: true,
            default: '*',
        },
    },
    components: {
        Draggable,
        PhotoIcon,
        ArrowPathRoundedSquareIcon,
        XCircleIcon,
        ArrowPathIcon,
        ArrowTopRightOnSquareIcon,
        VideoCameraIcon,
        SpeakerWaveIcon,
        DocumentIcon,
        ArrowsPointingOutIcon,
    },
    mounted() {
        this.setMedia();
    },
    data() {
        return {
            media: [],
            loading: false,
            dragging: false,
        }
    },
    methods: {
        setMedia() {
            if (!this.modelValue.length) {
                return;
            }

            if (this.multipleFiles) {
                _.forEach(this.modelValue, (value) => {
                    this.media.push(this.mediaMapping(value, 'update'));
                });
            } else {
                this.media.push(this.mediaMapping(this.modelValue[0], 'update'));
            }
        },
        async selectFiles(e) {
            await this.uploadFiles(this.multipleFiles ? e.target.files : [e.target.files[0]]);
        },
        async dropFiles(e) {
            await this.uploadFiles(this.multipleFiles ? e.dataTransfer.files : [e.dataTransfer.files[0]]);
        },
        async uploadFiles(files) {
            if (!files.length) {
                return;
            }

            try {
                this.loading = true;

                if (!this.multipleFiles && this.media.length) {
                    this.media = this.media.map(item => {
                        item.action = 'destroy';

                        return item;
                    });
                }

                for (let i = 0; i < files.length; i++) {
                    if (!this.multipleFiles && i > 0) {
                        break;
                    }

                    let file = files[i],
                        form = new FormData();

                    form.append('file', file);

                    const result = await axios.post(route(this.uploadRoute), form, {
                        headers: {'content-type': 'multipart/form-data'}
                    });

                    this.media.push(this.mediaMapping(result.data.data, 'create'));
                }

                this.$emit('update:modelValue', this.media);
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        async removeFile(index) {
            if (!this.hasMedia || !this.media[index]) {
                return;
            }

            this.media[index]['action'] = 'destroy';

            this.$emit('update:modelValue', this.media);
        },
        isSvg(file) {
            return file.mime_type?.startsWith('image/svg+xml') ?? false;
        },
        isImage(file) {
            return file.mime_type?.startsWith('image/') ?? false;
        },
        isVideo(file) {
            return file.mime_type?.startsWith('video/') ?? false;
        },
        isAudio(file) {
            return file.mime_type?.startsWith('audio/') ?? false;
        },
        mediaMapping(media, action) {
            return {
                action: action,
                id: media.id,
                model_id: media.model_id,
                model_type: media.model_type,
                path: media.path,
                name: media.name,
                file_name: media.file_name,
                file_size: media.human_readable_size,
                mime_type: media.mime_type,
                original_url: media.original_url,
                preview_url: media.preview_url,
                collection_name: this.collectionName,
                old_collection_name: media.collection_name,
                custom_properties: media.custom_properties ?? {},
                order_column: media.order_column ?? null,
            }
        },
        updateMediaOrder(e) {
            this.media.forEach((item, index) => {
                item.order_column = index + 1;
            });

            this.$emit('update:modelValue', this.media);
        },
        updateMediaTitle(index, $event) {
            if (Array.isArray(this.media[index].custom_properties)) {
                this.media[index].custom_properties = {};
            }

            this.media[index].custom_properties['title'] = $event.target.value;

            this.$emit('update:modelValue', this.media);
        },
        updateMediaAlt(index, $event) {
            if (Array.isArray(this.media[index].custom_properties)) {
                this.media[index].custom_properties = {};
            }

            this.media[index].custom_properties['alt'] = $event.target.value;

            this.$emit('update:modelValue', this.media);
        },
    },
    computed: {
        hasMedia() {
            return this.media.length;
        },
        acceptAttribute() {
            if (this.acceptedMimeTypes === '*' || this.acceptedMimeTypes === '*/*') {
                return '*/*';
            }

            if (_.isString(this.acceptedMimeTypes)) {
                return this.acceptedMimeTypes;
            }

            if (_.isArray(this.acceptedMimeTypes)) {
                return _.join(this.acceptedMimeTypes, ',');
            }
        },
        acceptText() {
            if (this.acceptedMimeTypes === '*' || this.acceptedMimeTypes === '*/*') {
                return 'Any file is accepted';
            }

            let acceptText = '';

            if (_.isString(this.acceptedMimeTypes)) {
                if (this.acceptedMimeTypes.endsWith('/*')) {
                    acceptText = this.acceptedMimeTypes.split('/*')[0];
                } else if (this.acceptedMimeTypes.indexOf('/') > -1) {
                    acceptText = this.acceptedMimeTypes.split('/')[1];
                } else {
                    acceptText = this.acceptedMimeTypes;
                }
            }

            if (_.isArray(this.acceptedMimeTypes)) {
                acceptText = _.map(this.acceptedMimeTypes, (mimeType) => {
                    if (mimeType.endsWith('/*')) {
                        return mimeType.split("/*")[0];
                    }

                    if (mimeType.indexOf('/') > -1) {
                        return mimeType.split('/')[1];
                    }

                    return mimeType;
                }).join(', ');
            }

            return `Accepting: ${acceptText}`;
        }
    }
}
</script>
