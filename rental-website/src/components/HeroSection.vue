<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { PublicVehicle } from '@/types/fleet'
import QuickSearchWidget from '@/components/QuickSearchWidget.vue'

defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()

const emit = defineEmits<{
  (e: 'search', params: any): void
}>()
</script>

<template>
  <section class="hero-bg relative min-h-screen min-h-[100dvh] flex flex-col justify-between overflow-visible">
    <!-- Spacer untuk Fixed Navbar di paling atas -->
    <div class="h-16 sm:h-20 shrink-0"></div>

    <!-- Main Hero Content Container (Mobile: di tengah, Desktop: di langit atas mobil) -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto sm:my-0 sm:mt-1 lg:mt-2 sm:mb-auto py-3 sm:py-4">
      <div v-reveal:fade-up class="max-w-4xl xl:max-w-5xl text-center mx-auto">
        <!-- Eyebrow Gold Badge -->
        <p class="inline-block text-[10px] sm:text-xs font-black uppercase tracking-widest text-amber-300 mb-2 sm:mb-3 bg-slate-950/40 backdrop-blur-xs px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full border border-amber-400/40 shadow-md">
          RENTAL MOBIL &amp; TOUR BINTAN
        </p>

        <!-- Headline (1 baris di desktop & lebih besar) -->
        <h1 class="font-display text-2xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-white tracking-tight leading-tight mb-2 sm:mb-3 sm:whitespace-nowrap drop-shadow-[0_4px_16px_rgba(0,0,0,0.7)]">
          Jelajahi Bintan dengan Nyaman.
        </h1>

        <!-- Subtitle -->
        <p class="text-xs sm:text-sm lg:text-base xl:text-lg text-white/95 font-medium leading-relaxed mb-4 sm:mb-6 max-w-2xl mx-auto drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]">
          Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-4">
          <!-- Primary Gold Button (Lihat Armada) -->
          <a
            href="#armada"
            class="inline-flex items-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs sm:text-sm shadow-xl shadow-amber-500/30 transition-all hover:shadow-amber-500/40 active:scale-95 cursor-pointer"
          >
            <span>Lihat Armada</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </a>

          <!-- Secondary Outline Button (Paket Tour) -->
          <RouterLink
            to="/paket-tour-bintan"
            class="inline-flex items-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3.5 rounded-xl bg-slate-950/45 hover:bg-slate-950/65 text-white border border-white/40 backdrop-blur-md font-bold text-xs sm:text-sm transition-all active:scale-95 shadow-lg"
          >
            <span>Paket Tour</span>
            <svg class="w-4 h-4 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Floating Quick Search Widget: tepat di bawah layar hero 1 layar penuh -->
    <div v-reveal:fade-up="100" class="relative z-20 pb-4 sm:pb-8 lg:pb-10 pt-2 sm:pt-4 shrink-0">
      <QuickSearchWidget @search="(p) => emit('search', p)" />
    </div>
  </section>
</template>

<style scoped>
/* Mobile: portrait image, focus on cars */
.hero-bg {
  background-image: url('/images/hero-mobile.jpg');
  background-size: cover;
  background-position: center 60%;
  background-repeat: no-repeat;
}

/* Desktop (sm: 640px+): landscape fleet lineup */
@media (min-width: 640px) {
  .hero-bg {
    background-image: url('/images/hero-desktop.jpg');
    background-position: center 70%;
  }
}
</style>
