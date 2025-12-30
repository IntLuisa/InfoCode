<script setup>
import { sidebarState } from '@/stores/sidebar';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
});

const user_level = {
    Board: 10,
    Manager: 9,
    Finance: 6,
    "Assembly Manager": 5,
    Consultant: 2,
    Assembler: 1,
    Observer: 0
}[usePage().props.auth.user.role];

const logout = () => {
    router.post(route('logout'));
};

</script>
<template>
    <div v-if="sidebarState.isOpen" class="fixed inset-0 z-30 bg-gray-900 dark:bg-gray-500 opacity-50 sm:hidden"
        @click="sidebarState.isOpen = false"></div>
    <aside :class="sidebarState.isOpen ? 'translate-y-0'
        : 'translate-y-full sm:-translate-y-0 sm:translate-x-0'" class="fixed z-40 flex flex-col
            left-0 sm:top-0 sm:w-[205px] sm:h-full
            bottom-0 w-full h-auto
            pt-16 font-normal transition-transform duration-300">

        <div @click="sidebarState.isOpen = false"
            class="rounded-t-3xl sm:rounded-t-sm relative flex flex-col flex-1  min-h-0 pt-0 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                <div class="flex-1 px-3 space-y-1 bg-white dark:bg-gray-800">
                    <ul class="pb-2 space-y-2">
                        <li>
                            <Link :href="route('dashboard')"
                                :class="route().current('dashboard') ? 'dark:bg-gray-700 bg-gray-100' : ''"
                                class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                <i class="fa-solid fa-chart-line text-gray-400"></i>
                                <span class="ml-3">Dashboard</span>
                            </Link>
                        </li>
                        <li v-if="user_level >= 2">
                            <Link :href="route('clients.index', { order_by: { asc: 'name' } })"
                                :class="route().current('clients.index') ? 'dark:bg-gray-700 bg-gray-100' : ''"
                                class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                <i class="fa fa-puzzle-piece text-gray-400"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Clientes</span>
                            </Link>
                        </li>
                        <li v-if="user_level >= 2">
                            <Link :href="route('parts.index', { order_by: { asc: 'name' } })"
                                :class="route().current('parts.index') ? 'dark:bg-gray-700 bg-gray-100' : ''"
                                class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                <i class="fa fa-puzzle-piece text-gray-400"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Peças</span>
                            </Link>
                        </li>
                        <li v-if="user_level >= 2">
                            <Link :href="route('playgrounds.index', { order_by: { asc: 'name' } })"
                                :class="route().current('playgrounds.index') ? 'dark:bg-gray-700' : ''"
                                class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                <i class="fa-solid fa-layer-group text-gray-400"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Parques</span>
                            </Link>
                        </li>

                        <li >
                            <Link :href="route('calendar.index', { order_by: { asc: 'name' } })"
                                :class="route().current('calendar.index') ? 'dark:bg-gray-700 bg-gray-100' : ''"
                                class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                <i class="fa-solid fa-calendar text-gray-400"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Calendário</span>
                            </Link>
                        </li>
                        <li v-if="user_level >= 9">
                            <Link :href="route('users.index', { order_by: { asc: 'id' } })"
                                :class="route().current('users.index') ? 'dark:bg-gray-700 bg-gray-100' : ''"
                                class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                                <i class="fa-solid fa-users text-gray-400"></i>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Usuários</span>
                            </Link>
                        </li>
                    </ul>
                    <div class="pt-2 space-y-2">
                        <Link :href="route('profile.show')"
                            :class="route().current('profile.show') ? 'dark:bg-gray-700 bg-gray-100' : ''"
                            class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 group dark:text-gray-200 dark:hover:bg-gray-700">
                            <i class="fa-solid fa-cog text-gray-400"></i>
                            <span class="ml-3">Perfil</span>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 justify-center w-full p-4 space-x-4 bg-white flex dark:bg-gray-800"
                sidebar-bottom-menu="">
                <Link :href="route('profile.show')"
                    class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                    <i class="fa-solid fa-cog text-lg"></i>
                </Link>
                <form method="POST" @submit.prevent="logout">
                    <button type="submit"
                        class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                        <i class="fa-solid fa-sign-out text-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>
</template>
