<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="bg-slate-950 text-slate-400 text-xs border-t border-slate-800/80 pt-12 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- 4 Columns Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10 mb-12">
        
        <!-- Kolom 1: Brand & Bio -->
        <div class="sm:col-span-2 lg:col-span-1">
          <div class="flex items-center gap-3 text-white font-bold text-base mb-3">
            <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center p-1 border border-amber-500/30 shrink-0 overflow-hidden">
              <img src="/images/logo-3pm.png" alt="Logo 3 Putri Mulya" class="w-full h-full object-contain" />
            </div>
            <div>
              <span class="font-display text-base font-black text-white block leading-tight tracking-tight">{{ siteConfig.rentalName }}</span>
              <span class="text-[10px] font-medium tracking-wide text-slate-300 block leading-none mt-0.5">Rental Mobil &amp; Tour Bintan</span>
            </div>
          </div>
          <p class="text-slate-400 leading-relaxed text-xs mb-4">
            Rental mobil lepas kunci &amp; dengan driver serta paket tour di Bintan untuk perjalanan pribadi, keluarga, dan rombongan.
          </p>
          <div class="text-[11px] text-slate-400">
            Wilayah Operasional: <span class="text-slate-200 font-medium">Bintan &amp; Tanjungpinang</span>
          </div>
        </div>

        <!-- Kolom 2: Navigasi -->
        <div>
          <h4 class="text-slate-200 font-bold uppercase tracking-wider text-[11px] mb-3">Navigasi</h4>
          <ul class="space-y-2 text-xs">
            <li><a href="/#cara-perjalanan" class="hover:text-white transition-colors">Pilihan Layanan</a></li>
            <li><a href="/#armada" class="hover:text-white transition-colors">Pilihan Armada</a></li>
            <li><RouterLink to="/paket-tour-bintan" class="hover:text-white transition-colors">Paket Tour HiAce</RouterLink></li>
            <li><a href="/#inspirasi" class="hover:text-white transition-colors">Inspirasi Destinasi</a></li>
            <li><a href="/#faq" class="hover:text-white transition-colors">Pertanyaan Umum (FAQ)</a></li>
          </ul>
        </div>

        <!-- Kolom 3: Layanan -->
        <div>
          <h4 class="text-slate-200 font-bold uppercase tracking-wider text-[11px] mb-3">Layanan</h4>
          <ul class="space-y-2 text-xs">
            <li><a href="/#cara-perjalanan" class="hover:text-white transition-colors">Rental Mobil Harian</a></li>
            <li><a href="/#cara-perjalanan" class="hover:text-white transition-colors">Sewa Lepas Kunci (Self-Drive)</a></li>
            <li><a href="/#cara-perjalanan" class="hover:text-white transition-colors">Sewa dengan Driver</a></li>
            <li><RouterLink to="/paket-tour-bintan" class="hover:text-white transition-colors">Paket Tour HiAce 15 Kursi</RouterLink></li>
            <li><RouterLink to="/paket-tour-bintan" class="hover:text-white transition-colors">Charter &amp; Rombongan</RouterLink></li>
          </ul>
        </div>

        <!-- Kolom 4: Hubungi Kami -->
        <div>
          <h4 class="text-slate-200 font-bold uppercase tracking-wider text-[11px] mb-3">Hubungi Kami</h4>
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
                <span class="text-[10px] text-slate-400">(Admin 1)</span>
              </a>
            </li>
            <li class="flex items-center gap-2 text-slate-400">
              <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span>Tanjungpinang &amp; Bintan, Kep. Riau</span>
            </li>
            <li class="flex items-center gap-2 text-slate-400">
              <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Setiap Hari: 06.00 &ndash; 22.00 WIB</span>
            </li>
          </ul>
        </div>

      </div>

      <!-- Copyright Bar -->
      <div class="pt-6 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-slate-400 text-xs">
        <p>&copy; {{ currentYear }} {{ siteConfig.rentalName }}. Seluruh hak cipta dilindungi.</p>
        <div class="flex items-center gap-4 text-[11px]">
          <span class="text-slate-400">Rental Mobil &amp; Tour Bintan Terpercaya</span>
        </div>
      </div>

    </div>
  </footer>
</template>
