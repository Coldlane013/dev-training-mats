import { beforeAll } from 'vitest'

// Mock Inertia.js
global.window = global.window || {}
global.window.location = { href: '' }

// Mock Pinia stores
vi.mock('@/stores/axios-store', () => ({
  useAuthStore: () => ({
    login: vi.fn(),
    isLoading: false,
    error: null,
  }),
}))

// Mock Inertia components
vi.mock('@inertiajs/vue3', () => ({
  Head: {
    name: 'Head',
    props: ['title'],
    template: '<title>{{ title }}</title>',
  },
  Link: {
    name: 'Link',
    props: ['href', 'method', 'as', 'tabindex'],
    template: '<a :href="href" @click.prevent="$emit(\'click\')"><slot /></a>',
  },
}))

// Mock route functions
vi.mock('@/routes', () => ({
  register: vi.fn(() => '/register'),
  dashboard: { url: vi.fn(() => '/dashboard') },
}))

vi.mock('@/routes/password', () => ({
  request: vi.fn(() => '/password/reset'),
}))
