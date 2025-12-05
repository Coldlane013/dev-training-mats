import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import {
    authService
} from '@/services/'

import {
    type UserResponse
} from '@/types/axios'

/**
 * Authentication Store
 * Manages user authentication state and operations
 */
export const useAuthStore = defineStore('auth', () => {
    const user = ref<UserResponse | null>(null)
    const isLoading = ref(false)
    const error = ref<string | null>(null)
    const isAuthenticated = computed(() => !!user.value)

    const login = async (email: string, password: string) => {
        isLoading.value = true
        error.value = null
        try {
            const response = await authService.login({ email, password })

            // authService.login already calls ApiClient.setToken() with the correct key
            user.value = response.user

            return response
        } catch (err: any) {
            error.value = err.message || 'Login failed'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const register = async (name: string, email: string, password: string, passwordConfirmation: string) => {
        isLoading.value = true
        error.value = null
        try {
            const newUser = await authService.register({
                name,
                email,
                password,
                password_confirmation: passwordConfirmation,
            })
            user.value = newUser
            return newUser
        } catch (err: any) {
            error.value = err.message || 'Registration failed'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const logout = async () => {
        isLoading.value = true
        error.value = null
        try {
            await authService.logout()
            user.value = null
        } catch (err: any) {
            error.value = err.message || 'Logout failed'
        } finally {
            isLoading.value = false
        }
    }

    const fetchProfile = async () => {
        isLoading.value = true
        error.value = null
        try {
            user.value = await authService.getProfile()
            return user.value
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch profile'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const updateProfile = async (name?: string, email?: string) => {
        isLoading.value = true
        error.value = null
        try {
            user.value = await authService.updateProfile({ name, email })
            return user.value
        } catch (err: any) {
            error.value = err.message || 'Failed to update profile'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const changePassword = async (currentPassword: string, password: string, passwordConfirmation: string) => {
        isLoading.value = true
        error.value = null
        try {
            await authService.changePassword({
                current_password: currentPassword,
                password,
                password_confirmation: passwordConfirmation,
            })
        } catch (err: any) {
            error.value = err.message || 'Failed to change password'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    return {
        user,
        isLoading,
        error,
        isAuthenticated,
        login,
        register,
        logout,
        fetchProfile,
        updateProfile,
        changePassword,
    }
})