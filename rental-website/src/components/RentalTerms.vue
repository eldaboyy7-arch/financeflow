<script setup lang="ts">
import { computed } from 'vue'
import { rentalRequirements } from '@/config/rentalPolicy'
import { siteConfig } from '@/config/site'
import { generateGeneralWhatsAppUrl } from '@/utils/whatsapp'

const requirements = computed(() => rentalRequirements)
const waUrl = computed(() => generateGeneralWhatsAppUrl(siteConfig.rentalPhone, siteConfig.rentalName))
</script>

<template>
  <!-- Hanya render section persyaratan jika terdapat data persyaratan yang dikonfigurasi resmi -->
  <section v-if="requirements.length > 0" id="syarat" class="py-10 sm:py-14 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl mb-8">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Ketentuan & Persyaratan Sewa
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Dokumen dan persyaratan umum untuk proses serah terima kendaraan.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="(cat, idx) in requirements"
          :key="idx"
          class="bg-white rounded-xl border border-slate-200 p-6"
        >
          <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">
            {{ cat.category }}
          </h3>
          <ul class="space-y-3">
            <li
              v-for="(item, i) in cat.items"
              :key="i"
              class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-600"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-2 shrink-0"></span>
              <span>{{ item }}</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="mt-6 text-center">
        <p class="text-xs text-slate-500 mb-2">
          Ada pertanyaan terkait persyaratan dokumen atau jaminan?
        </p>
        <a
          :href="waUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1"
        >
          Konsultasikan dengan Admin via WhatsApp &rarr;
        </a>
      </div>
    </div>
  </section>
</template>
