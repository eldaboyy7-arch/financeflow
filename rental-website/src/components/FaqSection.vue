<script setup lang="ts">
import { ref } from 'vue'

interface FaqItem {
  question: string
  answer: string
}

const faqs: FaqItem[] = [
  {
    question: 'Bagaimana cara melakukan reservasi armada?',
    answer: 'Pilih unit mobil yang Anda inginkan pada katalog di atas, lalu klik tombol "Chat Sewa". Anda akan terhubung langsung ke WhatsApp admin dengan data mobil yang sudah terisi otomatis untuk memeriksa ketersediaan tanggal yang Anda rencanakan.'
  },
  {
    question: 'Bagaimana jika unit mobil yang saya inginkan berstatus "Sedang Disewa"?',
    answer: 'Anda tetap dapat mengklik tombol "Jadwal Lain" untuk menanyakan kepada admin perkiraan tanggal unit tersebut kembali tersedia atau untuk melakukan pemesanan di jadwal berikutnya.'
  },
  {
    question: 'Apakah tarif yang tercantum sudah termasuk bahan bakar atau pengemudi?',
    answer: 'Tarif yang tercantum pada katalog adalah tarif sewa harian kendaraan. Detail mengenai bahan bakar, pengemudi, atau biaya operasional lainnya dapat disesuaikan dan dikonfirmasi langsung dengan admin sesuai opsi paket yang Anda pilih.'
  },
  {
    question: 'Bagaimana mekanisme serah terima kendaraan?',
    answer: 'Serah terima dilakukan dengan pengecekan kondisi fisik kendaraan dan kelengkapan dokumen bersama di lokasi garasi atau titik temu yang telah disepakati bersama admin.'
  },
  {
    question: 'Kapan kepastian booking dianggap resmi?',
    answer: 'Kepastian jadwal dan ketersediaan armada dinyatakan resmi setelah konfirmasi langsung dan persetujuan dari admin rental melalui WhatsApp.'
  }
]

const openIndex = ref<number | null>(0)

const toggleFaq = (index: number) => {
  openIndex.value = openIndex.value === index ? null : index
}
</script>

<template>
  <section id="faq" class="py-10 sm:py-14 bg-white border-t border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-10">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Pertanyaan yang Sering Diajukan
        </h2>
        <p class="text-sm text-slate-500 mt-2">
          Informasi ringkas seputar proses pemesanan dan penyewaan kendaraan.
        </p>
      </div>

      <div class="space-y-3">
        <div
          v-for="(faq, idx) in faqs"
          :key="idx"
          class="rounded-xl border border-slate-200 overflow-hidden transition-colors"
          :class="openIndex === idx ? 'border-slate-300 bg-slate-50/50' : 'bg-white'"
        >
          <button
            @click="toggleFaq(idx)"
            type="button"
            class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-semibold text-sm sm:text-base text-slate-900 focus:outline-none"
          >
            <span>{{ faq.question }}</span>
            <svg
              class="w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0"
              :class="{ 'rotate-180': openIndex === idx }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <div
            v-show="openIndex === idx"
            class="px-5 pb-4 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100"
          >
            {{ faq.answer }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
