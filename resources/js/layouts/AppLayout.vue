<script setup lang="ts">
import AppHeader from '@/components/AppHeader.vue';
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
    class?: HTMLAttributes['class']
}>();
</script>

<template>
    <div class="min-h-screen">
        <AppHeader />         
        <header v-if="$slots.header" class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>
        <main class="mt-8 mb-10 max-w-7xl mx-auto px-8">
            <div :class="cn('p-15 bg-white rounded shadow', props.class)">
                <slot />
            </div>            
        </main>
    </div>
</template>