<script setup lang="ts">
const reasons = [
  {
    id: 'armada',
    title: 'Armada Siap Digunakan',
    desc: 'Unit dibersihkan dan dicek kondisinya secara berkala sebelum diserahkan kepada pelanggan.',
    iconPath: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
  },
  {
    id: 'fleksibel',
    title: 'Pilihan Perjalanan Fleksibel',
    desc: 'Tersedia opsi rental lepas kunci, dengan driver, hingga paket HiAce untuk perjalanan rombongan.',
    iconPath: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'
  },
  {
    id: 'hiburan',
    title: 'HiAce Fasilitas Hiburan',
    desc: 'Unit HiAce tertentu dilengkapi fasilitas karaoke dan Smart TV untuk perjalanan lebih menyenangkan.',
    iconPath: 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3'
  },
  {
    id: 'harga',
    title: 'Informasi Harga Jelas',
    desc: 'Rincian biaya, durasi, dan fasilitas disampaikan secara transparan sejak awal sebelum Anda memesan.',
    iconPath: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  }
]
</script>

<template>
  <section id="kenapa-kami" class="py-14 sm:py-20 bg-white border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="lg:flex lg:gap-12 xl:gap-16">

        <!-- Kolom Judul (Kiri) -->
        <div class="lg:w-72 xl:w-80 shrink-0 mb-8 lg:mb-0 lg:pt-2">
          <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">
            Komitmen Kami untuk Anda
          </p>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-3">
            Kenapa Memilih 3 Putri Mulya?
          </h2>
          <p class="text-sm text-slate-600 leading-relaxed">
            Kami berusaha memastikan kendaraan, pilihan layanan, dan informasi perjalanan Anda, jelas sejak awal.
          </p>
        </div>

        <!-- Kolom Kartu (Kanan: 4-col di desktop, 2-col di tablet/mobile) -->
        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
          <div
            v-for="item in reasons"
            :key="item.id"
            class="p-5 rounded-2xl border border-slate-200/90 bg-slate-50/50 flex flex-col justify-start hover:border-blue-200 hover:bg-blue-50/20 hover:shadow-xs transition-all"
          >
            <!-- Icon with soft blue background -->
            <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 mb-4 shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.iconPath" />
              </svg>
            </div>

            <h3 class="text-sm sm:text-base font-bold text-slate-900 mb-2 leading-snug">
              {{ item.title }}
            </h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
              {{ item.desc }}
            </p>
          </div>
        </div>

      </div>

    </div>
  </section>
</template>
