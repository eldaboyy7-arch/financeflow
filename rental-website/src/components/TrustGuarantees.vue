<script setup lang="ts">
import { ref } from 'vue'

interface GuaranteeItem {
  id: string
  title: string
  tag: string
  image: string
  imageAlt: string
  desc: string
  highlights: string[]
  iconColor: string
  iconBg: string
  badgeColor: string
}

const reasons: GuaranteeItem[] = [
  {
    id: 'armada',
    title: 'Armada Prima & Terawat',
    tag: 'Unit Bersih & Prima',
    image: '/images/fleet/veloz-putih-bp1815oq.jpg',
    imageAlt: 'Toyota Veloz 3 Putri Mulya plat BP dalam kondisi bersih dan prima',
    desc: 'Unit selalu dicuci bersih luar-dalam dan melalui inspeksi teknis rutin sebelum diserahterimakan ke pelanggan.',
    highlights: [
      'Kabin wangi segar & AC dingin optimal',
      'Pengecekan rem, ban, & mesin berkala',
      'Unit tahun muda & siap rute jauh'
    ],
    iconColor: 'text-blue-600',
    iconBg: 'bg-blue-50 border-blue-100',
    badgeColor: 'bg-blue-600/90 text-white'
  },
  {
    id: 'fleksibel',
    title: 'Pilihan Perjalanan Fleksibel',
    tag: 'Eksplorasi Rute Bebas',
    image: '/images/bintan-roadtrip-consultation.jpg',
    imageAlt: 'Perjalanan road trip menyusuri pantai pesisir Pulau Bintan',
    desc: 'Bebas jelajahi pesisir dan penjuru Bintan: sewa lepas kunci untuk privasi penuh atau didampingi supir lokal berpengalaman.',
    highlights: [
      'Bebas tentukan destinasi wisata & kuliner',
      'Pilihan lepas kunci 24 jam atau supir',
      'Antar-jemput fleksibel bandara & pelabuhan'
    ],
    iconColor: 'text-emerald-600',
    iconBg: 'bg-emerald-50 border-emerald-100',
    badgeColor: 'bg-emerald-700/90 text-white'
  },
  {
    id: 'hiburan',
    title: 'HiAce Fasilitas Hiburan',
    tag: 'Karaoke & Smart TV',
    image: '/images/fleet/hiace-premio-interior-tv.jpg',
    imageAlt: 'Kabin HiAce Premio dilengkapi Smart TV plafon dan dua mic karaoke wireless',
    desc: 'Trip rombongan semakin hidup dan berkesan dengan fasilitas multimedia eksklusif Smart TV dan mikrofon karaoke nirkabel.',
    highlights: [
      'Smart TV plafon & audio jernih',
      '2 mikrofon nirkabel untuk karaoke',
      'Port charger HP di tiap baris kursi'
    ],
    iconColor: 'text-amber-600',
    iconBg: 'bg-amber-50 border-amber-100',
    badgeColor: 'bg-amber-600/90 text-white'
  },
  {
    id: 'kenyamanan',
    title: 'Kenyamanan Kabin Maksimal',
    tag: 'Kabin Lega & Nyaman',
    image: '/images/fleet/hiace-premio-interior-seats.jpg',
    imageAlt: 'Interior kabin kursi captain seat kulit HiAce Premio yang bersih, empuk, dan nyaman',
    desc: 'Perjalanan jauh keliling Bintan tetap santai dan bebas pegal dengan kursi empuk reclining, ruang kaki lega, dan AC sejuk di tiap baris.',
    highlights: [
      'Kursi reclining empuk & legroom leluasa',
      'Hembusan kisi AC sejuk di tiap baris',
      'Kabin bersih, higienis, & wangi segar'
    ],
    iconColor: 'text-indigo-600',
    iconBg: 'bg-indigo-50 border-indigo-100',
    badgeColor: 'bg-indigo-700/90 text-white'
  }
]

const scrollContainer = ref<HTMLElement | null>(null)
const activeIndex = ref(0)

const onScroll = () => {
  if (!scrollContainer.value) return
  const { scrollLeft, clientWidth } = scrollContainer.value
  const cardWidth = clientWidth * 0.84
  const newIndex = Math.round(scrollLeft / cardWidth)
  activeIndex.value = Math.min(Math.max(0, newIndex), reasons.length - 1)
}

