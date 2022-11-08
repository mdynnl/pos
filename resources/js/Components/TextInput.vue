<script setup>
import { onMounted, ref } from 'vue';

const input = ref(null);

defineProps(['modelValue']);
defineEmits(['update:modelValue']);
defineExpose({ input })

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <input
        class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    >
</template>
