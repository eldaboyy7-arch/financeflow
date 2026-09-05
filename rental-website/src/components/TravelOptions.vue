<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <section id="cara-perjalanan" class="py-14 sm:py-20 bg-slate-50 border-t border-slate-200 scroll-mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Section Header -->
      <div class="max-w-2xl mx-auto text-center mb-10 sm:mb-14">
        <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-blue-600 mb-2">
          Pilihan Layanan
        </p>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
          Pilih Cara Terbaik untuk Menjelajahi Bintan
        </h2>
        <p class="text-sm sm:text-base text-slate-600 mt-2.5 leading-relaxed">
          Sesuaikan pilihan kendaraan dengan rencana dan gaya perjalanan Anda.
        </p>
      </div>

      <!-- 2 Cards Decision Fork (Visual, Uncluttered, Easy to Scan) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 max-w-4xl mx-auto">
        
        <!-- Pilihan 1: Rental Mobil (Perjalanan Mandiri) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-7 sm:p-9 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all group">
          <div>
            <div class="flex items-center justify-between mb-4">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                Perjalanan Mandiri
              </span>
              <span class="text-2xl">🚗</span>
            </div>

            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors">
              Rental Mobil
            </h3>

            <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6">
              Bebas menentukan rute dan jadwal perjalanan sendiri.
            </p>
          </div>

          <div class="pt-5 border-t border-slate-100">
            <a
              href="#armada"
              class="inline-flex items-center gap-2 text-sm sm:text-base font-bold text-blue-600 group-hover:text-blue-700 transition-colors group-hover:translate-x-1 duration-200"
            >
              <span>Lihat Armada</span>
              <span aria-hidden="true">&rarr;</span>
            </a>
          </div>
        </div>

        <!-- Pilihan 2: Tour & Charter HiAce (Perjalanan Rombongan) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-7 sm:p-9 flex flex-col justify-between hover:border-blue-200 hover:shadow-md transition-all group">
          <div>
            <div class="flex items-center justify-between mb-4">
              <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-semibold">
                Perjalanan Rombongan
              </span>
              <span class="text-2xl">🚐</span>
            </div>

            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors">
              Tour &amp; Charter HiAce
            </h3>

            <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6">
              HiAce 15 kursi, termasuk supir &amp; BBM.
            </p>
          </div>

          <div class="pt-5 border-t border-slate-100">
            <RouterLink
              to="/paket-tour-bintan"
              class="inline-flex items-center gap-2 text-sm sm:text-base font-bold text-blue-600 group-hover:text-blue-700 transition-colors group-hover:translate-x-1 duration-200"
            >
              <span>Lihat Paket Tour</span>
              <span aria-hidden="true">&rarr;</span>
            </RouterLink>
          </div>
        </div>

      </div>

    </div>
  </section>
</template>
