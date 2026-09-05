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
    waText: 'Halo 3 Putri Mulya, saya ingin tanya sewa mobil atau paket tour untuk rute ke Kawasan Lagoi.',
    image: '/images/destinations/lagoi.jpg'
  },
  {
    id: 'busung',
    name: 'Gurun Pasir & Danau Biru Busung',
    badge: 'Spot Foto Ikonik',
    icon: '🏜️',
    description: 'Kawasan perbukitan pasir dengan pemandangan danau berwarna biru toska, menjadi salah satu titik singgah favorit untuk berfoto saat melintasi rute Tanjung Uban.',
    ctaText: 'Rencanakan rute ke Danau Biru',
    waText: 'Halo 3 Putri Mulya, saya ingin tanya kendaraan yang cocok untuk kunjungan ke Danau Biru & Gurun Pasir Busung.',
    image: '/images/destinations/busung.jpg'
  },
  {
    id: 'trikora',
    name: 'Pantai Trikora',
    badge: 'Pesisir Alami',
    icon: '🌴',
    description: 'Garis pantai alami dengan susunan batu granit besar dan suasana pesisir yang tenang, cocok untuk menikmati hembusan angin laut dan kuliner lokal pesisir.',
    ctaText: 'Rencanakan perjalanan ke Trikora',
    waText: 'Halo 3 Putri Mulya, saya ingin sewa kendaraan untuk rute santai ke Pantai Trikora.',
    image: '/images/destinations/trikora.jpg'
  },
  {
    id: 'patung-seribu',
    name: 'Vihara Ksitigarbha (Patung Seribu)',
    badge: 'Religi & Budaya',
    icon: '🏛️',
    description: 'Destinasi religi dan budaya di Tanjungpinang dengan ratusan patung arhat yang tertata rapi di area perbukitan dengan arsitektur yang megah.',
    ctaText: 'Kunjungi Patung Seribu bersama kami',
    waText: 'Halo 3 Putri Mulya, saya ingin tanya sewa mobil atau tur untuk ke Vihara Patung Seribu.',
    image: '/images/destinations/patung-seribu.jpg'
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

      <!-- 4 Destination Cards Grid (2x2 on mobile, 4 columns on desktop) -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <div
          v-for="dest in destinations"
          :key="dest.id"
          class="bg-white rounded-xl sm:rounded-2xl border border-slate-200 overflow-hidden flex flex-col justify-between hover:border-slate-300 hover:shadow-lg transition-all group"
        >
          <!-- Image Top Section with smooth zoom & contrast gradient -->
          <div class="relative aspect-[4/3] sm:aspect-[16/10] overflow-hidden bg-slate-900">
            <img
              v-if="dest.image"
              :src="dest.image"
              :alt="dest.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
              loading="lazy"
            />
            
            <!-- Soft Bottom Gradient for Text Legibility -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-slate-950/20 pointer-events-none"></div>

            <!-- Top Left Floating Badge -->
            <div class="absolute top-2 left-2 sm:top-3 sm:left-3 z-10">
              <span class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-bold bg-slate-900/80 text-slate-100 border border-white/20 backdrop-blur-md shadow-xs">
                <span>{{ dest.icon }}</span>
                <span class="truncate max-w-[80px] sm:max-w-none">{{ dest.badge }}</span>
              </span>
            </div>

            <!-- Bottom Left Title over image -->
            <div class="absolute bottom-2 left-2.5 right-2.5 sm:bottom-3 sm:left-4 sm:right-4 z-10">
              <h3 class="text-xs sm:text-lg font-bold text-white leading-tight sm:leading-snug drop-shadow-md group-hover:text-blue-200 transition-colors line-clamp-2">
                {{ dest.name }}
              </h3>
            </div>
          </div>

          <!-- Card Content Body -->
          <div class="p-3 sm:p-5 flex-1 flex flex-col justify-between">
            <p class="text-[11px] sm:text-sm text-slate-600 leading-snug sm:leading-relaxed mb-3 sm:mb-5 line-clamp-2 sm:line-clamp-none">
              {{ dest.description }}
            </p>

            <!-- Subtle Link CTA to WhatsApp planning -->
            <div class="pt-2 sm:pt-3 border-t border-slate-100">
              <a
                :href="getWhatsAppUrl(dest.waText)"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1 sm:gap-1.5 text-[11px] sm:text-xs font-semibold text-blue-600 hover:text-blue-800 group-hover:translate-x-0.5 transition-all w-full justify-between sm:justify-start"
              >
                <span class="sm:hidden">Tanya Rute WA</span>
                <span class="hidden sm:inline">{{ dest.ctaText }}</span>
                <span class="text-xs sm:text-sm leading-none shrink-0">&rarr;</span>
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
