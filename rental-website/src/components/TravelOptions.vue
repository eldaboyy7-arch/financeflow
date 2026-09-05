<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <section id="cara-perjalanan" class="py-12 sm:py-20 bg-slate-50 border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Mobile-only compact header (hidden on desktop) -->
      <div class="lg:hidden mb-6">
        <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-1.5">Pilihan Layanan</p>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-snug">
          Pilih Cara Terbaik untuk Menjelajahi Bintan
        </h2>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-10 items-start">

        <!-- Kolom 1: Section Header (Kiri) — hanya tampil di desktop -->
        <div class="hidden lg:flex lg:col-span-1 flex-col justify-center">
          <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">Pilihan Layanan</p>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug mb-3">
            Pilih Cara Terbaik untuk Menjelajahi Bintan
          </h2>
          <p class="text-sm text-slate-600 leading-relaxed">
            Sesuaikan pilihan kendaraan dengan rencana dan gaya perjalanan Anda.
          </p>
        </div>

        <!-- Kolom 2: Rental Mobil Harian -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 flex flex-col">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M17 4H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2z"/>
              </svg>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Perjalanan Mandiri</span>
          </div>

          <h3 class="text-lg sm:text-xl font-bold text-slate-900 mb-2">Rental Mobil Harian</h3>
          <p class="text-sm text-slate-600 leading-relaxed mb-5">
            Bebas menentukan rute dan jadwal perjalanan sendiri.
          </p>

          <ul class="space-y-2.5 mb-7 flex-1">
            <li class="flex items-start gap-2.5 text-sm text-slate-700">
              <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              Lepas kunci atau dengan driver
            </li>
            <li class="flex items-start gap-2.5 text-sm text-slate-700">
              <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              Pilihan matic maupun manual
            </li>
            <li class="flex items-start gap-2.5 text-sm text-slate-700">
              <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              Syarat sewa yang memudahkan perjalanan
            </li>
          </ul>

          <a href="#armada"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm transition-all active:scale-95">
            Lihat Armada
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </a>
        </div>

        <!-- Kolom 3: Paket Tour & Charter HiAce -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 flex flex-col">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Perjalanan Rombongan</span>
          </div>

          <h3 class="text-lg sm:text-xl font-bold text-slate-900 mb-2">Paket Tour &amp; Charter HiAce</h3>
          <p class="text-sm text-slate-600 leading-relaxed mb-5">
            Perjalanan rombongan yang nyaman dengan fasilitas hiburan lengkap.
          </p>

          <ul class="space-y-2.5 mb-7 flex-1">
            <li class="flex items-start gap-2.5 text-sm text-slate-700">
              <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              HiAce 15 kursi, termasuk supir &amp; BBM
            </li>
            <li class="flex items-start gap-2.5 text-sm text-slate-700">
              <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              Fasilitas Karaoke &amp; Smart TV unit hiburan
            </li>
            <li class="flex items-start gap-2.5 text-sm text-slate-700">
              <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              Pilihan rute populer atau charter per jam
            </li>
          </ul>

          <RouterLink to="/paket-tour-bintan"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-sm transition-all active:scale-95">
            Lihat Paket Tour
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </RouterLink>
        </div>

      </div>
    </div>
  </section>
</template>
