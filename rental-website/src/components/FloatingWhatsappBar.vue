<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'
import { useLanguage } from '@/composables/useLanguage'

const { isEnglish } = useLanguage()

const waUrl1 = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
const waUrl2 = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhoneSecondary, siteConfig.rentalName))

const isVisible = ref(true)

const handleScroll = () => {
  if (typeof window !== 'undefined') {
    // Di mobile (< 768px): Selalu tampil agar pengunjung mobile langsung dapat akses WA
    // Di desktop (>= 768px): Tampil elegan saat scroll melewati header (> 150px)
    isVisible.value = window.innerWidth < 768 || window.scrollY > 150
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  window.addEventListener('resize', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('resize', handleScroll)
})
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-6 scale-75"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-6 scale-75"
  >
    <aside
      v-show="isVisible"
      aria-label="Aksi Cepat WhatsApp"
      class="fixed bottom-5 right-4 sm:bottom-6 sm:right-6 z-40 flex flex-col items-end gap-2.5 pointer-events-auto"
    >
      <!-- Admin 2 (Cadangan / Tour Support) -->
      <a
        :href="waUrl2"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat WhatsApp Admin 2"
        class="group flex items-center gap-2"
        :title="isEnglish ? 'Chat Admin 2 · 0852-6326-7909' : 'Chat Admin 2 · 0852-6326-7909'"
      >
        <span class="hidden md:group-hover:flex items-center bg-slate-900/90 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-xl shadow-lg whitespace-nowrap border border-slate-700 pointer-events-none">
          Admin 2 · 0852-6326-7909
        </span>
        <div class="relative flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105 active:scale-95 ring-2 ring-white/95">
          <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>
          <span class="sm:hidden absolute -top-1 -left-1 bg-slate-800 text-slate-100 text-[8px] font-extrabold px-1 rounded-full shadow border border-slate-600 leading-tight">
            CS 2
          </span>
        </div>
      </a>

      <!-- Admin 1 (Utama - Lebih Besar) -->
      <a
        :href="waUrl1"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat WhatsApp Admin 1"
        class="group flex items-center gap-2"
        :title="isEnglish ? 'Chat WhatsApp Admin 1 · 0813-7237-1120 (Fast Response)' : 'Chat WhatsApp Admin 1 · 0813-7237-1120 (Fast Response)'"
      >
        <span class="hidden md:group-hover:flex items-center bg-slate-900/90 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-xl shadow-lg whitespace-nowrap border border-slate-700 pointer-events-none">
          Admin 1 · 0813-7237-1120 (Fast Response)
        </span>
        <div class="relative flex items-center justify-center w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white shadow-xl hover:shadow-2xl hover:shadow-emerald-500/40 transition-all duration-200 hover:scale-105 active:scale-95 ring-4 ring-white/95">
          <!-- Pulse animation online indicator -->
          <span class="absolute top-0 right-0 flex h-3.5 w-3.5 -mt-0.5 -mr-0.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-400 border-2 border-white"></span>
          </span>

          <svg class="w-6 h-6 sm:w-7 sm:h-7 shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>

          <span class="sm:hidden absolute -top-1 -left-1 bg-emerald-800 text-white text-[8px] font-extrabold px-1 rounded-full shadow border border-white leading-tight">
            CS 1
          </span>
        </div>
      </a>
    </aside>
  </Transition>
</template>
