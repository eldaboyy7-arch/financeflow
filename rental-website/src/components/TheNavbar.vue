<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const route = useRoute()
const isHome = computed(() => route.path === '/')

const isScrolled = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
</script>

<template>
  <header
    class="z-40 transition-all duration-300"
    :class="[
      isHome ? 'fixed top-0 inset-x-0' : 'sticky top-0 bg-slate-950 border-b border-slate-800 text-white shadow-md',
      isHome && !isScrolled
        ? 'bg-gradient-to-b from-slate-950/70 via-slate-950/20 to-transparent text-white'
        : '',
      isHome && isScrolled
        ? 'bg-slate-950/95 backdrop-blur-md border-b border-slate-800 text-white shadow-2xl'
        : ''
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Brand Logo & Name -->
        <RouterLink to="/" class="flex items-center gap-3 group">
          <div class="w-10 h-10 flex items-center justify-center shrink-0">
            <img
              src="/images/logo-3pm.png"
              alt="Logo 3 Putri Mulya"
              class="w-full h-full object-contain drop-shadow-md group-hover:scale-105 transition-transform"
            />
          </div>
          <div>
            <span class="font-display text-base sm:text-lg font-black text-white tracking-tight block leading-tight group-hover:text-slate-100 transition-colors">
              {{ siteConfig.rentalName }}
            </span>
            <span class="text-[10px] sm:text-[11px] font-medium tracking-wide text-slate-300 block leading-none mt-1">
              Rental Mobil &amp; Tour Bintan
            </span>
          </div>
        </RouterLink>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-200">
          <RouterLink to="/#cara-perjalanan" class="hover:text-white hover:drop-shadow transition-colors">Layanan</RouterLink>
          <RouterLink to="/#armada" class="hover:text-white hover:drop-shadow transition-colors">Armada</RouterLink>
          <RouterLink to="/paket-tour-bintan" class="text-blue-400 font-semibold hover:text-blue-300 transition-colors">Paket Tour</RouterLink>
          <RouterLink to="/#inspirasi" class="hover:text-white hover:drop-shadow transition-colors">Inspirasi</RouterLink>
          <RouterLink to="/#faq" class="hover:text-white hover:drop-shadow transition-colors">FAQ</RouterLink>
        </nav>

        <!-- WhatsApp CTA (Persistent top access) -->
        <div class="flex items-center gap-3">
          <a
            :href="waUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full sm:rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-emerald-600/30 transition-all active:scale-95 shrink-0"
          >
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            <span class="hidden sm:inline">Hubungi Kami</span>
            <span class="sm:hidden">Chat WA</span>
          </a>
        </div>
      </div>
    </div>
  </header>
</template>
