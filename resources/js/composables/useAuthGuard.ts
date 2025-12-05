import { computed } from 'vue'
import { useAuthStore } from '@/stores/axios-store'

/**
 * Composable to check if user is authenticated
 * Redirects to login if not authenticated
 */
export const useAuthGuard = () => {
    const authStore = useAuthStore()

    const hasToken = computed(() => {
        const token = localStorage.getItem('api_token')
        return !!token
    })

    const isAuthenticated = computed(() => {
        return hasToken.value && authStore.isAuthenticated
    })

    const redirectIfNotAuthenticated = () => {
        if (!hasToken.value) {
            window.location.href = '/login'
        }
    }

    return {
        hasToken,
        isAuthenticated,
        redirectIfNotAuthenticated,
    }
}
