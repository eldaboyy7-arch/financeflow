<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { authApi } from '@/api/auth'
import AuthLayout from '@/layouts/AuthLayout.vue'
import BoyThinkingIllustration from '@/components/illustrations/BoyThinkingIllustration.vue'
import MailEnvelopeIllustration from '@/components/illustrations/MailEnvelopeIllustration.vue'
import {
  EnvelopeIcon,
  ExclamationCircleIcon,
  InformationCircleIcon,
  ArrowLeftIcon,
} from '@heroicons/vue/24/outline'

const router = useRouter()
const email = ref('')
const loading = ref(false)
const error = ref('')
const step = ref<1 | 2>(1) // 1: Input Email, 2: Cek Email (Sent)
const resendCooldown = ref(0)

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    await authApi.forgotPassword({ email: email.value })
    step.value = 2
    startCooldown()
  } catch (err: any) {
    // If backend returns status or error message
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      // Graceful fallback for mock/demo
      step.value = 2
      startCooldown()
    }
  } finally {
    loading.value = false
  }
}

function startCooldown() {
  resendCooldown.value = 60
  const timer = setInterval(() => {
    resendCooldown.value--
    if (resendCooldown.value <= 0) {
      clearInterval(timer)
    }
  }, 1000)
}

async function handleResend() {
  if (resendCooldown.value > 0 || loading.value) return
  loading.value = true
  try {
    await authApi.forgotPassword({ email: email.value })
    startCooldown()
  } catch {
    startCooldown()
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div>
    <!-- ================= MOBILE APP-BAR (Only on Mobile) ================= -->
    <div class="flex lg:hidden items-center justify-between px-4 py-3 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 fixed top-0 inset-x-0 z-30">
      <button
        @click="step === 2 ? (step = 1) : router.push('/login')"
        class="p-1.5 -ml-1.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
      >
        <ArrowLeftIcon class="w-5 h-5" />
      </button>
      <h2 class="text-sm font-bold text-slate-900 dark:text-white">
        Lupa Password
      </h2>
      <div class="w-8"></div>
    </div>

    <!-- Padding for Mobile App Bar -->
    <div class="h-10 lg:hidden"></div>

    <AuthLayout
      :title="step === 1 ? 'Lupa password?' : 'Cek email kamu'"
      :subtitle="
        step === 1
          ? 'Jangan khawatir, kami akan bantu kamu mereset password dengan mudah.'
          : `Kami telah mengirimkan link reset password ke email: ${email || 'email kamu'}`
      "
      :show-mobile-illustration="false"
    >
      <!-- Custom Illustration Slot for Desktop Left -->
      <template #illustration>
        <div class="relative flex items-center justify-center py-4">
          <div class="absolute inset-0 m-auto w-64 h-64 bg-blue-100/60 dark:bg-blue-950/30 rounded-full blur-2xl pointer-events-none"></div>
          <img
            v-if="step === 1"
            src="/assets/3d/auth_forgot_step1.png"
            alt="Lupa Password"
            class="relative z-10 w-full max-w-[280px] h-auto object-contain drop-shadow-xl animate-float-slow"
          />
          <img
            v-else
            src="/assets/3d/auth_forgot_step2.png"
            alt="Cek Email"
            class="relative z-10 w-full max-w-[280px] h-auto object-contain drop-shadow-xl animate-float-slow"
          />
        </div>
      </template>

      <!-- Mobile Top Illustration Inside Card / Header -->
      <div class="block lg:hidden mb-4 text-center">
        <img
          v-if="step === 1"
          src="/assets/3d/auth_forgot_step1.png"
          alt="Lupa Password"
          class="w-36 h-auto mx-auto object-contain drop-shadow-md"
        />
        <img
          v-else
          src="/assets/3d/auth_forgot_step2.png"
          alt="Cek Email"
          class="w-36 h-auto mx-auto object-contain drop-shadow-md"
        />
      </div>

      <!-- ================= STEP 1: INPUT EMAIL ================= -->
      <div v-if="step === 1" class="space-y-4">
        <div class="mb-4">
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Masukkan email kamu
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
            Kami akan mengirimkan link reset password ke email yang terdaftar.
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
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
              Email
            </label>
            <div class="relative">
              <EnvelopeIcon class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
              <input
                v-model="email"
                type="email"
                class="w-full pl-10 pr-3.5 py-2.5 sm:py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:border-transparent transition-all"
                placeholder="Masukkan email kamu"
                required
                autocomplete="email"
              />
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 px-4 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.99] text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-[#0066FF]/25 hover:shadow-xl hover:shadow-[#0066FF]/35 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <span>{{ loading ? 'Mengirim link...' : 'Kirim Link Reset' }}</span>
          </button>
        </form>

        <div class="pt-2 text-center">
          <RouterLink
            to="/login"
            class="text-xs font-bold text-[#0066FF] hover:text-[#0052CC] dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
          >
            Kembali ke login
          </RouterLink>
        </div>
      </div>

      <!-- ================= STEP 2: CEK EMAIL (SENT) ================= -->
      <div v-else class="space-y-4 text-center">
        <!-- Blue Icon Circle -->
        <div class="w-12 h-12 mx-auto rounded-full bg-blue-50 dark:bg-blue-950/60 flex items-center justify-center text-[#0066FF]">
          <InformationCircleIcon class="w-7 h-7" />
        </div>

        <div class="space-y-1">
          <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">
            Link telah dikirim!
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-xs mx-auto">
            Silakan buka email kamu dan klik link untuk mereset password.
          </p>
        </div>

        <!-- Expiration info box -->
        <div class="p-3 bg-blue-50/80 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-2xl flex items-center justify-center gap-2 text-xs text-[#0066FF] dark:text-blue-300">
          <InformationCircleIcon class="w-4 h-4 shrink-0" />
          <span>Link akan kedaluwarsa dalam 15 menit.</span>
        </div>

        <!-- Resend Link -->
        <div class="text-xs text-slate-500 dark:text-slate-400 pt-2">
          Belum menerima email?
          <button
            type="button"
            @click="handleResend"
            :disabled="resendCooldown > 0 || loading"
            class="font-bold text-[#0066FF] hover:text-[#0052CC] dark:text-blue-400 dark:hover:text-blue-300 transition-colors ml-1 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ resendCooldown > 0 ? `Kirim ulang (${resendCooldown}s)` : 'Kirim ulang' }}
          </button>
        </div>

        <!-- Back to Login -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
          <RouterLink
            to="/login"
            class="text-xs font-bold text-[#0066FF] hover:text-[#0052CC] dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
          >
            Kembali ke login
          </RouterLink>
        </div>
      </div>

      <!-- ================= MOBILE STEPPER (1 - 2 - 3) ================= -->
      <div class="flex lg:hidden items-center justify-center gap-3 pt-6 mt-4 border-t border-slate-100 dark:border-slate-800">
        <!-- Step 1 -->
        <div
          class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold transition-all"
          :class="step >= 1 ? 'bg-[#0066FF] text-white shadow-md shadow-[#0066FF]/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'"
        >
          1
        </div>
        <div class="w-8 h-0.5" :class="step >= 2 ? 'bg-[#0066FF]' : 'bg-slate-200 dark:bg-slate-700'"></div>
        <!-- Step 2 -->
        <div
          class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold transition-all"
          :class="step >= 2 ? 'bg-[#0066FF] text-white shadow-md shadow-[#0066FF]/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'"
        >
          2
        </div>
        <div class="w-8 h-0.5 bg-slate-200 dark:bg-slate-700"></div>
        <!-- Step 3 -->
        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400">
          3
        </div>
      </div>
    </AuthLayout>
  </div>
</template>
