<script setup>
import { ref, onMounted } from 'vue';

const emit = defineEmits(['endDraw']);
const assign = ref(false);
const canvas = ref(false);
const isDrawing = ref(false);
const lastX = ref(0);
const lastY = ref(0);

onMounted(() => {
    assign.value = canvas.value.getContext('2d');
    canvas.value.width = canvas.value.offsetWidth;
    canvas.value.height = canvas.value.offsetHeight;
});

const startDrawing = (x, y) => {
    isDrawing.value = true;
    lastX.value = x - canvas.value.getBoundingClientRect().left;
    lastY.value = y - canvas.value.getBoundingClientRect().top;
};

const draw = (x, y) => {
    if (!isDrawing.value) {
        return;
    }
    assign.value.beginPath();
    assign.value.moveTo(lastX.value, lastY.value);
    assign.value.lineTo(
        x - canvas.value.getBoundingClientRect().left,
        y - canvas.value.getBoundingClientRect().top
    );
    assign.value.stroke();

    lastX.value = x - canvas.value.getBoundingClientRect().left;
    lastY.value = y - canvas.value.getBoundingClientRect().top;
};

const startTrace = (event) => startDrawing(event.clientX, event.clientY);

const moveTrace = (event) => draw(event.clientX, event.clientY);

const endTrace = () => {
    isDrawing.value = false;
    canvas.value.toBlob((blob) => {
        emit('endDraw', blob);
    });
};

const clearAssing = () => {
    assign.value.clearRect(0, 0, canvas.value.width, canvas.value.height);
};
</script>
<template>
    <div class="relative">
        <canvas
            ref="canvas"
            class="w-full border border-1 rounded-lg border-gray-700 bg-white"
            @touchend="endTrace"
            @mouseup="endTrace"
            @mouseout="endTrace"
            @touchmove="moveTrace($event.touches[0])"
            @mousemove="moveTrace($event)"
            @touchstart="startTrace($event.touches[0])"
            @mousedown="startTrace($event)"
        ></canvas>
        <button
            class="absolute bottom-2 right-2 bg-red-600 rounded-lg text-white px-4 py-2"
            @click="clearAssing"
        >
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>
</template>
