<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useTourPackages } from '@/composables/useTourPackages'
import { useCurrency } from '@/composables/useCurrency'
import { useLanguage } from '@/composables/useLanguage'

const { packages: tourPackages, fetchTourPackages } = useTourPackages()
const { convertPrice } = useCurrency()
const { t, isEnglish } = useLanguage()

function formatTourCardPrice(pkg: any): string {
  const raw = pkg.rawPrice || (pkg.slug?.includes('commuter') ? 1400000 : pkg.slug?.includes('premio') ? 1500000 : 0)
  if (!raw || raw <= 0) return isEnglish.value ? 'Free Consultation' : 'Konsultasi Gratis'
  const converted = convertPrice(raw)
  return isEnglish.value ? `From ${converted.formatted}` : `Mulai ${converted.formatted}`
}

onMounted(() => {
  fetchTourPackages()
})

// Fallback scenic photos � cycled by index so even unknown slugs always get a nice photo
const scenicByIndex = [
  '/images/destinations/treasure-bay.jpg',
  '/images/destinations/trikora.jpg',
  '/images/destinations/patung-seribu.jpg',
]

// Explicit slug/id overrides (supports API slug, fallback id, numeric id)
const scenicByKey: Record<string, string> = {
  '1': '/images/destinations/treasure-bay.jpg',
  '2': '/images/destinations/trikora.jpg',
  '3': '/images/destinations/patung-seribu.jpg',
  'tour-hiace-commuter': '/images/destinations/treasure-bay.jpg',
  'tour-bintan-hiace-commuter': '/images/destinations/treasure-bay.jpg',
  'hiace-commuter': '/images/destinations/treasure-bay.jpg',
  'tour-hiace-premio': '/images/destinations/trikora.jpg',
  'tour-bintan-hiace-premio': '/images/destinations/trikora.jpg',
  'hiace-premio': '/images/destinations/trikora.jpg',
  'tour-hiace-custom': '/images/destinations/patung-seribu.jpg',
  'charter-hiace-bintan-custom': '/images/destinations/patung-seribu.jpg',
  'custom-charter': '/images/destinations/patung-seribu.jpg',
}

function getScenicPhoto(pkg: { id: string; slug: string }, idx: number): string {
  return (
    scenicByKey[pkg.id] ||
    scenicByKey[pkg.slug] ||
    scenicByIndex[idx % scenicByIndex.length]
  )
}

const badgeByKey: Record<string, string> = {
  '1': 'Paket 1 Hari',
  '2': 'Paket 2 Hari 1 Malam',
  '3': 'Paket Kustom',
  'tour-hiace-commuter': 'Full Day Tour',
  'tour-bintan-hiace-commuter': 'Full Day Tour',
  'hiace-commuter': 'Full Day Tour',
  'tour-hiace-premio': 'Luxury VIP',
  'tour-bintan-hiace-premio': 'Luxury VIP',
  'hiace-premio': 'Luxury VIP',
  'tour-hiace-custom': 'Rute Bebas',
  'charter-hiace-bintan-custom': 'Rute Bebas',
  'custom-charter': 'Paket Kustom',
}

function getTourBadge(pkg: { id: string; slug: string; badge?: string; duration?: string }): string {
  if (isEnglish.value) {
    if (pkg.id === '1' || pkg.slug?.includes('1-day')) return '1-Day Tour'
    if (pkg.id === '2' || pkg.slug?.includes('2d1n')) return '2D1N Tour'
    if (pkg.id === '3' || pkg.slug?.includes('custom')) return 'Custom Route'
    if (pkg.slug?.includes('commuter')) return 'Full Day Tour'
    if (pkg.slug?.includes('premio')) return 'Luxury VIP'
    return 'Bintan Tour'
  }
  return badgeByKey[pkg.id] || badgeByKey[pkg.slug] || pkg.badge || pkg.duration || 'Wisata Bintan'
}
</script>

