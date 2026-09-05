<script setup lang="ts">
const reasons = [
  {
    title: 'Armada Bersih & Prima',
    desc: 'Setiap mobil selalu dicuci wangi, AC dingin maksimal, dan diservis berkala sebelum kunci diserahkan ke tangan Anda.',
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  {
    title: 'Lepas Kunci / Driver',
    desc: 'Bebas pilih: setir sendiri untuk privasi perjalanan, atau santai didampingi driver lokal yang hafal seluruh rute Bintan.',
    icon: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'
  },
  {
    title: 'HiAce Spesial Rombongan',
    desc: 'Muat 15 penumpang dengan kabin lapang, dilengkapi fasilitas Karaoke System & Smart TV agar perjalanan makin seru.',
    icon: 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3'
  },
  {
    title: 'Tarif Jujur & Transparan',
    desc: 'Harga sewa jelas di depan tanpa biaya siluman. Syarat rental mudah dan proses konfirmasi cepat via WhatsApp.',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
  }
]
</script>

<template>
  <section id="kenapa-kami" class="py-12 sm:py-16 bg-white border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="max-w-3xl mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold mb-3 border border-slate-200">
          <span>Kepercayaan &amp; Kenyamanan</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Kenapa Memilih 3 Putri Mulya?
        </h2>
        <p class="text-sm sm:text-base text-slate-600 mt-2 leading-relaxed">
          Komitmen kami memberikan layanan sewa mobil yang aman, nyaman, dan terpercaya di Bintan.
        </p>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <div
          v-for="(item, idx) in reasons"
          :key="idx"
          class="p-3.5 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col justify-start hover:border-slate-300 hover:bg-white transition-all shadow-2xs"
        >
          <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2.5 sm:mb-4 shrink-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
            </svg>
          </div>
          <h3 class="text-xs sm:text-base font-bold text-slate-900 mb-1 sm:mb-2 leading-snug">{{ item.title }}</h3>
          <p class="text-[11px] sm:text-sm text-slate-600 leading-snug sm:leading-relaxed">{{ item.desc }}</p>
        </div>
      </div>

    </div>
  </section>
</template>
