<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { siteConfig } from '@/config/site'

interface DestinationItem {
  id: string
  name: string
  badge: string
  icon: string
  description: string
  ctaText: string
  waText: string
  image?: string
}

const destinations: DestinationItem[] = [
  {
    id: 'lagoi',
    name: 'Kawasan Lagoi',
    badge: 'Pantai & Resort',
    icon: '🏖️',
    description: 'Nikmati kawasan wisata di utara Bintan dengan pantai berpasir putih, area resort, dan berbagai pilihan aktivitas santai seperti di Lagoi Bay dan Treasure Bay.',
    ctaText: 'Rencanakan perjalanan ke Lagoi',
    waText: 'Halo 3 Putri Mulya, saya ingin tanya sewa mobil atau paket tour untuk rute ke Kawasan Lagoi.'
  },
  {
    id: 'busung',
    name: 'Gurun Pasir & Danau Biru Busung',
    badge: 'Spot Foto Ikonik',
    icon: '🏜️',
    description: 'Kawasan perbukitan pasir dengan pemandangan danau berwarna biru toska, menjadi salah satu titik singgah favorit untuk berfoto saat melintasi rute Tanjung Uban.',
    ctaText: 'Rencanakan rute ke Danau Biru',
    waText: 'Halo 3 Putri Mulya, saya ingin tanya kendaraan yang cocok untuk kunjungan ke Danau Biru & Gurun Pasir Busung.'
  },
  {
    id: 'trikora',
    name: 'Pantai Trikora',
    badge: 'Pesisir Alami',
    icon: '🌴',
    description: 'Garis pantai alami dengan susunan batu granit besar dan suasana pesisir yang tenang, cocok untuk menikmati hembusan angin laut dan kuliner lokal pesisir.',
    ctaText: 'Rencanakan perjalanan ke Trikora',
    waText: 'Halo 3 Putri Mulya, saya ingin sewa kendaraan untuk rute santai ke Pantai Trikora.'
  },
  {
    id: 'patung-seribu',
    name: 'Vihara Ksitigarbha (Patung Seribu)',
    badge: 'Religi & Budaya',
    icon: '🏛️',
    description: 'Destinasi religi dan budaya di Tanjungpinang dengan ratusan patung arhat yang tertata rapi di area perbukitan dengan arsitektur yang megah.',
    ctaText: 'Kunjungi Patung Seribu bersama kami',
    waText: 'Halo 3 Putri Mulya, saya ingin tanya sewa mobil atau tur untuk ke Vihara Patung Seribu.'
  }
]

const getWhatsAppUrl = (text: string) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}
</script>

<template>
  <section id="inspirasi" class="py-12 sm:py-16 bg-slate-50 border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Section Header -->
      <div class="max-w-3xl mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-3">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span>Eksplorasi Pulau Bintan</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Inspirasi Destinasi di Bintan
        </h2>
        <p class="text-sm sm:text-base text-slate-600 mt-2 leading-relaxed">
          Beberapa kawasan populer yang sering dikunjungi di Pulau Bintan. Kami siap mengantar atau menyediakan armada terbaik untuk perjalanan Anda.
        </p>
      </div>

      <!-- 4 Destination Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="dest in destinations"
          :key="dest.id"
          class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all group"
        >
          <!-- Visual Top Section (Clean atmospheric header with badge & icon) -->
          <div class="p-6 bg-gradient-to-br from-slate-900 via-slate-850 to-blue-950 text-white relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 text-5xl opacity-20 select-none pointer-events-none">
              {{ dest.icon }}
            </div>
            <div class="relative z-10 flex items-center justify-between gap-2 mb-3">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-200 border border-white/15 backdrop-blur-xs">
                <span>{{ dest.icon }}</span>
                <span>{{ dest.badge }}</span>
              </span>
            </div>
            <h3 class="relative z-10 text-lg font-bold text-white leading-snug group-hover:text-blue-300 transition-colors">
              {{ dest.name }}
            </h3>
          </div>

          <!-- Card Content Body -->
          <div class="p-5 flex-1 flex flex-col justify-between">
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
              {{ dest.description }}
            </p>

            <!-- Subtle Link CTA to WhatsApp planning -->
            <div class="pt-3 border-t border-slate-100">
              <a
                :href="getWhatsAppUrl(dest.waText)"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 group-hover:translate-x-0.5 transition-all"
              >
                <span>{{ dest.ctaText }}</span>
                <span class="text-sm leading-none">&rarr;</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Supporting Note: Bridge back to core services -->
      <div class="mt-8 p-4 rounded-xl bg-white border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-600">
        <div class="flex items-center gap-2">
          <span class="text-base">💡</span>
          <span>Punya rencana rute wisata lain atau custom keliling Bintan? Konsultasikan langsung bersama tim kami.</span>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          <a href="#armada" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">Lihat Armada &rarr;</a>
          <RouterLink to="/paket-tour-bintan" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">Paket Tour HiAce &rarr;</RouterLink>
        </div>
      </div>

    </div>
  </section>
</template>
