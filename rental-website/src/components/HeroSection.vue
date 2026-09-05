<script setup lang="ts">
import type { PublicVehicle } from '@/types/fleet'

defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()
</script>

<template>
  <section class="relative bg-slate-950 text-white overflow-hidden border-b border-slate-800">
    <!-- 1. Full-Width Edge-to-Edge Hero Banner (100% Clean & Unobstructed) -->
    <div class="w-full bg-slate-950">
      <picture class="w-full">
        <!-- Mobile portrait visual (< 640px) -->
        <source media="(max-width: 639px)" srcset="/images/hero-mobile.jpg" />
        <!-- Desktop landscape visual (>= 640px) -->
        <source media="(min-width: 640px)" srcset="/images/hero-desktop.jpg" />
        <img
          src="/images/hero-desktop.jpg"
          alt="Rental Mobil &amp; Tour Bintan - 3 Putri Mulya"
          class="w-full h-auto block"
          loading="eager"
        />
      </picture>
    </div>

    <!-- 2. Clean Minimalist Action Strip (Positioned Directly Below Banner - Zero Overlap) -->
    <div class="bg-slate-900 border-t border-slate-800/80 py-4 sm:py-5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span class="text-xs sm:text-sm font-bold text-white tracking-tight">Jelajahi Bintan dengan Nyaman</span>
          </div>
          <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">
            Rental mobil harian &amp; paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
          </p>
        </div>

        <div class="flex items-center gap-3 shrink-0 w-full sm:w-auto">
          <a
            href="#cara-perjalanan"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-blue-600/30 transition-all text-center"
          >
            <span>Lihat Pilihan Armada</span>
            <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
