<script setup>
import { ref, reactive } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import DialogModal from '@/Components/DialogModal.vue'
import { router } from '@inertiajs/vue3'
import Alert from '@/Components/Alert.vue'
import { defineProps } from 'vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'


const props = defineProps({
  budgets: Object
})

const title = 'Diagnósticos'
const showModal = ref(false)
const showModalSearch = ref(false)
const showConfirm = ref(false)
const formHtml = ref(null)
const processing = ref(false)

const input_search = ref('')
const input_search_client = ref('')
const select_status = ref('active')

const form = reactive({
  id: null,
  client_id: null,
  status: 'pending',
  project_name: '',
  service_type: '',
  note: '',

})

const collapsed = reactive({
  client: true,
  description: false,
})

const selectedClient = reactive({
  name: '',
  document: '',
})

const clients = reactive({
  data: [],
})

const search = () => {}
const reset = () => {
  input_search.value = ''
  select_status.value = 'active'
}

const submit = () => {
  processing.value = true

  if (form.id) {
    router.put(route('budgets.update', form.id), form, {
      onFinish: () => processing.value = false,
      onSuccess: () => {
        showModal.value = false
        resetForm()
      }
    })
  } else {
    router.post(route('budgets.store'), form, {
      onFinish: () => processing.value = false,
      onSuccess: () => {
        showModal.value = false
        resetForm()
      }
    })
  }
}

const update = (budget) => {
  form.id = budget.id
  form.client_id = budget.client_id
  form.status = budget.status
  form.project_name = budget.project_name
  form.service_type = budget.service_type
  form.note = budget.note

  showModal.value = true
}
const setClient = (index) => {
  const client = clients.data[index]

  selectedClient.id = client.id
  selectedClient.name = client.name
  selectedClient.document = client.document

  form.client_id = client.id

  showModalSearch.value = false
}


const searchClient = async (page = 1) => {
  try {
    const response = await axios.get(route('clients.index'), {
      params: {
        search: input_search_client.value,
        page: page,
        json: true, 
      }
    })

    clients.data = response.data.data
    clients.last_page = response.data.last_page
  } catch (error) {
    console.error('Erro ao buscar clientes', error)
  }
}

const confirmDestroy = (budget) => {
  form.id = budget.id
  showConfirm.value = true
}

const destroy = (id) => {
  showConfirm.value = false
  router.delete(route('budgets.destroy', id))
}

const translaterServiceType = (serviceType) => {
  switch (serviceType) {
    case 'service':
      return 'Serviço'
    case 'product':
      return 'Produto'
    default:
      return 'Serviço'
  }
}

const getContract = (budget) => {
    contract.value = {
        id: budget.contract?.id || null,
        budget_id: budget.id, // aqui é o ID do orçamento
        contract: budget.contract?.contract || clausesContractDefault.value,
        approved: budget.contract?.approved || false,
    };

    showModalContract.value = true
}


