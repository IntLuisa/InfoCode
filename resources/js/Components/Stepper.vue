<script setup>
defineProps({
    step: Number,
});
</script>
<template>
    <div class="px-10 max-w-full md:max-w-3xl mx-auto w-full mb-10">
        <ol class="flex items-center">
            <li
                :class="step > 0 ? 'after:border-green-700' : 'after:border-gray-700'"
                class="flex w-full items-center text-primary-100 after:content-[''] after:w-full after:h-1 after:border-b after:border-blue-100 after:border-4 after:inline-block"
            >
                <span
                    :class="step > 0 ? 'bg-green-700' : 'bg-blue-700'"
                    class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
                >
                    <i v-if="step > 0" class="fa-solid fa-check text-white"></i>
                    <i v-else class="fa-solid fa-clipboard-check text-xl text-gray-200"></i>
                </span>
            </li>
            <li
                :class="step > 1 ? 'after:border-green-700' : 'after:border-gray-700'"
                class="flex w-full items-center text-blue-600 after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-100 after:border-4 after:inline-block"
            >
                <span
                    :class="step > 1 ? 'bg-green-700' : step === 1 ? 'bg-blue-700' : 'bg-gray-700'"
                    class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
                >
                    <i v-if="step > 1" class="fa-solid fa-check text-white"></i>
                    <i v-else class="fa-solid fa-address-card text-xl text-gray-200"></i>
                </span>
            </li>
            <li class="flex items-center">
                <span
                    :class="step > 2 ? 'bg-green-700' : step === 2 ? 'bg-blue-700' : 'bg-gray-700'"
                    class="flex items-center justify-center w-10 h-10 rounded-full after:border-gray-700 lg:h-12 lg:w-12 shrink-0"
                >
                    <i v-if="step > 2" class="fa-solid fa-check text-white"></i>
                    <i v-else class="fa-solid fa-credit-card text-xl text-gray-200"></i>
                </span>
            </li>
        </ol>
    </div>
</template>
