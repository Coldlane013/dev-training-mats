<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = (e: Event) => {
  e.preventDefault()
  form.post('/register', {
    onFinish: () => {},
  })
}
</script>

<template>
  <AuthSimpleLayout title="Create account">
    <Head title="Register" />

    <form @submit="submit" class="space-y-4">
      <div>
        <label class="block text-sm text-slate-300 mb-1">Full name</label>
        <input v-model="form.name" type="text" required class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-700 text-slate-100" />
        <p v-if="form.errors.name" class="text-xs text-red-400 mt-1">{{ form.errors.name }}</p>
      </div>

      <div>
        <label class="block text-sm text-slate-300 mb-1">Email</label>
        <input v-model="form.email" type="email" required class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-700 text-slate-100" />
        <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
      </div>

      <div>
        <label class="block text-sm text-slate-300 mb-1">Password</label>
        <input v-model="form.password" type="password" required class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-700 text-slate-100" />
        <p v-if="form.errors.password" class="text-xs text-red-400 mt-1">{{ form.errors.password }}</p>
      </div>

      <div>
        <label class="block text-sm text-slate-300 mb-1">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" required class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-700 text-slate-100" />
      </div>

      <div>
        <button :disabled="form.processing" type="submit" class="w-full px-4 py-2 rounded bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-medium">
          Create account
        </button>
      </div>

      <p class="text-center text-sm text-slate-400">Already have an account? <a href="/login" class="text-blue-400 hover:underline">Sign in</a></p>
    </form>
  </AuthSimpleLayout>
</template>
