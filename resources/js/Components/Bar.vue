<script setup>
import * as echarts from 'echarts'
import { onMounted, ref } from 'vue';

const props = defineProps({ labels: Object, data: Object })
const ele = ref(null)
onMounted(() => {
    const chart = echarts.init(ele.value)
    chart.setOption({
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow',
            }
        },
        grid: {
            left: '3%',
            right: '0%',
            top: '3%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: props.labels
        },
        yAxis: {
            type: 'value'
        },
        series: [{
            data: props.data,
            type: 'line',
            name: 'Total',
            showBackground: true,
            backgroundStyle: {
                color: 'rgba(180, 180, 180, 0.2)',
            }
        }]
    })
})

</script>

<template>
    <div ref="ele" class="relative" />
</template>
