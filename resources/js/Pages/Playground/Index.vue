<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import DialogModal from '@/Components/DialogModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Alert from '@/Components/Alert.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import { onMounted } from 'vue';

const title = 'Parques';

const props = defineProps({
  playground: Array,
  pagination: Object,
  items: Array,
});

const alert = ref({
  show: false,
  message: '',
  type: 'success',
});

const showModal = ref(false);
const showModalSearchParts = ref(false);
const processing = ref(false);
const input_search_part = ref('');
const search_parts = ref([]);
const showConfirm = ref(false);
const formHtml = ref(null);
const input_search = ref(route().params.name?.lk || '');
const showConfirmItem = ref(false);
const selectedPlaygroundId = ref(null);
const playgroundLocal = ref([...props.playground]);
const picture = ref(null);
const showDuplicate = ref(false);
const is_consultant = usePage().props.auth.user.role === 'Consultant';
const is_admin = ['Board', 'Manager'].includes(usePage().props.auth.user.role);

const form = ref({
  id: null,
  name: null,
  code: null,
  picture: null,
  items: [],
  quantity: 1,
  discount: 0,
  discount_factory: 0
});

const item = ref({
  id: null,
  name: null,
  code: null,
  picture: null,
  amount: null,
});

const goToPage = (page) => {
  router.get(route('playgrounds.index', {
    page,
    ...(input_search.value ? { name: { lk: input_search.value } } : {}),
    order_by: { asc: 'name' },
  }));
};

const search = () => {
  router.get(route("playgrounds.index"), {
    name: { lk: input_search.value },
    order_by: { asc: 'name' },
  });
};

