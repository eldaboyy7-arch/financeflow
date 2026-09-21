<script setup lang="ts">
import { computed } from 'vue'
import { useLanguage } from '@/composables/useLanguage'
import {
  getActiveUpcomingEvents,
  getEventStatus,
  getDaysUntil,
  generateEventWhatsAppUrl,
  type BintanEvent
} from '@/config/bintanEvents'
import { siteConfig } from '@/config/site'

const { t, isEnglish } = useLanguage()

const events = computed(() => getActiveUpcomingEvents(4))

function getCountdownBadge(event: BintanEvent) {
  const status = getEventStatus(event)
  if (status === 'live') {
    return {
      text: isEnglish.value ? 'Live Now' : 'Sedang Berlangsung',
      classes: 'bg-rose-600 text-white font-bold',
      isLive: true
    }
  }
  const days = getDaysUntil(event.startDate)
  if (days <= 0) {
    return {
      text: isEnglish.value ? 'Starts Today' : 'Hari Ini',
      classes: 'bg-amber-600 text-white font-bold',
      isLive: false
    }
  }
  if (days <= 30) {
    return {
      text: isEnglish.value ? `In ${days} Days` : `H-${days} Hari`,
      classes: 'bg-amber-500 text-slate-950 font-extrabold shadow-xs',
      isLive: false
    }
  }
  return {
    text: isEnglish.value ? `In ${days} Days` : `H-${days} Hari`,
    classes: 'bg-slate-900/90 text-white font-semibold backdrop-blur-xs',
    isLive: false
  }
}

function getBookingUrl(event: BintanEvent) {
  return generateEventWhatsAppUrl(event, siteConfig.rentalPhone, isEnglish.value ? 'en' : 'id')
}
</script>

