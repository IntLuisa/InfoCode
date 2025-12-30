<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { ref } from 'vue';
import {
    startRegistration,
    browserSupportsWebAuthn,
    platformAuthenticatorIsAvailable
} from '@simplewebauthn/browser';

const message_error = ref(null);
const onRegister = async () => {
    try {
        if (browserSupportsWebAuthn()) {

            axios.defaults.withCredentials = true;
            const optionsResp = await axios.post('/webauthn/register/options')
            const options = optionsResp.data

            const credential = await startRegistration({ optionsJSON: options })

            axios.defaults.withCredentials = true;
            await axios.post('/webauthn/register', credential)

            window.location.href = route('dashboard');
        }
    } catch (error) {
        message_error.value = 'Error ao registrar passkey';
    }
}

const toToDashboard = () => {
    window.location.href = route('dashboard');
}

(async () => {
    if (await platformAuthenticatorIsAvailable()) {
        onRegister();
    } else {
        alert('Autenticador de plataforma *não* disponível.')
    }
})()
</script>

<template>
    <AuthLayout title="Passkey">
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
            <div v-if="message_error"
                class="mb-4 font-medium text-sm text-red-600 dark:text-red-400 border border-red-400 rounded px-4 py-3">
                {{ message_error }}
            </div>
            <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                Registrar Passkey
            </h1>
            <button @click="onRegister"
                class="w-full text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-orange-400 dark:hover:bg-orange-500 dark:focus:ring-orange-800">
                Tentar Novamente</button>
            <button
                class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                @click="toToDashboard()">Ir para Dashboard</button>
        </div>
    </AuthLayout>
</template>
