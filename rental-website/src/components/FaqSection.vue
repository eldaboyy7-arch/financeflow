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
    answer: "Renters must present a valid driver's license (domestic license or International Driving Permit), original identity card or passport, and standard identity verification processed quickly upon reservation."
  },
  {
    question: 'Can the car be delivered or picked up at Ferry Terminals or Airport?',
    answer: 'Yes, absolutely! We provide direct vehicle handover and pickup services at Sri Bintan Pura Ferry Terminal (Tanjung Pinang), Bandar Bentan Telani (BBT Lagoi), Tanjung Uban Ferry Port, RHF Airport, as well as hotels and resorts across Bintan Island.'
  },
  {
    question: 'What is the minimum rental duration and how are hours calculated?',
    answer: 'Minimum rental duration is 1 day. Self-drive rentals are calculated on a full 24-hour cycle per rental day. Chauffeur-driven rentals operate on a full-day basis (up to 10–12 service hours per day).'
  },
  {
    question: 'What is the fuel (BBM) policy for self-drive rentals?',
    answer: 'We operate on a transparent Bar-to-Bar policy (return as received). The vehicle should be returned with the same fuel gauge level as when it was originally handed over to you.'
  },
  {
    question: 'Are driver and fuel included in HiAce tour packages?',
    answer: 'Yes! All Toyota HiAce 15-seater tour and private charter packages are All-Inclusive, covering the private vehicle, experienced local chauffeur, and fuel (BBM) for agreed itinerary routes.'
  },
  {
    question: 'Can Toyota HiAce or minibus vans be rented self-drive (without driver)?',
    answer: 'No. For passenger safety, local road regulations, and group traveling comfort, all Toyota HiAce (Commuter & Premio) and minibus vans are provided exclusively with an experienced professional chauffeur.'
  },
  {
    question: 'What happens if I return the car late (Overtime policy)?',
    answer: 'Late returns are subject to an hourly overtime fee (10% of the daily rental rate per hour). Delays exceeding 5 hours will be calculated as an additional full day. Please inform our team in advance if you require an extension.'
  },
  {
    question: 'How do I book a vehicle and what are the payment terms?',
    answer: 'Select your preferred vehicle or tour package on this website, then tap the WhatsApp button to confirm your schedule. A down payment (DP) secures and locks your booking, with the remaining balance paid upon vehicle handover.'
  }
] : [
  {
    question: 'Apa saja syarat sewa mobil lepas kunci?',
    answer: 'Penyewa wajib menunjukkan e-KTP asli yang masih berlaku, SIM A aktif, serta dokumen pendukung atau jaminan identitas yang diverifikasi secara cepat oleh admin saat proses reservasi.'
  },
  {
    question: 'Apakah mobil bisa diantar-jemput di Pelabuhan Ferry atau Bandara?',
    answer: 'Bisa! Kami melayani serah terima unit langsung di Pelabuhan Ferry Sri Bintan Pura (Tanjung Pinang), Pelabuhan BBT Lagoi, Pelabuhan Roro Tanjung Uban, Bandara Raja Haji Fisabilillah (RHF), maupun di hotel/resort tempat Anda menginap.'
  },
  {
    question: 'Berapa minimal durasi sewa dan bagaimana hitungan jamnya?',
    answer: 'Minimal sewa harian adalah 1 hari. Untuk sistem lepas kunci (self-drive) dihitung 24 jam penuh per hari sewa. Untuk pemakaian dengan supir dihitung harian (full-day 10–12 jam pemakaian per hari).'
  },
  {
    question: 'Bagaimana ketentuan bahan bakar (BBM) untuk sewa lepas kunci?',
    answer: 'Menggunakan sistem Bar-to-Bar (kembali sesuai posisi awal). Posisi indikator bahan bakar (BBM) saat unit dikembalikan harus sama dengan posisi saat pertama kali mobil diserahterimakan.'
  },
  {
    question: 'Apakah paket tour & charter HiAce sudah termasuk supir dan BBM?',
    answer: 'Ya, semua tarif Paket Tour & Charter Toyota HiAce 15 Kursi sudah All-In termasuk armada HiAce bersih, supir profesional berpengalaman, dan bahan bakar minyak (BBM) untuk rute wisata yang disepakati.'
  },
  {
    question: 'Apakah unit Toyota HiAce atau minibus bisa disewa lepas kunci?',
    answer: 'Tidak. Khusus unit Toyota HiAce (Commuter & Premio) serta armada minibus/bus hanya disewakan lengkap dengan supir profesional demi standar keselamatan, regulasi lisensi, serta kenyamanan rombongan Anda.'
  },
  {
    question: 'Bagaimana jika terlambat mengembalikan mobil (Overtime)?',
    answer: 'Keterlambatan pengembalian unit dikenakan biaya overtime per jam (10% dari tarif sewa harian). Jika keterlambatan melebihi 5 jam, akan dihitung sewa 1 hari penuh. Harap kabari admin lebih awal jika memerlukan perpanjangan waktu sewa.'
  },
  {
    question: 'Bagaimana cara booking dan bagaimana sistem pembayarannya?',
    answer: 'Pilih unit armada atau paket tour di website ini, lalu klik tombol WhatsApp untuk konfirmasi tanggal. Pembayaran uang muka (DP) dilakukan untuk mengunci jadwal unit, dan sisa pelunasan dibayarkan saat serah terima mobil di lokasi.'
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
