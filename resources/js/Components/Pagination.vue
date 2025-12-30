<template>
  <div class="flex flex-wrap justify-between items-center p-2">
    <span class="text-sm w-auto text-gray-500 dark:text-gray-400 ms-1">Total de Registros:
      <strong>{{ pagination?.total }}</strong>
    </span>
    <nav class="flex items-center w-auto justify-end" aria-label="Table navigation">
      <ul class="inline-flex -space-x-px text-sm">
        <li>
          <button :disabled="pagination?.current_page === 1" @click="changePage(1)"
            class="flex items-center justify-center me-[2px] px-3 h-8 ml-0 bg-gray-200 hover:bg-gray-300 leading-tight dark:text-gray-100 dark:bg-gray-600 rounded-l-lg dark:hover:bg-gray-500 dark:hover:text-gray-100 disabled:opacity-50">
            Primeira
          </button>
        </li>
        <li v-if="pagination?.last_page > 5 && pagination?.current_page > 3">
          <span
            class="flex items-center justify-center px-3 h-8 leading-tight me-[2px] bg-gray-100 dark:text-white dark:bg-gray-700">...</span>
        </li>
        <li
          v-for="page in Array.from({ length: (pagination?.last_page > 5 ? 5 : pagination?.last_page) }, (_, index) => index + (pagination?.current_page > 3 ? (pagination?.current_page < pagination?.last_page - 2 ? pagination?.current_page - 2 : pagination?.last_page - 4) : 1))"
          :key="page">
          <button @click="changePage(page)" :class="[
            'flex items-center justify-center px-3 h-8 leading-tight me-[2px]',
            page === pagination?.current_page
              ? 'bg-gray-300 dark:text-white dark:bg-gray-500'
              : 'bg-gray-200 hover:bg-gray-300 dark:text-gray-100 dark:bg-gray-600 dark:hover:bg-gray-500 dark:hover:text-gray-100'
          ]">
            {{ page }}
          </button>
        </li>
        <li v-if="pagination?.last_page > 5 && pagination?.current_page < pagination?.last_page - 2">
          <span
            class="flex items-center justify-center px-3 h-8 leading-tight me-[2px] bg-gray-100 dark:text-white dark:bg-gray-700">...</span>
        </li>
        <li>
          <button :disabled="pagination?.current_page === pagination?.last_page"
            @click="changePage(pagination?.last_page)"
            class="flex items-center justify-center px-3 h-8 leading-tight bg-gray-200 hover:bg-gray-300 dark:text-gray-100 dark:bg-gray-600 rounded-r-lg dark:hover:bg-gray-500 dark:hover:text-gray-100 disabled:opacity-50">
            Última
          </button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
const props = defineProps({
  pagination: Object,
});

const emit = defineEmits(["page-change"]);

const changePage = (page) => {
  if (page >= 1 && page <= props.pagination?.last_page) {
    emit("page-change", page);
  }
};
</script>