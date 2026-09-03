<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
}>()

const router = useRouter()
const authStore = useAuthStore()
const isExiting = ref(false)
const exitProgress = ref(0)

function closeModal() {
  if (isExiting.value) return
  emit('update:modelValue', false)
}

async function confirmLogout() {
  isExiting.value = true
  exitProgress.value = 15

  // Animate exit sync screen
  const startTime = performance.now()
  const duration = 1000

  await new Promise<void>((resolve) => {
    const interval = setInterval(() => {
      const elapsed = performance.now() - startTime
      const progress = Math.min(100, Math.round((elapsed / duration) * 100))
      exitProgress.value = Math.max(15, progress)

      if (progress >= 100) {
        clearInterval(interval)
        resolve()
      }
    }, 20)
  })

  await new Promise((r) => setTimeout(r, 150))

  try {
    await authStore.logout()
  } catch (e) {
    console.warn('Logout notice:', e)
  }

  emit('update:modelValue', false)
  window.location.href = '/login'
}
</script>

<template>
  <Teleport to="body">
    <!-- ================= FULLSCREEN SMOOTH EXIT SCREEN ================= -->
    <Transition name="fade-exit">
      <div
        v-if="isExiting"
        class="fixed inset-0 z-[10000] bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl flex flex-col items-center justify-center p-6 text-slate-900 dark:text-white select-none text-center"
      >
        <!-- Ambient Blue/Indigo Aura -->
        <div class="absolute w-72 h-72 bg-blue-500/15 dark:bg-blue-500/20 rounded-full blur-3xl pointer-events-none animate-pulse"></div>

        <!-- Animated Vault Locking SVG -->
        <div class="relative z-10 w-28 h-28 mb-6">
          <svg class="w-full h-full drop-shadow-xl animate-float-slow" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="60" cy="60" r="50" fill="url(#exitGlow)" fill-opacity="0.15" />
            <!-- Vault Body -->
            <rect x="25" y="32" width="70" height="60" rx="16" fill="url(#vaultGrad)" stroke="#0066FF" stroke-width="2" />
            <!-- Safe Door Dial -->
            <circle cx="60" cy="62" r="18" fill="#1E293B" stroke="#38BDF8" stroke-width="2" />
            <circle cx="60" cy="62" r="8" fill="#0066FF" />
            <!-- Dial spokes -->
            <line x1="60" y1="48" x2="60" y2="52" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" />
            <line x1="60" y1="72" x2="60" y2="76" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" />
            <line x1="46" y1="62" x2="50" y2="62" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" />
            <line x1="70" y1="62" x2="74" y2="62" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" />
            <!-- Shackle / Top handle -->
            <path d="M42 32V24C42 14.0589 50.0589 6 60 6C69.9411 6 78 14.0589 78 24V32" stroke="#0066FF" stroke-width="3" stroke-linecap="round" />
            <!-- Lock sparkle -->
            <circle cx="82" cy="22" r="3" fill="#38BDF8" class="animate-ping" />
            <defs>
              <linearGradient id="vaultGrad" x1="25" y1="32" x2="95" y2="92" gradientUnits="userSpaceOnUse">
                <stop stop-color="#0F172A" />
                <stop offset="1" stop-color="#1E293B" />
              </linearGradient>
              <radialGradient id="exitGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(60 60) scale(50)">
                <stop stop-color="#0066FF" />
                <stop offset="1" stop-color="#0066FF" stop-opacity="0" />
              </radialGradient>
            </defs>
          </svg>
        </div>

        <div class="relative z-10 space-y-2 max-w-xs">
          <p class="text-base font-black tracking-tight text-slate-900 dark:text-white">
            Mengamankan Sesi Akun...
          </p>
          <p class="text-xs text-slate-500 dark:text-slate-400">
            Data keuangan kamu tetap tersimpan aman di cloud
          </p>

          <!-- Smooth Mini Progress Bar -->
          <div class="w-48 mx-auto h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden mt-3 p-0.5">
            <div
              class="h-full bg-gradient-to-r from-[#0066FF] to-sky-400 rounded-full transition-all duration-75"
              :style="{ width: `${exitProgress}%` }"
            ></div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ================= FLUTTER-STYLE CONFIRMATION MODAL ================= -->
    <Transition name="modal-bounce">
      <div
        v-if="modelValue && !isExiting"
        class="fixed inset-0 z-[9990] flex items-center justify-center p-4"
        @keydown.esc="closeModal"
      >
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity"
          @click="closeModal"
        ></div>

        <!-- Main Card -->
        <div
          class="relative w-full max-w-[360px] bg-white dark:bg-slate-900 rounded-[28px] p-6 shadow-2xl border border-slate-100 dark:border-slate-800 text-center space-y-5 transform transition-all select-none overflow-hidden"
        >
          <!-- Ambient Card Light Glow -->
          <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
          <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>

          <!-- Pure Code Vector Illustration (Flutter-style Floating Safe & Coin) -->
          <div class="relative mx-auto w-24 h-24 flex items-center justify-center">
            <!-- Background Glow Circle -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/40 dark:to-slate-800/60 rounded-3xl transform rotate-6 scale-95 border border-blue-100/60 dark:border-blue-900/30"></div>

            <svg class="relative z-10 w-20 h-20 animate-float" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Shadow base -->
              <ellipse cx="50" cy="85" rx="28" ry="6" fill="#000000" fill-opacity="0.08" />

              <!-- Shield / Safe Base -->
              <rect x="22" y="28" width="56" height="50" rx="14" fill="url(#cardGrad)" stroke="url(#cardBorder)" stroke-width="1.5" />

              <!-- Metallic Accent Bar -->
              <rect x="28" y="34" width="44" height="4" rx="2" fill="#38BDF8" fill-opacity="0.4" />

              <!-- Central Padlock Ring -->
              <path d="M38 32V24C38 17.3726 43.3726 12 50 12C56.6274 12 62 17.3726 62 24V32" stroke="url(#lockGrad)" stroke-width="3.5" stroke-linecap="round" />
              
              <!-- Keyhole Core -->
              <circle cx="50" cy="53" r="8" fill="#0066FF" />
              <path d="M50 51V58" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />

              <!-- Floating Micro Coin -->
              <g class="animate-float-coin">
                <circle cx="75" cy="30" r="10" fill="url(#goldGrad)" stroke="#F59E0B" stroke-width="1" />
                <text x="75" y="34" font-size="9" font-weight="900" text-anchor="middle" fill="#78350F">F</text>
              </g>

              <!-- Gradients -->
              <defs>
                <linearGradient id="cardGrad" x1="22" y1="28" x2="78" y2="78" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#1E293B" />
                  <stop offset="1" stop-color="#0F172A" />
                </linearGradient>
                <linearGradient id="cardBorder" x1="22" y1="28" x2="78" y2="78" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#38BDF8" />
                  <stop offset="1" stop-color="#0066FF" />
                </linearGradient>
                <linearGradient id="lockGrad" x1="38" y1="12" x2="62" y2="32" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#38BDF8" />
                  <stop offset="1" stop-color="#0066FF" />
                </linearGradient>
                <linearGradient id="goldGrad" x1="65" y1="20" x2="85" y2="40" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#FDE047" />
                  <stop offset="1" stop-color="#F59E0B" />
                </linearGradient>
              </defs>
            </svg>
          </div>

          <!-- Title & Friendly fintech message -->
          <div class="space-y-1.5">
            <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
              Keluar dari Sesi Ini?
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed px-1">
              Catatan transaksi dan saldo kamu tersimpan aman. Kamu bisa masuk kembali kapan saja.
            </p>
          </div>

          <!-- Buttons -->
          <div class="space-y-2.5 pt-1">
            <!-- Primary Action: Stay inside app (App friendly) -->
            <button
              type="button"
              @click="closeModal"
              class="w-full py-3 px-4 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.99] text-white text-xs font-bold rounded-2xl shadow-lg shadow-[#0066FF]/20 transition-all"
            >
              Tetap di Sini
            </button>

            <!-- Secondary Danger Action: Confirm Logout -->
            <button
              type="button"
              @click="confirmLogout"
              class="w-full py-2.5 px-4 bg-slate-50 hover:bg-rose-50 dark:bg-slate-800/60 dark:hover:bg-rose-950/30 text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 text-xs font-semibold rounded-2xl border border-slate-200/60 dark:border-slate-700/60 hover:border-rose-200 dark:hover:border-rose-900/40 transition-all flex items-center justify-center gap-1.5"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
              </svg>
              <span>Ya, Keluar Akun</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-4px) rotate(-1deg);
  }
}

@keyframes floatSlow {
  0%, 100% {
    transform: translateY(0px) scale(1);
  }
  50% {
    transform: translateY(-6px) scale(1.03);
  }
}

@keyframes floatCoin {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-5px) rotate(8deg);
  }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-float-slow {
  animation: floatSlow 2.5s ease-in-out infinite;
}

.animate-float-coin {
  animation: floatCoin 2.2s ease-in-out infinite;
}

.fade-exit-enter-active,
.fade-exit-leave-active {
  transition: opacity 0.25s ease;
}
.fade-exit-enter-from,
.fade-exit-leave-to {
  opacity: 0;
}

.modal-bounce-enter-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-bounce-leave-active {
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-bounce-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(12px);
}
.modal-bounce-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