const scrollToIndex = (idx: number) => {
  if (!scrollContainer.value) return
  const cardWidth = scrollContainer.value.clientWidth * 0.84 + 16
  scrollContainer.value.scrollTo({ left: idx * cardWidth, behavior: 'smooth' })
}
</script>

<template>
  <section id="kenapa-kami" class="py-12 sm:py-20 bg-white border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Header Terpusat yang Elegan & Seimbang -->
      <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold mb-2.5">
          <span class="w-2 h-2 rounded-full bg-blue-600"></span>
          <span>Standar Mutu &amp; Kenyamanan</span>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
          Kenapa Memilih 3 Putri Mulya?
        </h2>
        <p class="text-xs sm:text-base text-slate-600 mt-2.5 leading-relaxed">
          Komitmen penuh kami memastikan perjalanan wisata dan dinas Anda di Pulau Bintan aman, nyaman, dengan fasilitas armada terbaik dan tarif transparan sejak awal.
        </p>
      </div>

      <!-- Kartu Keunggulan: Mobile Horizontal Carousel / Desktop 4-Kolom Grid -->
      <div
        ref="scrollContainer"
        @scroll="onScroll"
        class="flex gap-4 overflow-x-auto pb-3 pt-1 px-1 snap-x snap-mandatory no-scrollbar
               lg:grid lg:grid-cols-4 lg:gap-6 lg:overflow-visible lg:pb-0 lg:px-0"
      >
        <div
          v-for="item in reasons"
          :key="item.id"
          class="snap-start shrink-0 w-[84vw] max-w-[310px] lg:w-auto
                 bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md hover:border-blue-300 transition-all duration-300 overflow-hidden flex flex-col group"
        >
          <!-- Gambar Header Nyata & Otentik -->
          <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
            <img
              :src="item.image"
              :alt="item.imageAlt"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
            
            <!-- Tag Badge di atas Foto -->
            <div class="absolute top-3 left-3">
              <span
                class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold shadow-xs backdrop-blur-md"
                :class="item.badgeColor"
              >
                {{ item.tag }}
              </span>
            </div>
          </div>

          <!-- Konten Kartu -->
          <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
            <div>
              <!-- Judul -->
              <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors leading-snug mb-1.5">
                {{ item.title }}
              </h3>

              <!-- Deskripsi Singkat -->
              <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                {{ item.desc }}
              </p>
            </div>

            <!-- Poin Keunggulan Konkret (Checklist) -->
            <div class="mt-3.5 pt-3.5 border-t border-slate-100 space-y-1.5">
              <div
                v-for="(point, idx) in item.highlights"
                :key="idx"
                class="flex items-start gap-2 text-xs text-slate-700"
              >
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <span class="leading-tight">{{ point }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Carousel Indicators (Hidden on Desktop) -->
      <div class="flex lg:hidden items-center justify-between text-xs text-slate-400 mt-2 px-1">
        <span class="inline-flex items-center gap-1.5 text-slate-500 font-medium text-[11px]">
          <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
          Geser kartu (4 Keunggulan Layanan)
        </span>
        <div class="flex items-center gap-1.5">
          <button
            v-for="(_, idx) in reasons"
            :key="idx"
            type="button"
            @click="scrollToIndex(idx)"
            class="h-1.5 rounded-full transition-all duration-300"
            :class="idx === activeIndex ? 'w-5 bg-blue-600' : 'w-2 bg-slate-300'"
            :aria-label="'Buka slide ' + (idx + 1)"
          />
        </div>
      </div>

      <!-- Trust Strip: Jaminan Tarif Transparan & Antar-Jemput Fleksibel -->
      <div class="mt-8 sm:mt-12 p-4 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/90 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3.5 text-left">
          <div class="w-10 h-10 rounded-xl bg-blue-100/80 text-blue-700 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <p class="text-xs sm:text-sm font-bold text-slate-900">
              Jaminan Tarif Transparan &amp; Antar-Jemput di Seluruh Titik Utama Bintan
            </p>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-normal">
              Tarif pasti disepakati di awal tanpa biaya siluman. Melayani Pelabuhan Sri Bintan Pura, BBT Lagoi, Bandara RHF, hingga hotel &amp; resort tujuan Anda.
            </p>
          </div>
        </div>

        <a
          href="#kontak"
          class="w-full sm:w-auto shrink-0 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-xs sm:text-sm font-semibold hover:bg-blue-700 transition-colors shadow-xs text-center"
        >
          <span>Konsultasi Perjalanan</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </div>

    </div>
  </section>
</template>
