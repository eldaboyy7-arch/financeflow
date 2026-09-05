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
  <section id="kontak" class="bg-slate-950 text-white py-14 sm:py-18 border-t border-slate-800 scroll-mt-16 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8 lg:gap-12">

        <!-- Kiri: Headline & Values -->
        <div class="max-w-xl">
          <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight mb-3">
            Sudah Memiliki Rencana Perjalanan?
          </h2>
          <p class="text-sm sm:text-base text-slate-300 leading-relaxed mb-6">
            Ceritakan kebutuhan transportasi Anda di Bintan, tim kami siap membantu memberikan pilihan armada atau rute yang paling tepat.
          </p>
          <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-xs sm:text-sm text-slate-300 font-medium">
            <span class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
              Respon Cepat
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
              Informasi Jelas
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
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
            class="inline-flex items-center justify-center gap-3.5 px-8 py-4 sm:py-5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-base sm:text-lg shadow-xl shadow-emerald-950/40 hover:scale-[1.02] active:scale-98 transition-all group"
          >
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
              </svg>
            </div>
            <div class="text-left">
              <span class="block text-xs uppercase tracking-wider text-emerald-100 font-semibold">Hubungi Kami via WhatsApp</span>
              <span class="font-display font-black text-white text-base sm:text-lg tracking-wide">0813-7237-1120</span>
            </div>
            <span class="text-emerald-200 group-hover:translate-x-1 transition-transform ml-1">&rarr;</span>
          </a>
        </div>

      </div>
    </div>
  </section>
</template>
