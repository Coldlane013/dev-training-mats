<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

interface Props {
    title?: string
    description?: string
    class?: HTMLAttributes['class']
    padded?: boolean
    hoverable?: boolean
    bordered?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    padded: true,
    hoverable: true,
    bordered: false,
})
</script>

<template>
    <div
        :class="cn(
            'bg-white rounded-lg shadow',
            props.hoverable && 'hover:shadow-lg transition-shadow',
            props.bordered && 'border border-gray-200',
            props.padded && 'p-6',
            props.class
        )"
    >
        <!-- Header -->
        <div v-if="title || description || $slots.header" class="mb-4">
            <div v-if="title" class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                    <p v-if="description" class="mt-1 text-sm text-gray-600">{{ description }}</p>
                </div>
                <slot name="header-action" />
            </div>
            <slot name="header" />
        </div>

        <!-- Content -->
        <slot />

        <!-- Footer -->
        <slot name="footer" />
    </div>
</template>
