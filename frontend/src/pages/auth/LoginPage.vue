<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useDashboardStore } from '@/stores/dashboard'
import { useAccountsStore } from '@/stores/accounts'
import { useBudgetsStore } from '@/stores/budgets'
import { useGoalsStore } from '@/stores/goals'
import { useRecurringStore } from '@/stores/recurring'
import { authApi } from '@/api/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'
import MoneySpinner from '@/components/MoneySpinner.vue'
import BrandLogo from '@/components/BrandLogo.vue'
import {
  EnvelopeIcon,
  LockClosedIcon,
  EyeIcon,
  EyeSlashIcon,
  ExclamationCircleIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()
const dashboardStore = useDashboardStore()
const accountsStore = useAccountsStore()
const budgetsStore = useBudgetsStore()
const goalsStore = useGoalsStore()
const recurringStore = useRecurringStore()

const form = ref({ email: '', password: '' })
const rememberMe = ref(true)
const showPassword = ref(false)
const error = ref('')
const loading = ref(false)

// Fullscreen Clean White Syncing Screen State
const isSyncing = ref(false)
const syncProgress = ref(10)
const syncStep = ref('Memverifikasi akun & izin akses...')

async function handleLogin() {
  error.value = ''
  
  // 1. Instantly display clean white transition screen & start progress animation immediately
  isSyncing.value = true
  syncProgress.value = 10
  syncStep.value = 'Memverifikasi akun & izin akses...'

  const totalDuration = 1400 // Snappy & ultra smooth 1.4s total
  const startTime = performance.now()
  let hasError = false

  // 2. Start continuous 60fps progress bar animation immediately
  const animPromise = new Promise<void>((resolve) => {
    const interval = setInterval(() => {
      if (hasError) {
        clearInterval(interval)
        resolve()
        return
      }
      const elapsed = performance.now() - startTime
      const progress = Math.min(100, Math.round((elapsed / totalDuration) * 100))
      syncProgress.value = Math.max(10, progress)

      if (progress < 35) {
        syncStep.value = 'Memverifikasi akun & izin akses...'
      } else if (progress < 70) {
        syncStep.value = 'Mengambil saldo & data rekening...'
      } else if (progress < 95) {
        syncStep.value = 'Menyinkronkan transaksi & anggaran...'
      } else {
        syncStep.value = 'Dasbor siap! Membuka...'
      }

      if (progress >= 100) {
        clearInterval(interval)
        resolve()
      }
    }, 20)
  })

  // 3. Concurrently execute login API call & store credentials
  try {
    const res = await authApi.login(form.value)
    authStore.setAuth(res.data.user, res.data.token)
  } catch (err: any) {
    // If login truly failed: dismiss white screen immediately and show error message
    hasError = true
    isSyncing.value = false
    loading.value = false
    error.value = err.response?.data?.message ?? 'Email atau password salah. Silakan coba lagi.'
    return
  }

  // 4. Concurrently prefetch dashboard data
  const dataFetchPromise = Promise.allSettled([
    dashboardStore.fetchDashboard(true),
    goalsStore.fetchGoals(true),
    recurringStore.fetchAll(true),
    accountsStore.fetchAccounts(),
    budgetsStore.fetchBudgets(),
  ])

  // 5. Wait for both animation & pre-warm to complete
  await Promise.all([animPromise, dataFetchPromise])

  // 6. Brief hold at 100% full
  syncProgress.value = 100
  syncStep.value = 'Dasbor siap!'
  await new Promise((r) => setTimeout(r, 200))

  // 7. Transition to dashboard
  try {
    await router.replace('/')
  } catch {
    window.location.href = '/'
  }
}

function handleSocialLogin(provider: string) {
  // Demo toast or notification for social login
  alert(`Login dengan ${provider} akan segera tersedia. Silakan gunakan email dan password.`)
}
</script>

<template>
  <div>
    <!-- ================= FULLSCREEN CLEAN WHITE SYNC / TRANSITION SCREEN ================= -->
    <Teleport to="body">
      <Transition name="fade-sync">
        <div
          v-if="isSyncing"
          class="fixed inset-0 z-[9999] bg-gradient-to-b from-white via-slate-50 to-blue-50/40 dark:from-slate-900 dark:via-slate-900 dark:to-slate-950 flex flex-col items-center justify-center p-6 text-slate-900 dark:text-white select-none overflow-hidden"
        >
          <!-- Subtle Ambient Glow -->
          <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-[#0066FF]/10 dark:bg-[#0066FF]/15 rounded-full blur-3xl pointer-events-none"></div>
          <div class="absolute -bottom-32 -right-32 w-[500px] h-[500px] bg-sky-400/10 dark:bg-sky-400/10 rounded-full blur-3xl pointer-events-none"></div>

          <div class="relative z-10 flex flex-col items-center max-w-sm w-full text-center space-y-6">
            <!-- Brand Logo -->
            <BrandLogo variant="full" size="xl" show-tagline />

            <!-- 3D Money Spinner Animation -->
            <div class="py-2 transform scale-110">
              <MoneySpinner size="lg" message="" />
            </div>

            <!-- Loading Status & Progress Bar -->
            <div class="w-full space-y-3 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xl shadow-blue-500/5">
              <!-- Animated Step Text -->
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-700 dark:text-slate-200 font-bold flex items-center gap-2 truncate">
                  <span
                    class="w-2 h-2 rounded-full"
                    :class="syncProgress >= 100 ? 'bg-emerald-500' : 'bg-[#0066FF] animate-ping'"
                  ></span>
                  {{ syncStep }}
                </span>
                <span
                  class="font-mono font-extrabold tabular-nums text-sm transition-colors"
                  :class="syncProgress >= 100 ? 'text-emerald-500 dark:text-emerald-400' : 'text-[#0066FF] dark:text-sky-400'"
                >
                  {{ syncProgress }}%
                </span>
              </div>

              <!-- Progress Bar -->
              <div class="w-full h-3 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden p-0.5 border border-slate-200/60 dark:border-slate-600">
                <div
                  class="h-full rounded-full transition-all duration-75 ease-linear shadow-[0_0_10px_rgba(0,102,255,0.4)]"
                  :class="syncProgress >= 100 ? 'bg-emerald-500 shadow-emerald-500/50' : 'bg-gradient-to-r from-[#0066FF] via-[#0052CC] to-sky-400'"
                  :style="{ width: `${syncProgress}%` }"
                ></div>
              </div>

              <div class="flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500 pt-1">
                <span>Sinkronisasi Data Finansial</span>
                <span v-if="syncProgress >= 100" class="text-emerald-500 font-semibold flex items-center gap-1">
                  <CheckCircleIcon class="w-3.5 h-3.5" /> 100% Selesai
                </span>
                <span v-else>Memproses...</span>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ================= AUTH LOGIN FORM CARD ================= -->
    <AuthLayout
      title="Kelola keuanganmu dengan lebih mudah"
      subtitle="Financial Flow membantu kamu merencanakan, melacak, dan mengembangkan keuangan pribadi & bisnis secara efisien."
    >
      <!-- Header -->
      <div class="mb-6">
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
          Masuk ke akun
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 font-normal">
          Kelola arus keuangan pribadi & bisnis dengan cerdas
        </p>
      </div>

      <!-- Error Alert -->
      <div
        v-if="error"
        class="flex items-center gap-2 p-3 text-xs sm:text-sm text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl mb-4"
      >
        <ExclamationCircleIcon class="w-4 h-4 shrink-0" />
        <span>{{ error }}</span>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <!-- Email Input -->
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
        </div>

        <!-- Password Input with Show/Hide Toggle -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
              Password
            </label>
            <RouterLink
              to="/lupa-password"
              class="text-xs font-semibold text-[#0066FF] hover:text-[#0052CC] dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
            >
              Lupa password?
            </RouterLink>
          </div>
          <div class="relative">
            <LockClosedIcon class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="w-full pl-10 pr-10 py-2.5 sm:py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:border-transparent transition-all font-mono"
              placeholder="Masukkan password"
              required
              autocomplete="current-password"
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

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-0.5">
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input
              v-model="rememberMe"
              type="checkbox"
              class="w-4 h-4 rounded text-[#0066FF] focus:ring-[#0066FF] border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
            />
            <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Ingat saya</span>
          </label>
        </div>

        <!-- Submit Button (Masuk) -->
        <button
          type="submit"
          :disabled="loading || isSyncing"
          class="w-full py-3 px-4 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.99] text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-[#0066FF]/25 hover:shadow-xl hover:shadow-[#0066FF]/35 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed mt-2"
        >
          <span>Masuk</span>
        </button>
      </form>

      <!-- Footer Register CTA -->
      <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
        Belum punya akun?
        <RouterLink
          to="/daftar"
          class="font-bold text-[#0066FF] hover:text-[#0052CC] dark:text-blue-400 dark:hover:text-blue-300 transition-colors ml-1"
        >
          Daftar sekarang
        </RouterLink>
      </div>
    </AuthLayout>
  </div>
</template>

<style scoped>
.fade-sync-enter-active,
.fade-sync-leave-active {
  transition: opacity 0.3s ease;
}

.fade-sync-enter-from,
.fade-sync-leave-to {
  opacity: 0;
}
</style>
