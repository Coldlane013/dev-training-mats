<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { Head } from '@inertiajs/vue3'
import { onMounted, ref, computed } from 'vue'
import { ApiClient } from '@/lib/axios-client'
import { useAuthGuard } from '@/composables/useAuthGuard'

const { redirectIfNotAuthenticated } = useAuthGuard()

// Minimal metrics state
const totalUsers = ref<number | null>(null)
const loggedInUsers = ref<number | null>(null)
const failedAttempts = ref<number | null>(null)
const loading = ref(true)
const lastUpdated = ref<string | null>(null)

// Computed properties
const onlinePercentage = computed(() => {
    if (!totalUsers.value || totalUsers.value === 0) return 0
    return Math.round((loggedInUsers.value! / totalUsers.value) * 100)
})

const offlineUsers = computed(() => {
    if (!totalUsers.value || !loggedInUsers.value) return 0
    return totalUsers.value - loggedInUsers.value
})

const fetchMetrics = async () => {
    loading.value = true
    try {
        const data = await ApiClient.get('/api/v1/admin/metrics')
        totalUsers.value = data.total_users ?? null
        loggedInUsers.value = data.logged_in_users ?? null
        failedAttempts.value = data.failed_attempts ?? null
        lastUpdated.value = new Date().toISOString()
    } catch (err) {
        console.error('Failed to load metrics', err)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    redirectIfNotAuthenticated()
    void fetchMetrics()
})
</script>

<template>
    <DashboardLayout title="">
        <Head title="Dashboard" />

        <div class="space-y-8">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-4xl font-semibold text-gray-900 tracking-tight">Dashboard</h2>
                    <p class="text-gray-500 text-sm mt-2">System overview and metrics</p>
                </div>
                <button
                    @click="fetchMetrics"
                    :disabled="loading"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-md"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 4a8 8 0 0112.9-1.4L18 2a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1l.5-.5A6 6 0 106 6v1a1 1 0 01-2 0V4z" clip-rule="evenodd"/></svg>
                    <span>{{ loading ? 'Refreshing...' : 'Refresh' }}</span>
                </button>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users Card -->
                <div class="group relative p-6 bg-white rounded-2xl transition-all duration-300 hover:shadow-lg hover:shadow-blue-100 border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Users</p>
                            <p class="mt-3 text-3xl font-bold text-gray-900">{{ loading ? '—' : (totalUsers ?? '0') }}</p>
                            <p class="mt-2 text-xs text-gray-400">All registered accounts</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 group-hover:from-blue-100 group-hover:to-blue-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.048M12 4.354L8.646 7.708m6.708 0L15.354 7.708M9 12H9.01M15 12H15.01M12 20h.01" />
                            </svg>
                        </div>
                    </div>
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/0 to-transparent opacity-0 group-hover:opacity-5 transition-opacity duration-300 pointer-events-none"></div>
                </div>

                <!-- Logged In Users Card -->
                <div class="group relative p-6 bg-white rounded-2xl transition-all duration-300 hover:shadow-lg hover:shadow-green-100 border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Currently Online</p>
                            <p class="mt-3 text-3xl font-bold text-gray-900">{{ loading ? '—' : (loggedInUsers ?? '0') }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    {{ onlinePercentage }}% online
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-green-50 to-green-100 group-hover:from-green-100 group-hover:to-green-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-green-500/0 to-transparent opacity-0 group-hover:opacity-5 transition-opacity duration-300 pointer-events-none"></div>
                </div>

                <!-- Offline Users Card -->
                <div class="group relative p-6 bg-white rounded-2xl transition-all duration-300 hover:shadow-lg hover:shadow-gray-100 border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Offline Users</p>
                            <p class="mt-3 text-3xl font-bold text-gray-900">{{ loading ? '—' : offlineUsers }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                    {{ 100 - onlinePercentage }}% offline
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 group-hover:from-gray-100 group-hover:to-gray-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-gray-500/0 to-transparent opacity-0 group-hover:opacity-5 transition-opacity duration-300 pointer-events-none"></div>
                </div>

                <!-- Failed Attempts Card -->
                <div class="group relative p-6 bg-white rounded-2xl transition-all duration-300 hover:shadow-lg hover:shadow-red-100 border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Failed Attempts</p>
                            <p class="mt-3 text-3xl font-bold text-gray-900">{{ loading ? '—' : (failedAttempts ?? '0') }}</p>
                            <p class="mt-2 text-xs text-gray-400">This session</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-red-50 to-red-100 group-hover:from-red-100 group-hover:to-red-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-red-500/0 to-transparent opacity-0 group-hover:opacity-5 transition-opacity duration-300 pointer-events-none"></div>
                </div>
            </div>

            <!-- Footer with Timestamp -->
            <div class="flex items-center justify-between text-xs text-gray-400">
                <div></div>
                <span v-if="lastUpdated">Updated {{ new Date(lastUpdated).toLocaleString() }}</span>
            </div>
        </div>
    </DashboardLayout>
</template>
