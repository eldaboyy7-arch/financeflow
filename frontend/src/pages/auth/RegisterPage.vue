<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { authApi } from '@/api/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'
import {
  UserIcon,
  EnvelopeIcon,
  LockClosedIcon,
  EyeIcon,
  EyeSlashIcon,
  ExclamationCircleIcon,
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const showPassword = ref(false)
const errors = ref<Record<string, string[]>>({})
const loading = ref(false)
const globalError = ref('')

async function handleRegister() {
  errors.value = {}
  globalError.value = ''
  loading.value = true
  try {
    const { data } = await authApi.register(form.value)
    authStore.setAuth(data.user, data.token)
    router.push('/')
  } catch (err: any) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      globalError.value = err.response?.data?.message ?? 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}

function fieldError(field: string): string | undefined {
  return errors.value[field]?.[0]
}

function handleSocialLogin(provider: string) {
  alert(`Daftar dengan ${provider} akan segera tersedia. Silakan gunakan formulir pendaftaran.`)
}
</script>

<template>
  <AuthLayout
    title="Mulai kelola keuanganmu sekarang"
    subtitle="Bergabunglah dengan Financial Flow untuk merencanakan, melacak, dan mengoptimalkan aset kamu secara cerdas."
  >
    <!-- 3D Illustration for Desktop Left -->
    <template #illustration>
      <div class="relative flex items-center justify-center py-4">
        <div class="absolute inset-0 m-auto w-64 h-64 bg-blue-100/60 dark:bg-blue-950/30 rounded-full blur-2xl pointer-events-none"></div>
        <img
          src="/assets/3d/auth_register_form.png"
          alt="Daftar Akun"
          class="relative z-10 w-full max-w-[280px] h-auto object-contain drop-shadow-xl animate-float-slow"
        />
      </div>
    </template>

    <!-- 3D Illustration for Mobile Bottom -->
    <template #mobile-illustration>
      <div class="flex items-center justify-center">
        <img
          src="/assets/3d/auth_register_form.png"
          alt="Daftar Akun"
          class="w-36 h-auto object-contain drop-shadow-md"
        />
      </div>
    </template>

    <!-- Header -->
    <div class="mb-5">
      <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
        Daftar Akun Baru
      </h2>
      <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 font-normal">
        Kelola arus keuangan pribadi & bisnis dengan cerdas
      </p>
    </div>

    <!-- Error Alert -->
    <div
      v-if="globalError"
      class="flex items-center gap-2 p-3 text-xs sm:text-sm text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl mb-4"
    >
      <ExclamationCircleIcon class="w-4 h-4 shrink-0" />
      <span>{{ globalError }}</span>
    </div>

    <!-- Register Form -->
    <form @submit.prevent="handleRegister" class="space-y-3.5">
      <!-- Name -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
          Nama Lengkap
        </label>
        <div class="relative">
          <UserIcon class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
          <input
            v-model="form.name"
            type="text"
            class="w-full pl-10 pr-3.5 py-2.5 sm:py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:border-transparent transition-all"
            placeholder="Masukkan nama lengkap kamu"
            required
            autocomplete="name"
          />
        </div>
        <p v-if="fieldError('name')" class="text-xs text-rose-500 mt-1">{{ fieldError('name') }}</p>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
          Email
        </label>
        <div class="relative">
          <EnvelopeIcon class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
          <input
            v-model="form.email"
            type="email"
            class="w-full pl-10 pr-3.5 py-2.5 sm:py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:border-transparent transition-all"
            placeholder="Masukkan email kamu"
            required
            autocomplete="email"
          />
        </div>
        <p v-if="fieldError('email')" class="text-xs text-rose-500 mt-1">{{ fieldError('email') }}</p>
      </div>

      <!-- Password -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
          Password
        </label>
        <div class="relative">
          <LockClosedIcon class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
          <input
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            class="w-full pl-10 pr-10 py-2.5 sm:py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:border-transparent transition-all font-mono"
            placeholder="Minimal 8 karakter"
            required
            autocomplete="new-password"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-0.5"
            tabindex="-1"
          >
            <EyeSlashIcon v-if="showPassword" class="w-4 h-4" />
            <EyeIcon v-else class="w-4 h-4" />
          </button>
        </div>
        <p v-if="fieldError('password')" class="text-xs text-rose-500 mt-1">{{ fieldError('password') }}</p>
      </div>

      <!-- Password Confirmation -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
          Konfirmasi Password
        </label>
        <div class="relative">
          <LockClosedIcon class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
          <input
            v-model="form.password_confirmation"
            :type="showPassword ? 'text' : 'password'"
            class="w-full pl-10 pr-3.5 py-2.5 sm:py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:border-transparent transition-all font-mono"
            placeholder="Ulangi password"
            required
            autocomplete="new-password"
          />
        </div>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="loading"
        class="w-full py-3 px-4 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.99] text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-[#0066FF]/25 hover:shadow-xl hover:shadow-[#0066FF]/35 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed mt-2"
      >
        <span>{{ loading ? 'Mendaftarkan...' : 'Daftar Sekarang' }}</span>
      </button>
    </form>

    <!-- Footer Login CTA -->
    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
      Sudah punya akun?
      <RouterLink
        to="/login"
        class="font-bold text-[#0066FF] hover:text-[#0052CC] dark:text-blue-400 dark:hover:text-blue-300 transition-colors ml-1"
      >
        Masuk di sini
      </RouterLink>
    </div>
  </AuthLayout>
</template>
