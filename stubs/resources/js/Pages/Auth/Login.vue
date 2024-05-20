<template>
    <p class="block text-gray-800 font-bold pb-5 text-xl border-b border-gray-200">
        Sign into your account
        <br />
        <span class="text-gray-400 text-sm font-normal">
            Enter your credentials below
        </span>
    </p>

    <form @submit.prevent="loginUser" class="mt-6 space-y-6">
        <div>
            <InputText v-model="form.email" v-model:error="errors.email" name="email" label="Email" />
        </div>
        <div>
            <InputPassword v-model="form.password" v-model:error="errors.password" name="password" label="Password" />
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input v-model="form.remember" id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                <label for="remember" class="ml-3 block text-sm leading-6 text-gray-900">Remember me</label>
            </div>
            <div class="text-sm leading-6">
                <Link :href="route('admin.password_forgot.create')" class="font-semibold text-indigo-600 hover:text-indigo-500 text-sm underline">
                    Forgot password?
                </Link>
            </div>
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
import InputPassword from "crudhub/Components/InputPassword.vue";
import InputText from "crudhub/Components/InputText.vue";

export default {
    props: {
        errors: Object,
    },
    components: {
        ArrowPathIcon,
        LockClosedIcon,
        LockOpenIcon,
        InputPassword,
        InputText,
        Link,
    },
    layout: Auth,
    data() {
        return {
            form: useForm({
                email: null,
                password: null,
                remember: false,
            })
        }
    },
    methods: {
        loginUser() {
            this.form.post(route('admin.login.store'));
        }
    }
}
</script>
