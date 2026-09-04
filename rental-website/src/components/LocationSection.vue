<script setup lang="ts">
import { computed } from 'vue'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const hasAddress = computed(() => Boolean(siteConfig.rentalAddress))
const hasMapsUrl = computed(() => Boolean(siteConfig.rentalMapsUrl))
const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
</script>

<template>
  <section id="lokasi" class="py-10 sm:py-14 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl mb-8">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Lokasi & Serah Terima
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Informasi garasi fisik dan koordinasi serah terima kendaraan.
        </p>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-base font-bold text-slate-900">Garasi & Kantor Operasional</h3>
                <span v-if="siteConfig.rentalCity" class="text-xs text-slate-500">{{ siteConfig.rentalCity }}</span>
              </div>
            </div>

            <div v-if="hasAddress" class="text-sm text-slate-600 leading-relaxed mb-6">
              {{ siteConfig.rentalAddress }}
            </div>
            <div v-else class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6">
              Titik serah terima dan lokasi garasi akan dikoordinasikan langsung bersama admin saat reservasi.
            </div>

            <div class="flex flex-wrap items-center gap-3">
              <a
                v-if="hasMapsUrl"
                :href="siteConfig.rentalMapsUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors"
              >
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Buka di Google Maps
              </a>

              <a
                :href="waUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors shadow-xs"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                Koordinasi Titik Jemput
              </a>
            </div>
          </div>

          <!-- Information card right side -->
          <div class="bg-slate-50 rounded-lg p-5 border border-slate-200/80">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Informasi Tambahan</h4>
            <ul class="space-y-2.5 text-xs text-slate-600">
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1.5 shrink-0"></span>
                <span>Kendaraan dapat diserahkan di garasi fisik atau titik temu yang disepakati.</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1.5 shrink-0"></span>
                <span>Pemeriksaan kondisi fisik dan dokumen kendaraan dilakukan bersama saat serah terima.</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1.5 shrink-0"></span>
                <span>Pastikan membawa kartu identitas yang sah saat proses pengambilan unit.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
