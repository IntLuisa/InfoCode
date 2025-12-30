<template>
    <div
        v-for="(item, index) in items"
        :key="index"
        :name="item"
        :item="item"
        :index="index"
        draggable="true"
        @dragstart="onDragStart(index)"
        @touchstart="onTouchStart($event, index)"
        @dragover.prevent
        @touchmove.prevent="onTouchMove($event)"
        @dragenter="onDragEnter(index)"
        @dragend="onDragEnd"
        @touchend="onDragEnd"
    >
        <slot :item="item" :index="index" :draggedItemIndex="draggedItemIndex" />
    </div>
</template>
<script setup>
import { ref } from 'vue';

const props = defineProps({
    items: Array,
});

const emit = defineEmits(['dragend']);

const draggedItemIndex = ref(null);
const touchItemIndex = ref(null);
const touchStartX = ref(0);
const touchStartY = ref(0);

const onDragStart = (index) => {
    draggedItemIndex.value = index;
};

const onTouchStart = (event, index) => {
    touchStartX.value = event.touches[0].clientX;
    touchStartY.value = event.touches[0].clientY;
    draggedItemIndex.value = index;
};

const onDragEnter = (index) => {
    if (draggedItemIndex.value !== null) {
        const draggedItem = props.items.splice(draggedItemIndex.value, 1)[0];
        props.items.splice(index, 0, draggedItem);

        draggedItemIndex.value = index;
    }
};

const onDragEnd = () => {
    touchItemIndex.value = null;
    draggedItemIndex.value = null;
    emit('dragend', props.items);
};

const onTouchMove = (event) => {
    if (touchItemIndex.value !== null) {
        const touchMoveX = event.touches[0].clientX;
        const touchMoveY = event.touches[0].clientY;

        const deltaX = touchMoveX - touchStartX.value;
        const deltaY = touchMoveY - touchStartY.value;

        if (Math.abs(deltaX) > 30 || Math.abs(deltaY) > 30) {
            const draggedItem = props.items.splice(touchItemIndex.value, 1)[0];
            const newIndex = Math.min(
                Math.max(touchItemIndex.value + (deltaY > 0 ? 1 : -1), 0),
                props.items.length
            );
            props.items.splice(newIndex, 0, draggedItem);
            touchItemIndex.value = newIndex;
        }
    }
};
</script>
