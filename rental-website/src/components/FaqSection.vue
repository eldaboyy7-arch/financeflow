<script setup lang="ts">
import { ref } from 'vue'

interface FaqItem {
  question: string
  answer: string
}

const faqs: FaqItem[] = [
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
    question: 'Bagaimana cara melakukan pemesanan?',
    answer: 'Pilih unit armada atau paket tour di website ini, lalu klik tombol WhatsApp untuk konfirmasi tanggal, ketersediaan unit, dan titik penjemputan bersama admin.'
  }
]

const openIndex = ref<number | null>(0)

const toggleFaq = (index: number) => {
  openIndex.value = openIndex.value === index ? null : index
}
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 flex flex-col justify-between shadow-xs">
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">
        Bantuan &amp; Informasi
      </p>
      <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mb-2">
        Pertanyaan yang Sering Diajukan
      </h3>
      <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
        Jawaban singkat untuk pertanyaan yang paling sering ditanyakan seputar layanan kami.
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
        Ada pertanyaan lain? Hubungi admin via WhatsApp untuk respons cepat.
      </p>
    </div>
  </div>
</template>