const submit = async () => {
  if (processing.value) return;
  processing.value = true;

  try {
    const formData = new FormData();
    if (form.value.id) formData.append('_method', 'PUT');
    formData.append('name', form.value.name);
    formData.append('code', form.value.code);
    formData.append('discount', form.value.discount);
    formData.append('discount_factory', form.value.discount_factory);

    const url = form.value.id
      ? route('playgrounds.update', form.value.id)
      : route('playgrounds.store');

    const response = await axios.post(url, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    const playgroundId = response.data.id;

    for (const img of coverImages.value) {
      const imageData = new FormData();
      if (img.file) {
        imageData.append('file', img.file);
      }
      imageData.append('is_thumb', img.selected ? 1 : 0);

      const imgUrl = img.id && !img.file
        ? route('playgrounds.images.updateThumb', { playground: playgroundId, image: img.id })
        : route('playgrounds.images.store', playgroundId);

      await axios.post(imgUrl, imageData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }

    alert.value = { show: true, message: 'Playground salvo com sucesso!', type: 'success' };
    showModal.value = false;

  } catch (error) {
    alert.value = { show: true, message: error.response?.data?.message ?? error.message, type: 'error' };
  } finally {
    processing.value = false;
  }
};


const reset = (id) => {
  form.value = {
    id: id,
    name: '',
    picture: null,
    code: '',
    items: [],
    quantity: 1,
    discount: 0,
    discount_factory: 0,
  };
  imagePreview.value = null;
  fileName.value = '';
  picture.value = null;
};


const update = async (index) => {
  const playgroundData = playgroundLocal.value[index]?.data;
  if (!playgroundData) return;

  form.value = {
    id: playgroundData.id,
    name: playgroundData.name,
    code: playgroundData.code,
    discount: playgroundData.discount || 0,
    discount_factory: playgroundData.discount_factory || 0,
    cover_image: null,
  };

  coverImages.value = (playgroundData.images || []).map(img => ({
    id: img.id,
    url: img.url || `/storage/images/playgrounds/${img.filename}`,
    name: img.filename || 'imagem',
    selected: !!img.is_thumb,
    file: null
  }));

  const thumb = coverImages.value.find(img => img.selected);
  form.value.cover_image = thumb ? thumb.id : null;

  showModal.value = true;
};



const confirmDestroy = (id) => {
  const playground = playgroundLocal.value.find(st => st.data.id === id);
  if (playground) {
    form.value = JSON.parse(JSON.stringify(playground.data));
    showConfirm.value = true;
  }
};

const confirmDestroyItem = (id, index, idx) => {

  if (!id) {
    alert.value = {
      show: true,
      message: 'ID do item não encontrado para exclusão.',
      type: 'error',
    };
    return;
  }

  const playground = props.playground[index];
  const selectedItem = playground.data.items[idx];

  item.value = {
    ...selectedItem,
    playground_id: playground.data.id,
  };

  showConfirmItem.value = true;
};

const destroy = id => {
  showConfirm.value = false;
  const index = playgroundLocal.value.findIndex(({ data: { id: _id } }) => _id === id);

  axios.delete(route('playgrounds.destroy', id))
    .then(response => response.data)
    .then(() => {
      playgroundLocal.value.splice(index, 1);
      props.pagination.total--;
    })
};

const destroyItem = (pivot_id) => {
  if (!pivot_id) {
    alert.value = {
      show: true,
      message: 'ID do item não encontrado para exclusão.',
      type: 'error',
    };
    return;
  }
  router.delete(route('playgrounds.items.destroy', { pivot: pivot_id }), {
    onSuccess: () => {
      alert.value = {
        show: true,
        message: 'Item removido!',
        type: 'success',
      };
      showConfirmItem.value = false;
      const playgroundIndex = playgroundLocal.value.findIndex(
        ({ data: { id } }) => id === item.value.playground_id
      );
      const itemIndex = playgroundLocal.value[playgroundIndex].data.items.findIndex(
        ({ id: _id }) => _id === item.value.id
      );
      playgroundLocal.value[playgroundIndex].data.items.splice(itemIndex, 1);
    },
  });
};

const searchParts = async (search) => {
  search_parts.value = props.items.filter(items =>
    (new RegExp(search, 'i')).test(items.name)
  ) || [];
};

const collapsed = ref({
  parts: true,
});

const submitItem = (item) => {
  if (!selectedPlaygroundId.value) {
    return;
  }

  const partToAdd = {
    id: item.id,
    code: item.code,
    name: item.name,
    amount: item.amount,
    playground_id: selectedPlaygroundId.value,
    discount: item.discount,
  };

  axios.post(route('playgrounds.items.store', selectedPlaygroundId.value), partToAdd)
    .then(response => {
      const data = response.data;

      const index = playgroundLocal.value.findIndex(
        ({ data: { id } }) => id === selectedPlaygroundId.value
      );

      if (index !== -1) {
        const current = playgroundLocal.value[index];

        const updatedItems = current.data.items ? [...current.data.items, data] : [data];

        const updated = {
          ...current,
          data: {
            ...current.data,
            items: updatedItems,
          },
        };

        playgroundLocal.value.splice(index, 1, updated);
      }

      alert.value = {
        show: true,
        message: 'Peça salva com sucesso',
        type: 'success',
      };
      showModalSearchParts.value = false;
      selectedPlaygroundId.value = null;
    })
    .catch(error => {
      alert.value = {
        show: true,
        message: 'Erro ao salvar a peça',
        type: 'error',
      };
    });
};

const imagePreview = ref(null);
const fileName = ref('');

const shouldDeleteImage = ref(false);
const quantityTimeout = ref(null);
const pendingUpdates = ref(new Map());

const updateQuantity = (item) => {
  pendingUpdates.value.set(item.pivot_id, {
    pivot_id: item.pivot_id,
    quantity: item.quantity
  });

  if (quantityTimeout.value) clearTimeout(quantityTimeout.value);

  quantityTimeout.value = setTimeout(async () => {
    try {
      const updates = Array.from(pendingUpdates.value.values());

      await axios.put('/playground-items/batch-update', {
        updates: updates
      });

      alert.value = {
        show: true,
        message: `${updates.length} item(ns) atualizado(s) com sucesso`,
        type: 'success',
      };

      pendingUpdates.value.clear();

    } catch (error) {
      alert.value = {
        show: true,
        message: 'Erro ao atualizar quantidades',
        type: 'error',
      };
    }
  }, 1500);
};

const increase = (item) => {
  if (!item.quantity) item.quantity = 1;
  item.quantity++;
  updateQuantity(item);
};

const decrease = (item) => {
  if (!item.quantity) item.quantity = 1;
  if (item.quantity > 1) {
    item.quantity--;
    updateQuantity(item);
  }
};

const brlFormatter = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});

