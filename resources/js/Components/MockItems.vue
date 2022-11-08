<script setup lang="ts">
const props = defineProps<{
    items: {
        id: string;
        code: string;
        name: string;
    }[];
}>();
</script>

<template>
    <div class="fixed inset-0 z-10 overflow-auto">
        <div class="grid gap-4 p-5 bg-gray-300 sm:grid-cols-3 md:grid-cols-5">
            <div @click.stop="$emit('selected', item)"
                class="flex flex-col px-4 py-2 bg-white rounded-md cursor-pointer hover:ring-2 ring-blue-400"
                v-for="item in items" :key="item.id">
                <div>{{ item.code }}</div>
                <div>{{ item.name }}</div>
            </div>
        </div>
    </div>
</template>
