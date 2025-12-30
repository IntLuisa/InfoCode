<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    startAuthentication,
    browserSupportsWebAuthn,
} from '@simplewebauthn/browser';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    processing: false,
});
const isBrowserSupportsWebAuthn = browserSupportsWebAuthn();
const showPassword = ref(false);

const submit = (keypass = false) => {
    form.processing = true;

    const formData = new FormData();
    formData.append('email', form.email);
    formData.append('password', form.password);
    formData.append('remember', form.remember ? 'on' : '');

    axios.post(route('login'), formData)
        .then(() => window.location.href = keypass ? route('passkey') : route('dashboard'))
        .catch(error => {
            if (error.response) {
                const errors = error.response.data.errors;

                if (errors.email) {
                    form.errors.email = errors.email[0];
                }
                if (errors.password) {
                    form.errors.password = errors.password[0];
                }
            } else {
                form.errors.email = 'Erro ao realizar login';
            }
        }).finally(() => {
            form.processing = false;
        })
};

const onLogin = async () => {
    try {
        form.processing = true;

        const optionsResp = await axios.post('/webauthn/login/options')
        const options = optionsResp.data

        const assertion = await startAuthentication({ optionsJSON: options })

        await axios.post('/webauthn/login', assertion)

        window.location.href = route('dashboard');
    } catch (error) {
        form.errors.email = 'Passkey inválida';
    } finally {
        form.processing = false;
    }
}

</script>

<template>
    <AuthLayout title="Log in" :status="status">
        <form @submit.prevent="submit()" novalidate>
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Entrar na sua conta
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
                    <div class="grid grid-cols-[3fr_2fr] gap-2">
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
                        <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            class="text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-orange-400 dark:hover:bg-orange-500 dark:focus:ring-orange-800 truncate">
                            <i class="fa-solid me-2"
                                :class="{ 'animate-spin fa-circle-notch': form.processing, 'fa-right-to-bracket': !form.processing }"></i>
                            Entrar
                        </button>
                    </div>
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 dark:text-red-500">
                        {{ form.errors.password }}
                    </p>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember" aria-describedby="remember" type="checkbox" v-model="form.remember"
                                class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-orange-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-orange-600 dark:ring-offset-gray-800" />
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="remember" class="text-gray-500 dark:text-gray-300">
                                Lembrar
                            </label>
                        </div>
                    </div>
                    <Link :href="route('password.request')"
                        class="text-sm font-medium text-orange-600 hover:underline dark:text-orange-300">
                    Esqueceu sua senha?
                    </Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button type="button" v-if="isBrowserSupportsWebAuthn" @click="onLogin"
                        :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-3 text-center">
                        <i class="fa-solid fa-fingerprint me-2"
                            :class="{ 'animate-spin fa-circle-notch': form.processing, 'fa-fingerprint': !form.processing }"></i>
                        Entrar com Passkey
                    </button>

                    <button type="button" v-if="isBrowserSupportsWebAuthn" @click="submit(true)"
                        :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        class="w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-3 py-3 text-center">
                        <i class="fa-solid fa-qrcode me-2"></i>
                        Cadastrar Passkey
                    </button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
