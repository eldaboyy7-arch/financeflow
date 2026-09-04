<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="bg-slate-900 text-slate-400 text-xs border-t border-slate-800 pb-20 sm:pb-8 pt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <!-- Brand & Info -->
        <div>
          <div class="flex items-center gap-3 text-white font-bold text-base mb-2">
            <div class="w-9 h-9 rounded-xl bg-slate-950 flex items-center justify-center p-1 border border-amber-500/30 shrink-0 overflow-hidden">
              <img src="/images/logo-3pm.png" alt="Logo 3 Putri Mulya" class="w-full h-full object-contain" />
            </div>
            <div>
              <span class="text-base font-extrabold text-white block leading-tight">{{ siteConfig.rentalName }}</span>
              <span class="text-[10px] text-slate-400 font-normal">Rental Mobil &amp; Tour Bintan</span>
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

        <!-- WhatsApp Contact -->
        <div>
          <h4 class="text-slate-200 font-bold uppercase tracking-wider text-[11px] mb-3">Layanan Pelanggan</h4>
          <p class="text-slate-400 leading-relaxed mb-3">
            Untuk pemeriksaan ketersediaan tanggal dan konfirmasi serah terima, hubungi kami via WhatsApp:
          </p>
          <a
            :href="waUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-700/60 hover:bg-emerald-700 text-emerald-100 font-medium transition-colors"
          >
            <svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            <span>Buka Chat WhatsApp</span>
          </a>
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
