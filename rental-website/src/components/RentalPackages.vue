<script setup lang="ts">
import { computed } from 'vue'
import { rentalPackages } from '@/config/rentalPolicy'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const packages = computed(() => rentalPackages)
const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
</script>

<template>
  <!-- Hanya render section paket jika terdapat data paket yang dikonfigurasi resmi -->
  <section v-if="packages.length > 0" id="paket" class="py-10 sm:py-14 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl mb-8">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Pilihan Paket Layanan
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Opsi sewa kendaraan yang tersedia sesuai kebutuhan perjalanan Anda.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="pkg in packages"
          :key="pkg.id"
          class="rounded-xl border border-slate-200 p-6 flex flex-col justify-between hover:border-slate-300 transition-colors"
        >
          <div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">{{ pkg.title }}</h3>
            <p class="text-sm text-slate-600 mb-4 leading-relaxed">{{ pkg.description }}</p>

            <ul v-if="pkg.highlights.length > 0" class="space-y-2 mb-6">
              <li
                v-for="(h, idx) in pkg.highlights"
                :key="idx"
                class="flex items-start gap-2 text-xs text-slate-600"
              >
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ h }}</span>
              </li>
            </ul>
          </div>

          <a
            :href="waUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors"
          >
            Tanya Detail Paket
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
