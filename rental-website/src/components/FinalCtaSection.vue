<script setup lang="ts">
import { computed } from 'vue'
import { siteConfig } from '@/config/site'

const getWaUrl = (phone: string) => {
  const clean = phone.replace(/\D/g, '')
  const text = 'Halo 3 Putri Mulya, saya ingin konsultasi kebutuhan sewa mobil / paket tour di Bintan.'
  return `https://wa.me/${clean}?text=${encodeURIComponent(text)}`
}

const waUrl = computed(() => getWaUrl(siteConfig.rentalPhone))
</script>

<template>
  <section id="kontak" class="py-12 sm:py-20 bg-slate-50 border-t border-slate-200 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Main Card: Editorial Travel Banner yang Natural & Bersih -->
      <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
          
          <!-- Left Column: Konten & WhatsApp CTA (7 Cols) -->
          <div class="lg:col-span-7 order-2 lg:order-1 p-6 sm:p-10 lg:p-12 xl:p-14 flex flex-col justify-between z-10">
            <div>
              <!-- Kategori Label -->
              <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-2">
                Konsultasi &amp; Pemesanan Cepat
              </p>

              <!-- Main Heading -->
              <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-3 sm:mb-4">
                Sudah Memiliki Rencana Perjalanan?
              </h2>

              <!-- Description -->
              <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6 sm:mb-8">
                Ceritakan kebutuhan transportasi Anda di Pulau Bintan. Tim kami siap memberikan rekomendasi unit yang paling nyaman, urutan rute wisata yang efisien, dan penawaran harga terbaik untuk rombongan Anda.
              </p>

              <!-- Keunggulan Layanan: Alami & Bersih (Non-Template AI) -->
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 sm:mb-8">
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Rute Fleksibel</h3>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-snug">Bebas tentukan tujuan atau ikuti saran supir lokal.</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Harga Transparan</h3>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-snug">Tarif pasti disepakati awal tanpa biaya siluman.</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Respon Cepat</h3>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-snug">Admin standby via WhatsApp untuk cek ketersediaan.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Bar: Tombol WhatsApp -->
            <div class="pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-4">
              <a
                :href="waUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-3 px-6 sm:px-7 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-all shadow-sm active:scale-95 group shrink-0"
              >
                <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                  </svg>
                </div>
                <div class="text-left">
                  <span class="block text-[10px] uppercase tracking-wider text-emerald-100 font-semibold leading-tight">Hubungi via WhatsApp</span>
                  <span class="font-extrabold text-white text-base tracking-wide leading-none mt-0.5 block">0813-7237-1120</span>
                </div>
                <svg class="w-4 h-4 text-emerald-200 group-hover:translate-x-1 transition-transform ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
              </a>

              <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                <span>Siap melayani sewa lepas kunci &amp; paket tour seluruh Bintan</span>
              </div>
            </div>
          </div>

          <!-- Right Column: Foto Pesisir Bintan Dibuat Transparan Sedikit & Menyatu Halus (Non-AI Template) -->
          <div class="lg:col-span-5 order-1 lg:order-2 relative min-h-[220px] sm:min-h-[260px] lg:min-h-full bg-slate-50 overflow-hidden">
            <!-- Foto dengan transparansi halus -->
            <img
              src="/images/bintan-roadtrip-consultation.jpg"
              alt="Perjalanan Wisata Pesisir Pulau Bintan bersama 3 Putri Mulya"
              class="w-full h-full object-cover object-center opacity-80 hover:opacity-90 transition-opacity duration-300"
              loading="lazy"
            />
            
            <!-- Gradient Blend Kiri (Desktop) agar menyatu lembut dengan background putih kartu -->
            <div class="hidden lg:block absolute inset-y-0 left-0 w-28 bg-gradient-to-r from-white via-white/50 to-transparent pointer-events-none"></div>

            <!-- Gradient Blend Bawah (Mobile) -->
            <div class="lg:hidden absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>

            <!-- Keterangan Lokasi Sederhana & Organik -->
            <div class="absolute bottom-3 right-3 sm:bottom-4 sm:right-4 z-10">
              <span class="text-[11px] font-medium text-slate-600 bg-white/75 backdrop-blur-xs px-2.5 py-1 rounded-md border border-slate-200/50 shadow-2xs">
                Rute Pesisir Pantai Bintan
              </span>
            </div>
          </div>

        </div>
      </div>

    </div>
  </section>
</template>
