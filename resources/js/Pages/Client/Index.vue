<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import DialogModal from '@/Components/DialogModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { formatCEP, formatCPF, formatPhone } from '@/Utils/maskUtils';
import Alert from '@/Components/Alert.vue';
import { getAddressByCep } from './clientServices';

const title = 'Clientes';
const props = defineProps({
    clients: Array,
    pagination: Object,
});

const alert = ref({
    show: false,
})
const route_params = route().params;
const showModal = ref(false);
const showConfirm = ref(false);
const formHtml = ref(null);
const processing = ref(false);
const input_search = ref(route_params.likes?.[0]?.name || '');
const select_status = ref(route_params.not_null ? 'inactive' : route_params.trash ? 'all' : 'active');
const is_consultant = usePage().props.auth.user.role === 'Consultant';

const form = ref({
    id: null,
    name: null,
    document: null,
    email: null,
    phone: null,
    address: null,
    number: null,
    complement: null,
    district: null,
    state: null,
    city: null,
    zip_code: null,
    origin: null,
    codename: null,
    stateRegistration: null,
});

const goToPage = page => {
    router.get(route("clients.index", {
        page,
        ...(input_search.value ? {
            likes: [
                { name: input_search.value },
                { document: input_search.value?.replace(/\D/g, '') },
                { phone: input_search?.value.replace(/\D/g, '') },
                { codename: input_search.value },
                { address: input_search.value },
                { city: input_search.value },
            ]
        } : {}),
        ...(select_status.value !== 'active' ? { trash: true } : {}),
        ...(select_status.value === 'inactive' ? { not_null: 'deleted_at' } : {}),
        order_by: { asc: 'name' },
    }));
};

const search = () => {
    router.get(route("clients.index"), {
        ...(input_search.value ? {
            likes: [
                { name: input_search.value },
                { document: input_search?.value.replace(/\D/g, '') },
                { phone: input_search?.value.replace(/\D/g, '') },
                { codename: input_search.value },
                { address: input_search.value },
                { city: input_search.value },
            ]
        } : {}),
        ...(select_status.value !== 'active' ? { trash: true } : {}),
        ...(select_status.value === 'inactive' ? { not_null: 'deleted_at' } : {}),
        order_by: { asc: 'name' },
    });
};

const submit = () => {

    if (form.value.origin === 'Outros' && form.value.customGround) {
        form.value.origin = form.value.customGround;
    }

    (form.value.id ? axios.put(route('clients.update', form.value.id), form.value)
        : axios.post(route('clients.store'), form.value))
        .then(response => response.data)
        .then(data => {
            if (form.value.id) {
                props.clients[props.clients.findIndex(({ data: { id } }) => id === form.value.id)].data = data;
            } else {
                props.clients.unshift({ data });
                props.pagination.total++;
            }
            showModal.value = false;
            alert.value = {
                show: true,
                message: 'Cliente salvo com sucesso',
                type: 'success',
            };
        })
        .catch((error) => {
            const is_email = error.response.data.message.includes('email_unique');
            alert.value = {
                show: true,
                message: error.response.data.message.includes('Duplicate') ? `Já existe um cliente com esse ${is_email ? 'e-mail' : 'CPF/CNPJ'}` : error.response.data.message,
                type: 'error',
            };
        });
};

const reset = () => {
    form.value = {
        id: null,
        name: null,
        document: null,
        email: null,
        phone: null,
        address: null,
        number: null,
        complement: null,
        district: null,
        state: null,
        city: null,
        zip_code: null,
        origin: null,
        codename: null,
        stateRegistration: null,
    };
}

const update = index => {
    const client = JSON.parse(JSON.stringify(props.clients[index].data));

    if (!originsOptions.includes(client.origin)) {
        client.customGround = client.origin;
        client.origin = 'Outros';
    }
    form.value = client;
    showModal.value = true;
};

const confirmDestroy = index => {
    form.value = props.clients[index].data;
    showConfirm.value = true;
}

const destroy = id => {
    showConfirm.value = false;
    const index = props.clients.findIndex(({ data: { id: _id } }) => _id === id);

    axios.delete(route('clients.destroy', id))
        .then(response => response.data)
        .then(() => {

            if (select_status.value === 'active') {
                props.clients.splice(index, 1);
                props.pagination.total--;
            } else {
                props.clients[index].data.deleted_at = new Date();
            }

            alert.value = {
                show: true,
                message: 'Cliente excluído com sucesso',
                type: 'success',
            };
        })
}