<template>
  <section id="upcoming-events" class="py-12 sm:py-16 bg-slate-50 border-t border-slate-200/80 scroll-mt-16 sm:scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Section Header -->
      <div v-reveal:fade-up class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6 sm:mb-10">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-2">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
            <span>{{ t('events.sectionBadge') }}</span>
          </div>
          <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
            {{ t('events.title') }}
          </h2>
          <p class="text-xs sm:text-sm text-slate-600 mt-1.5 max-w-2xl leading-relaxed">
            {{ t('events.subtitle') }}
          </p>
        </div>

        <a
          :href="'https://wa.me/' + siteConfig.rentalPhone + '?text=' + encodeURIComponent(isEnglish ? 'Hello 3 Putri Mulya, I would like to inquire about rental cars for upcoming events in Bintan.' : 'Halo 3 Putri Mulya, saya ingin konsultasi sewa armada untuk menghadiri event di Bintan.')"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-emerald-700 hover:text-emerald-800 transition-colors shrink-0 group py-1 self-start sm:self-end"
        >
          <span>{{ isEnglish ? 'Consult Group Charter' : 'Konsultasi Carter Rombongan' }}</span>
          <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      </div>

      <!-- MOBILE: Horizontal Swipe Carousel -->
      <div class="sm:hidden -mx-4 px-4">
        <div class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory" style="scrollbar-width:none;-ms-overflow-style:none;">
          <article
            v-for="(event, idx) in events"
            :key="event.id"
            v-reveal:fade-up="idx * 70"
            class="flex-none snap-start w-[82vw] max-w-[320px] bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm flex flex-col justify-between"
          >
            <!-- Card Image Box -->
            <div class="relative aspect-[16/10] bg-slate-900 overflow-hidden">
              <img
                :src="event.image"
                :alt="isEnglish ? event.titleEn : event.title"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>

              <!-- Top Floating Badges -->
              <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between gap-1.5 pointer-events-none">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 text-slate-900 shadow-sm backdrop-blur-xs">
                  {{ isEnglish ? (event.highlightBadgeEn || event.categoryEn) : (event.highlightBadge || event.category) }}
                </span>
                <span :class="['inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px]', getCountdownBadge(event).classes]">
                  <span v-if="getCountdownBadge(event).isLive" class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                  <span>{{ getCountdownBadge(event).text }}</span>
                </span>
              </div>

              <!-- Bottom Photo Credit -->
              <div class="absolute bottom-1.5 right-2 text-[9px] text-white/75 drop-shadow-sm font-medium">
                {{ t('events.sourcePhoto') }}: {{ event.sourceCredit }}
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-4 flex-1 flex flex-col justify-between">
              <div>
                <!-- Date & Location Meta -->
                <div class="flex flex-col gap-1 text-[11px] text-slate-600 mb-2">
                  <div class="inline-flex items-center gap-1.5 font-bold text-indigo-700">
                    <svg class="w-3.5 h-3.5 shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ isEnglish ? event.dateDisplayEn : event.dateDisplay }}</span>
                  </div>
                  <div class="inline-flex items-center gap-1.5 text-slate-500 line-clamp-1">
                    <svg class="w-3.5 h-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>{{ isEnglish ? event.locationEn : event.location }}</span>
                  </div>
                </div>

                <!-- Event Title -->
                <h3 class="text-sm font-extrabold text-slate-900 leading-snug line-clamp-2 mb-1.5">
                  {{ isEnglish ? event.titleEn : event.title }}
                </h3>

                <!-- Short Description -->
                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mb-3">
                  {{ isEnglish ? event.descriptionEn : event.description }}
                </p>
              </div>

              <div>
                <!-- Recommended Fleet Box (Clean SVG Icon) -->
                <div class="p-2 rounded-lg bg-slate-100/90 border border-slate-200/80 mb-3 text-[11px] text-slate-800 flex items-start gap-2">
                  <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4m-9-2h10m2 0h1a1 1 0 001-1v-4a2 2 0 00-1-1.73l-2.4-4.8A2 2 0 0015.82 4H8.18a2 2 0 00-1.78 1.1L4.01 9.9A2 2 0 003 11.63V15a1 1 0 001 1h1"/>
                  </svg>
                  <div class="leading-tight">
                    <span class="font-bold block text-slate-900">{{ t('events.recommendedFleet') }}</span>
                    <span class="text-slate-600">{{ isEnglish ? event.recommendedFleetEn : event.recommendedFleet }}</span>
                  </div>
                </div>

                <!-- Direct WhatsApp Booking Button -->
                <a
                  :href="getBookingUrl(event)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                  </svg>
                  <span>{{ t('events.bookTransport') }}</span>
                </a>
              </div>
            </div>
          </article>
        </div>
      </div>

      <!-- DESKTOP / TABLET: Responsive Grid (2 cols md, 4 cols lg) -->
      <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6">
        <article
          v-for="(event, idx) in events"
          :key="event.id"
          v-reveal:fade-up="idx * 80"
          class="group bg-white rounded-2xl overflow-hidden border border-slate-200/90 hover:border-slate-300 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col justify-between"
        >
          <div>
            <!-- Image Box -->
            <div class="relative aspect-[16/10] bg-slate-900 overflow-hidden">
              <img
                :src="event.image"
                :alt="isEnglish ? event.titleEn : event.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>

              <!-- Top Floating Badges -->
              <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between gap-1 pointer-events-none">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 text-slate-900 shadow-sm backdrop-blur-xs">
                  {{ isEnglish ? (event.highlightBadgeEn || event.categoryEn) : (event.highlightBadge || event.category) }}
                </span>
                <span :class="['inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px]', getCountdownBadge(event).classes]">
                  <span v-if="getCountdownBadge(event).isLive" class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                  <span>{{ getCountdownBadge(event).text }}</span>
                </span>
              </div>

              <!-- Bottom Photo Credit -->
              <div class="absolute bottom-1.5 right-2 text-[9px] text-white/75 drop-shadow-sm font-medium">
                {{ t('events.sourcePhoto') }}: {{ event.sourceCredit }}
              </div>
            </div>

            <!-- Card Content -->
            <div class="p-4 sm:p-5">
              <!-- Meta (Date & Location) -->
              <div class="flex flex-col gap-1 text-[11px] text-slate-600 mb-2.5">
                <div class="inline-flex items-center gap-1.5 font-bold text-indigo-700">
                  <svg class="w-3.5 h-3.5 shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span>{{ isEnglish ? event.dateDisplayEn : event.dateDisplay }}</span>
                </div>
                <div class="inline-flex items-center gap-1.5 text-slate-500 line-clamp-1">
                  <svg class="w-3.5 h-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span :title="isEnglish ? event.locationEn : event.location">{{ isEnglish ? event.locationEn : event.location }}</span>
                </div>
              </div>

              <!-- Title -->
              <h3 class="text-sm sm:text-base font-extrabold text-slate-900 leading-snug group-hover:text-blue-700 transition-colors line-clamp-2 mb-2 min-h-[2.5rem]">
                {{ isEnglish ? event.titleEn : event.title }}
              </h3>

              <!-- Description -->
              <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                {{ isEnglish ? event.descriptionEn : event.description }}
              </p>
            </div>
          </div>

          <!-- Card Bottom Section (Fleet recommendation + WA Button) -->
          <div class="p-4 sm:p-5 pt-0">
            <!-- Recommended Fleet Box (Clean SVG Icon) -->
            <div class="p-2.5 rounded-xl bg-slate-100/90 border border-slate-200/80 mb-3 text-[11px] text-slate-800 flex items-start gap-2">
              <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4m-9-2h10m2 0h1a1 1 0 001-1v-4a2 2 0 00-1-1.73l-2.4-4.8A2 2 0 0015.82 4H8.18a2 2 0 00-1.78 1.1L4.01 9.9A2 2 0 003 11.63V15a1 1 0 001 1h1"/>
              </svg>
              <div class="leading-tight">
                <span class="font-bold block text-slate-900">{{ t('events.recommendedFleet') }}</span>
                <span class="text-slate-600 line-clamp-2">{{ isEnglish ? event.recommendedFleetEn : event.recommendedFleet }}</span>
              </div>
            </div>

            <!-- WhatsApp Booking Button -->
            <a
              :href="getBookingUrl(event)"
              target="_blank"
              rel="noopener noreferrer"
              class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white text-xs font-bold shadow-xs hover:shadow-md transition-all duration-200"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
              </svg>
              <span>{{ t('events.bookTransport') }}</span>
            </a>
          </div>
        </article>
      </div>

    </div>
  </section>
</template>
