<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
// import { dashboard } from '@/routes';
// import { type BreadcrumbItem } from '@/types';
import { Head, usePage, Link } from '@inertiajs/vue3';
// import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
// import { type NavItem } from '@/types';
import { DASHBOARD_NAV_ITEMS } from '@/config/navigation'

const page = usePage();

</script>

<template>
  <Head title="Dashboard" />
  <AppLayout
      class="p-0 rounded"
  >

    <div class="flex flex-col h-screen bg-white rounded">
        <div class="flex flex-1 overflow-hidden">

            <aside class="w-64 bg-gray-800 text-white overflow-y-auto hidden md:block rounded-tl-lg rounded-bl-lg">
                <nav class="space-y-2">
                    <Link
                        v-for="item in DASHBOARD_NAV_ITEMS"
                        :key="item.title"
                        :href="item.href"
                        :class="{
                            'transition duration-300 ease-in-out px-6 py-3 mb-0 block rounded text-base text-gray-300': true,
                            'bg-gray-700 text-white': item.href === page.url,
                            'block p-3 rounded-md hover:bg-gray-700 transition duration-150': item.href !== page.url
                        }"
                    >
                        {{ item.title }}
                    </Link>
                </nav>
            </aside>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="flex justify-between w-full">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h2>
                    <!-- <button @click="theme.toggleTheme()" class="theme-toggle" >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                        </svg>
                    </button> -->
                </div>

                <!-- <p class="text-gray-700 leading-relaxed">
                    This is the primary area for page content, forms, tables, and data displays.
                    The layout uses Flexbox (`flex`) on the main container (`.flex-1 overflow-hidden`)
                    to place the sidebar and main content side-by-side. The content area is given
                    `flex-1` so it automatically expands to fill the rest of the available width.
                </p>
                <div class="h-[1000px] mt-8 bg-gray-50 p-4 rounded-md border border-dashed">
                    Scrollable content placeholder...
                </div> -->
            </main>
        </div>
    </div>
    </AppLayout>
</template>