const restore = id => {
    axios.get(route('clients.restore', id))
        .then(response => response.data)
        .then(() => {
            const index = props.clients.findIndex(({ data: { id: _id } }) => _id === id);
            props.clients[index].data.deleted_at = null;
            alert.value = {
                show: true,
                message: 'Cliente restaurado com sucesso',
                type: 'success',
            };
        })
}

const getAddress = () => {
    processing.value = true;

    getAddressByCep(form.value.zip_code)
        .then(data => {
            const { bairro, cep, localidade, logradouro, uf } = data;

            form.value.address = logradouro;
            form.value.district = bairro;
            form.value.city = localidade;
            form.value.state = uf;
            form.value.zip_code = cep;
        })
        .catch((error) => console.error('Error:', error))
        .finally(() => {
            processing.value = false;
        });
};
const originsOptions = [
    { value: 'Instagram', label: 'Instagram' },
    { value: 'Site', label: 'Site' },
    { value: 'Indication', label: 'Indicação' },
    { value: 'Prospection', label: 'Prospecção' },
    { value: 'Other', label: 'Outros' },
];
// const getContract = index => {
//     contract.value = {
//         id: index.contract?.id || null,
//         service_id: index.id,
//         contract: index.contract?.contract || clausesContractDefault,
//         type: index.contract?.type || 'krenke',
//         approved: index.contract?.approved || false,
//     };

//     // if (is_admin) {
//     //     showModalContract.value = true;
//     // } else {
//     //     printContract();
//     // }
// }

// const updateContract = () => {
//     axios.post(route("contracts.store"), { ...contract.value })
//         .then(response => response.data)
//         .then(data => {
//             contract.value.id = data.id;
//             const index = props.services.findIndex(({ data: { id: _id } }) => _id === data.service_id);
//             props.services[index].data.contract = data;
//             alert.value = {
//                 show: true,
//                 message: 'Contrato salvo com sucesso',
//                 type: 'success',
//             };
//         })
// }
// const printContract = () => {
//     window.open(route("contracts.pdf", { id: contract.value.id, type: contract.value.type }), "_blank");
// };
// const contract = ref({});
// const showModalContract = ref(false);
// const clausesContractDefault = ref(`CONTRATO DE PRESTAÇÃO DE SERVIÇOS DE CONSULTORIA E LEVANTAMENTO DE REQUISITOS

// CONTRATADA: [NOME DA SUA SOFTHOUSE], CNPJ [00.000.000/0000-00], com sede em [Endereço Completo].
// CONTRATANTE: [NOME DO CLIENTE OU EMPRESA], CPF/CNPJ [000.000.000-00], residente ou sediada em [Endereço Completo].

// CLÁUSULA PRIMEIRA – DO OBJETO
// 1.1. O presente contrato tem como objeto a prestação de serviços de consultoria técnica para o Levantamento de Requisitos e Estruturação de Projeto de Software (cujo nome comercial e técnico será definido ao final deste processo).

// CLÁUSULA SEGUNDA – DA METODOLOGIA E ACESSO À INFORMAÇÃO
// 2.1. Para a execução do serviço, a CONTRATADA realizará entrevistas diagnósticas com o proprietário da empresa CONTRATANTE e com os líderes de cada área pertinente ao projeto.
// 2.2. DA AGENDA: A CONTRATANTE compromete-se a viabilizar a agenda dos líderes e gestores em um prazo máximo de [7] dias úteis após a assinatura deste contrato.
// 2.3. A indisponibilidade dos líderes ou o adiamento constante das reuniões por parte da CONTRATANTE desobriga a CONTRATADA de cumprir o prazo de entrega original, podendo o contrato ser pausado ou dado como concluído com base nas informações obtidas até o momento.

// CLÁUSULA TERCEIRA – DAS REVISÕES E ALINHAMENTO FINAL
// 3.1. Após a coleta de dados, será realizada uma única Reunião de Alinhamento e Apresentação, com duração estimada de até [4] horas.
// 3.2. Durante esta reunião, a CONTRATANTE poderá solicitar todos os ajustes, revisões e correções necessários para o fechamento do escopo.
// 3.3. DO ESCOPO FECHADO: Após o encerramento da reunião mencionada no item 3.1 e a entrega do documento final, qualquer nova alteração, inclusão de funcionalidade ou mudança de regra de negócio será considerada Item Extra, não estando incluída neste contrato e sujeita a novo orçamento de desenvolvimento.

