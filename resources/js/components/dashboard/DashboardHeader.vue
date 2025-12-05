<script setup lang="ts">
import { useAuthStore } from '@/stores/axios-store'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'

const props = defineProps<{
    sidebarOpen: boolean
}>()

const emit = defineEmits<{
    toggleSidebar: []
}>()

const authStore = useAuthStore()
const userMenuOpen = ref(false)

const handleLogout = async () => {
    await authStore.logout()
    window.location.href = '/login'
}
</script>

<template>
    <header class="sticky top-0 z-40 border-b border-gray-200 bg-white">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <!-- Left: Menu Toggle -->
            <div class="flex items-center gap-4">
                <button
                    @click="emit('toggleSidebar')"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden transition-colors"
                    aria-label="Toggle sidebar"
                >
                    <svg
                        v-if="!sidebarOpen"
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <template>
                            <header class="flex items-center justify-between h-14 px-4 bg-white border-b">
                                <div class="flex items-center gap-3">
                                    <button @click="$emit('toggle-sidebar')" class="p-2 rounded-md hover:bg-gray-100">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                    </button>
                                    <div class="text-sm font-medium text-gray-800">Dashboard</div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <input type="text" placeholder="Search" class="px-3 py-1 rounded border text-sm" />
                                    <button class="text-sm text-gray-700" @click="handleLogout">Logout</button>
                                </div>
                            </header>
                        </template>
                        />
                    </svg>
                </button>
            </div>

            <!-- Right: Search + Notifications + User Menu -->
            <div class="flex items-center gap-6">
                <!-- Search -->
                <div class="hidden md:flex items-center">
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Search anything..."
                            class="bg-gray-50 text-gray-900 text-sm rounded-lg pl-10 pr-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all"
                        />
                        <svg
                            class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>
                </div>

                <!-- Notifications -->
                <button
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg relative transition-colors"
                    aria-label="Notifications"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                        />
                    </svg>
                    <span
                        class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg"
                    >
                        3
                    </span>
                </button>

                <!-- User Menu -->
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button
                            class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        >
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-medium text-sm shadow-lg"
                            >
                                {{ authStore.user?.name?.charAt(0).toUpperCase() ?? 'U' }}
                            </div>
                            <span class="hidden sm:inline text-sm font-medium text-gray-900">
                                {{ authStore.user?.name ?? 'User' }}
                            </span>
                            <svg
                                class="hidden sm:block h-4 w-4 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56 bg-white border-gray-200">
                        <DropdownMenuLabel class="font-normal">
                            <div class="flex flex-col space-y-1">
                                <p class="text-sm font-medium leading-none text-gray-900">{{ authStore.user?.name }}</p>
                                <p class="text-xs leading-none text-gray-500">
                                    {{ authStore.user?.email }}
                                </p>
                            </div>
                        </DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem>
                            <Link href="/settings/profile" class="w-full text-gray-700 hover:text-gray-900">Profile Settings</Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem>
                            <Link href="/settings/appearance" class="w-full text-gray-700 hover:text-gray-900">Appearance</Link>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem as-child>
                            <button
                                @click="handleLogout"
                                class="w-full text-left text-red-600 hover:text-red-700 transition-colors"
                            >
                                Logout
                            </button>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    </header>
</template>
