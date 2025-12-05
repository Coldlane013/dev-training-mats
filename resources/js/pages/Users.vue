<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { Head } from '@inertiajs/vue3'
import DataTable from '@/components/dashboard/DataTable.vue'
import { useAuthGuard } from '@/composables/useAuthGuard'
import { useUserStore } from '@/stores/axios-store'
import { onMounted, ref, computed } from 'vue'
import { Button } from '@/components/ui/button'

const { redirectIfNotAuthenticated } = useAuthGuard()
const userStore = useUserStore()

const isLoading = ref(false)

// Check authentication on mount
onMounted(() => {
    redirectIfNotAuthenticated()
    fetchUsers()
})

const fetchUsers = async () => {
    isLoading.value = true
    try {
        await userStore.fetchUsers(userStore.currentPage)
    } catch (error) {
        console.error('Failed to fetch users:', error)
    } finally {
        isLoading.value = false
    }
}

const goToPage = (page: number) => {
    userStore.currentPage = page
    fetchUsers()
}

// Table columns configuration
const columns = [
    { key: 'id', label: 'ID', width: '80px', sortable: true },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'user_roles', label: 'Role' },
    { key: 'created_at', label: 'Created At' },
]

// Computed pagination info
const paginationInfo = computed(() => {
    const start = (userStore.currentPage - 1) * userStore.perPage + 1
    const end = Math.min(userStore.currentPage * userStore.perPage, userStore.total)
    return `${start} to ${end} of ${userStore.total}`
})

const pageNumbers = computed(() => {
    const pages: number[] = []
    const total = userStore.totalPages || 1
    const maxVisible = 5

    // sliding window of pages centered around currentPage
    let start = Math.max(1, userStore.currentPage - Math.floor(maxVisible / 2))
    let end = start + maxVisible - 1

    if (end > total) {
        end = total
        start = Math.max(1, end - maxVisible + 1)
    }

    for (let i = start; i <= end; i++) {
        pages.push(i)
    }

    return pages
})
</script>

<template>
    <DashboardLayout title="Users">
        <Head title="Users" />

        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-100">Manage Users</h1>
                <p class="text-slate-400 text-sm mt-1">View and manage all users in the system</p>
            </div>
            <Button class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white">
                + Add User
            </Button>
        </div>

        <!-- Stats -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                <div class="text-sm text-slate-400">Total Users</div>
                <div class="text-3xl font-bold text-transparent bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text mt-2">
                    {{ userStore.total }}
                </div>
            </div>
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                <div class="text-sm text-slate-400">Current Page</div>
                <div class="text-3xl font-bold text-slate-100 mt-2">{{ userStore.currentPage }}</div>
            </div>
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                <div class="text-sm text-slate-400">Per Page</div>
                <div class="text-3xl font-bold text-slate-100 mt-2">{{ userStore.perPage }}</div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-lg border border-slate-700/50 overflow-hidden">
            <DataTable
                :columns="columns"
                :data="userStore.users"
                :loading="isLoading || userStore.isLoading"
                class="bg-transparent border-0"
            >
                <template #cell-created_at="{ value }">
                    {{ new Date(value).toLocaleDateString() }}
                </template>
                <template #cell-user_roles="{ value }">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium"
                        :class="
                            value === 'admin'
                                ? 'bg-red-500/20 text-red-300'
                                : value === 'manager'
                                ? 'bg-yellow-500/20 text-yellow-300'
                                : 'bg-blue-500/20 text-blue-300'
                        "
                    >
                        {{ value || 'User' }}
                    </span>
                </template>
            </DataTable>
        </div>

        <!-- Pagination Info -->
        <div class="mt-6 text-sm text-slate-400">
            Showing {{ paginationInfo }}
        </div>

        <!-- Pagination -->
        <div v-if="userStore.totalPages > 1" class="mt-6 flex items-center justify-center gap-2">
            <button
                @click="goToPage(Math.max(1, userStore.currentPage - 1))"
                :disabled="userStore.currentPage === 1"
                class="px-4 py-2 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-700/50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                Previous
            </button>

            <div class="flex gap-1">
                <button
                    v-for="page in pageNumbers"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                        'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                        userStore.currentPage === page
                            ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-lg shadow-blue-500/20'
                            : 'border border-slate-700 text-slate-300 hover:bg-slate-700/50',
                    ]"
                >
                    {{ page }}
                </button>
            </div>

            <button
                @click="goToPage(Math.min(userStore.totalPages, userStore.currentPage + 1))"
                :disabled="userStore.currentPage === userStore.totalPages"
                class="px-4 py-2 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-700/50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                Next
            </button>
        </div>
    </DashboardLayout>
</template>
