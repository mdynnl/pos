<script setup>
import Chart from '@/Components/Chart.vue';
import Pie from '@/Components/Pie.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { computed, ref } from 'vue';
import { getDaysInMonth, getDaysInYear } from 'date-fns'

const props = defineProps({
    orders: Object,
    total: Object,
    items: Object,
})

const filter = ref('week')

const length = ref(7)

let getDates = length => Array.from({ length: 100 }, (_, i) => {
    let date = new Date()
    date.setDate(date.getDate() - i)
    return date
}).reverse()

const dates = computed(() => getDates(length.value))
const data = computed(() => dates.value.map(d => props.total.byDate[d.toISOString().split('T')[0]] ?? 0))
const labels = computed(() => dates.value.map(d => d.toLocaleString({}, { weekday: 'short', day: 'numeric' })))
const type = ref('bar')


const ordersByDate = computed(() => dates.value.map(d => props.orders.byDate[d.toISOString().split('T')[0]]?.length ?? 0))


</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div>
            <div class="flex justify-end gap-8 m-10">
                <div
                    class="flex flex-col items-end w-64 gap-4 px-10 py-5 text-xl font-bold text-white bg-blue-400 rounded-md">
                    <div>Total</div>

                    <div>{{ total.all }}</div>

                </div>
                <div
                    class="flex flex-col items-end w-64 gap-4 px-10 py-5 text-xl font-bold text-white bg-blue-400 rounded-md">
                    <div>Orders</div>

                    <div>{{ orders.all.length }}</div>

                </div>

            </div>
            <div class="flex">
                <div class="flex-1 px-4">
                    <div class="flex justify-between">
                        <h1 class="text-xl font-bold">Total over time</h1>
                        <select v-model="filter">
                            <option value="week">This week</option>
                            <option value="month">This month</option>
                            <option value="year">This year</option>
                        </select>
                    </div>

                    <Chart name="Total" :type="type" :labels="labels" :data="data" class="w-full mt-4"
                        style="min-height: 30em;" />
                </div>
                <div class="flex-1 px-4">
                    <h1 class="text-xl font-bold">Orders over time</h1>
                    <Chart name="Orders" :labels="labels" :data="ordersByDate" class="w-full mt-4"
                        style="min-height: 30em;" />
                </div>
            </div>
        </div>


    </AuthenticatedLayout>
</template>
