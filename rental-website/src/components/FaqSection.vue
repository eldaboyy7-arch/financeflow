<script setup lang="ts">
import { ref, computed } from 'vue'
import { useLanguage } from '@/composables/useLanguage'

const { t, isEnglish } = useLanguage()

interface FaqItem {
  question: string
  answer: string
}

const faqs = computed<FaqItem[]>(() => isEnglish.value ? [
  {
    question: 'What are the requirements for self-drive car rental?',
    answer: "Renters are required to present a valid driver's license (international driving permit or domestic license), passport or national identity card, and refundable security deposit verified upon reservation."
  },
  {
    question: 'Are chauffeur (with driver) services available?',
    answer: 'Yes, we offer full-day car rentals complete with professional, polite chauffeurs who know every scenic shortcut and destination in Bintan Island.'
  },
  {
    question: 'What is the minimum rental duration?',
    answer: 'Minimum rental duration is 1 day (24 hours for self-drive, or full-day up to 12 hours for chauffeur-driven packages).'
  },
  {
    question: 'Can I extend my rental duration during the trip?',
    answer: 'Yes, as long as the vehicle has not been booked by another traveler for the subsequent date. Kindly inform our team before your rental expires.'
  },
  {
    question: 'Are fuel and driver included in HiAce tour packages?',
    answer: 'Yes! All Toyota HiAce 15-seater tour and charter packages include the private vehicle, professional chauffeur, and fuel (BBM) for full-day island exploration.'
  },
  {
    question: 'Can I rent a Toyota HiAce or minibus without driver (self-drive)?',
    answer: 'No. For passenger safety, insurance, and licensing regulations, all Toyota HiAce (Commuter & Premio) and minibuses are provided exclusively with a professional chauffeur.'
  },
  {
    question: 'How do I book a car or tour package?',
    answer: 'Select your preferred vehicle or tour on this website, then click the WhatsApp button to confirm schedule, rates, and ferry terminal meetup details with our team.'
  }
] : [
  {
    question: 'Apa saja syarat sewa lepas kunci?',
    answer: 'Penyewa wajib menunjukkan KTP asli yang masih berlaku, SIM A aktif, serta dokumen pendukung atau jaminan yang diverifikasi oleh admin saat proses reservasi.'
  },
  {
    question: 'Apakah tersedia rental mobil dengan driver?',
    answer: 'Ya, kami menyediakan layanan sewa harian lengkap dengan driver berpengalaman yang ramah dan memahami rute jalan di seluruh Pulau Bintan.'
  },
  {
    question: 'Berapa lama minimal durasi sewa?',
    answer: 'Minimal sewa harian adalah 1 hari (24 jam untuk sistem lepas kunci, atau full-day sesuai kesepakatan pemakaian dengan driver).'
  },
  {
    question: 'Apakah durasi sewa bisa diperpanjang?',
    answer: 'Bisa, selama unit yang sedang Anda gunakan belum dipesan oleh penyewa lain pada jadwal berikutnya. Harap informasikan ke admin sebelum masa sewa berakhir.'
  },
  {
    question: 'Apakah paket tour HiAce sudah termasuk supir dan BBM?',
    answer: 'Ya, tarif Paket Tour & Charter Toyota HiAce 15 Kursi sudah termasuk armada HiAce, supir profesional, dan bahan bakar minyak (BBM) sesuai rute yang disepakati.'
  },
  {
    question: 'Apakah unit Toyota HiAce atau minibus banyak kursi bisa disewa lepas kunci?',
    answer: 'Tidak. Khusus unit Toyota HiAce (Commuter & Premio) serta armada minibus/bus hanya disewakan lengkap dengan supir profesional dan BBM demi standar keselamatan, regulasi lisensi, serta kenyamanan rombongan Anda.'
  },
  {
    question: 'Bagaimana cara melakukan pemesanan?',
    answer: 'Pilih unit armada atau paket tour di website ini, lalu klik tombol WhatsApp untuk konfirmasi tanggal, ketersediaan unit, dan titik penjemputan bersama admin.'
  }
])

const openIndex = ref<number | null>(0)

const toggleFaq = (index: number) => {
  openIndex.value = openIndex.value === index ? null : index
}
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 flex flex-col justify-between shadow-xs">
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">
        {{ t('faq.sectionBadge') }}
      </p>
      <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mb-2">
        {{ t('faq.title') }}
      </h3>
      <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
        {{ t('faq.subtitle') }}
      </p>

      <!-- Accordion List -->
      <div class="divide-y divide-slate-100 border-t border-b border-slate-100">
        <div
          v-for="(faq, idx) in faqs"
          :key="idx"
          class="py-3 sm:py-3.5"
        >
          <button
            @click="toggleFaq(idx)"
            type="button"
            class="w-full text-left flex items-center justify-between gap-3 group focus:outline-none"
            :aria-expanded="openIndex === idx"
          >
            <span
              class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors leading-snug"
            >
              {{ faq.question }}
            </span>
            <div
              class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 transition-all"
              :class="openIndex === idx ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-400'"
            >
              <svg
                class="w-3.5 h-3.5 transition-transform duration-200"
                :class="{ 'rotate-180': openIndex === idx }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </button>

          <div
            v-show="openIndex === idx"
            class="pt-2 pr-4 text-xs sm:text-sm text-slate-600 leading-relaxed"
          >
            {{ faq.answer }}
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6 pt-4 text-center">
      <p class="text-xs text-slate-500">
        {{ t('faq.contactSupport') }}
      </p>
    </div>
  </div>
</template>
