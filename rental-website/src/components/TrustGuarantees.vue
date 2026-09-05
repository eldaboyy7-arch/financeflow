<script setup lang="ts">
const reasons = [
  {
    number: '01',
    title: 'Armada Siap Digunakan',
    desc: 'Unit mobil selalu dalam kondisi prima, bersih wangi, dan dicek berkala sebelum kunci diserahkan ke tangan Anda.'
  },
  {
    number: '02',
    title: 'Pilihan Perjalanan Fleksibel',
    desc: 'Bebas tentukan gaya perjalanan: setir sendiri dengan sistem lepas kunci atau didampingi driver lokal berpengalaman.'
  },
  {
    number: '03',
    title: 'HiAce dengan Fasilitas Hiburan',
    desc: 'Khusus rombongan, unit Toyota HiAce 15 kursi dilengkapi Karaoke System & Smart TV agar perjalanan makin menyenangkan.'
  },
  {
    number: '04',
    title: 'Informasi Harga yang Jelas',
    desc: 'Tarif sewa transparan tanpa biaya tersembunyi. Syarat rental mudah dan proses konfirmasi cepat via WhatsApp.'
  }
]
</script>

<template>
  <section id="kenapa-kami" class="py-16 sm:py-24 bg-white border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Section Header -->
      <div class="max-w-3xl mb-12 sm:mb-16">
        <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-blue-600 mb-2">
          Standar Layanan
        </p>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
          Kenapa Memilih 3 Putri Mulya?
        </h2>
        <p class="text-sm sm:text-base text-slate-600 mt-2.5 leading-relaxed">
          Komitmen kami menghadirkan perjalanan yang aman, nyaman, dan terpercaya selama Anda berada di Bintan.
        </p>
      </div>

      <!-- Editorial 4-Column Layout (Typography & Whitespace Driven, No Mini-Cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10">
        <div
          v-for="item in reasons"
          :key="item.number"
          class="border-t border-slate-200 pt-5 sm:pt-6 flex flex-col justify-start"
        >
          <span class="text-xs font-bold text-blue-600 font-mono tracking-wider mb-2.5 block">
            {{ item.number }}
          </span>
          <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2 leading-snug">
            {{ item.title }}
          </h3>
          <p class="text-sm text-slate-600 leading-relaxed">
            {{ item.desc }}
          </p>
        </div>
      </div>

    </div>
  </section>
</template>
