<script setup>
import MockItems from "@/Components/MockItems.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { computed } from "@vue/reactivity";
import { nextTick, ref, watchEffect } from "vue";

const props = defineProps({
    allItems: Object,
    order: Object,
});

const form = useForm({ code: "" });
const paymentForm = useForm({ paid: props.order.paid ?? 0 })
const paid = computed(() => isNaN(paymentForm.paid) ? 0 : paymentForm.paid)

const submit = () => form.post(route('items.store', { order: props.order }));

const updateItem = (id, by) => {
    form.transform(() => ({ by })).put(route("item.update", { order: props.order, orderItem: id }), {
        preserveState: false
    });
};


const textInput = ref(null);
const paidInput = ref(null);
const status = computed(() => props.order.status);

const open = ref(false);

function itemSelected(item) {
    form.code = item.code;
    open.value = false;
    nextTick(() => {
        textInput.value?.$el.select();
        submit();
    });
}

const payment = () => {
    form.put(route('order.payment', { order: props.order }), {
    });
};

const cancel = () => {
    form.put(route('order.cancel', { order: props.order }), {
    });
};

const complete = () => {
    paymentForm.transform(v => ({ paid: +v.paid })).put(route('order.complete', { order: props.order }), {
    });
};

const newOrder = () => Inertia.visit(route('orders.create'))


const select = () => paidInput.value?.$el.select()
watchEffect(select)
watchEffect(() => paymentForm.hasErrors && select())

addEventListener("keydown", () => textInput.value?.$el.focus());
</script>

