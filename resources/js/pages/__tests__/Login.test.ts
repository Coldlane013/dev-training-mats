import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount, VueWrapper } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import Login from '../Login.vue'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'

// Mock the auth store
const mockAuthStore = {
  login: vi.fn(),
  isLoading: false,
  error: null,
}

// Get the mocked auth store from the setup
vi.mock('@/stores/axios-store', () => ({
  useAuthStore: () => mockAuthStore,
}))

describe('Login.vue', () => {
  let wrapper: VueWrapper

  const defaultProps = {
    canResetPassword: true,
    canRegister: true,
  }

  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
    mockAuthStore.login.mockClear()
    mockAuthStore.isLoading = false
    mockAuthStore.error = null

    // Mock window.location
    Object.defineProperty(window, 'location', {
      value: { href: '' },
      writable: true,
    })
  })

  const mountComponent = (props = {}) => {
    wrapper = mount(Login, {
      props: { ...defaultProps, ...props },
      global: {
        stubs: {
          'router-link': true,
          'inertia-link': true,
        },
      },
    })
  }

  describe('Component Rendering', () => {
    it('renders the login form correctly', () => {
      mountComponent()

      expect(wrapper.find('h2').text()).toBe('Sign in')
      expect(wrapper.find('form').exists()).toBe(true)
      expect(wrapper.find('input[type="email"]').exists()).toBe(true)
      expect(wrapper.find('input[type="password"]').exists()).toBe(true)
      expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
    })

    it('renders the page title correctly', () => {
      mountComponent()

      const head = wrapper.find('title')
      expect(head.text()).toBe('Log in')
    })

    it('renders the branding section on large screens', () => {
      mountComponent()

      const branding = wrapper.find('.hidden.lg\\:flex')
      expect(branding.exists()).toBe(true)
      expect(branding.text()).toContain('Welcome!')
    })
  })

  describe('Props Handling', () => {
    it('shows registration link when canRegister is true', () => {
      mountComponent({ canRegister: true })

      const registerLink = wrapper.findComponent(TextLink)
      expect(registerLink.exists()).toBe(true)
      expect(registerLink.text()).toBe('Sign up')
    })

    it('hides registration link when canRegister is false', () => {
      mountComponent({ canRegister: false })

      const registerSection = wrapper.find('.text-center')
      expect(registerSection.exists()).toBe(false)
    })

    it('shows password reset link when canResetPassword is true', () => {
      mountComponent({ canResetPassword: true })

      const resetLink = wrapper.findAllComponents(TextLink).find(link =>
        link.text().includes('Forgot password')
      )
      expect(resetLink).toBeDefined()
    })

    it('hides password reset link when canResetPassword is false', () => {
      mountComponent({ canResetPassword: false })

      const resetLinks = wrapper.findAllComponents(TextLink).filter(link =>
        link.text().includes('Forgot password')
      )
      expect(resetLinks.length).toBe(0)
    })

    it('displays status message when status prop is provided', () => {
      mountComponent({ status: 'Login successful!' })

      const statusDiv = wrapper.find('.bg-green-50')
      expect(statusDiv.exists()).toBe(true)
      expect(statusDiv.text()).toBe('Login successful!')
    })

    it('does not display status message when status prop is not provided', () => {
      mountComponent()

      const statusDiv = wrapper.find('.bg-green-50')
      expect(statusDiv.exists()).toBe(false)
    })
  })

  describe('Form Interactions', () => {
    it('updates email field when user types', async () => {
      mountComponent()

      const emailInput = wrapper.find('input[type="email"]')
      await emailInput.setValue('test@example.com')

      expect(wrapper.vm.form.email).toBe('test@example.com')
    })

    it('updates password field when user types', async () => {
      mountComponent()

      const passwordInput = wrapper.find('input[type="password"]')
      await passwordInput.setValue('password123')

      expect(wrapper.vm.form.password).toBe('password123')
    })

    it('has autofocus on email field', () => {
      mountComponent()

      const emailInput = wrapper.find('input[type="email"]')
      expect(emailInput.attributes('autofocus')).toBeDefined()
    })

    it('has required attributes on form fields', () => {
      mountComponent()

      const emailInput = wrapper.find('input[type="email"]')
      const passwordInput = wrapper.find('input[type="password"]')

      expect(emailInput.attributes('required')).toBeDefined()
      expect(passwordInput.attributes('required')).toBeDefined()
    })
  })

  describe('Form Submission', () => {
    it('calls auth.login with correct credentials on form submission', async () => {
      mountComponent()
      mockAuthStore.login.mockResolvedValue({})

      const emailInput = wrapper.find('input[type="email"]')
      const passwordInput = wrapper.find('input[type="password"]')
      const form = wrapper.find('form')

      await emailInput.setValue('test@example.com')
      await passwordInput.setValue('password123')
      await form.trigger('submit.prevent')

      expect(mockAuthStore.login).toHaveBeenCalledWith('test@example.com', 'password123')
    })

    it('redirects to dashboard on successful login', async () => {
      mountComponent()
      mockAuthStore.login.mockResolvedValue({})

      const form = wrapper.find('form')
      await form.trigger('submit.prevent')

      expect(window.location.href).toBe('/dashboard')
    })

    it('displays general error message on login failure', async () => {
      mountComponent()
      const error = new Error('Login failed')
      mockAuthStore.login.mockRejectedValue(error)

      const form = wrapper.find('form')
      await form.trigger('submit.prevent')

      await wrapper.vm.$nextTick()

      const errorDiv = wrapper.find('.bg-red-50')
      expect(errorDiv.exists()).toBe(true)
      expect(errorDiv.text()).toBe('Login failed')
    })

    it('displays field-specific errors when validation errors are present', async () => {
      mountComponent()
      const validationError = {
        response: {
          data: {
            errors: {
              email: ['The email field is required.'],
              password: ['The password field is required.'],
            },
          },
        },
      }
      mockAuthStore.login.mockRejectedValue(validationError)

      const form = wrapper.find('form')
      await form.trigger('submit.prevent')

      await wrapper.vm.$nextTick()

      expect(wrapper.vm.errors.email).toBe('The email field is required.')
      expect(wrapper.vm.errors.password).toBe('The password field is required.')
    })

    it('clears previous errors before new submission', async () => {
      mountComponent()

      // Set initial errors
      wrapper.vm.errors.email = 'Previous error'
      wrapper.vm.errors.password = 'Previous error'

      // Mock successful login
      mockAuthStore.login.mockResolvedValue({})

      const form = wrapper.find('form')
      await form.trigger('submit.prevent')

      expect(wrapper.vm.errors.email).toBeUndefined()
      expect(wrapper.vm.errors.password).toBeUndefined()
    })
  })

  describe('Loading States', () => {
    it('shows spinner and loading text when processing', async () => {
      mountComponent()
      mockAuthStore.isLoading = true

      await wrapper.vm.$nextTick()

      const button = wrapper.find('button[type="submit"]')
      const spinner = wrapper.findComponent(Spinner)
      const buttonText = button.find('span')

      expect(button.attributes('disabled')).toBeDefined()
      expect(spinner.exists()).toBe(true)
      expect(buttonText.text()).toBe('Signing in...')
    })

    it('shows normal submit button when not processing', () => {
      mountComponent()
      mockAuthStore.isLoading = false

      const button = wrapper.find('button[type="submit"]')
      const spinner = wrapper.findComponent(Spinner)
      const buttonText = button.find('span')

      expect(button.attributes('disabled')).toBeUndefined()
      expect(spinner.exists()).toBe(false)
      expect(buttonText.text()).toBe('Sign in')
    })
  })

  describe('Error Display', () => {
    it('displays general error message', () => {
      mountComponent()

      wrapper.vm.errors.general = 'Authentication failed'

      const errorDiv = wrapper.find('.bg-red-50')
      expect(errorDiv.exists()).toBe(true)
      expect(errorDiv.text()).toBe('Authentication failed')
    })

    it('renders InputError components for field errors', () => {
      mountComponent()

      wrapper.vm.errors.email = 'Invalid email'
      wrapper.vm.errors.password = 'Invalid password'

      const inputErrors = wrapper.findAllComponents(InputError)
      expect(inputErrors.length).toBe(2)
    })
  })

  describe('Accessibility', () => {
    it('has proper labels for form fields', () => {
      mountComponent()

      const emailLabel = wrapper.find('label[for="email"]')
      const passwordLabel = wrapper.find('label[for="password"]')

      expect(emailLabel.text()).toBe('Email address')
      expect(passwordLabel.text()).toBe('Password')
    })

    it('has proper form attributes', () => {
      mountComponent()

      const form = wrapper.find('form')
      expect(form.attributes('role')).toBeUndefined() // Forms don't need role by default

      const emailInput = wrapper.find('input[type="email"]')
      expect(emailInput.attributes('autocomplete')).toBe('email')

      const passwordInput = wrapper.find('input[type="password"]')
      expect(passwordInput.attributes('autocomplete')).toBe('current-password')
    })
  })
})