<template>
  <section class="py-14 sm:py-20 bg-white border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Section Header -->
      <div v-reveal:fade-up class="flex items-end justify-between gap-3 mb-6 sm:mb-10">
        <div>
          <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-amber-500 mb-1.5 sm:mb-2">
            {{ t('tours.sectionBadge') }}
          </p>
          <h2 class="text-xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
            {{ t('tours.title') }}
          </h2>
          <p class="hidden sm:block text-sm text-slate-500 mt-1 max-w-xl">
            {{ t('tours.subtitle') }}
          </p>
        </div>

        <RouterLink
          to="/paket-tour-bintan"
          class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors shrink-0 group py-1"
        >
          <span class="sm:hidden">{{ isEnglish ? 'View All' : 'Lihat Semua' }}</span>
          <span class="hidden sm:inline">{{ isEnglish ? 'View All Tour Packages' : 'Lihat Semua Paket Tour' }}</span>
          <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </RouterLink>
      </div>

      <!-- MOBILE: Horizontal Scroll Carousel -->
      <div class="sm:hidden -mx-4 px-4">
        <div class="flex gap-4 overflow-x-auto pb-3 snap-x snap-mandatory" style="scrollbar-width:none;-ms-overflow-style:none;">
          <RouterLink
            v-for="(pkg, idx) in tourPackages.slice(0, 3)"
            :key="pkg.id || idx"
            v-reveal:fade-up="idx * 80"
            to="/paket-tour-bintan"
            class="group relative rounded-2xl overflow-hidden bg-slate-900 shadow-md flex-none snap-start block"
            style="width:72vw;max-width:280px;aspect-ratio:3/4;"
          >
            <!-- Scenic Background Photo -->
            <img
              :src="getScenicPhoto(pkg, idx)"
              :alt="pkg.title"
              class="w-full h-full object-cover opacity-90"
              loading="lazy"
            />

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent"></div>

            <!-- Top Badge -->
            <div class="absolute top-3 left-3 z-10">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/90 text-slate-900 shadow-sm">
                {{ getTourBadge(pkg) }}
              </span>
            </div>

            <!-- Bottom Content -->
            <div class="absolute bottom-0 inset-x-0 p-4 z-10">
              <h3 class="font-display text-sm font-bold text-white leading-snug line-clamp-2 mb-1.5">
                {{ pkg.title }}
              </h3>
              <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] text-slate-300 font-medium leading-tight">
                  {{ formatTourCardPrice(pkg) }}
                </span>
                <span class="text-[11px] font-bold text-amber-400 inline-flex items-center gap-0.5 shrink-0">
                  {{ isEnglish ? 'Details' : 'Detail' }}
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                  </svg>
                </span>
              </div>
            </div>
          </RouterLink>

          <!-- End spacer -->
          <div class="flex-none w-4 sm:hidden"></div>
        </div>

        <!-- Dot indicators -->
        <div class="flex justify-center gap-1.5 mt-3">
          <span
            v-for="(_, idx) in tourPackages.slice(0, 3)"
            :key="idx"
            class="h-1.5 rounded-full transition-all"
            :class="idx === 0 ? 'w-4 bg-amber-500' : 'w-1.5 bg-slate-300'"
          />
        </div>
      </div>

      <!-- DESKTOP: 3-Column Grid -->
      <div class="hidden sm:grid grid-cols-3 gap-6">
        <RouterLink
          v-for="(pkg, idx) in tourPackages.slice(0, 3)"
          :key="pkg.id || idx"
          v-reveal:fade-up="idx * 120"
          to="/paket-tour-bintan"
          class="group relative rounded-3xl overflow-hidden aspect-[16/10] bg-slate-900 shadow-md hover:shadow-xl transition-all duration-300 block"
        >
          <!-- Scenic Background Photo -->
          <img
            :src="getScenicPhoto(pkg, idx)"
            :alt="pkg.title"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85 group-hover:opacity-95"
            loading="lazy"
          />

          <!-- Gradient Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

          <!-- Top Badge -->
          <div class="absolute top-3.5 left-3.5 z-10">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-white/90 text-slate-900 shadow-sm backdrop-blur-sm">
              {{ getTourBadge(pkg) }}
            </span>
          </div>

          <!-- Bottom Content (Title & Price Tag) -->
          <div class="absolute bottom-0 inset-x-0 p-5 z-10">
            <h3 class="font-display text-lg font-bold text-white leading-snug group-hover:text-amber-300 transition-colors line-clamp-1 mb-1">
              {{ pkg.title }}
            </h3>
            <div class="flex items-center justify-between">
              <span class="text-xs text-slate-300 font-medium">
                {{ formatTourCardPrice(pkg) }}
              </span>
              <span class="text-xs font-bold text-amber-400 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                {{ isEnglish ? 'Details' : 'Detail' }}
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
              </span>
            </div>
          </div>
        </RouterLink>
      </div>

    </div>
  </section>
</template>
