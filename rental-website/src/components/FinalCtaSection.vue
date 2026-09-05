<script setup lang="ts">
import { computed } from 'vue'
import { siteConfig } from '@/config/site'

const getWaUrl = (phone: string) => {
  const clean = phone.replace(/\D/g, '')
  const text = 'Halo 3 Putri Mulya, saya ingin konsultasi kebutuhan sewa mobil / paket tour di Bintan.'
  return `https://wa.me/${clean}?text=${encodeURIComponent(text)}`
}

const waUrl = computed(() => getWaUrl(siteConfig.rentalPhone))
const waUrlSecondary = computed(() => getWaUrl(siteConfig.rentalPhoneSecondary))
</script>

<template>
  <section id="kontak" class="py-14 sm:py-20 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white relative overflow-hidden">
    <!-- Subtle background accent -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
      <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 text-slate-200 text-xs font-semibold mb-5 border border-white/15 backdrop-blur-xs">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>Konsultasi &amp; Reservasi Cepat</span>
      </div>

      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white mb-4">
        Sudah Memiliki Rencana Perjalanan?
      </h2>

      <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto mb-8 leading-relaxed">
        Ceritakan kebutuhan transportasi Anda di Bintan, tim kami siap membantu memberikan pilihan armada atau rute yang paling tepat.
      </p>

      <!-- Single Focused Primary Action -->
      <div class="max-w-md mx-auto">
        <a
          :href="waUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="w-full inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm sm:text-base shadow-lg shadow-emerald-950/40 hover:shadow-emerald-600/30 transition-all active:scale-98 group"
        >
          <svg class="w-5 h-5 shrink-0 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
          </svg>
          <span>Chat WhatsApp (0813-7237-1120)</span>
        </a>

        <!-- Clean Subtle Alternative Link -->
        <p class="text-xs text-slate-400 mt-4">
          Jalur alternatif:
          <a
            :href="waUrlSecondary"
            target="_blank"
            rel="noopener noreferrer"
            class="text-slate-300 hover:text-emerald-400 font-medium underline underline-offset-2 transition-colors ml-1"
          >
            Admin 2 (0852-6326-7909)
          </a>
        </p>
      </div>

      <!-- Reassurance Points -->
      <div class="mt-8 pt-6 border-t border-slate-800/80 flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-xs text-slate-400">
        <span class="flex items-center gap-1.5"><span class="text-emerald-400 font-bold">✓</span> Respon Cepat dari Admin</span>
        <span class="flex items-center gap-1.5"><span class="text-emerald-400 font-bold">✓</span> Konfirmasi Unit Instan</span>
        <span class="flex items-center gap-1.5"><span class="text-emerald-400 font-bold">✓</span> Bebas Konsultasi Rute</span>
      </div>
    </div>
  </section>
</template>
