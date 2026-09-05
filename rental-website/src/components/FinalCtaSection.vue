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
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Framed Master Card -->
      <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-900 via-slate-900 to-blue-950 text-white border border-slate-800 shadow-2xl p-7 sm:p-12 lg:p-14">
        <!-- Ambient Glow Accents -->
        <div class="absolute -top-28 -right-28 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-28 -left-28 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
          <!-- Left Column: Editorial & Value Proposition (7 cols) -->
          <div class="lg:col-span-7 text-left">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-blue-500/15 border border-blue-400/25 text-blue-300 text-xs font-semibold mb-4 backdrop-blur-xs">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span>Layanan Reservasi &amp; Konsultasi Bintan</span>
            </div>

            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-display font-black text-white tracking-tight leading-tight mb-3">
              Siap Menjelajahi Keindahan Pulau Bintan?
            </h2>

            <p class="text-sm sm:text-base text-slate-300 leading-relaxed mb-6 max-w-xl">
              Dari sewa mobil lepas kunci, antar-jemput bandara &amp; pelabuhan, hingga tour keliling Bintan dengan HiAce berfasilitas karaoke. Diskusikan jadwal atau rute bersama tim lokal kami.
            </p>

            <!-- Key Trust Badges -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-5 border-t border-white/10 text-xs text-slate-300">
              <div class="flex items-center gap-2">
                <span class="text-emerald-400 font-bold">✓</span>
                <span>Respon Admin Cepat</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-blue-400 font-bold">✓</span>
                <span>Mobil Bersih &amp; Prima</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-amber-400 font-bold">✓</span>
                <span>Tarif Jujur &amp; Jelas</span>
              </div>
            </div>
          </div>

          <!-- Right Column: Structured Contact Action Box (5 cols) -->
          <div class="lg:col-span-5">
            <div class="bg-slate-950/70 border border-white/15 rounded-2xl p-5 sm:p-6 backdrop-blur-md shadow-xl flex flex-col gap-3">
              <div class="flex items-center justify-between pb-3 border-b border-white/10 text-xs">
                <span class="font-bold uppercase tracking-wider text-slate-300">Pilih Jalur Chat:</span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-semibold border border-emerald-500/30">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                  <span>Siap Membantu</span>
                </span>
              </div>

              <!-- Admin 1 (Primary Action) -->
              <a
                :href="waUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="group flex items-center justify-between p-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white transition-all shadow-md shadow-emerald-950/40 active:scale-98"
              >
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                    </svg>
                  </div>
                  <div>
                    <div class="text-[11px] text-emerald-100 font-medium">WhatsApp Admin 1 (Utama)</div>
                    <div class="text-sm font-bold tracking-tight">0813-7237-1120</div>
                  </div>
                </div>
                <span class="text-sm font-bold text-white group-hover:translate-x-1 transition-transform">&rarr;</span>
              </a>

              <!-- Admin 2 (Alternative Action) -->
              <a
                :href="waUrlSecondary"
                target="_blank"
                rel="noopener noreferrer"
                class="group flex items-center justify-between p-3.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 hover:border-white/20 transition-all active:scale-98"
              >
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                    </svg>
                  </div>
                  <div>
                    <div class="text-[11px] text-slate-400 font-medium">WhatsApp Admin 2 (Cadangan)</div>
                    <div class="text-sm font-bold text-slate-200 tracking-tight">0852-6326-7909</div>
                  </div>
                </div>
                <span class="text-sm font-bold text-slate-400 group-hover:translate-x-1 transition-transform">&rarr;</span>
              </a>

              <div class="text-center pt-2 text-[11px] text-slate-400">
                Operasional: 06.00 &ndash; 22.00 WIB &bull; Bintan &amp; Tanjungpinang
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