<template>

    <Head title="Main" />

    <AuthenticatedLayout>
        <template v-if="status === 'ongoing'" #header>
            <div class="flex items-center px-2 py-2 bg-white border-b">
                <h1 class="px-2 text-xl font-bold">Order #{{ order.id }}</h1>
                <div class="flex items-start flex-1 max-w-md gap-3 mx-auto">
                    <form id="form" @submit.prevent="submit" class="flex flex-col flex-1">
                        <TextInput placeholder="enter code" ref="textInput" id="code" type="text" class="" maxlength="8"
                            :class="{ '!ring-red-300': form.errors.code }" v-model="form.code"
                            @input="form.clearErrors()" autofocus required autocomplete="off" />
                        <div v-if="form.errors.code" class="text-red-400">
                            {{ form.errors.code }}
                        </div>
                    </form>

                    <PrimaryButton class="px-5 py-3 text-md" form="form">
                        Add
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div v-if="!order.items.length" class="flex flex-col items-center justify-center flex-1 min-w-max">
            No items
        </div>

        <table v-if="order.items.length" class="table-auto">
            <thead class="bg-gray-200">
                <tr class="border-b">
                    <th class="px-3 py-3 text-right last:pr-6">#</th>
                    <th class="px-3 py-3 text-left last:pr-6 ">Name</th>
                    <th class="px-3 py-3 text-right last:pr-6 ">Price</th>
                    <th class="px-3 py-3 text-right last:pr-6 ">Quantity</th>
                    <th class="px-3 py-3 text-right last:pr-6 ">Subtotal</th>
                    <th class="px-3 py-3 pr-5 text-center last:pr-6" v-if="status === 'ongoing'">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-white border-b" v-for="(item, index) in order.items">
                    <td class="px-3 py-2 text-right last:pr-6 ">{{ index + 1 }}</td>
                    <td class="px-3 py-2 last:pr-6 ">{{ item.name }}</td>
                    <td class="px-3 py-2 text-right last:pr-6 ">{{ item.price }}</td>
                    <td class="px-3 py-2 text-right last:pr-6 ">{{ item.pivot.quantity }}</td>
                    <td class="px-3 py-2 text-right last:pr-6 ">
                        {{ (item.pivot.quantity * item.price).toFixed(2) }}
                    </td>
                    <td class="px-3 py-2 last:pr-6 " v-if="status === 'ongoing'">
                        <div class="flex items-center justify-center gap-2">
                            <button @click="updateItem(item.pivot.id, -1)"
                                class="grid w-8 h-8 font-bold bg-gray-200 rounded-md hover:bg-gray-200 place-items-center">
                                &minus;
                            </button>
                            <button @click="updateItem(item.pivot.id, 1)"
                                class="grid w-8 h-8 font-bold bg-gray-200 rounded-md hover:bg-gray-200 place-items-center">
                                &plus;
                            </button>
                            <button @click="updateItem(item.pivot.id, -item.pivot.quantity)"
                                class="grid w-8 h-8 font-bold text-white bg-red-400 rounded-md hover:bg-red-500 place-items-center">
                                &times;
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot class="sticky bottom-0">
                <tr class="border-b">
                    <th class="px-3 py-3 text-right last:pr-6" colspan="4">Total</th>
                    <th class="px-3 py-3 text-right last:pr-6">
                        {{ order.total }}
                    </th>
                </tr>
                <div class="contents" v-if="status === 'payment'">
                    <tr class="border-b">
                        <th class="px-3 py-3 text-right last:pr-6" colspan="4">Paid</th>
                        <th class="px-3 py-3 text-right last:pr-6">
                            <form id="paymentForm" @submit.prevent="complete" class="flex flex-col items-end gap-3">
                                <TextInput placeholder="enter amount" @input="paymentForm.clearErrors()" required
                                    autocomplete="off" ref="paidInput" @click="select" v-model="paymentForm.paid"
                                    class="px-3 py-2 -mr-2 text-right tabular-nums" style="width: 8em;"
                                    :class="{ '!ring-red-300': paymentForm.errors.paid }" autofocus />
                                <div v-if="paymentForm.errors.paid" class="text-red-400">
                                    {{ paymentForm.errors.paid }}
                                </div>
                            </form>
                        </th>
                    </tr>
                    <tr class="border-b">
                        <th class="px-3 py-3 text-right last:pr-6" colspan="4">Change</th>
                        <th class="px-3 py-3 text-right last:pr-6">
                            {{ Math.max(0, paid - order.total).toFixed(2) }}
                        </th>
                    </tr>
                </div>

                <div class="contents" v-if="status === 'complete'">
                    <tr class="border-b">
                        <th class="px-3 py-3 text-right last:pr-6" colspan="4">Paid</th>
                        <th class="px-3 py-3 text-right last:pr-6">
                            {{ (+order.paid).toFixed(2) }}
                        </th>
                    </tr>
                    <tr class="border-b">
                        <th class="px-3 py-3 text-right last:pr-6" colspan="4">Change</th>
                        <th class="px-3 py-3 text-right last:pr-6">
                            {{ (order.paid - order.total).toFixed(2) }}
                        </th>
                    </tr>
                </div>
            </tfoot>
        </table>

        <template #footer>
            <div class="text-xl font-bold border-t bg-gray-50">
                <div class="flex justify-between gap-4 px-4 py-2 md:mx-24 lg:mx-48 xl:mx-64">
                    <div v-if="status === 'ongoing'" class="contents">
                        <PrimaryButton class="bg-gray-600 hover:bg-gray-500" @click="open = !open">
                            Mock Scan
                        </PrimaryButton>

                        <PrimaryButton @click="payment" :disabled="!order.items.length"
                            class="bg-blue-600 hover:bg-blue-500 disabled:bg-blue-300 disabled:pointer-events-none">
                            Payment
                        </PrimaryButton>
                    </div>
                    <div class="contents" v-if="status === 'payment'">
                        <PrimaryButton class="bg-gray-600 hover:bg-gray-500" @click="cancel">
                            Cancel
                        </PrimaryButton>

                        <PrimaryButton :disabled="paid < order.total" form="paymentForm"
                            class="bg-blue-600 hover:bg-blue-500 disabled:bg-blue-300 disabled:pointer-events-none">
                            Complete
                        </PrimaryButton>
                    </div>

                    <div class="contents" v-if="status === 'complete'">
                        <!-- <PrimaryButton class="bg-gray-600 hover:bg-gray-500" @click="payment">
                            Back
                        </PrimaryButton> -->

                        <PrimaryButton @click="newOrder"
                            class="ml-auto bg-blue-600 hover:bg-blue-500 disabled:bg-blue-300 disabled:pointer-events-none">
                            New Order
                        </PrimaryButton>

                        <a :href="route('order.download', { order: props.order })" target="_blank">
                            <PrimaryButton
                                class="ml-auto bg-blue-600 hover:bg-blue-500 disabled:bg-blue-300 disabled:pointer-events-none">
                                Print
                            </PrimaryButton>
                        </a>
                    </div>
                </div>
            </div>
        </template>

        <MockItems v-show="open" @selected="itemSelected" :items="allItems" />

    </AuthenticatedLayout>
</template>
