<script setup lang="ts" generic="T extends Record<string, any>">
import type { HTMLAttributes } from 'vue'
import { ref } from 'vue'
import { cn } from '@/lib/utils'

interface Column<T> {
    key: keyof T
    label: string
    sortable?: boolean
    width?: string
    class?: string
}

interface Props<T> {
    columns: Column<T>[]
    data: T[]
    striped?: boolean
    hover?: boolean
    bordered?: boolean
    class?: HTMLAttributes['class']
    loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    striped: true,
    hover: true,
    bordered: false,
    loading: false,
})

const emit = defineEmits<{
    sort: [key: keyof T]
    rowClick: [row: any]
}>()

const sortKey = ref<keyof T | null>(null)
const sortDirection = ref<'asc' | 'desc'>('asc')

const handleSort = (key: keyof T) => {
    if (sortKey.value === key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortKey.value = key
        sortDirection.value = 'asc'
    }
    emit('sort', key)
}

const handleRowClick = (row: any) => {
    emit('rowClick', row)
}
</script>

<template>
    <div :class="cn('overflow-x-auto rounded-lg border bg-transparent', props.class)">
        <div v-if="props.loading" class="flex items-center justify-center py-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-8 h-8 border-2 border-blue-900 border-t-blue-500 rounded-full animate-spin" />
                <p class="mt-2 text-sm text-slate-400">Loading...</p>
            </div>
        </div>

        <table v-else class="w-full text-sm text-slate-300">
            <!-- Header -->
            <thead class="bg-slate-800/50 border-b border-slate-700/50">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="String(column.key)"
                        :class="[
                            'px-6 py-3 text-left font-semibold text-slate-300',
                            column.width && `w-[${column.width}]`,
                            column.class,
                        ]"
                    >
                        <button
                            v-if="column.sortable"
                            @click="handleSort(column.key)"
                            class="flex items-center gap-2 hover:text-slate-100 transition-colors"
                        >
                            {{ column.label }}
                            <svg
                                :class="[
                                    'w-4 h-4 transition-transform',
                                    sortKey === column.key && sortDirection === 'desc' ? 'rotate-180' : '',
                                ]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 16V4m0 0L3 8m4-4l4 4"
                                />
                            </svg>
                        </button>
                        <span v-else>{{ column.label }}</span>
                    </th>
                </tr>
            </thead>

            <!-- Body -->
            <tbody>
                <tr
                    v-for="(row, index) in data"
                    :key="index"
                    :class="[
                        'border-b border-slate-700/50 transition-colors',
                        props.striped && index % 2 === 0 ? 'bg-slate-800/30' : 'bg-transparent',
                        props.hover && 'hover:bg-slate-700/30 cursor-pointer',
                    ]"
                    @click="handleRowClick(row)"
                >
                    <td
                        v-for="column in columns"
                        :key="String(column.key)"
                        :class="[
                            'px-6 py-4 text-slate-300',
                            column.width && `w-[${column.width}]`,
                            column.class,
                        ]"
                    >
                        <slot :name="`cell-${String(column.key)}`" :row="row" :value="row[column.key]">
                            {{ row[column.key] }}
                        </slot>
                    </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!props.loading && data.length === 0">
                    <td :colspan="columns.length" class="px-6 py-8 text-center text-slate-500">
                        <svg
                            class="mx-auto h-12 w-12 text-slate-600 mb-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                            />
                        </svg>
                        <p class="text-sm">No data available</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
