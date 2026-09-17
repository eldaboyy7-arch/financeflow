<script setup lang="ts">
import { computed } from 'vue'
import { siteConfig } from '@/config/site'
import { useLanguage } from '@/composables/useLanguage'

const { t, isEnglish } = useLanguage()

const getWaUrl = (phone: string) => {
  const clean = phone.replace(/\D/g, '')
  const text = isEnglish.value
    ? 'Hello 3 Putri Mulya, I would like to inquire about car rental and tour packages in Bintan Island.'
    : 'Halo 3 Putri Mulya, saya ingin konsultasi kebutuhan sewa mobil / paket tour di Bintan.'
  return `https://wa.me/${clean}?text=${encodeURIComponent(text)}`
}

const waUrl = computed(() => getWaUrl(siteConfig.rentalPhone))
const waUrl2 = computed(() => getWaUrl(siteConfig.rentalPhoneSecondary))
</script>

<template>
  <section id="kontak" class="py-12 sm:py-16 bg-slate-50 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- MOBILE: Full-bleed background card -->
      <div class="lg:hidden relative rounded-3xl overflow-hidden shadow-2xl">
        <!-- Background Image -->
        <img
          src="/images/bintan-roadtrip-consultation.jpg"
          alt="Wisata Perjalanan Pesisir Pulau Bintan"
          class="absolute inset-0 w-full h-full object-cover object-center"
          loading="lazy"
        />
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/80 to-slate-950/95"></div>

        <!-- Content -->
        <div class="relative z-10 p-6 pt-7 pb-8">
          <!-- Eyebrow Badge -->
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-4">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            <span>{{ isEnglish ? 'FAST INQUIRY & RESERVATION' : 'KONSULTASI & PEMESANAN CEPAT' }}</span>
          </div>

          <!-- Heading -->
          <h2 class="font-display text-2xl font-black text-white tracking-tight leading-tight mb-3">
            {{ t('finalCta.title') }}
          </h2>

          <!-- Subtitle -->
          <p class="text-sm text-slate-300 leading-relaxed mb-6">
            {{ t('finalCta.subtitle') }}
          </p>

          <!-- 3 Service Highlights — horizontal pills -->
          <div class="flex flex-wrap gap-2 mb-7">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white/10 border border-white/15 text-white">
              <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              {{ isEnglish ? 'Self-Drive & Chauffeur' : 'Lepas Kunci & Supir' }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white/10 border border-white/15 text-white">
              <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              {{ isEnglish ? 'Airport & Ferry Transfer' : 'Antar Bandara & Ferry' }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white/10 border border-white/15 text-white">
              <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              {{ isEnglish ? 'Clean & Prime Fleet' : 'Unit Bersih & Prima' }}
            </span>
          </div>

          <!-- WhatsApp Buttons — 2 Admin -->
          <div class="flex flex-col gap-2.5 mb-4">
            <a
              :href="waUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="flex items-center justify-center gap-3 w-full px-6 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-extrabold text-sm shadow-xl shadow-emerald-900/50 transition-all"
            >
              <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
              </svg>
              <span>Admin 1 · 0813-7237-1120</span>
            </a>
            <a
              :href="waUrl2"
              target="_blank"
              rel="noopener noreferrer"
              class="flex items-center justify-center gap-3 w-full px-6 py-3.5 rounded-2xl bg-emerald-700 hover:bg-emerald-600 active:scale-95 text-white font-extrabold text-sm shadow-lg shadow-emerald-900/40 transition-all"
            >
              <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
              </svg>
              <span>Admin 2 · 0852-6326-7909</span>
            </a>
          </div>

          <div class="flex items-center justify-center gap-2 text-xs text-slate-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
            <span>{{ isEnglish ? 'Fast Response via 0813-7237-1120' : 'Respon Cepat via 0813-7237-1120' }}</span>
          </div>
        </div>
      </div>

      <!-- DESKTOP: Side-by-side layout -->
      <div class="hidden lg:block relative rounded-3xl bg-white border border-slate-200 shadow-xl overflow-hidden group">
        <div class="grid grid-cols-12 items-stretch">

          <!-- Left Content Area (7 Cols) -->
          <div class="col-span-7 p-12 xl:p-14 flex flex-col justify-between relative z-10">
            <div>
              <!-- Eyebrow Gold Badge -->
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 mb-4">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>{{ isEnglish ? 'FAST INQUIRY & RESERVATION' : 'KONSULTASI & PEMESANAN CEPAT' }}</span>
              </div>

              <!-- Main Heading -->
              <h2 class="font-display text-3xl lg:text-4xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                {{ t('finalCta.title') }}
              </h2>

              <!-- Subtitle -->
              <p class="text-base text-slate-600 leading-relaxed mb-8 max-w-xl">
                {{ t('finalCta.subtitle') }}
              </p>

              <!-- 3 Service Highlights -->
              <div class="grid grid-cols-3 gap-3 mb-8">
                <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                  <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <span class="text-xs font-bold text-slate-700">{{ isEnglish ? 'Self-Drive & Chauffeur' : 'Lepas Kunci & Supir' }}</span>
                </div>
                <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                  <div class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <span class="text-xs font-bold text-slate-700">{{ isEnglish ? 'Airport & Ferry Transfer' : 'Antar Bandara & Ferry' }}</span>
                </div>
                <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                  <div class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <span class="text-xs font-bold text-slate-700">{{ isEnglish ? 'Clean & Prime Fleet' : 'Unit Bersih & Prima' }}</span>
                </div>
              </div>
            </div>

            <!-- WhatsApp Action Row — 2 Admin -->
            <div class="flex flex-col gap-2.5 pt-4 border-t border-slate-200">
              <a
                :href="waUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-3 px-6 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/30 transition-all active:scale-95"
              >
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                <span>Admin 1 · 0813-7237-1120</span>
              </a>
              <a
                :href="waUrl2"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-3 px-6 py-3.5 rounded-2xl bg-emerald-700 hover:bg-emerald-600 text-white font-extrabold text-sm shadow-lg shadow-emerald-700/30 transition-all active:scale-95"
              >
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                </svg>
                <span>Admin 2 · 0852-6326-7909</span>
              </a>
            </div>
          </div>

          <!-- Right Image Area (5 Cols) -->
          <div class="col-span-5 relative overflow-hidden bg-slate-100">
            <img
              src="/images/bintan-roadtrip-consultation.jpg"
              alt="Wisata Perjalanan Pesisir Pulau Bintan"
              class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 opacity-90"
              loading="lazy"
            />
            <!-- Left gradient blend -->
            <div class="absolute inset-y-0 left-0 w-24 bg-gradient-to-r from-white to-transparent pointer-events-none"></div>
            <!-- Image Badge -->
            <div class="absolute bottom-4 right-4 z-10">
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold bg-white/90 text-slate-900 border border-slate-200/80 backdrop-blur-md shadow-md">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <span>{{ isEnglish ? 'Bintan Tours & Car Rental' : 'Rute Wisata & Rental Bintan' }}</span>
              </span>
            </div>
          </div>

        </div>
      </div>

    </div>
  </section>
</template>

