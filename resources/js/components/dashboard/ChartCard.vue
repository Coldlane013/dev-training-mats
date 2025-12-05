<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

defineProps<{
    class?: HTMLAttributes['class']
}>()

// Sample data for chart
const chartData = [
    { month: 'Jan', value: 400 },
    { month: 'Feb', value: 300 },
    { month: 'Mar', value: 200 },
    { month: 'Apr', value: 278 },
    { month: 'May', value: 189 },
    { month: 'Jun', value: 239 },
    { month: 'Jul', value: 349 },
    { month: 'Aug', value: 430 },
    { month: 'Sep', value: 520 },
    { month: 'Oct', value: 480 },
    { month: 'Nov', value: 590 },
    { month: 'Dec', value: 650 },
]

const maxValue = Math.max(...chartData.map(d => d.value))

const getBarHeight = (value: number) => {
    return (value / maxValue) * 100
}
</script>

<template>
    <div :class="cn('rounded-lg p-4 border bg-white', $props.class)">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-800">Revenue Overview</h3>
            <select class="text-sm border rounded px-2 py-1">
                <option>Last 12 months</option>
                <option>Last 30 days</option>
                <option>Last 7 days</option>
            </select>
        </div>

        <!-- Simple bar chart -->
        <div class="flex items-end justify-between gap-2 h-48">
            <div v-for="item in chartData" :key="item.month" class="flex-1 flex flex-col items-center gap-2">
                <div class="relative w-full flex items-end justify-center h-full">
                    <div class="w-full bg-gray-200 rounded-t" :style="{ height: getBarHeight(item.value) + '%' }" />
                </div>
                <span class="text-xs text-gray-500">{{ item.month }}</span>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
            <div>Revenue</div>
            <div class="font-semibold text-gray-900">$4,582</div>
        </div>
    </div>
</template>
