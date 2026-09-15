<script setup lang="ts">
const pillars = [
  {
    title: 'Armada Terawat',
    desc: 'Kendaraan bersih, nyaman dan selalu prima',
    iconColor: 'text-amber-500',
    iconBg: 'bg-amber-50',
    icon: 'shield'
  },
  {
    title: 'Driver Profesional',
    desc: 'Berpengalaman, ramah dan tepat waktu',
    iconColor: 'text-blue-500',
    iconBg: 'bg-blue-50',
    icon: 'user'
  },
  {
    title: 'Harga Transparan',
    desc: 'Harga jelas tanpa biaya tersembunyi',
    iconColor: 'text-emerald-500',
    iconBg: 'bg-emerald-50',
    icon: 'tag'
  },
  {
    title: 'Layanan 24 Jam',
    desc: 'Siap membantu kapan pun anda membutuhkan',
    iconColor: 'text-indigo-500',
    iconBg: 'bg-indigo-50',
    icon: 'support'
  },
]
</script>

<template>
  <section class="py-6 sm:py-10 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
      <div class="grid grid-cols-4 gap-2 sm:gap-6">
        <div
          v-for="(p, idx) in pillars"
          :key="idx"
          v-reveal:fade-up="idx * 80"
          class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-1.5 sm:gap-3.5 p-1.5 sm:p-3 rounded-2xl transition-all hover:bg-slate-50"
        >
          <!-- Icon Circle / Frame matching mockup -->
          <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full sm:rounded-2xl flex items-center justify-center shrink-0 border border-amber-400/30 bg-amber-400/10 text-amber-600 shadow-2xs">
            <!-- Shield -->
            <svg v-if="p.icon === 'shield'" class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <!-- User / Driver -->
            <svg v-else-if="p.icon === 'user'" class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <!-- Price Tag -->
            <svg v-else-if="p.icon === 'tag'" class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <!-- Support 24 Jam -->
            <svg v-else class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>

          <!-- Text -->
          <div class="min-w-0">
            <h4 class="text-[10px] sm:text-sm font-bold text-slate-900 leading-tight">
              {{ p.title }}
            </h4>
            <p class="hidden sm:block text-xs text-slate-500 leading-relaxed mt-0.5">
              {{ p.desc }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
