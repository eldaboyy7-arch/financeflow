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
  <section class="relative bg-slate-900 overflow-visible">
    <!-- Hero Background with Scenic Tropical Backdrop & Gradient -->
    <div
      class="relative pt-24 sm:pt-28 md:pt-36 pb-20 sm:pb-28 md:pb-36 bg-cover bg-center overflow-hidden"
      :style="{ backgroundImage: 'url(/images/hero-desktop.png)' }"
    >
      <!-- Premium Dark/Blue Gradient Overlay so text and cars pop with high contrast -->
      <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/75 to-slate-900/40"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/40"></div>

      <!-- Main Hero Content Container -->
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl text-center lg:text-left mx-auto lg:mx-0 pt-2 sm:pt-4">
          <!-- Eyebrow Gold Badge -->
          <p class="inline-block text-[11px] sm:text-xs font-black uppercase tracking-widest text-amber-400 mb-3 bg-amber-400/10 px-3.5 py-1 rounded-full border border-amber-400/20 shadow-2xs">
            RENTAL MOBIL &amp; TOUR BINTAN
          </p>

          <!-- Headline -->
          <h1 class="font-display text-3xl sm:text-5xl xl:text-6xl font-black text-white tracking-tight leading-[1.1] mb-4">
            Jelajahi Bintan<br class="hidden sm:inline" />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-100 to-slate-300">dengan Nyaman.</span>
          </h1>

          <!-- Subtitle -->
          <p class="text-sm sm:text-base xl:text-lg text-slate-200 leading-relaxed mb-6 sm:mb-8 max-w-xl mx-auto lg:mx-0">
            Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
          </p>

          <!-- Action Buttons -->
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 sm:gap-4">
            <!-- Primary Gold Button (Lihat Armada) -->
            <a
              href="#armada"
              class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-amber-500/20 transition-all hover:shadow-amber-500/30 active:scale-95"
            >
              <span>Lihat Armada</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </a>

            <!-- Secondary Outline Button (Paket Tour) -->
            <RouterLink
              to="/paket-tour-bintan"
              class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/30 backdrop-blur-xs font-bold text-xs sm:text-sm transition-all active:scale-95"
            >
              <span>Paket Tour</span>
              <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </RouterLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Floating Quick Search Widget: positioned right overlapping bottom of hero -->
    <div class="-mt-8 sm:-mt-10 md:-mt-12 relative z-20 pb-4">
      <QuickSearchWidget @search="(p) => emit('search', p)" />
    </div>
  </section>
</template>
