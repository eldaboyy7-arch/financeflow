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

function formatOriginalPrice(pkg: any): string {
  const isPremio = pkg.slug?.includes('premio') || pkg.title?.toLowerCase().includes('premio')
  const isCommuter = pkg.slug?.includes('commuter') || pkg.title?.toLowerCase().includes('commuter')
  const raw = pkg.rawOriginalPrice || (isPremio ? 2000000 : isCommuter ? 1800000 : 0)
  if (!raw || raw <= 0) return ''
  return convertPrice(raw).formatted
}

function formatSavings(pkg: any): string {
  const isPremio = pkg.slug?.includes('premio') || pkg.title?.toLowerCase().includes('premio')
  const isCommuter = pkg.slug?.includes('commuter') || pkg.title?.toLowerCase().includes('commuter')
  const orig = pkg.rawOriginalPrice || (isPremio ? 2000000 : isCommuter ? 1800000 : 0)
  const current = pkg.rawPrice || (isPremio ? 1500000 : isCommuter ? 1400000 : 0)
  const diff = Math.max(0, orig - current)
  if (diff <= 0) return ''
  return convertPrice(diff).formatted
}

onMounted(() => {
  fetchTourPackages()
})

// Fallback scenic vertical posters (9:16)
const scenicByIndex = [
  '/images/tours/poster-hiace-premio.jpg',
  '/images/tours/poster-hiace-commuter.jpg',
  '/images/tours/poster-custom-charter.jpg',
]

// Explicit slug/id overrides to vertical 9:16 posters
const scenicByKey: Record<string, string> = {
  '1': '/images/tours/poster-hiace-commuter.jpg',
  '2': '/images/tours/poster-hiace-premio.jpg',
  '3': '/images/tours/poster-custom-charter.jpg',
  'tour-hiace-commuter': '/images/tours/poster-hiace-commuter.jpg',
  'tour-bintan-hiace-commuter': '/images/tours/poster-hiace-commuter.jpg',
  'hiace-commuter': '/images/tours/poster-hiace-commuter.jpg',
  'tour-hiace-premio': '/images/tours/poster-hiace-premio.jpg',
  'tour-bintan-hiace-premio': '/images/tours/poster-hiace-premio.jpg',
  'tour-bintan-hiace-premio-luxury': '/images/tours/poster-hiace-premio.jpg',
  'hiace-premio': '/images/tours/poster-hiace-premio.jpg',
  'tour-hiace-custom': '/images/tours/poster-custom-charter.jpg',
  'charter-hiace-bintan-custom': '/images/tours/poster-custom-charter.jpg',
  'custom-charter': '/images/tours/poster-custom-charter.jpg',
  'custom-charter-bintan': '/images/tours/poster-custom-charter.jpg',
}

