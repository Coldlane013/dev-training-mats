/**
 * Authentication Service
 * Handles login, logout, and token management
 */

import { ApiClient } from '@/lib/axios-client'
import {
    LoginRequest,
    LoginResponse,
    ApiResponse,
    RegisterRequest,
    UserResponse,
    UpdateUserRequest,
    ChangePasswordRequest,
} from '@/types/axios'

export const authService = {
    async login(credentials: LoginRequest): Promise<LoginResponse> {
        // Ensure Sanctum CSRF cookie is present for session-based auth flows
        try {
            await ApiClient.get('/sanctum/csrf-cookie')
        } catch (e) {
            // ignore — some environments don't need CSRF cookie
        }

        const response = await ApiClient.post<any>('/api/v1/auth', credentials)

        // Normalize response shapes: some backends return { token } while others return { access_token }
        const token = response?.token ?? response?.access_token ?? response?.data?.token ?? response?.data?.access_token

        if (token) {
            ApiClient.setToken(token)
            // Return the user payload and token in a consistent shape
            const user = response?.user ?? response?.data?.user
            return {
                token: response?.token,
                access_token: response?.access_token ?? token,
                token_type: response?.token_type ?? response?.data?.token_type ?? 'Bearer',
                user,
            } as LoginResponse
        }

        throw new Error('No auth token received')
    },

    async register(data: RegisterRequest): Promise<UserResponse> {
        const response = await ApiClient.post<ApiResponse<UserResponse>>(
            '/api/v1/register',
            data
        )
        return response.data || ({} as UserResponse)
    },

    async logout(): Promise<void> {
        try {
            await ApiClient.post('/api/v1/deauth')
        } finally {
            ApiClient.clearToken()
        }
    },

    async getProfile(): Promise<UserResponse> {
        const response = await ApiClient.get<ApiResponse<UserResponse>>('/api/v1/user')
        return response.data || ({} as UserResponse)
    },

    async updateProfile(data: UpdateUserRequest): Promise<UserResponse> {
        const response = await ApiClient.patch<ApiResponse<UserResponse>>(
            '/api/v1/user',
            data
        )
        return response.data || ({} as UserResponse)
    },

    async changePassword(data: ChangePasswordRequest): Promise<void> {
        await ApiClient.post('/api/v1/user/password', data)
    },
}