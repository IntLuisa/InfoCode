<script setup>
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { router } from '@inertiajs/vue3';
import { sidebarState } from '@/stores/sidebar';
import DialogModal from './DialogModal.vue';
import axios from 'axios';
import { ref } from 'vue';

const isDarkMode = ref(false);

if (localStorage.getItem('color-theme') === 'dark' ||
    (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
    isDarkMode.value = true;
} else {
    document.documentElement.classList.remove('dark');
    isDarkMode.value = false;
}

defineEmits(['toggleSidebar']);

const toggleDarkMode = () => {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('color-theme', isDarkMode.value ? 'light' : 'dark');
    isDarkMode.value = !isDarkMode.value;
}

const logout = () => {
    router.post(route('logout'));
};

const showModalNotificacion = ref(false);
const notifications = ref([]);

const loadNotifications = (show = false) => {
    axios.get(route('notifications.index'))
        .then(response => response.data)
        .then(data => {
            notifications.value = data || [];
        })
        .finally(() => {
            showModalNotificacion.value = show;
        })
};

loadNotifications(location.pathname.includes('dashboard'));

const openNotification = (url) => {
    if (url) {
        window.location.href = url;
    }
};

</script>
<template>
    <nav class="fixed z-30 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button @click="sidebarState.toggle()" aria-expanded="true" aria-controls="sidebar"
                        class="p-2 text-gray-600 rounded cursor-pointer sm:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg v-if="sidebarState.isOpen" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <div class="flex ml-2 mr-4 sm:mr-10">
                        <img src="/assets/images/template/logo_1.png" class="h-10 me-2" />
                        <span
                            class="hidden sm:flex peer-focus:block self-center text-xl font-semibold sm:text-2xl text-[#f39302]">
                            Krenke
                        </span>
                    </div>
                    <slot />
                </div>
                <div class="flex items-center">
                    <div class="mr-2 ">
                        <div class="relative">
                            <button class="relative" @click="loadNotifications(true)">
                                <i class="fa-solid fa-bell text-lg text-gray-400"
                                    :class="{ 'animate-bell-shake': notifications.length }"></i>
                                <span v-if="notifications.length"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ notifications.length }}
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="group">
                        <button @click="toggleDarkMode" type="button"
                            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                            <svg v-if="!isDarkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center ml-3 group">
                        <button @type="button"
                            class="peer flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                            id="user-menu-button-2" aria-expanded="false" data-dropdown-toggle="dropdown-2">
                            <img class="w-8 h-8 rounded-full object-cover"
                                :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                        </button>

                        <div
                            class="hidden group-focus-within:block peer-focus:block z-50 my-4 text-base list-none bg-white rounded shadow dark:bg-gray-700 absolute top-12 right-2">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{ $page.props.auth.user.email }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <ResponsiveNavLink :href="route('profile.show')">
                                        Perfil
                                    </ResponsiveNavLink>
                                </li>
                                <li>
                                    <form method="POST" @submit.prevent="logout">
                                        <ResponsiveNavLink as="button">
                                            Deslogar
                                        </ResponsiveNavLink>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <DialogModal  :show="showModalNotificacion && notifications.length > 0" @close="showModalNotificacion = false">
        <template #title>
            <div class="flex justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-semibold">Notificações</h2>
                    <span v-if="notifications.length > 0"
                        class=" bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
                        {{ notifications.length }}
                    </span>
                </div>
                <div @click="showModalNotificacion = false"
                    class="bg-red-500 text-white text-xs rounded-lg w-10 h-8 flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>
        </template>

        <template #content>
            <div class="space-y-2 max-h-[400px] overflow-y-auto">
                <div v-if="notifications.length" class="text-gray-400 text-center py-4">
                    <div v-for="{ notification, url, created_at }, index in notifications" :key="index"
                        @click="openNotification(url)"
                        class="p-4 cursor-pointer hover:bg-gray-900 bg-gray-700 mt-2 rounded-xl">
                        <div class="flex justify-between items-center ">
                            <div class="text-start">{{ notification }}</div>
                            <div>{{ new Date(created_at).toLocaleString() }}</div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-gray-400 text-center py-4">
                    <i class="fa-solid fa-bell-slash text-lg mr-2"></i>
                    Nenhuma notificação disponível.
                </div>
            </div>
        </template>
    </DialogModal>
</template>
<style>
@keyframes bell-shake {

    0%,
    55% {
        transform: rotate(0deg);
    }

    10%,
    20%,
    30%,
    40%,
    50% {
        transform: rotate(-15deg);
    }

    5%,
    15%,
    25%,
    35%,
    45% {
        transform: rotate(15deg);
    }
}

.animate-bell-shake {
    animation: bell-shake 2s ease-in-out infinite;
    transform-origin: top center;
}
</style>