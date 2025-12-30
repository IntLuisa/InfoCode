<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    email: String,
    token: String,
});

const showPassword = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout title="Reset Password" :status="status">
        <form @submit.prevent="submit" novalidate>
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Trocar Senha
                </h1>
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        E-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" v-model="form.email" name="email" id="email"
                            class="ps-10 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-600 focus:border-orange-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-orange-500 dark:focus:border-orange-500"
                            placeholder="email@example.com" required="" />
                    </div>
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 dark:text-red-500">
                        {{ form.errors.email }}
                    </p>
                </div>
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Senha
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                            v-model="form.password" placeholder="••••••••"
                            class="ps-10 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-600 focus:border-orange-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-orange-500 dark:focus:border-orange-500"
                            required="" />
                        <div class="absolute inset-y-0 end-0 flex items-center pe-3.5"
                            @click="showPassword = !showPassword">
                            <i v-if="showPassword" class="fa-solid fa-eye-slash text-gray-400"></i>
                            <i v-else class="fa-solid fa-eye text-gray-400"></i>
                        </div>
                    </div>
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 dark:text-red-500">
                        {{ form.errors.password }}
                    </p>
                </div>
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Confirmar Senha
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                            v-model="form.password_confirmation" placeholder="••••••••"
                            class="ps-10 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-orange-600 focus:border-orange-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-orange-500 dark:focus:border-orange-500"
                            required="" />
                    </div>
                    <p v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600 dark:text-red-500">
                        {{ form.errors.password_confirmation }}
                    </p>
                </div>

                <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    class="w-full text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-orange-400 dark:hover:bg-orange-500 dark:focus:ring-orange-800">
                    Mudar Senha
                </button>
                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    <Link :href="route('login')"
                        class="font-medium text-orange-600 hover:underline dark:text-orange-300">
                    Voltar
                    </Link>
                </p>
            </div>
        </form>
    </AuthLayout>
</template>
