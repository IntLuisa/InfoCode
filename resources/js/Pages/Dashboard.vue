<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const title = 'Dashboard';
const props = defineProps({
    data: Object,
});

</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ title }}
            </h2>
        </template>
        <template #content>
            <div class="p-5">

                <div class="grid grid-cols-1 gap-4 mb-5 xl:grid-cols-4 sm:grid-cols-3">
                    <div
                        class="flex flex-col p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <Link :href="route('clients.index', { order_by: { asc: 'name' } })">
                        <div class="flex flex-col">
                            <h2 class="text-gray-700 dark:text-gray-200 text-bold text-2xl">
                                <i class="fa fa-users mr-2"></i>Clientes
                            </h2>
                            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 mt-3">Total</h3>
                            <span class="text-center text-5xl font-bold leading-none text-gray-900 dark:text-white">{{
                                data.clients }}</span>
                        </div>
                        </Link>
                    </div>
                    <div
                        class="flex flex-col p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <Link :href="route('users.index', { order_by: { desc: 'id' } })">
                        <div class="flex flex-col">
                            <h2 class="text-gray-700 dark:text-gray-200 text-bold text-2xl">
                                <i class="fa fa-helmet-safety mr-2"></i>Usuários
                            </h2>
                            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 mt-3">Total</h3>
                            <span class="text-center text-5xl font-bold leading-none text-gray-900 dark:text-white">{{
                                data.employees }}</span>
                        </div>
                        </Link>
                    </div>
                    <div
                        class="flex flex-col p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="flex flex-col">
                            <h2 class="text-gray-700 dark:text-gray-200 text-bold text-2xl">
                                <i class="fa fa-users mr-2"></i>Contratos Para L.R.
                            </h2>
                            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 mt-3">Total</h3>
                            <!-- <span class="text-center text-5xl font-bold leading-none text-gray-900 dark:text-white">{{
                                data.clients }}</span> -->
                        </div>
                    </div>
                    <div
                        class="flex flex-col p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="flex flex-col">
                            <h2 class="text-gray-700 dark:text-gray-200 text-bold text-2xl">
                                <i class="fa fa-users mr-2"></i>Levantamento de Requisitos
                            </h2>
                            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 mt-3">Total</h3>
                            <!-- <span class="text-center text-5xl font-bold leading-none text-gray-900 dark:text-white">{{
                                data.clients }}</span> -->
                        </div>
                    </div>
                    <div
                        class="flex flex-col p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="flex flex-col">
                            <h2 class="text-gray-700 dark:text-gray-200 text-bold text-2xl">
                                <i class="fa fa-users mr-2"></i>Orçamentos
                            </h2>
                            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 mt-3">Total</h3>
                            <!-- <span class="text-center text-5xl font-bold leading-none text-gray-900 dark:text-white">{{
                                data.clients }}</span> -->
                        </div>
                    </div>
                    <div
                        class="flex flex-col p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="flex flex-col">
                            <h2 class="text-gray-700 dark:text-gray-200 text-bold text-2xl">
                                <i class="fa fa-users mr-2"></i>Contrato de Serviços
                            </h2>
                            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 mt-3">Total</h3>
                            <!-- <span class="text-center text-5xl font-bold leading-none text-gray-900 dark:text-white">{{
                                data.clients }}</span> -->
                        </div>
                    </div>
                    <div
                        class="flex flex-col p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="flex flex-col">
                            <h2 class="text-gray-700 dark:text-gray-200 text-bold text-2xl">
                                <i class="fa fa-users mr-2"></i>Projetos Finalizados
                            </h2>
                            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 mt-3">Total</h3>
                            <!-- <span class="text-center text-5xl font-bold leading-none text-gray-900 dark:text-white">{{
                                data.clients }}</span> -->
                        </div>
                    </div>
                    
                </div>
            </div>
        </template>
    </AppLayout>
</template>