const formatCurrency = (cents) => {
  if (cents === null || cents === undefined) return '-';
  const n = typeof cents === 'number' ? cents : Number(cents);
  if (!Number.isFinite(n)) return '-';
  return brlFormatter.format(n / 100);
};

const confirmDuplicate = index => {
  form.value = playgroundLocal.value[index].data;
  showDuplicate.value = true;
};

const duplicate = id => {
  showDuplicate.value = false;

  axios.get(route('playgrounds.duplicate', id))
    .then(() => location.reload())
    .catch(err => console.error('Erro ao duplicar orçamento', err));
};


const calcularTotalParque = (items = [], discount = 0) => {
  const total = (items || []).reduce((sum, item) => {
    const valor = parseFloat(
      (item.amount || '0').toString().replace(/[^\d,]/g, '').replace(',', '.')
    ) || 0;

    const quantidade = item.quantity;

    return sum + (valor * quantidade);
  }, 0);

  return total - (parseFloat(discount) || 0);
};

const exportCSV = async () => {
  try {
    const response = await axios.get('/playgrounds?json=1&limit=999999');

    const items = response.data;

    const headers = "Código;Nome;Valor;Valor Fábrica;Desconto;Desconto Fábrica";

    const csvContent =
      `${headers}\n${items.map((p) => {

        const amount = (p.items ?? []).reduce((sum, item) => {
          return sum + (item.amount * item.quantity);
        }, 0);

        const amountFabric = (p.items ?? []).reduce((sum, item) => {
          return sum + (item.amountFabric * item.quantity);
        }, 0);

        return `${p.code};${p.name};${amount};${amountFabric};${p.discount ?? ''};${p.discount_factory ?? ''}`;

      }).join('\n')}`;

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');

    link.href = URL.createObjectURL(blob);
    link.download = `parques_${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();

    URL.revokeObjectURL(link.href);
    link.remove();

    alert.value = { show: true, message: 'CSV exportado com sucesso!', type: 'success' };
  } catch (error) {
    alert.value = { show: true, message: 'Erro ao exportar CSV', type: 'error' };
    console.error(error);
  }
};


const coverImages = ref([]);
const onFileChange = (event) => {
  const files = Array.from(event.target.files);

  files.forEach(file => {
    coverImages.value.push({
      id: Date.now() + Math.random(),
      file,
      url: URL.createObjectURL(file),
      name: file.name,
    });
  });
};
const removeImage = async (id) => {

  const img = coverImages.value.find(i => i.id === id);
  if (!img) return;

  if (img.file) {
    coverImages.value = coverImages.value.filter(i => i.id !== id);
    return;
  }

  try {
    await axios.delete(route('playgrounds.images.destroy', {
      playground: form.value.id,
      image: img.id
    }));

    coverImages.value = coverImages.value.filter(i => i.id !== id);

    alert.value = { show: true, message: 'Imagem removida com sucesso!', type: 'success' };


  } catch (error) {
    alert.value = { show: true, message: 'Erro ao remover imagem', type: 'error' };
  }

};

const openModal = (index) => {
  update(index);
  showModal.value = true;
};

const selectAsCover = (id) => {
  coverImages.value = coverImages.value.map(img => ({
    ...img,
    selected: img.id === id
  }));

  form.value.cover_image = id;
};
const thumbsLoaded = ref(false);

const loadThumbs = async () => {
  try {
    const { data } = await axios.get('/debug/playground/{id}');
    playgroundLocal.value.forEach(pg => {
      const thumbs = data.filter(img => img.playground_id === pg.data.id)
                        .map(img => ({
                            ...img,
                            thumb_url: img.is_thumb 
                              ? `/storage/images/playgrounds/${img.filename}`
                              : null
                        }));

      // Se não houver thumb definida, usa a picture antiga do playground
      if (!thumbs.find(img => img.thumb_url) && pg.data.picture) {
        thumbs.push({
          id: 'old-' + pg.data.id,
          url: `/storage/${pg.data.picture}`,
          name: 'Imagem antiga',
          selected: true,
          thumb_url: `/storage/${pg.data.picture}`,
        });
      }

      pg.data.images = thumbs;
    });


    thumbsLoaded.value = true;
  } catch (error) {
    console.error('Erro ao carregar thumbs:', error);
  }
};

onMounted(() => {
  loadThumbs();
});

</script>

<template>
  <AppLayout :title="title">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ title }}</h2>
    </template>

    <template #content>
      <div class="flex flex-col mt-2">
        <div class="flex flex-col sm:flex-row gap-2 px-2 mt-1">
          <form @submit.prevent="search" method="GET" class="w-full sm:w-auto">
            <input type="text" v-model="input_search" name="search" placeholder="Buscar"
              class="p-2.5 w-full sm:w-64 xl:w-96 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 text-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
          </form>
          <div class="flex w-full items-center justify-between">
            <button @click="search" type="button"
              class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:focus:ring-primary-800">
              <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </button>
            <div class="flex gap-2">
              <button @click="exportCSV" type="button"
                class="flex items-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                <i class="fa-solid fa-file-csv me-2 text-lg"></i>
                Exportar
              </button>

              <button @click="reset(); showModal = true"
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
              <table class="min-w-full table-fixed">
                <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                    <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 w-20">
                      Cód.
                    </th>
                    <th scope="col"
                      class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 w-32">
                      Imagem
                    </th>
                    <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                      Nome
                    </th>
                    <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 w-32 p">
                      Valor
                    </th>
                    <th v-if="is_admin"
                      class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400 w-32 p">
                      Valor Fábrica
                    </th>
                    <th class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400"></th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                  <template v-for="{ data }, index in playgroundLocal" :key="data.id">
                    <tr :class="{ 'bg-gray-50 dark:bg-gray-900': index % 2 === 0 }">

                      <td class="p-4 py-2 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                        <div class="text-base font-semibold text-gray-900 dark:text-white">
                          {{ data.code }}</div>
                      </td>
                      <td class="p-4">
  <template v-for="img in data.images" :key="img.id">
    <img v-if="img.thumb_url" :src="img.thumb_url" class="h-12 w-auto rounded" />
  </template>
</td>

                      <td
                        class="cursor-pointer p-4 py-3 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                        <div class="flex items-center"
                          @click="playgroundLocal[index].collapsed = !playgroundLocal[index]?.collapsed">
                          <i :class="[
                            'fa-solid me-2 text-yellow-400',
                            playgroundLocal[index]?.collapsed ? 'fa-folder-open' : 'fa-folder',
                          ]"></i>
                          <div class="text-base font-semibold text-gray-900 dark:text-white">{{ data.name }}
                          </div>
                        </div>
                      </td>
                      <td
                        class="p-4 py-2 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 text-end align-top">
                        <div class="text-base font-semibold text-gray-900 dark:text-white">
                          {{ formatCurrency(calcularTotalParque(data.items, data.discount)) }}
                        </div>
                      </td>
                      <td v-if="is_admin"
                        class="p-4 py-2 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 text-end align-top">
                        <div class="text-base font-semibold text-gray-900 dark:text-white">
                          {{formatCurrency(data.items?.reduce((acc, { amountFabric, quantity }) => acc + (amountFabric *
                            quantity), 0))}}
                        </div>
                      </td>
                      <td class="w-28 align-top p-4 py-2 space-x-2 whitespace-nowrap text-end">

                        <button @click="selectedPlaygroundId = data.id; showModalSearchParts = true"
                          class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-900">
                          <i class="fas fa-plus me-2"></i>
                        </button>

                        <button @click="openModal(index)" type="button"
                          class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                          <i class="fas fa-pen me-2 "></i>
                        </button>

                        <button v-if="!is_consultant" @click="confirmDestroy(data.id)" type="button"
                          class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                          <i class="fas fa-trash me-2"></i>
                        </button>
                        <button @click="confirmDuplicate(index)" type="button"
                          class="w-7 inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-yellow-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                          <i class="fa-solid fa-clone"></i>
                        </button>
                      </td>
                    </tr>
                    <tr :class="{ 'bg-gray-50 dark:bg-gray-900': index % 2 === 0 }">

                      <td colspan="6" v-if="playgroundLocal[index]?.collapsed"
                        class="cursor-pointer p-4 py-3 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                        <div>
                          <div v-for="(item, idx) in data.items" :key="item.id"
                            class="border-t border-gray-200 dark:border-gray-700 flex text-end items-center"
                            :class="{ 'bg-gray-100 dark:bg-gray-700': idx % 2 === 0 }">
                            <div class="flex text-start text-sm font-semibold text-gray-900 dark:text-white ps-3 w-16">
                              {{ item.code }}
                            </div>
                            <div class="p-2">
                              <img v-if="item.picture" :alt="item.picture" :src="`/storage/${item.picture}`"
                                class="h-12 w-12 rounded" />
                            </div>
                            <div
                              class="flex-1 ms-4 text-start text-sm font-semibold text-gray-900 dark:text-white ps-3">
                              {{ item.name }}
                            </div>

                            <div
                              class="flex text-start text-sm font-semibold text-gray-900 dark:text-white ps-3 px-3 w-40 ite">
                              <div class="inline-flex items-center py-1 ">
                                <button @click="decrease(item)"
                                  class="inline-flex items-center px-2 mx-1 pe-0 py-2 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                  <i class="fas fa-minus me-2"></i>
                                </button>
                                <input type="text" v-model="item.quantity" @input="updateQuantity(item)"
                                  class="w-16 text-center rounded border-0 bg-gray-800 py-1 " />

                                <button @click="increase(item)"
                                  class="inline-flex items-center px-2 mx-1 pe-0 py-2 text-sm font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-900">
                                  <i class="fas fa-plus me-2"></i>
                                </button>
                              </div>
                            </div>

                            <div class="text-sm font-semibold text-gray-900 dark:text-white me-3 w-24">
                              {{ formatCurrency((item.quantity || 1) * item.amount) }}
                            </div>

                            <button @click="confirmDestroyItem(item.id, index, idx); showConfirmItem = true"
                              type="button"
                              class=" me-2 inline-flex items-center px-1 pe-0 py-1 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                              <i class="fas fa-trash me-2 ms-1 text-xs "></i>
                            </button>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </template>
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
    <template #title>Excluir Parque</template>
    <template #content>
      Deseja realmente excluir <strong class="text-red-600">{{ form.name }}</strong>?
    </template>
  </ConfirmationModal>

  <ConfirmationModal :show="showConfirmItem" @close="showConfirmItem = false" @confirm="destroyItem(item.pivot_id)">
    <template #title>Excluir Item</template>
    <template #content>
      Deseja realmente excluir <strong class="text-red-600">{{ item.name }}</strong>?
    </template>
  </ConfirmationModal>
  <ConfirmationModal :show="showDuplicate" @close="showDuplicate = false" @confirm="duplicate(form.id)">
    <template #title>
      Duplicar Parque
    </template>
    <template #content>
      Deseja realmente duplicar o parque n. <strong class="text-red-600">{{ form.id }}</strong>?
    </template>
  </ConfirmationModal>

  <DialogModal :show="showModal" @close="showModal = false">
    <template #title>
      <h2 class="text-xl font-semibold mb-4">Cadastro de Parque</h2>
    </template>
    <template #content>
      <form ref="formHtml" @submit.prevent="submit">
        <div class="grid grid-cols-1 sm:grid-cols-[1fr_4fr_1fr_1fr] gap-4">
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
              Código
            </label>
            <input type="text" v-model="form.code"
              class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
              required />
          </div>
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
              Nome
            </label>
            <input type="text" v-model="form.name"
              class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
              required />
          </div>
          <div v-if="is_admin">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
              Desconto
            </label>
            <CurrencyInput type="text" v-model="form.discount"
              class=" bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 text-end" />
          </div>

          <div v-if="is_admin">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
              Desconto Fábrica
            </label>
            <CurrencyInput type="text" v-model="form.discount_factory"
              class=" bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 text-end" />
          </div>
        </div>
        <div class="mt-2">
          <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
            Imagem
          </label>

          <div class="relative">

            <!-- INPUT SEMPRE VISÍVEL -->
            <input type="file" multiple accept="image/*" @change="onFileChange" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-600 file:text-white
                hover:file:bg-blue-700" />

            <div v-if="coverImages.length" class="mt-3 grid grid-cols-3 gap-3">

              <div v-for="img in coverImages" :key="img.id" class="flex flex-col items-center">

                <img :src="img.url" class="w-32 h-32 object-contain rounded shadow bg-gray-800" />

                <button @click="selectAsCover(img.id)" type="button"
                  class="px-2 py-1 my-2 text-xs rounded-full flex items-center gap-1" :class="img.selected
                    ? 'bg-green-600 text-white'
                    : 'bg-gray-600 text-white hover:bg-gray-700'">
                  <i v-if="img.selected" class="fa-solid fa-check"></i>
                  {{ img.selected ? "Selecionado" : "Selecionar" }}
                </button>
                <button @click="removeImage(img.id)"
                  class="px-2 py-1 my-3 text-xs rounded-full bg-red-600 text-white hover:bg-red-700">
                  <i class="fa-solid fa-trash"></i>
                </button>

              </div>

            </div>

          </div>
        </div>
      </form>
    </template>
    <template #footer>
      <div class="flex w-full items-center justify-between">
        <button @click="showModal = false" type="button"
          class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
          Cancelar
        </button>
        <button type="submit"
          class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
          @click="formHtml?.requestSubmit()">
          Salvar
        </button>
      </div>
    </template>
  </DialogModal>
  <DialogModal :show="showModalSearchParts" @close="showModalSearchParts = false">
    <template #title>
      <h2 class="text-xl font-semibold mb-4">Pesquisar Peças</h2>
    </template>

    <template #content>
      <div class="space-y-2">
        <div class="flex items-center justify-between gap-2">
          <input type="text" v-model="input_search_part" @input="searchParts($event.target.value)"
            placeholder="Buscar por nome"
            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" />
          <button @click="searchParts(input_search_part)"
            class="px-3 py-2 ms-2 whitespace-nowrap text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
            type="submit">
            <i class="fa-solid fa-search me-1"></i>
            Buscar
          </button>

        </div>
        <hr class="dark:border-gray-700" />
      </div>
      <div class="overflow-y-auto max-h-[calc(100vh-250px)]" v-if="search_parts.length">
        <table class="min-w-full table-fixed whitespace-nowrap text-dark dark:text-white">
          <tr v-for="item in search_parts" :key="item.id" @click="submitItem(item)"
            class="border-t dark:border-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-900">
            <td class="p-1 py-2 w-24">{{ item.code }}</td>
            <td class="p-1 py-2">
              <img v-if="item.picture" :alt="item.picture" :src="`/storage/${item.picture}`"
                class="h-12 w-12 rounded" />
            </td>
            <td class="p-1 py-2">{{ item.name }}</td>
          </tr>
        </table>
      </div>
    </template>

    <template #footer>
      <div class="flex w-full items-center justify-between">
        <button @click="showModalSearchParts = false"
          class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
          type="button">
          Cancelar
        </button>

      </div>
    </template>
  </DialogModal>

  <Alert v-if="alert.show" :message="alert.message" @remove="alert.show = false" :type="alert.type" />
</template>
