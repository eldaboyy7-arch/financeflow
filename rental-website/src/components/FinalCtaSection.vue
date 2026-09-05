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
  <section id="kontak" class="py-14 sm:py-20 bg-slate-50 border-t border-slate-200 scroll-mt-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Scenic Travel Master Card -->
      <div class="relative rounded-3xl overflow-hidden border border-slate-800 shadow-2xl text-center text-white p-8 sm:p-14 lg:p-16">
        
        <!-- Background Image (Lagoi Bay Sunset / Coastal Bintan) -->
        <img
          src="/images/destinations/lagoi.jpg"
          alt="Keindahan Wisata Bintan"
          class="absolute inset-0 w-full h-full object-cover object-center scale-105"
        />

        <!-- Cinematic Gradient Overlays for High Legibility & Warm Mood -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-950/70"></div>
        <div class="absolute inset-0 bg-blue-950/30 mix-blend-overlay"></div>

        <!-- Ambient Glow Accents -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-48 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Content Container -->
        <div class="relative z-10 max-w-2xl mx-auto flex flex-col items-center">
          
          <!-- Top Badge -->
          <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 border border-white/20 text-emerald-300 text-xs font-semibold mb-5 backdrop-blur-md shadow-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Layanan Reservasi &amp; Konsultasi Wisata Bintan</span>
          </div>

          <!-- Headline -->
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight mb-4">
            Siap Menjelajahi Keindahan Pulau Bintan?
          </h2>

          <!-- Warm Editorial Copy -->
          <p class="text-sm sm:text-base text-slate-200 leading-relaxed mb-8 max-w-xl font-normal">
            Dari sewa mobil lepas kunci, antar-jemput pelabuhan &amp; bandara, hingga tour keliling Bintan dengan HiAce karaoke. Diskusikan rencana perjalanan Anda bersama tim lokal kami &mdash; ramah, cepat, dan tanpa komitmen.
          </p>

          <!-- Primary Prominent WhatsApp CTA -->
          <div class="w-full sm:w-auto flex flex-col items-center gap-3">
            <a
              :href="waUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm sm:text-base shadow-xl shadow-emerald-950/50 hover:shadow-emerald-900/60 hover:scale-[1.02] active:scale-98 transition-all group"
            >
              <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
              </div>
              <span>Chat WhatsApp Admin 1 (0813-7237-1120)</span>
              <span class="text-emerald-200 group-hover:translate-x-1 transition-transform">&rarr;</span>
            </a>

            <!-- Subtle Secondary Admin Link -->
            <p class="text-xs text-slate-300">
              Jalur cadangan:
              <a
                :href="waUrlSecondary"
                target="_blank"
                rel="noopener noreferrer"
                class="text-white hover:text-emerald-300 underline underline-offset-2 font-medium transition-colors ml-1"
              >
                Admin 2 (0852-6326-7909)
              </a>
              <span class="mx-2 text-white/30">&bull;</span>
              <span class="text-slate-400">06.00 &ndash; 22.00 WIB</span>
            </p>
          </div>

          <!-- Trust Signals Row -->
          <div class="mt-8 pt-6 border-t border-white/10 flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-xs text-slate-300">
            <span class="flex items-center gap-1.5">
              <span class="text-emerald-400 font-bold">✓</span> Respon Cepat Admin
            </span>
            <span class="flex items-center gap-1.5">
              <span class="text-emerald-400 font-bold">✓</span> Mobil Bersih &amp; Terawat
            </span>
            <span class="flex items-center gap-1.5">
              <span class="text-emerald-400 font-bold">✓</span> Tarif Jujur &amp; Jelas
            </span>
          </div>

        </div>
      </div>

    </div>
  </section>
</template>
