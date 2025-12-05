import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import {
    userService,
} from '@/services/'

import {
    type UserResponse,
} from '@/types/axios'
/**
 * User Store
 * Manages user CRUD operations and list state
 */
export const useUserStore = defineStore('users', () => {
    const users = ref<UserResponse[]>([])
    const currentPage = ref(1)
    const totalPages = ref(1)
    const perPage = ref(15)
    const total = ref(0)
    const isLoading = ref(false)
    const error = ref<string | null>(null)
    const selectedUser = ref<UserResponse | null>(null)

    const fetchUsers = async (page = 1) => {
        isLoading.value = true
        error.value = null
        try {
            const response = await userService.list(page)
            users.value = response.data
            currentPage.value = response.current_page
            totalPages.value = response.last_page
            perPage.value = response.per_page
            total.value = response.total
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch users'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const fetchUser = async (id: number) => {
        isLoading.value = true
        error.value = null
        try {
            selectedUser.value = await userService.get(id)
            return selectedUser.value
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch user'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const createUser = async (name: string, email: string) => {
        isLoading.value = true
        error.value = null
        try {
            const newUser = await userService.create({ name, email })
            users.value.push(newUser)
            return newUser
        } catch (err: any) {
            error.value = err.message || 'Failed to create user'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const updateUser = async (id: number, name?: string, email?: string) => {
        isLoading.value = true
        error.value = null
        try {
            const updated = await userService.update(id, { name, email })
            const index = users.value.findIndex(u => u.id === id)
            if (index !== -1) users.value[index] = updated
            if (selectedUser.value?.id === id) selectedUser.value = updated
            return updated
        } catch (err: any) {
            error.value = err.message || 'Failed to update user'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const deleteUser = async (id: number) => {
        isLoading.value = true
        error.value = null
        try {
            await userService.delete(id)
            users.value = users.value.filter(u => u.id !== id)
        } catch (err: any) {
            error.value = err.message || 'Failed to delete user'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    return {
        users,
        currentPage,
        totalPages,
        perPage,
        total,
        isLoading,
        error,
        selectedUser,
        fetchUsers,
        fetchUser,
        createUser,
        updateUser,
        deleteUser,
    }
})