function getScenicPhoto(pkg: { id: string; slug: string; title?: string }, idx: number): string {
  const slug = (pkg.slug || '').toLowerCase()
  const title = (pkg.title || '').toLowerCase()
  const idStr = String(pkg.id || '')

  if (slug.includes('premio') || title.includes('premio') || idStr === '2') {
    return '/images/tours/poster-hiace-premio.jpg'
  }
  if (slug.includes('commuter') || title.includes('commuter') || idStr === '1') {
    return '/images/tours/poster-hiace-commuter.jpg'
  }
  if (slug.includes('custom') || title.includes('custom') || idStr === '3') {
    return '/images/tours/poster-custom-charter.jpg'
  }

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
  'tour-bintan-hiace-premio-luxury': 'Luxury VIP',
  'hiace-premio': 'Luxury VIP',
  'tour-hiace-custom': 'Rute Bebas',
  'charter-hiace-bintan-custom': 'Rute Bebas',
  'custom-charter': 'Paket Kustom',
  'custom-charter-bintan': 'Rute Bebas',
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
  <section id="tour-packages" class="py-12 sm:py-16 bg-white border-t border-slate-200/80 scroll-mt-16 sm:scroll-mt-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Section Header -->
      <div v-reveal:fade-up class="flex items-end justify-between gap-3 mb-6 sm:mb-8">
        <div>
          <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-amber-500 mb-1 sm:mb-1.5">
            {{ t('tours.sectionBadge') }}
          </p>
          <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
            {{ t('tours.title') }}
          </h2>
          <p class="hidden sm:block text-xs sm:text-sm text-slate-500 mt-1 max-w-lg">
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
        <div class="flex gap-3.5 overflow-x-auto pb-3 snap-x snap-mandatory" style="scrollbar-width:none;-ms-overflow-style:none;">
          <RouterLink
            v-for="(pkg, idx) in tourPackages.slice(0, 3)"
            :key="pkg.id || idx"
            v-reveal:fade-up="idx * 80"
            to="/paket-tour-bintan"
            class="group relative rounded-2xl overflow-hidden bg-slate-900 shadow-md flex-none snap-start block border border-slate-200/50"
            style="width:68vw;max-width:245px;aspect-ratio:9/16;"
          >
            <!-- Vertical Poster Background Photo -->
            <img
              :src="getScenicPhoto(pkg, idx)"
              :alt="pkg.title"
              class="w-full h-full object-cover opacity-95 group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />

            <!-- Subtle Gradient Overlay (Mainly Bottom for Readability) -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/20 to-transparent pointer-events-none"></div>

            <!-- Top Badge -->
            <div class="absolute top-2.5 left-2.5 z-10 flex flex-wrap gap-1 items-center">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 text-slate-900 shadow-sm backdrop-blur-xs">
                {{ getTourBadge(pkg) }}
              </span>
              <span v-if="pkg.rawOriginalPrice || pkg.slug?.includes('premio') || pkg.slug?.includes('commuter')" class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-black bg-rose-600 text-white shadow-xs animate-pulse">
                🔥 -{{ pkg.discountPercent || (pkg.slug?.includes('premio') ? 25 : 22) }}%
              </span>
            </div>

            <!-- Bottom Content -->
            <div class="absolute bottom-0 inset-x-0 p-3 z-10">
              <h3 class="font-display text-xs sm:text-sm font-bold text-white leading-snug line-clamp-1 mb-1 drop-shadow-sm">
                {{ isEnglish ? (pkg.titleEn || pkg.title) : pkg.title }}
              </h3>
              <div class="flex items-end justify-between gap-1.5">
                <div class="min-w-0">
                  <div v-if="pkg.rawOriginalPrice || pkg.slug?.includes('premio') || pkg.slug?.includes('commuter')" class="flex items-center gap-1 leading-none mb-0.5">
                    <span class="text-[9px] text-slate-300 line-through whitespace-nowrap">
                      {{ formatOriginalPrice(pkg) }}
                    </span>
                    <span class="text-[8px] font-bold text-rose-300 bg-rose-950/80 px-1 py-0.2 rounded border border-rose-500/40 whitespace-nowrap">
                      {{ isEnglish ? `Save ${formatSavings(pkg)}` : `Hemat ${formatSavings(pkg)}` }}
                    </span>
                  </div>
                  <span class="text-[11px] font-black text-amber-300 leading-tight block whitespace-nowrap">
                    {{ formatTourCardPrice(pkg) }}
                  </span>
                </div>
                <span class="text-[10px] font-bold text-amber-300 inline-flex items-center gap-0.5 shrink-0 bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/30 backdrop-blur-xs">
                  {{ isEnglish ? 'Details' : 'Detail' }}
                  <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

      <!-- DESKTOP: Centered 3-Column Grid with Medium Vertical Cards -->
      <div class="hidden sm:grid grid-cols-3 gap-6">
        <RouterLink
          v-for="(pkg, idx) in tourPackages.slice(0, 3)"
          :key="pkg.id || idx"
          v-reveal:fade-up="idx * 120"
          to="/paket-tour-bintan"
          class="group relative rounded-2xl sm:rounded-3xl overflow-hidden aspect-[9/16] bg-slate-900 shadow-md hover:shadow-xl transition-all duration-300 block border border-slate-200/60 hover:-translate-y-1.5"
        >
          <!-- Vertical Poster Background Photo -->
          <img
            :src="getScenicPhoto(pkg, idx)"
            :alt="pkg.title"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-95 group-hover:opacity-100"
            loading="lazy"
          />

          <!-- Subtle Gradient Overlay (Mainly Bottom for Readability) -->
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/20 to-transparent pointer-events-none"></div>

          <!-- Top Badge -->
          <div class="absolute top-3.5 left-3.5 z-10 flex items-center gap-1.5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/95 text-slate-900 shadow-sm backdrop-blur-xs">
              {{ getTourBadge(pkg) }}
            </span>
            <span v-if="pkg.rawOriginalPrice || pkg.slug?.includes('premio') || pkg.slug?.includes('commuter')" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white shadow-md animate-pulse">
              🔥 -{{ pkg.discountPercent || (pkg.slug?.includes('premio') ? 25 : 22) }}%
            </span>
          </div>

          <!-- Bottom Content (Title & Price Tag) -->
          <div class="absolute bottom-0 inset-x-0 p-4 sm:p-4.5 z-10">
            <h3 class="font-display text-sm sm:text-base font-bold text-white leading-snug group-hover:text-amber-300 transition-colors line-clamp-1 mb-1.5 drop-shadow-sm">
              {{ isEnglish ? (pkg.titleEn || pkg.title) : pkg.title }}
            </h3>
            <div class="flex items-end justify-between gap-2">
              <div class="min-w-0">
                <div v-if="pkg.rawOriginalPrice || pkg.slug?.includes('premio') || pkg.slug?.includes('commuter')" class="flex items-center gap-1.5 mb-0.5">
                  <span class="text-[10px] sm:text-[11px] text-slate-300 line-through whitespace-nowrap">
                    {{ formatOriginalPrice(pkg) }}
                  </span>
                  <span class="text-[9px] sm:text-[10px] font-bold text-rose-300 bg-rose-950/80 px-1.5 py-0.5 rounded border border-rose-500/40 whitespace-nowrap">
                    {{ isEnglish ? `Save ${formatSavings(pkg)}` : `Hemat ${formatSavings(pkg)}` }}
                  </span>
                </div>
                <span class="text-xs sm:text-sm lg:text-base font-black text-amber-300 whitespace-nowrap">
                  {{ formatTourCardPrice(pkg) }}
                </span>
              </div>
              <span class="text-xs font-bold text-amber-300 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform shrink-0 bg-amber-400/10 px-2.5 py-1 rounded-lg border border-amber-400/30 backdrop-blur-xs">
                {{ isEnglish ? 'Details' : 'Detail' }}
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
