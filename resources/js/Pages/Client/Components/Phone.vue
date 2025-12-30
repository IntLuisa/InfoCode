<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import { ref } from 'vue';
import { formatPhone } from '@/Utils/maskUtils';

const props = defineProps({
    phones: Array,
});

const show = ref(false);
const showList = ref(false);
const form = ref({
    numero_TelefoneCliente: null,
    operadora_TelefoneCliente: null,
});

const addNumber = () => {
    const index = props.phones.findIndex(({ codigo_TelefoneCliente: id }) => id === form.value.codigo_TelefoneCliente);

    if (index !== -1) {
        props.phones[index] = form.value;
    } else {
        form.value.codigo_TelefoneCliente = Math.floor(Math.random() * 1000000);
        props.phones.push(form.value);
    }
    show.value = false;
};

const reset = () => {
    form.value = {
        numero_TelefoneCliente: null,
        operadora_TelefoneCliente: null,
    }
}

const update = index => {
    form.value = JSON.parse(JSON.stringify(props.phones[index]));
    show.value = true;
}

const remove = index => {
    props.phones.splice(index, 1);
}

</script>
<template>
    <div>
        <hr class="my-3 dark:border-gray-700" />
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold cursor-pointer dark:text-white" @click="showList = !showList">
                <i class="fa" :class="showList ? 'fa-angle-down' : 'fa-angle-up'"></i>
                Telefones <span
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-1 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                    {{ phones?.length }}</span>
            </h2>
            <button @click="reset(); show = true"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                type="button">Adicionar</button>
        </div>
        <div v-if="showList"
            v-for="({ numero_TelefoneCliente: number, operadora_TelefoneCliente: type } = {}, index) in phones"
            class="flex items-center justify-between mt-2">
            <div class="text-blue-400">{{ formatPhone(number) }}</div>
            <div class="flex-1 text-end pe-3">{{ type }}</div>
            <div>
                <button @click="update(index)" type="button"
                    class="me-1 inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                    <i class="fas fa-pen me-2"></i>
                </button>

                <button @click="remove(index)" type="button"
                    class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                    <i class="fas fa-trash me-2"></i>
                </button>
            </div>
        </div>
    </div>
    <DialogModal :show="show" @close="show = false">
        <template #title>
            <h2 class="text-xl font-semibold mb-4">Adicionar Número</h2>
        </template>
        <template #content>
            <div class="space-y-4 md:space-y-6">
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr] gap-4 mt-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Número
                            </label>
                            <div>
                                <input type="tel"
                                    @input="form.numero_TelefoneCliente = formatPhone($event.target.value)"
                                    v-model="form.numero_TelefoneCliente"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="(00) 00000-0000" required maxlength="15" />
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Tipo
                            </label>
                            <div>
                                <input type="text" v-model="form.operadora_TelefoneCliente"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    maxlength="30" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-6 dark:border-gray-700" />
            </div>
        </template>
        <template #footer>
            <div class="flex w-full items-center justify-between">
                <button @click="show = false"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                    type="button">
                    Cancelar
                </button>
                <button @click="addNumber"
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                    type="button">
                    Salvar
                </button>
            </div>
        </template>
    </DialogModal>
</template>