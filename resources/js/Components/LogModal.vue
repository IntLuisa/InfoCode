<script setup>
import DialogModal from '@/Components/DialogModal.vue';

const props = defineProps({
    logs: Array,
    show: Boolean
});

const emit = defineEmits(['close']);

</script>

<template>
    <DialogModal :show="show" @close="emit('close')" max-width="2xl">
        <template #title>
            <h2 class="text-xl font-semibold">Histórico de Alterações</h2>
        </template>

        <template #content>
            <div class="space-y-4">
                <div class="overflow-y-auto max-h-96 pr-2">
                    <div class="space-y-4">
                        <div v-for="log in logs" :key="log.id" class="border-l-4 pl-4 py-0" :class="{
                            'border-green-500': log.action === 'CREATE',
                            'border-blue-500': log.action === 'UPDATE',
                            'border-red-500': log.action === 'DELETE'
                        }">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-medium capitalize">{{ {
                                        CREATE: 'Criado',
                                        UPDATE: 'Atualizado',
                                        DELETE: 'Removido'
                                    }[log.action] }}</span> em
                                    <span class="text-orange-500 font-semibold">
                                        {{ log.created_at }}
                                    </span> por
                                    <span class="text-blue-500 font-semibold">
                                        {{ log.user?.name || 'Sistema' }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="log.action === 'UPDATE'" class="mt-1 text-sm space-y-0">
                                <div v-for="(value, field) in JSON.parse(log.changes)" :key="field" class="flex">
                                    <div class="font-medium text-right">
                                        {{ field }}:
                                    </div>
                                    <div class="text-green-600 ms-2 font-semibold">
                                        {{ value }}
                                    </div>
                                </div>
                            </div>

                            <div v-else class="mt-1 text-green-500">
                                Registro {{ log.action === 'CREATE' ? 'criado' : 'removido' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex w-full items-center justify-between">
                <button @click="emit('close')"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                    type="button">
                    Fechar
                </button>
            </div>
        </template>
    </DialogModal>
</template>