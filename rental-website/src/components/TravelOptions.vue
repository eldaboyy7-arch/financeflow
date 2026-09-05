<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <section id="cara-perjalanan" class="py-12 sm:py-16 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Section Header -->
      <div class="max-w-3xl mx-auto text-center mb-10 sm:mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-3">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
          </svg>
          <span>Pilihan Layanan Transportasi</span>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
          Pilih Cara Terbaik untuk Menjelajahi Bintan
        </h2>
        <p class="text-sm sm:text-base text-slate-600 mt-2 leading-relaxed">
          Sesuaikan pilihan kendaraan dengan rencana dan gaya perjalanan Anda.
        </p>
      </div>

      <!-- 2 Cards Decision Fork -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 max-w-5xl mx-auto">
        
        <!-- Card 1: Rental Mobil -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
          <div>
            <div class="flex items-center justify-between gap-3 mb-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                🚗 Sewa Mobil Harian
              </span>
              <span class="text-xs text-slate-500 font-medium">Lepas Kunci / Driver</span>
            </div>

            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2.5">
              Rental Mobil Harian
            </h3>
            <p class="text-sm text-slate-600 leading-relaxed mb-6">
              Bebas tentukan rute wisata atau urusan bisnis Anda di Bintan. Tersedia unit matic &amp; manual, bisa lepas kunci maupun didampingi driver lokal.
            </p>

            <ul class="space-y-3 mb-6 pt-2 border-t border-slate-100 text-xs sm:text-sm text-slate-700">
              <li class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Pilihan <strong>lepas kunci</strong> atau <strong>dengan driver</strong></span>
              </li>
              <li class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Unit matic &amp; manual terawat (Veloz, Avanza, Agya)</span>
              </li>
              <li class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Bisa sewa harian, mingguan, maupun bulanan</span>
              </li>
            </ul>
          </div>

          <div class="pt-5 border-t border-slate-100">
            <a
              href="#armada"
              class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-98 text-white text-xs sm:text-sm font-semibold transition-all shadow-xs"
            >
              <span>Lihat Pilihan Mobil</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </a>
            <p class="text-[11px] text-slate-400 text-center mt-2.5">
              *Syarat lepas kunci mudah &amp; konfirmasi unit cepat
            </p>
          </div>
        </div>

        <!-- Card 2: Tour & Charter HiAce -->
        <div class="bg-white rounded-2xl border-2 border-blue-600/30 p-6 sm:p-8 flex flex-col justify-between hover:border-blue-600/50 hover:shadow-md transition-all">
          <div>
            <div class="flex items-center justify-between gap-3 mb-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                🚐 Paket Tour Rombongan
              </span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-600 text-white shadow-xs">
                All-In Supir &amp; BBM
              </span>
            </div>

            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2.5">
              Paket Tour &amp; Charter HiAce
            </h3>
            <p class="text-sm text-slate-600 leading-relaxed mb-6">
              Liburan seru keluarga besar atau rombongan kantor tanpa repot menyetir. Unit HiAce 15 kursi ber-AC dingin, supir ramah, dan BBM sudah termasuk.
            </p>

            <ul class="space-y-3 mb-6 pt-2 border-t border-slate-100 text-xs sm:text-sm text-slate-700">
              <li class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Termasuk unit <strong>Toyota HiAce 15 kursi</strong>, supir, dan BBM</span>
              </li>
              <li class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Fasilitas <strong>Karaoke System &amp; Smart TV</strong> (perjalanan makin seru)</span>
              </li>
              <li class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Pilihan rute wisata populer atau sewa charter fleksibel</span>
              </li>
            </ul>
          </div>

          <div class="pt-5 border-t border-slate-100">
            <RouterLink
              to="/paket-tour-bintan"
              class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-98 text-white text-xs sm:text-sm font-semibold transition-all shadow-xs"
            >
              <span>Lihat Rincian Paket Tour</span>
              <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </RouterLink>
            <p class="text-[11px] text-slate-400 text-center mt-2.5">
              *Tersedia paket tour 1 hari &amp; sewa charter per jam
            </p>
          </div>
        </div>

      </div>

    </div>
  </section>
</template>
