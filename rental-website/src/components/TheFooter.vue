<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="bg-slate-50 text-slate-600 text-xs border-t border-slate-200 pt-14 pb-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- 4 Columns Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10 mb-12">
        
        <!-- Kolom 1: Brand & Bio -->
        <div>
          <div class="flex items-center gap-3 text-slate-900 font-bold text-base mb-3">
            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center p-1 border border-amber-500/40 shrink-0 overflow-hidden shadow-2xs">
              <img src="/images/logo-3pm.png" alt="Logo 3 Putri Mulya" class="w-full h-full object-contain" />
            </div>
            <div>
              <span class="font-display text-base font-black text-slate-900 block leading-tight tracking-tight">{{ siteConfig.rentalName }}</span>
              <span class="text-[10px] font-medium tracking-wide text-slate-500 block leading-none mt-0.5">Rental Mobil &amp; Tour Bintan</span>
            </div>
          </div>
          <p class="text-slate-600 leading-relaxed text-xs mb-4">
            Rental mobil lepas kunci &amp; dengan driver serta paket tour di Bintan untuk perjalanan pribadi, keluarga, dan rombongan.
          </p>
          <!-- Social / Quick Contact Icons -->
          <div class="flex items-center gap-2 pt-1">
            <a :href="waUrl" target="_blank" rel="noopener noreferrer" title="WhatsApp" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-emerald-600 hover:border-emerald-300 transition-colors shadow-2xs">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" title="Instagram" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-pink-600 hover:border-pink-300 transition-colors shadow-2xs">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" title="Facebook" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-600 hover:border-blue-300 transition-colors shadow-2xs">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
          </div>
        </div>

        <!-- Kolom 2: Navigasi -->
        <div>
          <h4 class="text-slate-900 font-bold uppercase tracking-wider text-[11px] mb-3">Navigasi</h4>
          <ul class="space-y-2 text-xs">
            <li><a href="/#cara-perjalanan" class="hover:text-blue-600 transition-colors">Pilihan Layanan</a></li>
            <li><RouterLink to="/armada" class="hover:text-blue-600 transition-colors">Pilihan Armada</RouterLink></li>
            <li><RouterLink to="/paket-tour-bintan" class="hover:text-blue-600 transition-colors">Paket Tour HiAce</RouterLink></li>
            <li><RouterLink to="/destinasi" class="hover:text-blue-600 transition-colors">Inspirasi Destinasi</RouterLink></li>
            <li><a href="/#faq" class="hover:text-blue-600 transition-colors">Pertanyaan Umum (FAQ)</a></li>
          </ul>
        </div>

        <!-- Kolom 3: Layanan -->
        <div>
          <h4 class="text-slate-900 font-bold uppercase tracking-wider text-[11px] mb-3">Layanan</h4>
          <ul class="space-y-2 text-xs">
            <li><a href="/#cara-perjalanan" class="hover:text-blue-600 transition-colors">Rental Mobil Harian</a></li>
            <li><a href="/#cara-perjalanan" class="hover:text-blue-600 transition-colors">Sewa Lepas Kunci (Self-Drive)</a></li>
            <li><a href="/#cara-perjalanan" class="hover:text-blue-600 transition-colors">Sewa dengan Driver</a></li>
            <li><RouterLink to="/paket-tour-bintan" class="hover:text-blue-600 transition-colors">Paket Tour HiAce 15 Kursi</RouterLink></li>
            <li><RouterLink to="/paket-tour-bintan" class="hover:text-blue-600 transition-colors">Charter &amp; Rombongan</RouterLink></li>
          </ul>
        </div>

        <!-- Kolom 4: Hubungi Kami -->
        <div>
          <h4 class="text-slate-900 font-bold uppercase tracking-wider text-[11px] mb-3">Hubungi Kami</h4>
          <ul class="space-y-2.5 text-xs">
            <li>
              <a
                :href="waUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-slate-700 hover:text-emerald-600 transition-colors group"
              >
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 group-hover:scale-125 transition-transform"></span>
                <span class="font-bold text-slate-900">0813-7237-1120</span>
                <span class="text-[10px] text-slate-500">(Admin 1)</span>
              </a>
            </li>
            <li class="flex items-center gap-2 text-slate-600">
              <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span>Tanjungpinang &amp; Bintan, Kep. Riau</span>
            </li>
            <li class="flex items-center gap-2 text-slate-600">
              <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Setiap Hari: 06.00 &ndash; 22.00 WIB</span>
            </li>
          </ul>
        </div>

      </div>

      <!-- Copyright Bar -->
      <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-slate-500 text-xs">
        <p>&copy; {{ currentYear }} {{ siteConfig.rentalName }}. Seluruh hak cipta dilindungi.</p>
        <div class="flex items-center gap-4 text-[11px]">
          <span class="text-slate-500 hover:text-slate-800 transition-colors cursor-pointer">Kebijakan Privasi</span>
          <span class="text-slate-300">&bull;</span>
          <span class="text-slate-500 hover:text-slate-800 transition-colors cursor-pointer">Syarat &amp; Ketentuan</span>
        </div>
      </div>

    </div>
  </footer>
</template>
