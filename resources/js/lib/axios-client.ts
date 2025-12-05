import axios, { AxiosInstance, AxiosRequestConfig, AxiosError } from 'axios'
import {
    type ApiErrorResponse,
    type ApiConfig
} from '@/types/axios'

/**
 * Reusable Axios API Client
 * Handles authentication, token management, error handling, and common request patterns
 */
export class AxiosApiClient {
    private client: AxiosInstance
    private tokenKey = 'api_token'

    constructor(config: ApiConfig = {}) {
        this.client = axios.create({
            baseURL: config.baseURL || import.meta.env.VITE_API_URL || 'http://localhost:8080',
            timeout: config.timeout || 10000,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...config.headers,
            },
            withCredentials: true,
        })

        // Request interceptor - add auth token
        this.client.interceptors.request.use(
            (config) => {
                const token = this.getToken()
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`
                }
                return config
            },
            (error) => Promise.reject(error)
        )

        // Response interceptor - handle errors
        this.client.interceptors.response.use(
            (response) => response,
            (error) => this.handleError(error)
        )
    }

    /**
     * Set authentication token
     */
    public setToken(token: string): void {
        localStorage.setItem(this.tokenKey, token)
        this.client.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }

    /**
     * Get authentication token
     */
    public getToken(): string | null {
        return localStorage.getItem(this.tokenKey)
    }

    /**
     * Clear authentication token
     */
    public clearToken(): void {
        localStorage.removeItem(this.tokenKey)
        delete this.client.defaults.headers.common['Authorization']
    }

    /**
     * GET request
     */
    public async get<T = any>(url: string, config?: AxiosRequestConfig): Promise<T> {
        const response = await this.client.get<T>(url, config)
        return response.data
    }

    /**
     * POST request
     */
    public async post<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Promise<T> {
        const response = await this.client.post<T>(url, data, config)
        return response.data
    }

    /**
     * PUT request
     */
    public async put<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Promise<T> {
        const response = await this.client.put<T>(url, data, config)
        return response.data
    }

    /**
     * PATCH request
     */
    public async patch<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Promise<T> {
        const response = await this.client.patch<T>(url, data, config)
        return response.data
    }

    /**
     * DELETE request
     */
    public async delete<T = any>(url: string, config?: AxiosRequestConfig): Promise<T> {
        const response = await this.client.delete<T>(url, config)
        return response.data
    }

    /**
     * Centralized error handling
     */
    private handleError(error: AxiosError<ApiErrorResponse>): Promise<never> {
        let errorResponse: ApiErrorResponse = {
            status: error.response?.status,
            message: error.message,
        }

        if (error.response?.data) {
            errorResponse = { ...errorResponse, ...error.response.data }
        }

        // Handle 401 Unauthorized - could trigger logout
        if (error.response?.status === 401) {
            this.clearToken()
            // Optionally emit event or redirect to login
            window.dispatchEvent(new CustomEvent('unauthorized'))
        }

        return Promise.reject(errorResponse)
    }

    /**
     * Get axios instance for advanced usage
     */
    public getInstance(): AxiosInstance {
        return this.client
    }
}

// Default export - singleton instance
export const ApiClient = new AxiosApiClient()
