<template>
    <div class="space-y-8">
        <!-- Auth Section -->
        <section class="rounded-lg border p-6">
            <h2 class="mb-4 text-2xl font-bold">Authentication Example</h2>

            <div v-if="authStore.isAuthenticated" class="space-y-4">
                <div class="rounded-lg bg-green-100 p-4">
                    <p class="font-semibold">Logged in as: {{ authStore.user?.name }}</p>
                    <p class="text-sm text-gray-600">{{ authStore.user?.email }}</p>
                </div>
                <button
                    @click="handleLogout"
                    :disabled="authStore.isLoading"
                    class="rounded bg-red-500 px-4 py-2 text-white hover:bg-red-600 disabled:opacity-50"
                >
                    Logout
                </button>
            </div>

            <div v-else class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <input
                        v-model="loginForm.email"
                        type="email"
                        placeholder="Email"
                        class="rounded border px-3 py-2"
                    />
                    <input
                        v-model="loginForm.password"
                        type="password"
                        placeholder="Password"
                        class="rounded border px-3 py-2"
                    />
                </div>
                <button
                    @click="handleLogin"
                    :disabled="authStore.isLoading"
                    class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 disabled:opacity-50"
                >
                    {{ authStore.isLoading ? 'Logging in...' : 'Login' }}
                </button>
                <div v-if="authStore.error" class="rounded bg-red-100 p-3 text-red-800">
                    {{ authStore.error }}
                </div>
            </div>
        </section>

        <!-- Users Section -->
        <section class="rounded-lg border p-6">
            <h2 class="mb-4 text-2xl font-bold">Users Management</h2>

            <div v-if="authStore.isAuthenticated" class="space-y-4">
                <!-- Create User -->
                <div class="rounded-lg bg-gray-50 p-4">
                    <h3 class="mb-3 font-semibold">Add New User</h3>
                    <div class="grid gap-3 md:grid-cols-3">
                        <input
                            v-model="newUserForm.name"
                            type="text"
                            placeholder="Name"
                            class="rounded border px-3 py-2"
                        />
                        <input
                            v-model="newUserForm.email"
                            type="email"
                            placeholder="Email"
                            class="rounded border px-3 py-2"
                        />
                        <button
                            @click="handleCreateUser"
                            :disabled="userStore.isLoading"
                            class="rounded bg-green-500 px-3 py-2 text-white hover:bg-green-600 disabled:opacity-50"
                        >
                            {{ userStore.isLoading ? 'Creating...' : 'Create' }}
                        </button>
                    </div>
                </div>

                <!-- Users List -->
                <div>
                    <button
                        @click="handleFetchUsers"
                        :disabled="userStore.isLoading"
                        class="mb-4 rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 disabled:opacity-50"
                    >
                        {{ userStore.isLoading ? 'Loading...' : 'Fetch Users' }}
                    </button>

                    <div v-if="userStore.error" class="mb-4 rounded bg-red-100 p-3 text-red-800">
                        {{ userStore.error }}
                    </div>

                    <div v-if="userStore.users.length" class="space-y-2">
                        <div
                            v-for="user in userStore.users"
                            :key="user.id"
                            class="flex items-center justify-between rounded border p-3"
                        >
                            <div>
                                <p class="font-semibold">{{ user.name }}</p>
                                <p class="text-sm text-gray-600">{{ user.email }}</p>
                            </div>
                            <button
                                @click="handleDeleteUser(user.id)"
                                :disabled="userStore.isLoading"
                                class="rounded bg-red-500 px-3 py-1 text-sm text-white hover:bg-red-600 disabled:opacity-50"
                            >
                                Delete
                            </button>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex items-center justify-between rounded-lg border p-3">
                            <span class="text-sm">
                                Page {{ userStore.currentPage }} of {{ userStore.totalPages }}
                                (Total: {{ userStore.total }})
                            </span>
                            <div class="space-x-2">
                                <button
                                    @click="handleFetchUsers(userStore.currentPage - 1)"
                                    :disabled="userStore.currentPage === 1 || userStore.isLoading"
                                    class="rounded bg-gray-500 px-3 py-1 text-white hover:bg-gray-600 disabled:opacity-50"
                                >
                                    Previous
                                </button>
                                <button
                                    @click="handleFetchUsers(userStore.currentPage + 1)"
                                    :disabled="userStore.currentPage === userStore.totalPages || userStore.isLoading"
                                    class="rounded bg-gray-500 px-3 py-1 text-white hover:bg-gray-600 disabled:opacity-50"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>

                    <p v-else-if="!userStore.isLoading" class="text-gray-600">
                        No users loaded. Click "Fetch Users" to load data.
                    </p>
                </div>
            </div>

            <p v-else class="text-gray-600">
                Please login first to manage users.
            </p>
        </section>

        <!-- Banks Section -->
        <section class="rounded-lg border p-6">
            <h2 class="mb-4 text-2xl font-bold">Banks Management</h2>

            <div v-if="authStore.isAuthenticated" class="space-y-4">
                <!-- Create Bank -->
                <div class="rounded-lg bg-gray-50 p-4">
                    <h3 class="mb-3 font-semibold">Add New Bank</h3>
                    <div class="grid gap-3 md:grid-cols-3">
                        <input
                            v-model="newBankForm.name"
                            type="text"
                            placeholder="Bank Name"
                            class="rounded border px-3 py-2"
                        />
                        <input
                            v-model="newBankForm.code"
                            type="text"
                            placeholder="Code"
                            class="rounded border px-3 py-2"
                        />
                        <button
                            @click="handleCreateBank"
                            :disabled="bankStore.isLoading"
                            class="rounded bg-green-500 px-3 py-2 text-white hover:bg-green-600 disabled:opacity-50"
                        >
                            {{ bankStore.isLoading ? 'Creating...' : 'Create' }}
                        </button>
                    </div>
                </div>

                <!-- Banks List -->
                <div>
                    <button
                        @click="handleFetchBanks"
                        :disabled="bankStore.isLoading"
                        class="mb-4 rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 disabled:opacity-50"
                    >
                        {{ bankStore.isLoading ? 'Loading...' : 'Fetch Banks' }}
                    </button>

                    <div v-if="bankStore.error" class="mb-4 rounded bg-red-100 p-3 text-red-800">
                        {{ bankStore.error }}
                    </div>

                    <div v-if="bankStore.banks.length" class="space-y-2">
                        <div
                            v-for="bank in bankStore.banks"
                            :key="bank.id"
                            class="flex items-center justify-between rounded border p-3"
                        >
                            <div>
                                <p class="font-semibold">{{ bank.name }}</p>
                                <p class="text-sm text-gray-600">Code: {{ bank.code }}</p>
                            </div>
                            <button
                                @click="handleDeleteBank(bank.id)"
                                :disabled="bankStore.isLoading"
                                class="rounded bg-red-500 px-3 py-1 text-sm text-white hover:bg-red-600 disabled:opacity-50"
                            >
                                Delete
                            </button>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex items-center justify-between rounded-lg border p-3">
                            <span class="text-sm">
                                Page {{ bankStore.currentPage }} of {{ bankStore.totalPages }}
                                (Total: {{ bankStore.total }})
                            </span>
                        </div>
                    </div>

                    <p v-else-if="!bankStore.isLoading" class="text-gray-600">
                        No banks loaded. Click "Fetch Banks" to load data.
                    </p>
                </div>
            </div>

            <p v-else class="text-gray-600">
                Please login first to manage banks.
            </p>
        </section>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore, useUserStore, useBankStore } from '@/stores/axios-store'

