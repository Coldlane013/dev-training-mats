<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
})

const message = ref('')

const submit = (e: Event) => {
  e.preventDefault()
  message.value = ''
  form.post('/forgot-password', {
    onSuccess: (page: any) => { message.value = page.props?.flash?.status || 'If your email exists, a reset link has been sent.' },
  })
}
</script>

<template>
  <AuthSimpleLayout title="Reset password">
    <Head title="Forgot Password" />

    <form @submit="submit" class="space-y-4">
      <div>
        <label class="block text-sm text-slate-300 mb-1">Email</label>
        <input v-model="email" type="email" required class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-700 text-slate-100" />
        <p v-if="errors.email" class="text-xs text-red-400 mt-1">{{ errors.email }}</p>
      </div>

      <div>
        <button :disabled="submitting" type="submit" class="w-full px-4 py-2 rounded bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-medium">
          Send reset link
        </button>
      </div>

      <p v-if="message" class="text-sm text-emerald-400">{{ message }}</p>
      <p class="text-center text-sm text-slate-400">Remembered? <a href="/login" class="text-blue-400 hover:underline">Sign in</a></p>
    </form>
  </AuthSimpleLayout>
</template>
