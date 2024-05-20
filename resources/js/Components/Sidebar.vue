<template>
    <TransitionRoot as="template" :show="sidebar.open">
        <Dialog as="div" class="relative z-50 lg:hidden" @close="closeMobileSidebar">
            <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-900/80" />
            </TransitionChild>

            <div class="fixed inset-0 flex">
                <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
                    <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
                        <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                            <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                                <button type="button" class="-m-2.5 p-2.5" @click="closeMobileSidebar">
                                    <span class="sr-only">Close sidebar</span>
                                    <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                                </button>
                            </div>
                        </TransitionChild>

                        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6">
                            <Logo />
                            <SidebarNavigation @click="closeMobileSidebar" />
                        </div>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>

    <div class="lg:hidden fixed z-50 top-1 left-2">
        <button type="button" class="-m-2.5 p-2.5 ml-2 mt-2 text-indigo-500" @click="sidebar.open = true">
            <span class="sr-only">Open sidebar</span>
            <Bars3Icon class="h-8 w-8 stroke-2" aria-hidden="true"/>
        </button>
    </div>

    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6 pt-2">
            <Logo />
            <SidebarNavigation />
        </div>
    </div>
</template>

<script>
import {
    Dialog,
    DialogPanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';

import {
    XMarkIcon,
    Bars3Icon,
} from '@heroicons/vue/24/outline';

import SidebarNavigation from "./SidebarNavigation.vue";
import Logo from "@/crudhub/Components/Logo.vue";

export default {
    components: {
        Logo,
        SidebarNavigation,
        Dialog,
        DialogPanel,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        TransitionChild,
        TransitionRoot,
        XMarkIcon,
        Bars3Icon,
    },
    data() {
        return {
            sidebar: {
                open: false,
            },
        }
    },
    methods: {
        closeMobileSidebar() {
            this.sidebar.open = false;
        },
    },
}
</script>
