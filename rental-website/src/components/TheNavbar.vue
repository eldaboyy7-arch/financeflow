<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const route = useRoute()
const isMobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

// Close mobile menu whenever route or hash changes
watch(
  () => [route.path, route.hash],
  () => {
    closeMobileMenu()
  }
)

// Lock body scrolling when mobile menu is open
watch(isMobileMenuOpen, (isOpen) => {
  if (typeof document === 'undefined') return
  if (isOpen) {
    document.body.classList.add('overflow-hidden')
  } else {
    document.body.classList.remove('overflow-hidden')
  }
})

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && isMobileMenuOpen.value) {
    closeMobileMenu()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  if (typeof document !== 'undefined') {
    document.body.classList.remove('overflow-hidden')
  }
})

const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
</script>

<template>
  <header
    class="sticky top-0 z-50 bg-slate-950 border-b border-slate-800 text-white shadow-md"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Brand Logo & Name -->
        <RouterLink to="/" class="flex items-center gap-2.5 sm:gap-3 group" @click="closeMobileMenu">
          <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center shrink-0">
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
            <span class="text-[10px] sm:text-[11px] font-medium tracking-wide text-slate-300 block leading-none mt-0.5 sm:mt-1">
              Rental Mobil &amp; Tour Bintan
            </span>
          </div>
        </RouterLink>

        <!-- Desktop Navigation (hidden on mobile, visible on md+) -->
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-200">
          <RouterLink
            to="/#cara-perjalanan"
            class="hover:text-white hover:drop-shadow transition-colors"
          >
            Layanan
          </RouterLink>
          <RouterLink
            to="/armada"
            class="hover:text-white hover:drop-shadow transition-colors"
            :class="route.path === '/armada' ? 'text-blue-400 font-bold' : ''"
          >
            Armada
          </RouterLink>
          <RouterLink
            to="/paket-tour-bintan"
            class="hover:text-blue-300 transition-colors"
            :class="route.path === '/paket-tour-bintan' ? 'text-blue-400 font-bold' : 'text-blue-400 font-semibold'"
          >
            Paket Tour
          </RouterLink>
          <RouterLink
            to="/#inspirasi"
            class="hover:text-white hover:drop-shadow transition-colors"
          >
            Inspirasi
          </RouterLink>
          <RouterLink
            to="/#faq"
            class="hover:text-white hover:drop-shadow transition-colors"
          >
            FAQ
          </RouterLink>
        </nav>

        <!-- Right Side Cluster: WhatsApp CTA + Mobile Hamburger Button -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- WhatsApp CTA Button -->
          <a
            :href="waUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-emerald-600/30 transition-all active:scale-95 shrink-0"
          >
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            <span class="hidden sm:inline">Hubungi Kami</span>
            <span class="sm:hidden">Chat WA</span>
          </a>

          <!-- Mobile Hamburger Toggle Button (md:hidden) -->
          <button
            @click="toggleMobileMenu"
            type="button"
            class="md:hidden w-9 h-9 rounded-xl bg-slate-800/80 hover:bg-slate-800 active:bg-slate-700 text-slate-200 hover:text-white flex items-center justify-center transition-colors border border-slate-700/60 focus:outline-none"
            :aria-expanded="isMobileMenuOpen"
            aria-label="Toggle menu navigasi"
          >
            <!-- Hamburger Lines when closed -->
            <svg
              v-if="!isMobileMenuOpen"
              class="w-5 h-5 text-slate-200 transition-transform"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <!-- Close Icon when open -->
            <svg
              v-else
              class="w-5 h-5 text-slate-200 transition-transform"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Drawer & Backdrop -->
    <!-- Backdrop Overlay -->
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isMobileMenuOpen"
        class="fixed inset-0 top-16 bg-slate-950/75 backdrop-blur-sm z-40 md:hidden"
        @click="closeMobileMenu"
      ></div>
    </Transition>

    <!-- Drawer Panel Slide Down -->
    <Transition
      enter-active-class="transition duration-250 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div
        v-if="isMobileMenuOpen"
        class="fixed top-16 inset-x-0 bg-slate-950 border-b border-slate-800 text-white z-50 md:hidden shadow-2xl overflow-y-auto max-h-[calc(100vh-4rem)]"
      >
        <div class="max-w-7xl mx-auto px-4 py-4 space-y-4">
          <!-- Primary Navigation Links (Clean Monochromatic Slate Style, No Rainbow Colors) -->
          <nav class="space-y-1">
            <!-- 1. Beranda -->
            <RouterLink
              to="/"
              @click="closeMobileMenu"
              class="group flex items-center justify-between px-3.5 py-3 rounded-xl transition-all border"
              :class="route.path === '/' && !route.hash
                ? 'bg-slate-900/90 border-slate-800 text-white font-medium'
                : 'border-transparent text-slate-300 hover:text-white hover:bg-slate-900/50'"
            >
              <div class="flex items-center gap-3">
                <span
                  class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors border"
                  :class="route.path === '/' && !route.hash
                    ? 'bg-blue-600/15 text-blue-400 border-blue-500/30'
                    : 'bg-slate-900/90 text-slate-400 border-slate-800/80 group-hover:text-slate-200 group-hover:border-slate-700'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                </span>
                <div>
                  <div class="text-sm" :class="route.path === '/' && !route.hash ? 'text-white font-semibold' : 'text-slate-200 font-medium'">Beranda</div>
                  <div class="text-[11px] text-slate-400 font-normal">Halaman utama rental &amp; tour</div>
                </div>
              </div>
              <svg class="w-4 h-4 transition-colors" :class="route.path === '/' && !route.hash ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>

            <!-- 2. Katalog Lengkap Armada -->
            <RouterLink
              to="/armada"
              @click="closeMobileMenu"
              class="group flex items-center justify-between px-3.5 py-3 rounded-xl transition-all border"
              :class="route.path === '/armada'
                ? 'bg-slate-900/90 border-slate-800 text-white font-medium'
                : 'border-transparent text-slate-300 hover:text-white hover:bg-slate-900/50'"
            >
              <div class="flex items-center gap-3">
                <span
                  class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors border"
                  :class="route.path === '/armada'
                    ? 'bg-blue-600/15 text-blue-400 border-blue-500/30'
                    : 'bg-slate-900/90 text-slate-400 border-slate-800/80 group-hover:text-slate-200 group-hover:border-slate-700'"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                  </svg>
                </span>
                <div>
                  <div class="text-sm" :class="route.path === '/armada' ? 'text-white font-semibold' : 'text-slate-200 font-medium'">Katalog Lengkap Armada</div>
                  <div class="text-[11px] text-slate-400 font-normal">City Car, MPV, HiAce &amp; Bus Pariwisata</div>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-800/90 text-slate-300 border border-slate-700/60">Etalase</span>
            </RouterLink>

            <!-- 3. Paket Tour Bintan -->
            <RouterLink
              to="/paket-tour-bintan"
              @click="closeMobileMenu"
              class="group flex items-center justify-between px-3.5 py-3 rounded-xl transition-all border"
              :class="route.path === '/paket-tour-bintan'
                ? 'bg-slate-900/90 border-slate-800 text-white font-medium'
                : 'border-transparent text-slate-300 hover:text-white hover:bg-slate-900/50'"
            >
              <div class="flex items-center gap-3">
                <span
                  class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors border"
                  :class="route.path === '/paket-tour-bintan'
                    ? 'bg-blue-600/15 text-blue-400 border-blue-500/30'
                    : 'bg-slate-900/90 text-slate-400 border-slate-800/80 group-hover:text-slate-200 group-hover:border-slate-700'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                  </svg>
                </span>
                <div>
                  <div class="text-sm" :class="route.path === '/paket-tour-bintan' ? 'text-white font-semibold' : 'text-slate-200 font-medium'">Paket Tour Bintan</div>
                  <div class="text-[11px] text-slate-400 font-normal">Wisata All-In HiAce + BBM + Supir</div>
                </div>
              </div>
              <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-800/90 text-slate-300 border border-slate-700/60">Tour All-In</span>
            </RouterLink>

            <!-- 4. Layanan & Cara Sewa -->
            <RouterLink
              to="/#cara-perjalanan"
              @click="closeMobileMenu"
              class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-transparent text-slate-300 hover:text-white hover:bg-slate-900/50 transition-colors"
            >
              <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-slate-900/90 text-slate-400 border border-slate-800/80 group-hover:text-slate-200 group-hover:border-slate-700 flex items-center justify-center shrink-0 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </span>
                <span class="text-sm font-medium text-slate-200">Layanan &amp; Cara Sewa</span>
              </div>
              <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>

            <!-- 5. Inspirasi Destinasi -->
            <RouterLink
              to="/#inspirasi"
              @click="closeMobileMenu"
              class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-transparent text-slate-300 hover:text-white hover:bg-slate-900/50 transition-colors"
            >
              <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-slate-900/90 text-slate-400 border border-slate-800/80 group-hover:text-slate-200 group-hover:border-slate-700 flex items-center justify-center shrink-0 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                </span>
                <span class="text-sm font-medium text-slate-200">Inspirasi Destinasi</span>
              </div>
              <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>

            <!-- 6. Tanya Jawab (FAQ) -->
            <RouterLink
              to="/#faq"
              @click="closeMobileMenu"
              class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-transparent text-slate-300 hover:text-white hover:bg-slate-900/50 transition-colors"
            >
              <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-slate-900/90 text-slate-400 border border-slate-800/80 group-hover:text-slate-200 group-hover:border-slate-700 flex items-center justify-center shrink-0 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </span>
                <span class="text-sm font-medium text-slate-200">Tanya Jawab (FAQ)</span>
              </div>
              <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>
          </nav>

          <!-- Contact & Quick Info Box inside Drawer -->
          <div class="pt-3 border-t border-slate-800/80">
            <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-col gap-3">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-xs font-bold text-white">Konsultasi Rute &amp; Booking Cepat</div>
                  <div class="text-[11px] text-slate-400 mt-0.5">Admin 3 Putri Mulya siap merespons via WA</div>
                </div>
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              </div>

              <a
                :href="waUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold inline-flex items-center justify-center gap-2 shadow-md transition-all active:scale-95"
              >
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                <span>Hubungi Admin Sekarang</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </header>
</template>
