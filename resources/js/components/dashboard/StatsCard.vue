<script setup lang="ts">
defineProps<{
    title: string
    value: string
    change: string
    trend: 'up' | 'down'
    icon: string
}>()

const getIcon = (name: string) => {
    const icons: Record<string, string> = {
        users: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.048M12 4.354L8.646 7.708m6.708 0L15.354 7.708M9 12H9.01M15 12H15.01M12 20h.01" />
        </svg>`,
        building: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>`,
        chart: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>`,
        trending: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>`,
    }
    return icons[name] || icons.chart
}
</script>

<template>
  <div class="rounded-lg p-4 border bg-white">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs text-gray-500">{{ title }}</p>
        <p class="text-lg font-semibold text-gray-900 mt-1">{{ value }}</p>
      </div>
      <div class="text-sm text-gray-600">{{ change }}</div>
    </div>
  </div>
</template>
