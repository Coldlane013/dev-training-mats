import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import {
    bankService
} from '@/services/'

import {
    type BankResponse,
} from '@/types/axios'
/**
 * Bank Store
 * Manages bank CRUD operations and list state
 */
export const useBankStore = defineStore('banks', () => {
    const banks = ref<BankResponse[]>([])
    const currentPage = ref(1)
    const totalPages = ref(1)
    const perPage = ref(15)
    const total = ref(0)
    const isLoading = ref(false)
    const error = ref<string | null>(null)
    const selectedBank = ref<BankResponse | null>(null)

    const fetchBanks = async (page = 1) => {
        isLoading.value = true
        error.value = null
        try {
            const response = await bankService.list(page)
            banks.value = response.data
            currentPage.value = response.current_page
            totalPages.value = response.last_page
            perPage.value = response.per_page
            total.value = response.total
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch banks'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const fetchBank = async (id: number) => {
        isLoading.value = true
        error.value = null
        try {
            selectedBank.value = await bankService.get(id)
            return selectedBank.value
        } catch (err: any) {
            error.value = err.message || 'Failed to fetch bank'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const createBank = async (name: string, code: string) => {
        isLoading.value = true
        error.value = null
        try {
            const newBank = await bankService.create({ name, code })
            banks.value.push(newBank)
            return newBank
        } catch (err: any) {
            error.value = err.message || 'Failed to create bank'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const updateBank = async (id: number, name: string, code: string) => {
        isLoading.value = true
        error.value = null
        try {
            const updated = await bankService.update(id, { name, code })
            const index = banks.value.findIndex(b => b.id === id)
            if (index !== -1) banks.value[index] = updated
            if (selectedBank.value?.id === id) selectedBank.value = updated
            return updated
        } catch (err: any) {
            error.value = err.message || 'Failed to update bank'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    const deleteBank = async (id: number) => {
        isLoading.value = true
        error.value = null
        try {
            await bankService.delete(id)
            banks.value = banks.value.filter(b => b.id !== id)
        } catch (err: any) {
            error.value = err.message || 'Failed to delete bank'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    return {
        banks,
        currentPage,
        totalPages,
        perPage,
        total,
        isLoading,
        error,
        selectedBank,
        fetchBanks,
        fetchBank,
        createBank,
        updateBank,
        deleteBank,
    }
})
