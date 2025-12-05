/**
 * User Service
 * Handles user CRUD operations
 */
import { ApiClient } from '@/lib/axios-client'
import {
    ApiResponse,
    UserResponse,
    UserListResponse,
    UpdateUserRequest,
 } from '@/types/axios'

export const userService = {
    async list(page = 1): Promise<UserListResponse> {
        const response = await ApiClient.get<any>(
            `/api/v1/users?page=${page}`
        )
        // The API returns { message, user: {...pagination data} }
        const data = response?.user || response?.data || {}
        return {
            data: data.data || [],
            current_page: data.current_page || 1,
            per_page: data.per_page || 15,
            total: data.total || 0,
            last_page: data.last_page || 1
        }
    },

    async get(id: number): Promise<UserResponse> {
        const response = await ApiClient.get<ApiResponse<UserResponse>>(`/api/v1/users/${id}`)
        return response.data || ({} as UserResponse)
    },

    async create(data: UpdateUserRequest): Promise<UserResponse> {
        const response = await ApiClient.post<ApiResponse<UserResponse>>('/api/v1/users', data)
        return response.data || ({} as UserResponse)
    },

    async update(id: number, data: UpdateUserRequest): Promise<UserResponse> {
        const response = await ApiClient.patch<ApiResponse<UserResponse>>(
            `/api/v1/users/${id}`,
            data
        )
        return response.data || ({} as UserResponse)
    },

    async delete(id: number): Promise<void> {
        await ApiClient.delete(`/api/v1/users/${id}`)
    },
}