// CLÁUSULA QUARTA – DOS VALORES E CONDIÇÃO COMERCIAL
// 4.1. Pela execução do levantamento de requisitos, a CONTRATANTE pagará à CONTRATADA o valor fixo de R$ [Inserir Valor X], em parcela única, no ato da assinatura deste instrumento.
// 4.2. BONIFICAÇÃO POR FIDELIDADE: Caso a CONTRATANTE opte por contratar o desenvolvimento do software com a CONTRATADA, o valor pago por este levantamento (R$ Valor X) será integralmente bonificado, sendo descontado do valor total do sistema.
// 4.3. PRAZO DA CONDIÇÃO: A bonificação descrita no item 4.2 terá validade de 7 (sete) dias corridos após a entrega do levantamento final. Caso o contrato de desenvolvimento não seja assinado neste prazo, o valor do levantamento será considerado como pagamento definitivo pela consultoria prestada, sem direito a desconto futuro.

// CLÁUSULA QUINTA – PROPRIEDADE INTELECTUAL E SIGILO
// 5.1. Após a quitação do valor estipulado na Cláusula 4.1, a CONTRATADA entregará o documento de especificação técnica à CONTRATANTE, que passará a ter a propriedade intelectual sobre o documento.
// 5.2. As partes comprometem-se a manter sigilo absoluto sobre informações comerciais e regras de negócio trocadas durante a execução deste contrato.

// CLÁUSULA SEXTA - DO FORO
// 6.1. Para dirimir quaisquer controvérsias oriundas do presente contrato, as partes elegem o foro da comarca de [Sua Cidade/UF].

// [Cidade/UF], [Data].

// __________________________________________
// [NOME DA SUA SOFTHOUSE]

