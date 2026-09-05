<script setup lang="ts">
import { computed } from 'vue'
import { siteConfig } from '@/config/site'

const getWaUrl = (phone: string) => {
  const clean = phone.replace(/\D/g, '')
  const text = 'Halo 3 Putri Mulya, saya ingin konsultasi kebutuhan sewa mobil / paket tour di Bintan.'
  return `https://wa.me/${clean}?text=${encodeURIComponent(text)}`
}

const waUrl = computed(() => getWaUrl(siteConfig.rentalPhone))
</script>

<template>
  <section id="kontak" class="py-14 sm:py-20 bg-white border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Card: Clean Light with subtle warm travel gradient -->
      <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-blue-50/80 via-slate-50 to-amber-50/60 border border-slate-200/90 shadow-sm p-8 sm:p-12 lg:p-14">
        
        <!-- Subtle background accent blob -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-amber-200/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -top-20 w-80 h-80 bg-blue-200/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8 lg:gap-12">
          
          <!-- Kiri: Headline & Values -->
          <div class="max-w-xl">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2.5">
              Konsultasi &amp; Pemesanan Cepat
            </p>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-3">
              Sudah Memiliki Rencana Perjalanan?
            </h2>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6">
              Ceritakan kebutuhan transportasi Anda di Bintan, tim kami siap membantu memberikan pilihan armada atau rute wisata yang paling tepat.
            </p>
            
            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-slate-700 font-medium">
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-slate-200/80 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Respon Cepat
              </span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-slate-200/80 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Informasi Jelas
              </span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-slate-200/80 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Layanan Terpercaya
              </span>
            </div>
          </div>

          <!-- Kanan: Large WhatsApp Action Button -->
          <div class="shrink-0">
            <a
              :href="waUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center justify-center gap-4 px-7 sm:px-8 py-4 sm:py-5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition-all shadow-xl shadow-emerald-600/20 hover:scale-[1.02] active:scale-98 group"
            >
              <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
              </div>
              <div class="text-left">
                <span class="block text-[11px] uppercase tracking-wider text-emerald-100 font-semibold leading-tight">Hubungi Kami via WhatsApp</span>
                <span class="font-extrabold text-white text-base sm:text-lg tracking-wide leading-none mt-1 block">0813-7237-1120</span>
              </div>
              <svg class="w-5 h-5 text-emerald-200 group-hover:translate-x-1 transition-transform ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </a>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>
