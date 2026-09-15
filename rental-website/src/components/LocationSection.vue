<script setup lang="ts">
import { computed } from 'vue'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const waUrl = computed(() => generateGeneralWhatsAppUrl(
  siteConfig.rentalPhone,
  siteConfig.rentalName,
  'Halo 3 Putri Mulya, saya ingin konsultasi titik penjemputan / pengantaran armada di Bintan.'
))
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 flex flex-col justify-between shadow-xs">
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">
        Jangkauan Operasional
      </p>
      <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mb-2">
        Area Layanan &amp; Titik Koordinasi
      </h3>
      <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
        Kami melayani berbagai area utama di Pulau Bintan dan Tanjungpinang. Antar-jemput dapat disesuaikan kebutuhan Anda.
      </p>

      <!-- Card Kantor & Garasi Utama -->
      <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200/80">
        <div class="flex items-start gap-3">
          <div class="w-8 h-8 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-1 mb-1">
              <strong class="text-slate-900 text-sm font-bold">Kantor &amp; Garasi Utama</strong>
              <span class="inline-flex items-center text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-300/60">Pusat Armada</span>
            </div>
            <p class="text-xs text-slate-700 leading-relaxed mb-2.5">
              {{ siteConfig.rentalAddress }}
            </p>
            <a
              :href="siteConfig.rentalMapsUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-200 hover:border-blue-400 text-slate-900 text-xs font-bold transition-all shadow-2xs group"
            >
              <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
              </svg>
              <span>Buka Petunjuk Arah (Google Maps)</span>
              <svg class="w-3 h-3 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </a>
          </div>
        </div>
      </div>

      <!-- List of operational areas with map pin -->
      <div class="space-y-3 text-xs sm:text-sm text-slate-700">
        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
          <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div>
            <strong class="text-slate-900 block font-semibold">Tanjungpinang:</strong>
            <span class="text-slate-600 text-xs">Pelabuhan SBP, Bandara RHF, &amp; hotel pusat kota.</span>
          </div>
        </div>

        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
          <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div>
            <strong class="text-slate-900 block font-semibold">Kawasan Wisata Lagoi:</strong>
            <span class="text-slate-600 text-xs">Pelabuhan BBT &amp; seluruh area resort Bintan Resorts.</span>
          </div>
        </div>

        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
          <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div>
            <strong class="text-slate-900 block font-semibold">Pelabuhan Tanjung Uban:</strong>
            <span class="text-slate-600 text-xs">Pelabuhan Penyeberangan ASDP / RoRo Tanjung Uban &amp; sekitarnya.</span>
          </div>
        </div>

        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
          <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <div>
            <strong class="text-slate-900 block font-semibold">Pantai Trikora &amp; Kijang:</strong>
            <span class="text-slate-600 text-xs">Wilayah pesisir timur, Kijang, dan destinasi sekitarnya.</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom CTA banner button -->
    <div class="mt-6 pt-5 border-t border-slate-100">
      <a
        :href="waUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm transition-all shadow-xs group"
      >
        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
        </svg>
        <span>Titik jemput lainnya? Konsultasikan via WhatsApp</span>
        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
      </a>
    </div>
  </div>
</template>
