<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/axios-store'
import { dashboard } from '@/routes'

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const auth = useAuthStore()
const form = ref({ email: '', password: '', remember: false })
const errors = ref<{ email?: string; password?: string; general?: string }>({})
const processing = computed(() => auth.isLoading)

const submit = async () => {
    errors.value = {}
    try {
        const response = await auth.login(form.value.email, form.value.password)
        window.location.href = dashboard.url()
    } catch (err: any) {
        console.log('Error:', err);
        const e = err?.errors ?? err?.response?.data?.errors
        if (e) {
            errors.value.email = e.email?.[0] ?? undefined
            errors.value.password = e.password?.[0] ?? undefined
        } else {
            errors.value.general = auth.error || err?.message || 'Login failed'
        }
    }
}

// Expose reactive variables for testing
defineExpose({
    form,
    errors,
    processing,
})
</script>

<template>
    <div class="min-h-screen bg-white">
        <Head title="Log in" />

        <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
            <!-- Left side - Branding -->
            <div class="hidden lg:flex flex-col justify-center items-center bg-gradient-to-br from-blue-600 to-blue-700 p-12">
                <div class="max-w-md text-center text-white">
                    <div class="mb-8">
                        <div class="h-16 w-16 mx-auto bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold mb-4 text-white">Welcome!</h1>
                    <p class="text-blue-100 text-lg mb-8">Access your dashboard and manage your account securely.</p>
                </div>
            </div>

            <!-- Right side - Login Form -->
            <div class="flex flex-col justify-center px-6 sm:px-12 py-12 sm:py-0">
                <div class="w-full max-w-md mx-auto">
                    <!-- Header -->
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">Sign in</h2>
                        <p class="mt-2 text-gray-600">Enter your credentials to access your account</p>
                    </div>

                    <!-- Error message -->
                    <div v-if="errors.general" class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-800">
                        {{ errors.general }}
                    </div>

                    <!-- Status message -->
                    <div v-if="status" class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                        {{ status }}
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email -->
                        <div>
                            <Label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                                Email address
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="you@example.com"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <Label for="password" class="block text-sm font-medium text-gray-900">
                                    Password
                                </Label>
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="request()"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors"
                                >
                                    Forgot password?
                                </TextLink>
                            </div>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="processing"
                            class="w-full mt-8 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 transform hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <Spinner v-if="processing" class="h-4 w-4" />
                            <span>{{ processing ? 'Signing in...' : 'Sign in' }}</span>
                        </button>

                        <!-- Divider -->
                        <div class="relative mt-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-3 bg-white text-gray-600">New to platform?</span>
                            </div>
                        </div>
                        <div v-if="canRegister" class="text-center">
                            <p class="text-gray-600">
                                Don't have an account?
                                <TextLink :href="register()" class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                                    Sign up
                                </TextLink>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
