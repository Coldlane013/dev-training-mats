<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { HEADER_NAV_ITEMS } from '@/config/navigation.ts'

const page = usePage();
</script>

<template>
    <div class="flex space-x-4">
        <Link
            v-for="item in HEADER_NAV_ITEMS"
            :key="item.title"
            :href="item.href"
            :class="{
                'transition duration-300 ease-in-out px-5 py-2 block rounded font-medium': true,                
                'bg-red-700 text-white': item.href === page.url,
                'hover:bg-red-200 hover:text-red-700 text-gray-600': item.href !== page.url
            }"
        >
            {{ item.title }}
        </Link>
    </div>
</template>