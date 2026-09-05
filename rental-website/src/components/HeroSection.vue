<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { PublicVehicle } from '@/types/fleet'

defineProps<{
  featuredVehicle?: PublicVehicle | null
}>()
</script>

<template>
  <!-- Hero Section -->
  <section class="relative bg-white border-b border-slate-200 overflow-hidden">

    <!-- ====== MOBILE: Background photo + teks overlay (< lg) ====== -->
    <div
      class="lg:hidden relative h-svh flex flex-col justify-end"
      :style="{ backgroundImage: 'url(/images/hero-mobile.png)', backgroundSize: 'cover', backgroundPosition: 'center bottom' }"
    >
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/45 to-slate-950/5"></div>
      <div class="relative z-10 px-5 pb-10 pt-6">
        <p class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-2">
          RENTAL MOBIL &amp; TOUR BINTAN
        </p>
        <h1 class="font-display text-3xl font-black text-white leading-tight tracking-tight mb-3">
          Jelajahi Bintan<br />dengan Nyaman
        </h1>
        <p class="text-sm text-slate-200 leading-relaxed mb-6 max-w-xs">
          Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
        </p>
        <div class="flex flex-col gap-2.5">
          <a
            href="#armada"
            class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-white text-slate-900 font-bold text-sm shadow-lg transition-all active:scale-95"
          >
            Lihat Pilihan Armada
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </a>
          <RouterLink
            to="/paket-tour-bintan"
            class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-sm shadow-sm transition-all active:scale-95"
          >
            Paket Tour HiAce
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- ====== DESKTOP: Full-bleed background + teks overlay center (>= lg) ====== -->
    <div
      class="hidden lg:flex flex-col justify-end h-screen relative"
      :style="{ backgroundImage: 'url(/images/hero-desktop.png)', backgroundSize: 'cover', backgroundPosition: 'center' }"
    >
      <!-- Gradient: lebih gelap di bawah agar teks terbaca, transparan di atas agar foto tetap vivid -->
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>

      <!-- Konten teks: terpusat horizontal, di bagian bawah hero -->
      <div class="relative z-10 w-full max-w-3xl mx-auto px-8 pb-16 text-center">
        <p class="text-xs font-bold uppercase tracking-widest text-amber-400 mb-3">
          RENTAL MOBIL &amp; TOUR BINTAN
        </p>
        <h1 class="font-display text-5xl xl:text-[3.75rem] font-black text-white tracking-tight leading-[1.06] mb-4">
          Jelajahi Bintan<br />dengan Nyaman
        </h1>
        <p class="text-base xl:text-lg text-slate-200 leading-relaxed mb-8 max-w-xl mx-auto">
          Rental mobil dan paket perjalanan di Bintan untuk kebutuhan pribadi, keluarga, maupun rombongan.
        </p>
        <div class="flex flex-row gap-3 justify-center">
          <a
            href="#armada"
            class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-white hover:bg-slate-100 text-slate-900 font-bold text-sm shadow-lg transition-all active:scale-95"
          >
            Lihat Pilihan Armada
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </a>
          <RouterLink
            to="/paket-tour-bintan"
            class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-sm shadow-sm transition-all active:scale-95"
          >
            Paket Tour HiAce
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </RouterLink>
        </div>
      </div>
    </div>

  </section>
</template>
