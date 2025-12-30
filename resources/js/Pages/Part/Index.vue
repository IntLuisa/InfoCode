<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import DialogModal from '@/Components/DialogModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import Alert from '@/Components/Alert.vue';

const title = 'Peças';
const props = defineProps({
    parts: Array,
    pagination: Object,
});

const alert = ref({
    show: false,
    message: '',
    type: 'success',
})
const showModal = ref(false);
const showConfirm = ref(false);
const processing = ref(false);
const input_search = ref(route().params.name?.lk || '');
const picture = ref(null);
const partsLocal = ref([...props.parts]);
const is_admin = ['Board', 'Manager'].includes(usePage().props.auth.user.role);

const form = ref({
    id: null,
    code: null,
    name: null,
    picture: null,
    amount: null,
    amountFabric: null,
    ipiPercentage: 0,
    kits: '',
    ipiNaoAplica: true
});

const goToPage = page => {
    router.get(route("parts.index", {
        page,
        ...(input_search.value ? { name: { lk: input_search.value } } : {}),
        order_by: { asc: 'name' },
    }));
};

const search = () => {
    router.get(route("parts.index"), {
        name: { lk: input_search.value },
        order_by: { asc: 'name' },
    });
};

const submit = () => {
    if (processing.value) return;
    processing.value = true;

    try {
        if (!form.value.code || !form.value.name) {
            throw new Error('Preencha o código e o nome da peça');
        }

        const formData = new FormData();
        if (form.value.id) formData.append('_method', 'PUT');

        if (picture.value) formData.append('picture', picture.value);

        if (form.value.id) formData.append('shouldDeleteImage', shouldDeleteImage.value ? 1 : 0);

        formData.append('code', form.value.code);
        formData.append('name', form.value.name);
        formData.append('amount', form.value.amount);
        formData.append('amountFabric', form.value.amountFabric);
        formData.append('ipiPercentage', form.value.ipiPercentage);
        formData.append('kits', form.value.kits);

        const url = form.value.id
            ? route('parts.update', form.value.id)
            : route('parts.store');

        axios.post(url, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
            .then(response => response.data)
            .then(data => {
                const index = partsLocal.value.findIndex(({ data: { id } }) => id === form.value.id);

                if (form.value.id) {
                    if (index !== -1) {
                        partsLocal.value[index].data = data;
                        if (shouldDeleteImage.value) partsLocal.value[index].data.picture = null;
                    }
                } else {
                    partsLocal.value.unshift({ data });
                    props.pagination.total++;
                }
                showModal.value = false;
                shouldDeleteImage.value = false;
                picture.value = null;
                imagePreview.value = null;
                fileName.value = '';

                alert.value = { show: true, message: 'Peça salva com sucesso', type: 'success' };
            })
            .catch((error) => {
                alert.value = {
                    show: true,
                    message: error.response.data.message.includes('Duplicate')
                        ? 'Já existe uma peça com esse código'
                        : error.response.data.message,
                    type: 'error',
                };
            }).finally(() => {
                processing.value = false;
            });

    } catch (error) {
        processing.value = false;
        alert.value = { show: true, message: error.message, type: 'error' };
    }
};


const reset = () => {
    form.value = {
        id: null,
        code: null,
        name: null,
        picture: null,
        amount: null,
        amountFabric: null,
        ipiPercentage: 0,
        kits: '',
        ipiNaoAplica: true
    };
    imagePreview.value = null;
    fileName.value = '';
    picture.value = null;
};

const update = (index) => {
    const item = partsLocal.value[index].data;

    form.value = {
        ...item,
        ipiNaoAplica: item.ipiPercentage == 0,
    };

    imagePreview.value = item.picture ? `/storage/${item.picture}` : null;
    fileName.value = item.picture ?? '';
    picture.value = null;
    showModal.value = true;
};

const confirmDestroy = index => {
    form.value = partsLocal.value[index].data;
    showConfirm.value = true;
};

const imagePreview = ref(null);
const fileName = ref('');
const fileInput = ref(null);

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    picture.value = file;
    fileName.value = file.name;

    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const shouldDeleteImage = ref(false);

const removeImage = () => {
    picture.value = null;
    imagePreview.value = null;
    fileName.value = '';

    if (fileInput.value) {
        fileInput.value.value = '';
    }

    shouldDeleteImage.value = true;
};

const destroy = id => {
    showConfirm.value = false;

    axios.delete(route('parts.destroy', id))
        .then(() => {
            const index = partsLocal.value.findIndex(({ data: { id: _id } }) => _id === id);
            partsLocal.value.splice(index, 1);
            props.pagination.total--;
        })
        .catch(() => {
            alert.value = { show: true, message: 'Erro ao excluir a peça', type: 'error' };
        });
};

const exportCSV = async () => {
    try {
        const response = await axios.get('/parts?all=true');
        const headers = "Código;Nome;Valor;Valor Fabrica;IPI;Kits";

        const csvContent = `${headers}\n${response.data.map(({ code, name, amount, amountFabric, ipiPercentage, kits }) =>
            `${code};${name};${amount / 100};${amountFabric / 100};${ipiPercentage}%;${kits || ''}`).join('\n')}`;

        const link = document.createElement('a');
        link.href = URL.createObjectURL(new Blob([csvContent], { type: 'text/csv;charset=utf-8;' }));
        link.setAttribute('download', `pecas_${new Date().toISOString().slice(0, 10)}.csv`);
        link.click();
        link.remove();
        alert.value = { show: true, message: 'CSV exportado com sucesso!', type: 'success' };
    } catch (error) {
        alert.value = { show: true, message: 'Erro ao exportar CSV', type: 'error' };
    }
};


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
                <div class="flex flex-col sm:flex-row gap-2 px-2 mt-1">
                    <form @submit.prevent="search()" method="GET" class="w-full sm:w-[300px]">
                        <input type="text" name="search" v-model="input_search"
                            class="p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Buscar">
                    </form>
                    <div class="flex w-full items-center justify-between">
                        <button @click="search()" type="button"
                            class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:focus:ring-primary-800">
                            <i class="fa-solid fa-magnifying-glass text-lg"></i>
                        </button>
                        <div class="flex gap-2">
                            <button @click="exportCSV" type="button"
                                class="flex items-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                                <i class="fa-solid fa-file-csv me-2 text-lg"></i>
                                Exportar
                            </button>
                            <button v-if="is_admin" @click="reset(); showModal = true"
                                class="flex items-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                                type="button">
                                <i class="fa-solid fa-file me-2 text-lg"></i>
                                Novo
                            </button>
                        </div>

                    </div>
                </div>

                <Pagination :pagination="props.pagination" @page-change="goToPage" />

                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow">
                            <table class="min-w-full table-fixed ">
                                <thead class="bg-gray-100 dark:bg-gray-700 ">
                                    <tr>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium  text-gray-500 uppercase dark:text-gray-400 text-left w-16">
                                            Cód.
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 w-40">
                                            Imagem
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Nome
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 text-end">
                                            Valor
                                        </th>
                                        <th v-if="is_admin" scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 text-end">
                                            Valor Fábrica
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">
                                    <tr v-for="{ data: { name, code, amount, picture, amountFabric } }, index in partsLocal"
                                        class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="{ 'bg-gray-50 dark:bg-gray-900': index % 2 === 0 }">
                                        <td
                                            class="p-4 py-2 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <div class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ code }}</div>
                                        </td>
                                        <td class="p-4">
                                            <img v-if="picture" :src="`/storage/${picture}`"
                                                class="h-12 w-auto rounded" />
                                        </td>
                                        <td
                                            class="p-4 py-2 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ name }}
                                        </td>
                                        <td
                                            class="p-4 py-2 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white text-end">
                                            R$ {{ (amount / 100).toCurrency() }}
                                        </td>
                                        <td v-if="is_admin"
                                            class="p-4 py-2 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white text-end">
                                            R$ {{ (amountFabric / 100).toCurrency() }}
                                        </td>
                                        <td class="p-4 py-2 space-x-2 whitespace-nowrap text-end">
                                            <button v-if="is_admin" @click="update(index)" type="button"
                                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                                                <i class="fas fa-pen me-2"></i>
                                            </button>

                                            <button v-if="is_admin" @click="confirmDestroy(index)" type="button"
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
    <ConfirmationModal :show="showConfirm" @close="showConfirm = false" @confirm="destroy(form.id)">
        <template #title>
            Excluir Peça
        </template>
        <template #content>
            Deseja realmente excluir <strong class="text-red-600">{{ form.name }}</strong>?
        </template>
    </ConfirmationModal>
    <DialogModal :show="showModal" @close="showModal = false">
        <template #title>
            <h2 class="text-xl font-semibold mb-4">Cadastro de Peça</h2>
        </template>
        <template #content>
            <form @submit.prevent="submit">
                <div class="space-y-4">
                    <div class="grid grid-cols-[1fr_2fr] sm:grid-cols-[1fr_5fr] gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Código
                            </label>
                            <input type="text" v-model="form.code"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                required maxlength="10" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Nome
                            </label>
                            <input type="text" v-model="form.name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                required />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-[1fr_1fr_1fr_1fr] gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Valor
                            </label>
                            <CurrencyInput v-model="form.amount"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                                    focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 
                                    dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                                    dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 text-end" />
                        </div>

                        <div v-if="is_admin">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Valor da Fábrica
                            </label>
                            <CurrencyInput v-model="form.amountFabric"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                                    focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 
                                    dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                                    dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 text-end" />
                        </div>

                        <div v-if="is_admin">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                IPI
                            </label>

                            <div class="flex items-center space-x-2 m-2">
                                <input id="ipi-nao-aplica" type="checkbox" v-model="form.ipiNaoAplica"
                                    @change="form.ipiPercentage = 0"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600" />
                                <label for="ipi-nao-aplica" class="text-sm text-gray-900 dark:text-primary-200">
                                    Não se aplica
                                </label>
                            </div>
                        </div>

                        <div v-if="!form.ipiNaoAplica && is_admin">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Porcentagem do IPI
                            </label>

                            <input type="number" v-model="form.ipiPercentage"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="%" />
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            Imagem
                        </label>
                        <div class="relative">
                            <label v-if="!imagePreview"
                                class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">

                                <div class="flex flex-col items-center justify-center py-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                        </path>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Clique para adicionar</span>
                                    </p>
                                </div>

                                <input ref="fileInput" type="file" class="hidden" accept="image/*"
                                    @change="onFileChange">
                            </label>
                            <div v-if="imagePreview" class="relative">
                                <img :src="imagePreview"
                                    class="w-full h-60 object-cover rounded-lg border-2 border-gray-200" alt="Preview">
                                <div class="absolute top-2 right-2 flex gap-2">
                                    <button @click="removeImage" type="button"
                                        class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            Kits
                        </label>
                        <input type="text" v-model="form.kits"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
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
                <button @click="submit"
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                    type="button">
                    Salvar
                </button>
            </div>
        </template>
    </DialogModal>
    <Alert v-if="alert.show" :message="alert.message" @remove="alert.show = false" :type="alert.type" />
</template>