// __________________________________________
// [NOME DO CLIENTE]`);

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
                    <div class="flex gap-2">
                        <form @submit.prevent="search()" method="GET" class="w-full sm:w-[300px]">
                            <input type="text" name="search" v-model="input_search"
                                class="p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Buscar">
                        </form>
                        <select v-model="select_status"
                            class="w-full sm:w-auto bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-500 focus:border-primary-500 block dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option value="all">Todos</option>
                            <option value="active">Ativos</option>
                            <option value="inactive">Inativos</option>
                        </select>
                    </div>
                    <div class="flex w-full items-center justify-between">
                        <button @click="search()" type="button"
                            class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:focus:ring-primary-800">
                            <i class="fa-solid fa-magnifying-glass text-lg"></i>
                        </button>
                        <button @click="reset(); showModal = true"
                            class="flex items-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                            type="button">
                            <i class="fa-solid fa-file me-2 text-lg"></i>
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
                                            class="ps-2 px-0 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 text-center">
                                            Cód.
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Nome
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Conhecido por
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            CPF / CNPJ
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Cidade / UF
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">

                                    <tr v-for="{ data: { id, name, city, state, document, codename, deleted_at } }, index in clients"
                                        class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="{ 'bg-gray-50 dark:bg-gray-900': index % 2 === 0 }">
                                        <td
                                            class="px-0 py-2 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
                                            {{ id }}
                                        </td>
                                        <td
                                            class="p-4 py-2 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <div
                                                class="text-base font-semibold text-gray-900 dark:text-white uppercase">
                                                {{ name
                                                }}</div>
                                        </td>
                                        <td
                                            class="p-4 py-2 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white uppercase">
                                            {{ codename }}
                                        </td>
                                        <td
                                            class="p-4 py-2 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ document }}</td>
                                        <td
                                            class="p-4 py-2 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ city && `${city}${state ? ` / ${state}` : ''}` }}
                                        </td>

                                        <td class="p-4 py-2 space-x-2 whitespace-nowrap text-end">
                                            <button @click="update(index)" type="button"
                                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                                                <i class="fas fa-pen me-2"></i>
                                            </button>

                                            <button v-if="!is_consultant && !deleted_at" @click="confirmDestroy(index)"
                                                type="button"
                                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                                <i class="fas fa-trash me-2"></i>
                                            </button>

                                            <button v-else-if="!is_consultant" @click="restore(id)" type="button"
                                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-orange-600 rounded-lg hover:bg-orange-800 focus:ring-4 focus:ring-orange-300 dark:focus:ring-orange-900">
                                                <i class="fas fa-rotate-left me-2"></i>
                                            </button>
                                            <button
                                                type="button" @click="getContract(index)"
                                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-yellow-600 rounded-lg hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 dark:focus:ring-yellow-900">
                                                <i class="fa-solid fa-file-signature me-2"></i>
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
            Excluir Cliente
        </template>
        <template #content>
            Deseja realmente excluir <strong class="text-red-600">{{ form.name }}</strong>?
        </template>
    </ConfirmationModal>
    <DialogModal :show="showModal" @close="showModal = false">
        <template #title>
            <h2 class="text-xl font-semibold mb-4">Cadastro de Cliente</h2>
        </template>
        <template #content>
            <form ref="formHtml" @submit.prevent="submit">
                <div class="space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto pr-2">
                    <div class="grid sm:grid-cols-[3fr_1fr] gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Nome Completo
                            </label>
                            <input type="text" v-model="form.name"
                                class="uppercase bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                required />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Conhecido por
                            </label>
                            <input type="text" v-model="form.codename"
                                class="uppercase bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>

                    </div>

                    <div class="grid sm:grid-cols-[1fr_1fr_1fr] gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Documento (CPF/CNPJ)
                            </label>
                            <input type="tel" v-model="form.document"
                                @input="form.document = formatCPF($event.target.value)"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Telefone
                            </label>
                            <input type="text" v-model="form.phone" maxlength="15"
                                @input="form.phone = formatPhone($event.target.value)"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Inscrição Estadual
                            </label>
                            <input type="text" v-model="form.stateRegistration"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                    </div>
                    <div class="row-span-2 sm:row-span-1">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            Origem do cliente
                        </label>
                        <div class="flex flex-row gap-2 items-center">
                            <select v-model="form.origin" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                                            focus:ring-primary-600 focus:border-primary-600 block w-full p-2 h-10
                                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                                            dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="" disabled>Selecione</option>
                                <option v-for="option in originsOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                            <input v-if="form.origin === 'Outros'" v-model="form.customGround" type="text"
                                class="p-2 border border-gray-300 rounded-lg w-full dark:bg-gray-700 dark:text-white " />
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            E-mail
                        </label>
                        <input type="email" v-model="form.email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                    </div>
                    <hr class="my-6 dark:border-gray-700" />
                    <h3 class="text-lg font-semibold mb-4">Endereço</h3>
                    <div class="grid sm:grid-cols-[1fr_4fr_1fr] gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                CEP
                            </label>
                            <div class="relative w-[150px]">
                                <input type="tel" @input="form.zip_code = formatCEP($event.target.value)"
                                    v-model="form.zip_code"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="_____-___" maxlength="10" />
                                <button :disabled="processing" @click="getAddress()"
                                    class="absolute right-1 top-1 rounded bg-slate-800 px-2.5 py-1 border border-transparent text-center text-sm text-white transition-all shadow-sm hover:shadow focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                                    type="button">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Logradouro
                            </label>
                            <input type="text" v-model="form.address"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Número
                            </label>
                            <input type="text" v-model="form.number"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>

                    </div>
                    <div class="grid grid-cols-[3fr_1fr] sm:grid-cols-[2fr_2fr_1fr] gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Bairro
                            </label>
                            <input type="text" v-model="form.district"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Cidade
                            </label>
                            <input type="text" v-model="form.city"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                UF
                            </label>
                            <input type="text" v-model="form.state"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                maxlength="2" />
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            Complemento
                        </label>
                        <input type="text" v-model="form.complement"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                    </div>
                    <hr class="my-6 dark:border-gray-700" />
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
    <DialogModal :show="showModalContract" @close="showModalContract = false">
        <template #title>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold mb-4">{{ 'Contrato | Pedido n. ' + contract.service_id }}</h2>
                <div class="flex gap-4 items-center">
                    <label class="inline-flex items-center cursor-pointer ms-1">
                        <input type="checkbox" v-model="contract.approved" :checked="contract.approved"
                            class="sr-only peer">
                        <div
                            class="me-2 relative w-7 h-4 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600">
                        </div>
                        <span class="text-base font-semibold text-gray-900 dark:text-white">Aprovado</span>
                    </label>
                </div>
            </div>
        </template>
        <template #content>
            <form>
                <div class="space-y-2 overflow-y-auto max-h-[calc(100vh-200px)]">
                    <div>
                        <textarea :disabled="contract.approved" v-model="contract.contract" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                                focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                                dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            rows="20"></textarea>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex w-full items-center justify-between">
                <button @click="showModalContract = false"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                    type="button">
                    Cancelar
                </button>
                <div class="flex gap-4">
                    <button v-if="contract.id" @click="printContract()"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                        type="button">
                        Imprimir
                    </button>
                    <button @click="updateContract()"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                        type="button">
                        Salvar
                    </button>
                </div>
            </div>
        </template>
    </DialogModal>
    <Alert v-if="alert.show" :message="alert.message" @remove="alert.show = false" :type="alert.type" />
</template>
