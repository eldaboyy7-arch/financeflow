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
  photoCredit: string
  photoCreditFull: string
}

const destinations: DestinationItem[] = [
  {
    id: 'lagoi',
    name: 'Kawasan Lagoi',
    badge: 'Pantai & Resort',
    icon: '🏖️',
    description: 'Kawasan resort utara Bintan dengan pantai pasir putih, Lagoi Bay, dan Treasure Bay.',
    ctaText: 'Tanya Rute Lagoi',
    waText: 'Halo 3 Putri Mulya, saya ingin tanya sewa mobil atau paket tour untuk rute ke Kawasan Lagoi.',
    image: '/images/destinations/lagoi.jpg',
    photoCredit: 'Dok. Kemenparekraf',
    photoCreditFull: 'Dokumentasi Resmi Kemenparekraf RI (Indonesia.travel)'
  },
  {
    id: 'busung',
    name: 'Gurun Pasir & Danau Biru',
    badge: 'Spot Foto Ikonik',
    icon: '🏜️',
    description: 'Perbukitan pasir eksotis berpadu danau toska, spot foto ikonik rute Tanjung Uban.',
    ctaText: 'Tanya Rute Danau Biru',
    waText: 'Halo 3 Putri Mulya, saya ingin tanya kendaraan yang cocok untuk kunjungan ke Danau Biru & Gurun Pasir Busung.',
    image: '/images/destinations/busung.jpg',
    photoCredit: 'Dok. Dispar Kepri',
    photoCreditFull: 'Dokumentasi Resmi Dinas Pariwisata Pemprov Kepulauan Riau'
  },
  {
    id: 'trikora',
    name: 'Pantai Trikora',
    badge: 'Pesisir Alami',
    icon: '🌴',
    description: 'Pesisir timur berpasir putih alami dengan susunan batu granit besar dan suasana tenang.',
    ctaText: 'Tanya Rute Trikora',
    waText: 'Halo 3 Putri Mulya, saya ingin sewa kendaraan untuk rute santai ke Pantai Trikora.',
    image: '/images/destinations/trikora.jpg',
    photoCredit: 'CC BY-SA 4.0',
    photoCreditFull: 'Foto oleh Andrian Vernandes (Wikimedia Commons, Lisensi CC BY-SA 4.0)'
  },
  {
    id: 'patung-seribu',
    name: 'Vihara Patung Seribu',
    badge: 'Religi & Budaya',
    icon: '🏛️',
    description: 'Kompleks vihara megah di perbukitan dengan ratusan patung arhat beraneka ekspresi.',
    ctaText: 'Tanya Rute Patung Seribu',
    waText: 'Halo 3 Putri Mulya, saya ingin tanya sewa mobil atau tur untuk ke Vihara Patung Seribu.',
    image: '/images/destinations/patung-seribu.jpg',
    photoCredit: 'Dok. Dispar Kepri',
    photoCreditFull: 'Dokumentasi Resmi Dinas Pariwisata Pemprov Kepulauan Riau'
  }
]

const getWhatsAppUrl = (text: string) => {
  const phone = siteConfig.rentalPhone.replace(/\D/g, '')
  return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
}
</script>

