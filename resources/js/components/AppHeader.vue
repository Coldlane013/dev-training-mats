<script setup lang="ts">
import { type NavItem } from '@/types';
import AppLogo from '@/components/AppLogo.vue'
import NavMain from '@/components/NavMain.vue'
import { Link } from '@inertiajs/vue3';

</script>
<template>
    <nav class="shadow bg-white border-b border-gray-100">
        <div class=" max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <AppLogo />
            <NavMain />
            <div class="flex items-center">
                <a href="/login" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                    Login
                </a>
            </div>
        </div>
    </nav>
</template>