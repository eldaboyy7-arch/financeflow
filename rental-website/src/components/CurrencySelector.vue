<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useCurrency, type CurrencyCode, CURRENCIES } from '@/composables/useCurrency'

const { currentCurrency, setCurrency } = useCurrency()

const isOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

const handleSelect = (code: CurrencyCode) => {
  setCurrency(code)
  closeDropdown()
}

// Click outside listener
const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown()
  }
}

// Escape key listener
const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <div ref="dropdownRef" class="relative inline-block text-left">
    <!-- Trigger Button -->
    <button
      @click.stop="toggleDropdown"
      type="button"
      class="inline-flex items-center gap-2 px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-100 hover:text-white border border-slate-700 hover:border-slate-600 transition-all shadow-md active:scale-95 text-xs sm:text-sm font-semibold focus:outline-none"
      :aria-expanded="isOpen"
      aria-label="Pilih Mata Uang / Currency"
    >
      <!-- Circular Flag Avatar in Navbar Button -->
      <span class="w-6 h-6 rounded-full overflow-hidden shrink-0 shadow-xs border border-white/20 flex items-center justify-center">
        <!-- Indonesia Flag -->
        <svg v-if="currentCurrency === 'IDR'" viewBox="0 0 512 512" class="w-full h-full">
          <circle cx="256" cy="256" r="256" fill="#f8fafc" />
          <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#e11d48" />
        </svg>

        <!-- Singapore Flag (Bulan Sabit Besar + 5 Bintang Putih Jelas) -->
        <svg v-else-if="currentCurrency === 'SGD'" viewBox="0 0 512 512" class="w-full h-full">
          <circle cx="256" cy="256" r="256" fill="#ffffff" />
          <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#ef4444" />
          <!-- Bulan Sabit Putih Besar & Tebal -->
          <path d="M90 30 A 95 95 0 1 0 90 226 A 76 76 0 1 1 90 30 Z" fill="#ffffff" />
          <!-- 5 Bintang Putih Singapura (Lebih Besar & Jelas) -->
          <polygon points="200,48 204,60 216,60 207,67 210,79 200,72 190,79 193,67 184,60 196,60" fill="#ffffff" />
          <polygon points="245,82 249,94 261,94 252,101 255,113 245,106 235,113 238,101 229,94 241,94" fill="#ffffff" />
          <polygon points="230,138 234,150 246,150 237,157 240,169 230,162 220,169 223,157 214,150 226,150" fill="#ffffff" />
          <polygon points="170,138 174,150 186,150 177,157 180,169 170,162 160,169 163,157 154,150 166,150" fill="#ffffff" />
          <polygon points="155,82 159,94 171,94 162,101 165,113 155,106 145,113 148,101 139,94 151,94" fill="#ffffff" />
        </svg>

        <!-- Malaysia Flag -->
        <svg v-else-if="currentCurrency === 'MYR'" viewBox="0 0 512 512" class="w-full h-full">
          <circle cx="256" cy="256" r="256" fill="#ffffff" />
          <path d="M0 36h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0z" fill="#dc2626" />
          <path d="M0 256C0 114.6 114.6 0 256 0v256H0z" fill="#1e3a8a" />
          <path d="M75 55 A 72 72 0 1 0 75 201 A 60 60 0 1 1 75 55 Z" fill="#facc15" />
          <polygon points="180,95 186,115 206,108 195,125 214,133 194,141 205,158 185,151 179,171 173,151 153,158 164,141 144,133 163,125 152,108 172,115" fill="#facc15" />
        </svg>
      </span>

      <!-- Currency Code Text -->
      <span class="tracking-wide font-bold">{{ currentCurrency }}</span>

      <!-- Small Chevron Down -->
      <svg
        class="w-3.5 h-3.5 text-slate-300 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Popover Menu -->
    <transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-1"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-64 sm:w-72 rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl p-2 z-50 text-slate-200"
        role="menu"
      >
        <div class="px-3 py-2 border-b border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
          <span>Pilih Mata Uang / Currency</span>
          <span class="text-[10px] text-blue-400 font-normal lowercase">3 opsi</span>
        </div>

        <div class="py-1.5 space-y-1">
          <button
            v-for="curr in CURRENCIES"
            :key="curr.code"
            @click="handleSelect(curr.code)"
            type="button"
            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all"
            :class="currentCurrency === curr.code
              ? 'bg-blue-600/25 text-white border border-blue-500/40 shadow-xs'
              : 'hover:bg-slate-800/90 text-slate-300 hover:text-white border border-transparent'"
            role="menuitem"
          >
            <div class="flex items-center gap-3">
              <!-- Flag Circle in dropdown: 36px (w-9 h-9) agar detail bendera terlihat besar & jelas -->
              <span class="w-9 h-9 rounded-full overflow-hidden shrink-0 border border-white/25 shadow-sm flex items-center justify-center bg-slate-800">
                <!-- Indonesia -->
                <svg v-if="curr.code === 'IDR'" viewBox="0 0 512 512" class="w-full h-full">
                  <circle cx="256" cy="256" r="256" fill="#f8fafc" />
                  <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#e11d48" />
                </svg>

                <!-- Singapore Flag (Bulan Sabit Besar + 5 Bintang Putih Jelas) -->
                <svg v-else-if="curr.code === 'SGD'" viewBox="0 0 512 512" class="w-full h-full">
                  <circle cx="256" cy="256" r="256" fill="#ffffff" />
                  <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#ef4444" />
                  <path d="M90 30 A 95 95 0 1 0 90 226 A 76 76 0 1 1 90 30 Z" fill="#ffffff" />
                  <polygon points="200,48 204,60 216,60 207,67 210,79 200,72 190,79 193,67 184,60 196,60" fill="#ffffff" />
                  <polygon points="245,82 249,94 261,94 252,101 255,113 245,106 235,113 238,101 229,94 241,94" fill="#ffffff" />
                  <polygon points="230,138 234,150 246,150 237,157 240,169 230,162 220,169 223,157 214,150 226,150" fill="#ffffff" />
                  <polygon points="170,138 174,150 186,150 177,157 180,169 170,162 160,169 163,157 154,150 166,150" fill="#ffffff" />
                  <polygon points="155,82 159,94 171,94 162,101 165,113 155,106 145,113 148,101 139,94 151,94" fill="#ffffff" />
                </svg>

                <!-- Malaysia Flag -->
                <svg v-else-if="curr.code === 'MYR'" viewBox="0 0 512 512" class="w-full h-full">
                  <circle cx="256" cy="256" r="256" fill="#ffffff" />
                  <path d="M0 36h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0z" fill="#dc2626" />
                  <path d="M0 256C0 114.6 114.6 0 256 0v256H0z" fill="#1e3a8a" />
                  <path d="M75 55 A 72 72 0 1 0 75 201 A 60 60 0 1 1 75 55 Z" fill="#facc15" />
                  <polygon points="180,95 186,115 206,108 195,125 214,133 194,141 205,158 185,151 179,171 173,151 153,158 164,141 144,133 163,125 152,108 172,115" fill="#facc15" />
                </svg>
              </span>

              <div class="text-left">
                <div class="flex items-center gap-1.5">
                  <span class="text-sm font-bold text-white leading-tight">{{ curr.code }}</span>
                  <span class="text-xs text-slate-400 font-semibold">({{ curr.symbol }})</span>
                </div>
                <span class="block text-xs text-slate-400 font-normal leading-tight mt-0.5">{{ curr.label }}</span>
              </div>
            </div>

            <!-- Active Checkmark -->
            <svg
              v-if="currentCurrency === curr.code"
              class="w-5 h-5 text-blue-400 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>

        <div class="px-3 py-2 border-t border-slate-800 text-[11px] text-slate-400 leading-relaxed">
          💡 Estimasi kurs untuk turis. Transaksi resmi mengacu pada Rupiah (IDR).
        </div>
      </div>
    </transition>
  </div>
</template>
