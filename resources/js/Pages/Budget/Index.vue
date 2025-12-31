<script setup>
import { ref, reactive } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import DialogModal from '@/Components/DialogModal.vue'
import { router } from '@inertiajs/vue3'
import Alert from '@/Components/Alert.vue'
import { defineProps } from 'vue'
import Pagination from '@/Components/Pagination.vue'


const props = defineProps({
  budgets: Object
})

const title = 'Diagnósticos'
const showModal = ref(false)
const showModalSearch = ref(false)
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
  client: false,
  description: false,
  details: false,
})

// cliente selecionado
const selectedClient = reactive({
  name: '',
  document: '',
})

// lista fake por enquanto (antes do banco)
const clients = reactive({
  data: [],
  last_page: 1,
})

// métodos base (stubs)
const search = () => {}
const reset = () => {
  input_search.value = ''
  select_status.value = 'active'
}

const submit = () => {
  processing.value = true

  if (form.id) {
    // UPDATE
    router.put(route('budgets.update', form.id), form, {
      onFinish: () => processing.value = false,
      onSuccess: () => {
        showModal.value = false
      }
    })
  } else {
    // CREATE
    router.post(route('budgets.store'), form, {
      onFinish: () => processing.value = false,
      onSuccess: () => {
        showModal.value = false
      }
    })
  }
}
const resetForm = () => {
  form.id = null
  form.client_id = null
  form.status = 'pending'
  form.project_name = ''
  form.service_type = ''
  form.note = ''

  selectedClient.name = ''
  selectedClient.document = ''
}


const update = (budget) => {
  form.id = budget.id
  form.client_id = budget.client_id
  form.status = budget.status
  form.project_name = budget.project_name
  form.service_type = budget.service_type
  form.note = budget.note

  selectedClient.name = budget.client?.name || ''
  selectedClient.document = budget.client?.document || ''

  showModal.value = true
}

const setClient = (index) => {
  const client = clients.data[index]

  selectedClient.id = client.id
  selectedClient.name = client.name
  selectedClient.document = client.document

  form.client_id = client.id // 👈 ESSENCIAL

  showModalSearch.value = false
}


const searchClient = async (page = 1) => {
  try {
    const response = await axios.get(route('clients.index'), {
      params: {
        search: input_search_client.value,
        page: page,
        json: true, // 👈 ISSO AQUI É O PULO DO GATO
      }
    })

    clients.data = response.data.data
    clients.last_page = response.data.last_page
  } catch (error) {
    console.error('Erro ao buscar clientes', error)
  }
}
</script>


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
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 text-white">
                                    <tr v-for="budget in props.budgets.data"
                                        :key="budget.id"
                                        class="hover:bg-gray-100 dark:hover:bg-gray-700">

                                        <!-- ID -->
                                        <td class="p-2 text-center font-semibold">
                                            {{ budget.id }}
                                        </td>

                                        <!-- Cliente + Projeto -->
                                        <td class="p-2 text-sm">
                                                {{ budget.client?.name }} / {{ budget.client?.codename }}
                                        </td>

                                        <!-- Codename -->
                                        <td class="p-2 uppercase text-sm">
                                            {{ budget.project_name }}
                                        </td>

                                        <!-- Tipo de serviço -->
                                        <td class="p-2 text-sm">
                                            {{ budget.service_type }}
                                        </td>

                                        <!-- Status -->
                                        <td class="p-2 text-center">
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

                                        <!-- Ações -->
                                        <td class="p-2 text-end space-x-2">
                                            <button
                                                @click="update(budget)"
                                                class="px-3 py-1 text-sm bg-primary-600 text-white rounded hover:bg-primary-700">
                                                <i class="fas fa-pen"></i>
                                            </button>

                                            <button
                                                @click="deleteBudget(budget.id)"
                                                class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                                <i class="fas fa-trash"></i>
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
            <form ref="formHtml" @submit.prevent="submit">
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
                <button @click="formHtml.requestSubmit()"
                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                    type="button">
                    Salvar
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
    <!-- <Alert v-if="alert.show" :message="alert.message" @remove="alert.show = false" :type="alert.type" /> -->
</template>
