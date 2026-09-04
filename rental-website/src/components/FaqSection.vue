<script setup lang="ts">
import { ref } from 'vue'

interface FaqItem {
  question: string
  answer: string
}

const faqs: FaqItem[] = [
  {
    question: 'Apa saja syarat untuk sewa mobil lepas kunci?',
    answer: 'Untuk sewa lepas kunci (self-drive), penyewa wajib menunjukkan identitas diri asli yang masih berlaku (KTP), Surat Izin Mengemudi (SIM A) aktif, serta dokumen pendukung atau jaminan yang telah diverifikasi oleh admin kami saat proses reservasi.'
  },
  {
    question: 'Apakah melayani rental mobil dengan driver?',
    answer: 'Ya, kami melayani rental mobil harian lengkap dengan pengemudi berpengalaman yang ramah dan memahami rute jalan di Pulau Bintan maupun Kota Tanjungpinang.'
  },
  {
    question: 'Berapa durasi minimal penyewaan mobil?',
    answer: 'Durasi sewa harian minimal adalah 1 hari (24 jam untuk sistem lepas kunci, atau full-day sesuai kesepakatan pemakaian dengan driver). Untuk kebutuhan sewa jangka panjang (mingguan atau bulanan), silakan hubungi admin kami.'
  },
  {
    question: 'Apakah durasi sewa mobil bisa diperpanjang?',
    answer: 'Bisa, selama unit kendaraan yang sedang Anda gunakan belum dipesan oleh penyewa lain pada jadwal berikutnya. Harap informasikan rencana perpanjangan kepada admin sesegera mungkin sebelum masa sewa berakhir.'
  },
  {
    question: 'Apakah paket tour HiAce sudah termasuk supir dan BBM?',
    answer: 'Ya, khusus Paket Tour & Charter Toyota HiAce 15 Kursi, tarif yang tercantum sudah termasuk unit HiAce, supir, dan bahan bakar minyak (BBM) untuk rute wisata yang disepakati.'
  },
  {
    question: 'Bagaimana cara melakukan pemesanan armada atau paket tour?',
    answer: 'Pilih unit kendaraan atau paket tour yang Anda inginkan di halaman website ini, lalu klik tombol WhatsApp. Anda akan langsung terhubung dengan admin 3 Putri Mulya untuk konfirmasi tanggal, ketersediaan unit, dan detail penjemputan.'
  }
]

const openIndex = ref<number | null>(0)

const toggleFaq = (index: number) => {
  openIndex.value = openIndex.value === index ? null : index
}
</script>

<template>
  <section id="faq" class="py-12 sm:py-16 bg-white border-t border-slate-200 scroll-mt-16">
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
