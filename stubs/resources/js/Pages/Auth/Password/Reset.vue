<template>
    <p class="block text-gray-800 font-bold pb-5 text-xl border-b border-gray-200">
        Reset your password
        <br />
        <span class="text-gray-400 text-sm font-normal">
            Type in your new password and automatically login
        </span>
        <br />
        <Link :href="route('admin.login.create')" class="underline font-semibold text-indigo-600 hover:text-indigo-500 text-sm">
            Back to login
        </Link>
    </p>

    <form @submit.prevent="resetPassword" class="mt-6 space-y-6">
        <div>
            <InputText v-model="form.email" v-model:error="errors.email" name="email" label="Email" />
        </div>
        <div>
            <InputPassword v-model="form.password" v-model:error="errors.password" name="password" label="Password" />
        </div>
        <div>
            <InputPassword v-model="form.password_confirmation" v-model:error="errors.password_confirmation" name="password_confirmation" label="Confirm password" />
        </div>
        <div>
            <button type="submit" :disabled="form.processing" :class="{'opacity-50 cursor-not-allowed': form.processing}" class="transition-all relative flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <template v-if="form.processing">
                    <ArrowPathIcon class="my-1 h-4 w-4 stroke-2 animate-spin" />
                </template>
                <template v-else>
                    <KeyIcon class="transform scale-x-[-1] h-4 w-4 absolute left-4 top-3 stroke-2" />
                    Reset password
                    <KeyIcon class="h-4 w-4 absolute right-4 top-3 stroke-2" />
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
    KeyIcon,
} from "@heroicons/vue/24/outline";

import Auth from "crudhub/Layouts/Auth.vue";
import InputPassword from "crudhub/Components/InputPassword.vue";
import InputText from "crudhub/Components/InputText.vue";

export default {
    layout: Auth,
    props: {
        errors: Object,
        email: String,
        token: String,
    },
    components: {
        ArrowPathIcon,
        InputPassword,
        InputText,
        KeyIcon,
        Link,
    },
    data() {
        return {
            form: useForm({
                token: this.token,
                email: this.email,
                password: null,
                password_confirmation: null,
            })
        }
    },
    methods: {
        resetPassword() {
            this.form.post(route('admin.password_reset.store'));
        }
    }
}
</script>
