/**
 * TypeScript Interfaces for Type-Safe API Calls
 */

export interface ApiErrorResponse {
    message?: string
    errors?: Record<string, string[]>
    status?: number
}

export interface ApiConfig {
    baseURL?: string
    timeout?: number
    headers?: Record<string, string>
}

// Auth Types
export interface LoginRequest {
    email: string
    password: string
}

export interface LoginResponse {
    token?: string
    access_token?: string
    token_type: string
    user: UserResponse
}

export interface RegisterRequest {
    name: string
    email: string
    password: string
    password_confirmation: string
}

// User Types
export interface UserResponse {
    id: number
    name: string
    email: string
    first_name?: string
    last_name?: string
    middle_name?: string
    user_roles?: string
    email_verified_at?: string | null
    created_at: string
    updated_at: string
}

export interface UpdateUserRequest {
    name?: string
    email?: string
    first_name?: string
    last_name?: string
    middle_name?: string
    user_roles?: string
    password?: string
}

export interface UserListResponse {
    data: UserResponse[]
    current_page: number
    per_page: number
    total: number
    last_page: number
}

export interface ChangePasswordRequest {
    current_password: string
    password: string
    password_confirmation: string
}

// Bank Types
export interface BankResponse {
    id: number
    name: string
    code: string
    created_at: string
    updated_at: string
}

export interface BankListResponse {
    data: BankResponse[]
    current_page: number
    per_page: number
    total: number
    last_page: number
}

export interface CreateBankRequest {
    name: string
    code: string
}

// Generic API Response Wrapper
export interface ApiResponse<T> {
    data?: T
    message?: string
    errors?: Record<string, string[]>
}
