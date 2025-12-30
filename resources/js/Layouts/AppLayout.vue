<script setup>
import { Head } from '@inertiajs/vue3';
import Topbar from '@/Components/Topbar.vue';
import SideMenu from '@/Components/SideMenu.vue';
import { ref } from 'vue';

defineProps({
    title: String,
    has_menu: {
        type: Boolean,
        default: true
    },
});

const showSidebar = ref(false);

const toggleSidebar = show => {
    showSidebar.value = show;
};

</script>

<template>

    <Head :title="title" />
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <Topbar @toggle-sidebar="toggleSidebar">
            <slot name="header" />
        </Topbar>
        <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">
            <SideMenu v-if="has_menu" />
            <div class="relative w-full h-full overflow-y-auto bg-gray-50 dark:bg-gray-900"
                :class="{ 'sm:ml-[205px]': has_menu }">
                <main>
                    <slot name="content" />
                </main>
            </div>
        </div>
    </div>
</template>