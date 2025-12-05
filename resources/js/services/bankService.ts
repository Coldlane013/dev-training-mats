/**
 * Bank Service
 * Handles bank data operations
 */

import { ApiClient } from '@/lib/axios-client'
import {
    ApiResponse,
    BankListResponse,
    BankResponse,
    CreateBankRequest
 } from '@/types/axios'

export const bankService = {
    async list(page = 1): Promise<BankListResponse> {
        const response = await ApiClient.get<ApiResponse<BankListResponse>>(
            `/api/v1/bank?page=${page}`
        )
        return response.data || { data: [], current_page: 1, per_page: 15, total: 0, last_page: 1 }
    },

    async get(id: number): Promise<BankResponse> {
        const response = await ApiClient.get<ApiResponse<BankResponse>>(`/api/v1/bank/${id}`)
        return response.data || ({} as BankResponse)
    },

    async create(data: CreateBankRequest): Promise<BankResponse> {
        const response = await ApiClient.post<ApiResponse<BankResponse>>('/api/v1/bank', data)
        return response.data || ({} as BankResponse)
    },

    async update(id: number, data: CreateBankRequest): Promise<BankResponse> {
        const response = await ApiClient.patch<ApiResponse<BankResponse>>(
            `/api/v1/bank/${id}`,
            data
        )
        return response.data || ({} as BankResponse)
    },

    async delete(id: number): Promise<void> {
        await ApiClient.delete(`/api/v1/bank/${id}`)
    },
}