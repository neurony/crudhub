<template>
    <p class="block text-gray-800 font-bold pb-5 text-xl border-b border-gray-200">
        Two-factor code
        <br />
        <span class="text-gray-400 text-sm font-normal">
            Type in the two-factor code we've emailed you at {{ emailObfuscated }}
        </span>
        <br />
        <Link :href="route('admin.login.create')" class="underline font-semibold text-indigo-600 hover:text-indigo-500 text-sm">
            Back to login
        </Link>
    </p>

    <form @submit.prevent="validateTwoFactorCode" class="mt-6 space-y-6">
        <div>
            <InputText v-model="form.code" v-model:error="errors.code" name="code" label="Code" />
        </div>
        <div>
            <button type="submit" :disabled="form.processing" :class="{'opacity-50 cursor-not-allowed': form.processing}" class="transition-all relative flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <template v-if="form.processing">
                    <ArrowPathIcon class="my-1 h-4 w-4 stroke-2 animate-spin" />
                </template>
                <template v-else>
                    <LockClosedIcon class="h-4 w-4 absolute left-4 top-3 stroke-2" />
                    Sign in
                    <LockOpenIcon class="h-4 w-4 absolute right-4 top-3 stroke-2" />
                </template>
            </button>
        </div>
    </form>
</template>

<script>
import {
    Link,
    useForm,
} from '@inertiajs/vue3';

import {
    ArrowPathIcon,
    LockClosedIcon,
    LockOpenIcon,
} from "@heroicons/vue/24/outline";

import Auth from "crudhub/Layouts/Auth.vue";
import InputText from "crudhub/Components/InputText.vue";

export default {
    props: {
        email: String,
        remember: Boolean,
        errors: Object,
    },
    components: {
        ArrowPathIcon,
        InputText,
        Link,
        LockClosedIcon,
        LockOpenIcon,
    },
    layout: Auth,
    data() {
        return {
            form: useForm({
                email: this.email ?? null,
                remember: this.remember ?? false,
                code: null,
            })
        }
    },
    methods: {
        validateTwoFactorCode() {
            this.form.post(route('admin.two_factor.check'));
        }
    },
    computed: {
        emailObfuscated() {
            return this.email.substring(0, 5) + '******' + this.email.substring(this.email.length - 2);
        }
    }
}
</script>
