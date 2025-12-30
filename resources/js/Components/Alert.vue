<template>
  <transition name="fade" @after-leave="$emit('remove')">
    <div style="z-index: 9999;" v-if="visible" class="fixed bottom-4 right-4 text-white px-4 py-2 rounded-lg shadow-lg"
      :class="type === 'success' ? 'bg-green-700' : 'bg-red-600'">
      {{ message }}
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  message: String,
  type: String
});
const visible = ref(true);

onMounted(() => {
  setTimeout(() => {
    visible.value = false;
  }, 5000);
});
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
