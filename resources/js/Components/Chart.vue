<script setup>
import * as echarts from 'echarts'
import { effect, onMounted, ref } from 'vue';

const props = defineProps({
    name: {
        default: '',
        type: String
    },
    labels: Object,
    data: Object, type: {
        default: 'line',
        type: String
    }
})
const ele = ref(null)

onMounted(() => {
    const chart = echarts.init(ele.value)
    effect(() => {
        console.log(props)

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
                type: props.type,
                name: props.name,
                showBackground: true,
                backgroundStyle: {
                    color: 'rgba(180, 180, 180, 0.2)',
                }
            }]
        })
    })
})

</script>

<template>
    <div ref="ele" class="relative" />
</template>
