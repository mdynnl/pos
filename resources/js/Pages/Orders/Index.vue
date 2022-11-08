<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import { ref } from "vue";

const props = defineProps({
    auth: Object,
    orders: Object,
});

const showCompleted = ref(props.auth.user.is_admin)

</script>

<template>

    <Head title="Main" />

    <AuthenticatedLayout>
        <template #header>
            <div class="px-2 py-2 bg-white border-b">
                <div class="flex items-start max-w-md gap-3 ml-auto">
                    <label class="flex items-center gap-2 py-2">
                        <Checkbox v-model:checked="showCompleted" /> Show Completed
                    </label>
                </div>
            </div>

        </template>

        <div v-if="!orders.length" class="flex flex-col items-center justify-center flex-1 min-w-max">
            No items
        </div>

        <table v-if="orders.length" class="table-auto">
            <thead class="bg-gray-200">
                <tr class="border-b">
                    <th class="px-3 py-3 text-right last:pr-6 ">#</th>
                    <th class="px-3 py-3 text-right last:pr-6 ">Status</th>
                    <th class="px-3 py-3 text-right last:pr-6 ">Paid</th>
                    <th class="px-3 py-3 text-right last:pr-6 ">Change</th>
                    <th class="px-3 py-3 text-right last:pr-6 ">Total</th>
                    <th class="px-3 py-3 text-right last:pr-6 ">Date</th>
                </tr>
            </thead>
            <tbody>
                <Link class="contents" v-for="order in orders" :href="route('order.edit', order)">
                <tr class="bg-white border-b hover:bg-gray-50">
                    <template v-if="showCompleted || order.status !== 'complete'">
                        <td class="px-3 py-2 text-right last:pr-6 ">{{ order.id }}</td>
                        <td class="px-3 py-2 text-right last:pr-6 ">{{ order.status }}</td>
                        <td class="px-3 py-2 text-right last:pr-6 ">{{ order.paid.toFixed(2) }}</td>
                        <td class="px-3 py-2 text-right last:pr-6 ">{{ order.change.toFixed(2) }}</td>
                        <td class="px-3 py-2 text-right last:pr-6 ">{{ order.total.toFixed(2) }}</td>
                        <td class="px-3 py-2 text-right last:pr-6 ">
                            {{ Intl.DateTimeFormat().format(new Date(order.updated_at)) }}
                        </td>
                    </template>
                </tr>
                </Link>
            </tbody>
        </table>


    </AuthenticatedLayout>
</template>