const updateContract = () => {
    axios.post(route("contracts.store"), { ...contract.value })
        .then(response => response.data)
        .then(data => {
            contract.value.id = data.id;
            const index = props.services.findIndex(({ data: { id: _id } }) => _id === data.service_id);
            props.services[index].data.contract = data;
            alert.value = {
                show: true,
                message: 'Contrato salvo com sucesso',
                type: 'success',
            };
        })
}
const printContract = () => {
    window.open(route("contracts.pdf", { id: contract.value.id, type: contract.value.type }), "_blank");
};
const contract = ref({});
const showModalContract = ref(false);
const clausesContractDefault = ref(`CONTRATO DE PRESTAÇÃO DE SERVIÇOS DE CONSULTORIA E LEVANTAMENTO DE REQUISITOS

CONTRATADA: [NOME DA SUA SOFTHOUSE], CNPJ [00.000.000/0000-00], com sede em [Endereço Completo].
CONTRATANTE: [NOME DO CLIENTE OU EMPRESA], CPF/CNPJ [000.000.000-00], residente ou sediada em [Endereço Completo].

CLÁUSULA PRIMEIRA – DO OBJETO
1.1. O presente contrato tem como objeto a prestação de serviços de consultoria técnica para o Levantamento de Requisitos e Estruturação de Projeto de Software (cujo nome comercial e técnico será definido ao final deste processo).

CLÁUSULA SEGUNDA – DA METODOLOGIA E ACESSO À INFORMAÇÃO
2.1. Para a execução do serviço, a CONTRATADA realizará entrevistas diagnósticas com o proprietário da empresa CONTRATANTE e com os líderes de cada área pertinente ao projeto.
2.2. DA AGENDA: A CONTRATANTE compromete-se a viabilizar a agenda dos líderes e gestores em um prazo máximo de [7] dias úteis após a assinatura deste contrato.
2.3. A indisponibilidade dos líderes ou o adiamento constante das reuniões por parte da CONTRATANTE desobriga a CONTRATADA de cumprir o prazo de entrega original, podendo o contrato ser pausado ou dado como concluído com base nas informações obtidas até o momento.

CLÁUSULA TERCEIRA – DAS REVISÕES E ALINHAMENTO FINAL
3.1. Após a coleta de dados, será realizada uma única Reunião de Alinhamento e Apresentação, com duração estimada de até [4] horas.
3.2. Durante esta reunião, a CONTRATANTE poderá solicitar todos os ajustes, revisões e correções necessários para o fechamento do escopo.
3.3. DO ESCOPO FECHADO: Após o encerramento da reunião mencionada no item 3.1 e a entrega do documento final, qualquer nova alteração, inclusão de funcionalidade ou mudança de regra de negócio será considerada Item Extra, não estando incluída neste contrato e sujeita a novo orçamento de desenvolvimento.

CLÁUSULA QUARTA – DOS VALORES E CONDIÇÃO COMERCIAL
4.1. Pela execução do levantamento de requisitos, a CONTRATANTE pagará à CONTRATADA o valor fixo de R$ [Inserir Valor X], em parcela única, no ato da assinatura deste instrumento.
4.2. BONIFICAÇÃO POR FIDELIDADE: Caso a CONTRATANTE opte por contratar o desenvolvimento do software com a CONTRATADA, o valor pago por este levantamento (R$ Valor X) será integralmente bonificado, sendo descontado do valor total do sistema.
4.3. PRAZO DA CONDIÇÃO: A bonificação descrita no item 4.2 terá validade de 7 (sete) dias corridos após a entrega do levantamento final. Caso o contrato de desenvolvimento não seja assinado neste prazo, o valor do levantamento será considerado como pagamento definitivo pela consultoria prestada, sem direito a desconto futuro.

CLÁUSULA QUINTA – PROPRIEDADE INTELECTUAL E SIGILO
5.1. Após a quitação do valor estipulado na Cláusula 4.1, a CONTRATADA entregará o documento de especificação técnica à CONTRATANTE, que passará a ter a propriedade intelectual sobre o documento.
5.2. As partes comprometem-se a manter sigilo absoluto sobre informações comerciais e regras de negócio trocadas durante a execução deste contrato.

CLÁUSULA SEXTA - DO FORO
6.1. Para dirimir quaisquer controvérsias oriundas do presente contrato, as partes elegem o foro da comarca de [Sua Cidade/UF].

[Cidade/UF], [Data].

__________________________________________
[NOME DA SUA SOFTHOUSE]

__________________________________________
[NOME DO CLIENTE]`);
const showModalModules = ref(false)
const addModules = (budget) => {
    form.id = budget.id
    form.modules = budget.modules
    showModalModules.value = true
}
</script>
<!-- cod. vai ser o id mas um math.random salvo no bando de dados -->
<!-- No modal da criação do LR colocar checkbox selecionar a necessidade de designer, hospedagem, dominio, etc -->
<!-- campos dos modulos nome do modulo, detalhamento, horas previstas trabalhadas, codigo do modulo, valor do modulo  -->

