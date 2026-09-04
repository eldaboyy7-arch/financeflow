<script setup lang="ts">
import { computed } from 'vue'
import { siteConfig } from '@/config/site'
import { rentalPackages, rentalRequirements } from '@/config/rentalPolicy'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
const hasPackages = computed(() => rentalPackages.length > 0)
const hasTerms = computed(() => rentalRequirements.length > 0)
const hasLocation = computed(() => Boolean(siteConfig.rentalAddress || siteConfig.rentalMapsUrl))
</script>

<template>
  <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-sm border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Brand Logo & Name -->
        <a href="#" class="flex items-center gap-3 group">
          <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-sm">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
            </svg>
          </div>
          <div>
            <span class="text-base sm:text-lg font-bold text-slate-900 tracking-tight block leading-tight">
              {{ siteConfig.rentalName }}
            </span>
            <span v-if="siteConfig.rentalCity" class="text-xs text-slate-500 font-medium">
              {{ siteConfig.rentalCity }}
            </span>
          </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
          <a href="#katalog" class="hover:text-blue-600 transition-colors">Armada</a>
          <a v-if="hasPackages" href="#paket" class="hover:text-blue-600 transition-colors">Paket Sewa</a>
          <a v-if="hasTerms" href="#syarat" class="hover:text-blue-600 transition-colors">Ketentuan</a>
          <a v-if="hasLocation" href="#lokasi" class="hover:text-blue-600 transition-colors">Lokasi</a>
          <a href="#faq" class="hover:text-blue-600 transition-colors">Bantuan</a>
        </nav>

        <!-- WhatsApp CTA -->
        <div class="flex items-center gap-3">
          <a
            :href="waUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition-colors"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
            </svg>
            <span class="hidden sm:inline">Hubungi Kami</span>
            <span class="sm:hidden">Chat</span>
          </a>
        </div>
      </div>
    </div>
  </header>
</template>
