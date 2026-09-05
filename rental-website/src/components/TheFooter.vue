<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
const waUrlSecondary = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhoneSecondary, siteConfig.rentalName))
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="bg-slate-950 text-slate-400 text-xs border-t border-slate-800/80 pb-12 sm:pb-8 pt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <!-- Brand & Info -->
        <div>
          <div class="flex items-center gap-3 text-white font-bold text-base mb-2">
            <div class="w-9 h-9 rounded-xl bg-slate-950 flex items-center justify-center p-1 border border-amber-500/30 shrink-0 overflow-hidden">
              <img src="/images/logo-3pm.png" alt="Logo 3 Putri Mulya" class="w-full h-full object-contain" />
            </div>
            <div>
              <span class="font-display text-base font-black text-white block leading-tight tracking-tight">{{ siteConfig.rentalName }}</span>
              <span class="text-[9.5px] font-bold tracking-widest uppercase text-amber-400 block leading-none mt-1">Rental Mobil &bull; Tour Bintan</span>
            </div>
          </div>
          <p class="text-slate-400 leading-relaxed max-w-sm mb-4">
            {{ siteConfig.rentalTagline }}
          </p>
          <div v-if="siteConfig.rentalCity" class="text-slate-400">
            Wilayah Operasional: <span class="text-slate-200 font-medium">{{ siteConfig.rentalCity }}</span>
          </div>
        </div>

        <!-- Navigation Links -->
        <div>
          <h4 class="text-slate-200 font-bold uppercase tracking-wider text-[11px] mb-3">Tautan Cepat</h4>
          <ul class="space-y-2">
            <li><a href="/#cara-perjalanan" class="hover:text-white transition-colors">Pilihan Layanan</a></li>
            <li><a href="/#armada" class="hover:text-white transition-colors">Pilihan Armada</a></li>
            <li><RouterLink to="/paket-tour-bintan" class="hover:text-white transition-colors">Paket Tour HiAce</RouterLink></li>
            <li><a href="/#inspirasi" class="hover:text-white transition-colors">Inspirasi Perjalanan</a></li>
            <li><a href="/#faq" class="hover:text-white transition-colors">Pertanyaan Umum (FAQ)</a></li>
          </ul>
        </div>

        <!-- Contact & Operating Info -->
        <div>
          <h4 class="text-slate-200 font-bold uppercase tracking-wider text-[11px] mb-3">Layanan &amp; Kontak</h4>
          <p class="text-slate-400 leading-relaxed mb-3">
            Pemeriksaan unit, konfirmasi jadwal, atau penjemputan hubungi:
          </p>
          <ul class="space-y-2.5 text-xs">
            <li>
              <a
                :href="waUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-slate-300 hover:text-emerald-400 transition-colors group"
              >
                <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0 group-hover:scale-125 transition-transform"></span>
                <span class="font-medium text-slate-200">0813-7237-1120</span>
                <span class="text-[10px] text-slate-500">(Admin 1 / Utama)</span>
              </a>
            </li>
            <li>
              <a
                :href="waUrlSecondary"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-slate-300 hover:text-emerald-400 transition-colors group"
              >
                <span class="w-2 h-2 rounded-full bg-slate-500 shrink-0 group-hover:scale-125 transition-transform"></span>
                <span class="font-medium text-slate-200">0852-6326-7909</span>
                <span class="text-[10px] text-slate-500">(Admin 2)</span>
              </a>
            </li>
            <li class="pt-2 border-t border-slate-800 text-slate-400 flex items-center gap-2">
              <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Setiap Hari: 06.00 &ndash; 22.00 WIB</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Copyright & Bottom Bar -->
      <div class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-slate-400">
        <p>&copy; {{ currentYear }} {{ siteConfig.rentalName }}. Seluruh hak cipta dilindungi.</p>
        <p class="text-slate-400">
          Katalog publik sinkron langsung dari sistem manajemen garasi.
        </p>
      </div>
    </div>
  </footer>
</template>