const authStore = useAuthStore()
const userStore = useUserStore()
const bankStore = useBankStore()

// Login form
const loginForm = ref({
    email: 'test@example.com',
    password: 'password',
})

// New user form
const newUserForm = ref({
    name: '',
    email: '',
})

// New bank form
const newBankForm = ref({
    name: '',
    code: '',
})

// Auth handlers
const handleLogin = async () => {
    try {
        await authStore.login(loginForm.value.email, loginForm.value.password)
    } catch (error) {
        console.error('Login failed:', error)
    }
}

const handleLogout = async () => {
    try {
        await authStore.logout()
    } catch (error) {
        console.error('Logout failed:', error)
    }
}

// User handlers
const handleFetchUsers = async (page = 1) => {
    try {
        await userStore.fetchUsers(page)
    } catch (error) {
        console.error('Failed to fetch users:', error)
    }
}

const handleCreateUser = async () => {
    try {
        await userStore.createUser(newUserForm.value.name, newUserForm.value.email)
        newUserForm.value = { name: '', email: '' }
    } catch (error) {
        console.error('Failed to create user:', error)
    }
}

const handleDeleteUser = async (id: number) => {
    try {
        await userStore.deleteUser(id)
    } catch (error) {
        console.error('Failed to delete user:', error)
    }
}

// Bank handlers
const handleFetchBanks = async (page = 1) => {
    try {
        await bankStore.fetchBanks(page)
    } catch (error) {
        console.error('Failed to fetch banks:', error)
    }
}

const handleCreateBank = async () => {
    try {
        await bankStore.createBank(newBankForm.value.name, newBankForm.value.code)
        newBankForm.value = { name: '', code: '' }
    } catch (error) {
        console.error('Failed to create bank:', error)
    }
}

const handleDeleteBank = async (id: number) => {
    try {
        await bankStore.deleteBank(id)
    } catch (error) {
        console.error('Failed to delete bank:', error)
    }
}
</script>