<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
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

                <!-- <Pagination :pagination="props.pagination" @page-change="goToPage" /> -->
                <div class="overflow-x-auto mt-2">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow">
                            <table class="min-w-full table-fixed">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Cód.
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Cliente / Conhecido por 
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Projeto </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            Situação</th>
                                        <!-- <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 whitespace-nowrap text-end">
                                            Valor
                                        </th> -->
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 text-white">
                                    <tr v-for="budget in props.budgets.data"
                                        :key="budget.id"
                                        class="hover:bg-gray-100 dark:hover:bg-gray-700">

                                        <td class="p-2 text-center font-semibold">
                                            {{ budget.id }}
                                        </td>

                                        <td class="p-2 text-sm">
                                                {{ budget.client?.name }} / {{ budget.client?.codename }}
                                        </td>

                                        <td class="p-2 uppercase text-sm">
                                            {{ budget.project_name }}
                                        </td>

                                        <td class="p-2 text-sm">
                                            {{ translaterServiceType(budget.service_type) }}
                                        </td>

                                        <td class="p-2 text-center w-64">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-semibold"
                                                :class="{
                                                'bg-yellow-100 text-yellow-800': budget.status === 'pending',
                                                'bg-blue-100 text-blue-800': budget.status === 'aberto',
                                                'bg-red-100 text-red-800': budget.status === 'canceled'
                                                }"
                                            >
                                                {{ {
                                                pending: 'Pendente',
                                                aberto: 'Aprovado',
                                                canceled: 'Cancelado'
                                                }[budget.status] }}
                                            </span>
                                        </td>

                                        <td class="grid grid-cols-2 gap-2 my-2 w-24 text-right">
                                            <button
                                                @click="addModules(budget)"
                                                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 items-center">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button
                                                @click="update(budget)"
                                                class="px-3 py-1 text-sm bg-primary-600 text-white rounded hover:bg-primary-700">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button
                                                @click="confirmDestroy(budget)"
                                                class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button
                                                type="button" @click="getContract(budget)"
                                                class="px-3 py-1 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                                <i class="fa-solid fa-file-signature "></i>
                                            </button>
                                        </td>

                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <!-- <Pagination :pagination="props.pagination" @page-change="goToPage" /> -->

        </div>
    </template>
  </AppLayout>
    <DialogModal :show="showModal" @close="showModal = false">
        <template #title>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold mb-4">{{ form.id ? 'Projeto de n. ' + form.id
                    : 'Cadastro de Projeto' }}</h2>
                <select v-model="form.status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-[200px] p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                    <option value="pending">Pendente</option>
                    <option value="aberto">Aprovado</option>
                    <option value="canceled">Cancelado</option>
                </select>
            </div>
        </template>
        <template #content>
            <form ref="formHtml" id="budget-form" @submit.prevent="submit">
                <div class="space-y-2 overflow-y-auto max-h-[calc(100vh-200px)]">

                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold cursor-pointer flex items-center gap-1"
                            @click="collapsed.client = !collapsed.client">
                            <i class="fa-solid me-1"
                                :class="collapsed.client ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            Cliente
                        </h3>
                        <button @click="showModalSearch = true"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-2 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                            type="button">
                            <i class="fa-solid fa-plus me-1"></i>
                            Adicionar
                        </button>
                    </div>

                    <div v-if="clients.data && !collapsed.client" class="grid grid-cols-1 sm:grid-cols-[2fr_1fr] gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Nome
                            </label>
                            <input type="text" v-model="selectedClient.name" disabled
                                class="uppercase bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                CPF / CNPJ
                            </label>
                            <input disabled type="tel" v-model="selectedClient.document"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                    </div>

                    <hr class="my-6 dark:border-gray-700" />
                    <h3 class="text-lg font-semibold mb-4 cursor-pointer"
                        @click="collapsed.description = !collapsed.description">
                        <i class="fa-solid me-1"
                            :class="collapsed.description ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        Descrição
                    </h3>

                    <div v-if="!collapsed.description">
                        <div class="grid grid-cols-2  gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                    Nome do Projeto
                                </label>
                                <input type="text" v-model="form.project_name"
                                    class=" bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                    Tipo de serviço
                                </label>
                                <select v-model="form.service_type"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option value="Maintenance">Manutenção</option>
                                    <option value="Create System">Criar sistema</option>
                                    <option value="Add Module">Adicionar Modulo</option>
                                </select>
                            </div>
                        </div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200 mt-2">
                            Descrição
                        </label>
                        <textarea type="text" v-model="form.note"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            rows="6"></textarea>
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
                <button
                    type="submit"
                    form="budget-form"
                    :disabled="processing"
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"

                    >
                    {{ form.id ? 'Atualizar' : 'Salvar' }}
                </button>
            </div>
        </template>
    </DialogModal>
    <DialogModal :show="showModalSearch" @close="showModalSearch = false">
        <template #title>
            <h2 class="text-xl font-semibold mb-4">Pesquisar Cliente</h2>
        </template>
        <template #content>
            <form @submit.prevent="searchClient">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <input type="text" v-model="input_search_client"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        <button
                            class="px-3 py-2 ms-2 whitespace-nowrap text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                            type="submit">
                            <i class="fa-solid fa-search me-1"></i>
                            Buscar
                        </button>
                    </div>
                    <hr class="dark:border-gray-700" />
                </div>
            </form>
            <div class="overflow-x-auto" v-if="clients.data.length">
                <table class="min-w-full table-fixed whitespace-nowrap text-dark dark:text-white">
                    <tr v-for="{ id, name, document, codename }, index in clients.data" :key="id"
                        @click="setClient(index)"
                        class="border-t dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900">
                        <td class="p-1 py-2 w-3/4">{{ name }}</td>
                        <td> {{ codename }}</td>
                        <td class="text-end">{{ document }}</td>
                    </tr>
                </table>
                <Pagination v-if="clients.last_page > 1" :pagination="clients" @page-change="searchClient" />
            </div>
            <hr class="my-0 dark:border-gray-700" />
        </template>
        <template #footer>
            <div class="flex w-full items-center justify-between">
                <button @click="showModalSearch = false"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                    type="button">
                    Cancelar
                </button>
            </div>
        </template>
    </DialogModal>
    <ConfirmationModal :show="showConfirm" @close="showConfirm = false" @confirm="destroy(form.id)">
        <template #title>
            Excluir Orçamento
        </template>
        <template #content>
            Deseja realmente excluir <strong class="text-red-600">{{ form.id }}</strong>?
        </template>
    </ConfirmationModal>
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

    <DialogModal :show="showModalModules" @close="showModalModules = false">
        <template #title>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold mb-4">Criar Módulos</h2>
            </div>
        </template>

        <template #content>
            <form @submit.prevent="createModules">
                <div class="space-y-2 overflow-y-auto max-h-[calc(100vh-200px)]">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Codigo
                            </label>
                            <input type="text" v-model="form.module_code"
                                class=" bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Nome
                            </label>
                            <input type="text" v-model="form.module_name"
                                class=" bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            Descrição
                        </label>
                        <textarea v-model="form.module_description" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                                focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                                dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            rows="20"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Quantidade de horas previstas
                            </label>
                            <input type="text" v-model="form.module_hours" placeholder="2hr30min = 2,5"
                                class=" bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                                Valor(R$)
                            </label>
                            <input type="text" v-model="form.module_value"
                                class=" bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                        </div>
                    </div>

                </div>
            </form>
        </template>

        <template #footer>
            <div class="flex w-full items-center justify-between">
                <button @click="showModalModules = false"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                    type="button">
                    Cancelar
                </button>
                <button
                    type="submit"
                    form="budget-form"
                    :disabled="processing"
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"

                    >
                    {{ form.id ? 'Atualizar' : 'Salvar' }}
                </button>
            </div>
        </template>

    </DialogModal>
    <!-- <Alert v-if="alert.show" :message="alert.message" @remove="alert.show = false" :type="alert.type" /> -->
</template>