<template>
  <section id="inspirasi" class="py-14 sm:py-20 bg-slate-50 border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Desktop: judul kiri + kartu kanan (2-kolom) -->
      <!-- Mobile: judul di atas, 4 kartu horizontal scroll -->
      <div class="lg:flex lg:gap-12 xl:gap-16">

        <!-- Kolom Judul (kiri di desktop, atas di mobile) -->
        <div class="lg:w-72 xl:w-80 shrink-0 mb-8 lg:mb-0 lg:pt-2">
          <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">
            Inspirasi Perjalanan
          </p>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-3">
            Destinasi Populer di Bintan
          </h2>
          <p class="text-sm text-slate-600 leading-relaxed mb-6">
            Pilihan kawasan wisata favorit yang siap kami antar dengan unit rental atau paket tour.
          </p>
          <!-- Note (desktop only) -->
          <div class="hidden lg:block text-xs text-slate-500 space-y-2">
            <p class="flex items-start gap-2">
              <span>💡</span>
              <span>Punya rencana custom keliling Bintan? Konsultasikan langsung bersama tim kami.</span>
            </p>
            <div class="flex gap-3 pt-1">
              <a href="#armada" class="font-semibold text-blue-600 hover:underline text-xs">Lihat Armada →</a>
              <RouterLink to="/paket-tour-bintan" class="font-semibold text-blue-600 hover:underline text-xs">Paket HiAce →</RouterLink>
            </div>
          </div>
        </div>

        <!-- Kolom Kartu -->
        <div class="flex-1 min-w-0">
          <!-- Mobile: horizontal scroll -->
          <!-- Desktop: 2x2 atau 4-col grid -->
          <div class="flex gap-3 overflow-x-auto pb-3 snap-x snap-mandatory
                      lg:grid lg:grid-cols-2 xl:grid-cols-4 lg:gap-4 lg:overflow-visible lg:pb-0">
            <div
              v-for="dest in destinations"
              :key="dest.id"
              class="snap-start shrink-0 w-[72vw] sm:w-[44vw] lg:w-auto
                     bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col
                     hover:border-slate-300 hover:shadow-md transition-all group"
            >
              <!-- Image -->
              <div class="relative aspect-[4/3] overflow-hidden bg-slate-900">
                <img
                  v-if="dest.image"
                  :src="dest.image"
                  :alt="dest.name"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-slate-950/10 pointer-events-none"></div>
                <!-- Badge -->
                <div class="absolute top-2 left-2 z-10">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-900/80 text-slate-100 border border-white/20 backdrop-blur-sm">
                    <span>{{ dest.icon }}</span>
                    <span class="truncate max-w-[70px]">{{ dest.badge }}</span>
                  </span>
                </div>
                <!-- Credit -->
                <div class="absolute top-2 right-2 z-10">
                  <span
                    :title="'Sumber foto: ' + dest.photoCreditFull"
                    class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[8px] font-medium bg-slate-950/65 text-slate-200/90 backdrop-blur-sm border border-white/15 cursor-help"
                  >
                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="truncate max-w-[68px]">{{ dest.photoCredit }}</span>
                  </span>
                </div>
                <!-- Title over image -->
                <div class="absolute bottom-2 left-3 right-3 z-10">
                  <h3 class="text-sm font-bold text-white leading-tight drop-shadow-md group-hover:text-blue-200 transition-colors line-clamp-1">
                    {{ dest.name }}
                  </h3>
                </div>
              </div>
              <!-- Body -->
              <div class="p-3 flex-1 flex flex-col justify-between">
                <p class="text-[11px] text-slate-600 leading-snug mb-3">
                  {{ dest.description }}
                </p>
                <div class="pt-2 border-t border-slate-100">
                  <a
                    :href="getWhatsAppUrl(dest.waText)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors"
                  >
                    <span>Tanya Rute</span>
                    <span aria-hidden="true">&rarr;</span>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile note (below cards) -->
          <div class="lg:hidden mt-5 text-xs text-slate-500 flex flex-col gap-2">
            <p class="flex items-start gap-2">
              <span>💡</span>
              <span>Punya rencana custom keliling Bintan? Konsultasikan langsung bersama tim kami.</span>
            </p>
            <div class="flex gap-3">
              <a href="#armada" class="font-semibold text-blue-600 hover:underline">Lihat Armada →</a>
              <RouterLink to="/paket-tour-bintan" class="font-semibold text-blue-600 hover:underline">Paket HiAce →</RouterLink>
            </div>
          </div>

          <!-- Attribution -->
          <p class="text-[10px] text-slate-400 mt-4">
            * Foto destinasi: Kemenparekraf RI / Indonesia.travel, Dinas Pariwisata Pemprov Kepri, &amp; Wikimedia Commons CC BY-SA 4.0.
          </p>
        </div>

      </div>
    </div>
  </section>
</template>
