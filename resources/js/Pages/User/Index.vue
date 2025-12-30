<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import Pagination from '@/Components/Pagination.vue';
import axios from 'axios';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Alert from '@/Components/Alert.vue';
import { formatPhone } from '@/Utils/maskUtils';

const title = 'Usuários';
const props = defineProps({
    users: Array,
    pagination: Object,
});

const alert = ref({
    show: false,
    message: '',
    type: 'success',
})
const showModal = ref(false);
const showConfirm = ref(false);
const formHtml = ref(null);
const input_search = ref(route().params.name?.lk || '');
const is_manager = usePage().props.auth.user.role === 'Manager';

const form = ref({
    name: null,
    email: null,
    phone: null,
    job_position: null,
    role: null,
});

const goToPage = (page) => {
    router.get(route("users.index", {
        page,
        ...(input_search.value ? { name: { lk: input_search.value } } : {}),
        order_by: { asc: 'name' },
    }));
};

const search = () => {
    router.get(route("users.index"), {
        name: { lk: input_search.value },
        order_by: { asc: 'name' },
    });
};

const submit = () => {
    (form.value.id ? axios.put(route('users.update', form.value.id), form.value)
        : axios.post(route('users.store'), form.value))
        .then(response => response.data)
        .then(data => {
            if (form.value.id) {
                props.users[props.users.findIndex(({ id }) => id === form.value.id)] = data;
            } else {
                props.users.unshift(data);
            }
            showModal.value = false;
            alert.value = {
                show: true,
                message: 'Usuário salvo com sucesso',
                type: 'success',
            };
        })
        .catch((error) => {
            alert.value = {
                show: true,
                message: error.status == 403 ? 'Sem permissão' : 'E-mail já cadastrado',
                type: 'error',
            };
        });
};

const resetForm = () => {
    form.value = {
        name: null,
        email: null,
        phone: null,
        job_position: null,
        role: null,
    };
};

const confirmDestroy = index => {
    form.value = props.users[index];
    showConfirm.value = true;
}


const update = index => {
    const user = JSON.parse(JSON.stringify(props.users[index]));
    form.value = { ...user };

    showModal.value = true;

};

const destroy = id => {
    const index = props.users.findIndex(({ id: _id }) => _id === id);

    axios.delete(route('users.destroy', id))
        .then(response => response.data)
        .then(() => {
            props.users.splice(index, 1);
        })
        .finally(() => {
            showConfirm.value = false;
        });
}
const optionsRole = [
    {value: 'Board', label: 'Diretoria'},
    {value: 'Manager', label: 'Administrador'},
    {value: 'Junior programmer', label: 'Programador Junior'},
    {value: 'Senior programmer', label: 'Programador Senior'},
    {value: 'Plenny programmer', label: 'Programador Pleno'},
    {value: 'Designer', label: 'Designer'},
    {value: 'Finance', label: 'Financeiro'},
    {value: 'Observer', label: 'Observador'},
    
]
</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ title }}
            </h2>
        </template>
        <template #content>
            <div class="flex flex-col mt-2">
                <div class="flex items-center gap-2 w-full px-2">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <form @submit.prevent="search()" method="GET">
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <input type="text" name="search" v-model="input_search"
                                    class="p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Buscar">
                            </div>
                        </form>
                    </div>
                    <div class="flex flex-1 items-center gap-2">
                        <div class="flex items-center justify-end sm:justify-start mt-1 w-full">
                            <button @click="search()" type="button"
                                class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:focus:ring-primary-800">
                                <i class="fa-solid fa-magnifying-glass text-lg"></i>
                            </button>
                        </div>
                        <button @click="resetForm(); showModal = true"
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                            type="button">
                            Novo
                        </button>
                    </div>
                </div>
                <Pagination :pagination="props.pagination" @page-change="goToPage" />

                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow">
                            <table class="min-w-full table-fixed">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Nome
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            E-mail
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Hierarquia
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">

                                    <tr v-for="{ name, email, id, role }, index in users"
                                        class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="[index % 2 === 0 ? `bg-gray-50 dark:bg-gray-900` : '', 'text-gray-900 dark:text-white']">
                                        <td class="p-4 py-2 text-sm font-normal whitespace-nowrap">
                                            <div class="text-base font-semibold">{{ name }}</div>
                                        </td>
                                        <td class="p-4 py-2 text-sm font-normal whitespace-nowrap">
                                            <div class="text-base font-semibold">{{ email }}</div>
                                        </td>
                                        <td class="p-4 py-2 text-sm font-normal whitespace-nowrap">
                                            <div class="text-base font-semibold">{{optionsRole.find(item => item.value
                                                == role)?.label}}</div>
                                        </td>

                                        <td class="p-4 py-2 space-x-2 whitespace-nowrap text-end">
                                            <button @click="update(index)" type="button"
                                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                                                <i class="fas fa-pen me-2"></i>
                                            </button>
                                            <button v-if="id != $page.props.auth.user.id && !deleted_at && !is_manager"
                                                @click="confirmDestroy(index)" type="button"
                                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                                <i class="fas fa-trash me-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <Pagination :pagination="props.pagination" @page-change="goToPage" />

            </div>
        </template>
    </AppLayout>
    <DialogModal :show="showModal" @close="showModal = false">
        <template #title>
            Novo Usuário
        </template>
        <template #content>
            <form ref="formHtml" @submit.prevent="submit">
                <div class="space-y-4 md:space-y-6">
                    <div>
                        <label for="" class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            Nome
                        </label>
                        <input type="text" v-model="form.name" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-[1fr_1fr_1fr] gap-4">
                        <div class="col-span-2">
                            <label for="" class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                E-mail
                            </label>
                            <input type="email" v-model="form.email" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                        <div>
                            <label for="" class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Telefone
                            </label>
                            <input type="tel" v-model="form.phone"
                                @input="form.phone = formatPhone($event.target.value)"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                maxlength="15" />
                        </div>
                    </div>
                    <div>
                        <div>
                            <label for="" class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Cargo
                            </label>
                            <select v-model="form.role" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="" disabled>Selecione a cargo</option>
                                <option v-for="option in optionsRole" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>

                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex w-full items-center justify-between">
                <button @click="showModal = false"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                    type="button">
                    Cancelar
                </button>
                <button @click="formHtml.requestSubmit()"
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                    type="button">
                    Salvar
                </button>
            </div>
        </template>
    </DialogModal>
    <ConfirmationModal :show="showConfirm" @close="showConfirm = false" @confirm="destroy(form.id)">
        <template #title>
            Inativar Usuário
        </template>
        <template #content>
            Deseja realmente deletar <strong class="text-red-600">{{ form.name }}</strong>?
        </template>
    </ConfirmationModal>
    <Alert v-if="alert.show" :message="alert.message" @remove="alert.show = false" :type="alert.type" />
</template>
