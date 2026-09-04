<script setup lang="ts">
const guarantees = [
  {
    title: 'Unit Bersih & Terawat',
    desc: 'Setiap kendaraan dibersihkan dan dicek kelengkapannya sebelum diserahterimakan.',
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  {
    title: 'Pemeriksaan Rutin',
    desc: 'Kondisi mesin, oli, rem, dan pendingin udara (AC) dipantau secara berkala.',
    icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
  },
  {
    title: 'Transparansi Tarif',
    desc: 'Tarif harian tercantum jelas sesuai informasi pada katalog unit resmi.',
    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  {
    title: 'Respon WhatsApp Langsung',
    desc: 'Pertanyaan mengenai jadwal, tarif, dan ketersediaan ditanggapi langsung oleh admin.',
    icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'
  }
]
</script>

<template>
  <section class="py-10 sm:py-14 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl mb-8">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Komitmen Layanan
        </h2>
        <p class="text-sm text-slate-500 mt-1">
          Standar operasional untuk menjamin kenyamanan dan keamanan berkendara Anda.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div
          v-for="(item, idx) in guarantees"
          :key="idx"
          class="p-5 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-start"
        >
          <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
            </svg>
          </div>
          <h3 class="text-sm font-bold text-slate-900 mb-1.5">{{ item.title }}</h3>
          <p class="text-xs text-slate-600 leading-relaxed">{{ item.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
