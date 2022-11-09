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

let getDates = length => Array.from({ length: 10 }, (_, i) => {
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
        <div class="flex flex-col gap-10 mx-20 my-10">
            <div class="flex gap-8">
                <div class="flex flex-col w-64 gap-4 px-8 py-5 text-xl font-bold text-white bg-blue-400 rounded-md">
                    <div>Total</div>

                    <div>{{ total.all.toFixed(2) }}</div>

                </div>
                <div class="flex flex-col w-64 gap-4 px-8 py-5 text-xl font-bold text-white bg-blue-400 rounded-md">
                    <div>Orders</div>

                    <div>{{ orders.all.length }}</div>

                </div>

            </div>
            <!-- TODO -->
            <div class="flex gap-6">
                <label for="from">
                    From <input class="ml-4" id="from" type="date" />
                </label>

                <label for="to">
                    To <input class="ml-4" id="to" type="date" />
                </label>
            </div>
            <div class="flex">
                <div class="flex-1 px-4">
                    <div class="flex justify-between">
                        <h1 class="text-xl font-bold">Total over time</h1>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2" for="dayinterval">
                                <input type="radio" id="dayinterval" name="interval" value="day" /> Daily
                            </label>
                            <label class="flex items-center gap-2" for="monthinterval">
                                <input type="radio" id="monthinterval" name="interval" value="month" /> Monthly
                            </label>
                            <label class="flex items-center gap-2" for="yearinterval">
                                <input type="radio" id="yearinterval" name="interval" value="year" /> Annually
                            </label>
                        </div>
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
