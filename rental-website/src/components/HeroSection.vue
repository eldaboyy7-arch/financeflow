<script setup lang="ts">
import { computed } from 'vue'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
</script>

<template>
  <section class="relative bg-white border-b border-slate-200 pt-10 pb-12 sm:pt-14 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl">
        <!-- Badge Status Real -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium mb-5 border border-slate-200">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>Katalog Resmi & Unit Terawat</span>
        </div>

        <!-- Main Headline -->
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
          Pilihan Sewa Mobil Andal untuk Kebutuhan Perjalanan Anda
        </h1>

        <!-- Subheadline / Description -->
        <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed">
          {{ siteConfig.rentalTagline }}. Telusuri spesifikasi lengkap, tarif harian transparan, dan lakukan reservasi langsung melalui WhatsApp resmi kami.
        </p>

        <!-- CTA Group -->
        <div class="flex flex-wrap items-center gap-3.5">
          <a
            href="#katalog"
            class="inline-flex items-center justify-center px-5 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition-colors"
          >
            Lihat Katalog Armada
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </a>

          <a
            :href="waUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center px-5 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            Tanya Ketersediaan
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
