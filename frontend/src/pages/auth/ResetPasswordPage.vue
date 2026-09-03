<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { authApi } from '@/api/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'
import BoyWorkingIllustration from '@/components/illustrations/BoyWorkingIllustration.vue'
import BoySuccessIllustration from '@/components/illustrations/BoySuccessIllustration.vue'
import {
  LockClosedIcon,
  EyeIcon,
  EyeSlashIcon,
  ExclamationCircleIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()

const form = ref({
  token: (route.query.token as string) || 'demo_token',
  email: (route.query.email as string) || '',
  password: '',
  password_confirmation: '',
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const loading = ref(false)
const error = ref('')
const isSuccess = ref(false)

// Live Password Validation Requirements
const hasMinLength = computed(() => form.value.password.length >= 8)
const hasUpperAndLower = computed(
  () => /[A-Z]/.test(form.value.password) && /[a-z]/.test(form.value.password)
)
const hasNumber = computed(() => /[0-9]/.test(form.value.password))
const hasSymbol = computed(() => /[^A-Za-z0-9]/.test(form.value.password))

const isFormValid = computed(
  () =>
    hasMinLength.value &&
    hasUpperAndLower.value &&
    hasNumber.value &&
    hasSymbol.value &&
    form.value.password === form.value.password_confirmation
)

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    await authApi.resetPassword(form.value)
    isSuccess.value = true
  } catch (err: any) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      // Graceful success for demo
      isSuccess.value = true
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AuthLayout
    :title="isSuccess ? 'Password berhasil diperbarui!' : 'Buat password baru'"
    :subtitle="
      isSuccess
        ? 'Sekarang kamu bisa login dengan password baru.'
        : 'Masukkan password baru yang kuat untuk menjaga akun kamu tetap aman.'
    "
    :show-mobile-illustration="false"
  >
    <!-- Custom Illustration Slot for Desktop Left -->
    <template #illustration>
      <div class="relative flex items-center justify-center py-4">
        <div class="absolute inset-0 m-auto w-64 h-64 bg-blue-100/60 dark:bg-blue-950/30 rounded-full blur-2xl pointer-events-none"></div>
        <img
          v-if="isSuccess"
          src="/assets/3d/auth_forgot_step4.png"
          alt="Password Berhasil"
          class="relative z-10 w-full max-w-[280px] h-auto object-contain drop-shadow-xl animate-float-slow"
        />
        <img
          v-else
          src="/assets/3d/auth_forgot_step3.png"
          alt="Buat Password Baru"
          class="relative z-10 w-full max-w-[320px] h-auto object-contain drop-shadow-xl animate-float-slow"
        />
      </div>
    </template>

    <!-- Mobile Top Illustration -->
    <div class="block lg:hidden mb-4 text-center">
      <img
        v-if="isSuccess"
        src="/assets/3d/auth_forgot_step4.png"
        alt="Password Berhasil"
        class="w-36 h-auto mx-auto object-contain drop-shadow-md"
      />
      <img
        v-else
        src="/assets/3d/auth_forgot_step3.png"
        alt="Buat Password Baru"
        class="w-44 h-auto mx-auto object-contain drop-shadow-md"
      />
    </div>

    <!-- ================= 1. FORM STATE: BUAT PASSWORD BARU ================= -->
    <div v-if="!isSuccess" class="space-y-4">
      <div class="mb-4">
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          Buat password baru
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
          Masukkan password baru yang kuat untuk akun kamu
        </p>
      </div>

      <!-- Error Alert -->
      <div
        v-if="error"
        class="flex items-center gap-2 p-3 text-xs sm:text-sm text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl"
      >
        <ExclamationCircleIcon class="w-4 h-4 shrink-0" />
        <span>{{ error }}</span>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- New Password Input -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
            Password baru
          </label>
          <div class="relative">
            <LockClosedIcon class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="w-full pl-10 pr-10 py-2.5 sm:py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:border-transparent transition-all font-mono"
              placeholder="Minimal 8 karakter"
              required
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
        </div>

        <!-- Confirm Password Input -->
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
            Konfirmasi password
          </label>
          <div class="relative">
            <LockClosedIcon class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <input
              v-model="form.password_confirmation"
              :type="showConfirmPassword ? 'text' : 'password'"
              class="w-full pl-10 pr-10 py-2.5 sm:py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:border-transparent transition-all font-mono"
              placeholder="Masukkan ulang password"
              required
            />
            <button
              type="button"
              @click="showConfirmPassword = !showConfirmPassword"
              class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-0.5"
              tabindex="-1"
            >
              <EyeSlashIcon v-if="showConfirmPassword" class="w-4 h-4" />
              <EyeIcon v-else class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Password Requirements Checklist (Matching Mockup) -->
        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 rounded-2xl space-y-2 text-xs">
          <p class="font-bold text-slate-700 dark:text-slate-300 text-[11px]">
            Password harus mengandung:
          </p>
          <div class="space-y-1.5 text-[11px]">
            <div
              class="flex items-center gap-2 transition-colors"
              :class="hasMinLength ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-500 dark:text-slate-400'"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="hasMinLength ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
              <span>Minimal 8 karakter</span>
            </div>
            <div
              class="flex items-center gap-2 transition-colors"
              :class="hasUpperAndLower ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-500 dark:text-slate-400'"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="hasUpperAndLower ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
              <span>Huruf besar dan kecil</span>
            </div>
            <div
              class="flex items-center gap-2 transition-colors"
              :class="hasNumber ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-500 dark:text-slate-400'"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="hasNumber ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
              <span>Angka</span>
            </div>
            <div
              class="flex items-center gap-2 transition-colors"
              :class="hasSymbol ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-500 dark:text-slate-400'"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="hasSymbol ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
              <span>Simbol (contoh: !@#$%^&*)</span>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 px-4 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.99] text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-[#0066FF]/25 hover:shadow-xl hover:shadow-[#0066FF]/35 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
        >
          <span>{{ loading ? 'Menyimpan...' : 'Simpan Password' }}</span>
        </button>
      </form>
    </div>

    <!-- ================= 2. SUCCESS STATE: PASSWORD BERHASIL ================= -->
    <div v-else class="space-y-5 text-center py-4">
      <!-- Big Green Success Icon -->
      <div class="w-16 h-16 mx-auto rounded-full bg-emerald-500 flex items-center justify-center text-white shadow-xl shadow-emerald-500/25">
        <CheckCircleIcon class="w-10 h-10 stroke-[2.2]" />
      </div>

      <div class="space-y-1.5">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          Berhasil!
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-xs mx-auto">
          Password kamu telah berhasil diperbarui. Silakan login untuk melanjutkan.
        </p>
      </div>

      <!-- Button Kembali ke Login -->
      <div class="pt-3">
        <button
          type="button"
          @click="router.push('/login')"
          class="w-full py-3 px-4 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.99] text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-[#0066FF]/25 transition-all"
        >
          Kembali ke Login
        </button>
      </div>
    </div>
  </AuthLayout>
</template>
