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
      class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-100 hover:text-white border border-slate-700/70 hover:border-slate-600 transition-all shadow-xs active:scale-95 text-xs font-semibold focus:outline-none"
      :aria-expanded="isOpen"
      aria-label="Pilih Mata Uang / Currency"
    >
      <!-- Circular Flag Avatar -->
      <span class="w-5 h-5 rounded-full overflow-hidden shrink-0 shadow-xs border border-white/20 flex items-center justify-center">
        <!-- Indonesia Flag -->
        <svg v-if="currentCurrency === 'IDR'" viewBox="0 0 512 512" class="w-full h-full">
          <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
          <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#e11d48" />
        </svg>

        <!-- Singapore Flag (Bulan Sabit Tebal + 5 Bintang Putih Jelas) -->
        <svg v-else-if="currentCurrency === 'SGD'" viewBox="0 0 512 512" class="w-full h-full">
          <circle cx="256" cy="256" r="256" fill="#ffffff" />
          <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#ef4444" />
          <!-- Bulan Sabit Putih Tebal -->
          <path d="M95 40 A 88 88 0 1 0 95 216 A 72 72 0 1 1 95 40 Z" fill="#ffffff" />
          <!-- 5 Bintang Putih Singapura -->
          <polygon points="195,65 198,75 208,75 200,81 203,91 195,85 187,91 190,81 182,75 192,75" fill="#ffffff" />
          <polygon points="235,95 238,105 248,105 240,111 243,121 235,115 227,121 230,111 222,105 232,105" fill="#ffffff" />
          <polygon points="220,145 223,155 233,155 225,161 228,171 220,165 212,171 215,161 207,155 217,155" fill="#ffffff" />
          <polygon points="170,145 173,155 183,155 175,161 178,171 170,165 162,171 165,161 157,155 167,155" fill="#ffffff" />
          <polygon points="155,95 158,105 168,105 160,111 163,121 155,115 147,121 150,111 142,105 152,105" fill="#ffffff" />
        </svg>

        <!-- Malaysia Flag (Stripes Merah Putih + Canton Biru + Bulan Bintang Kuning) -->
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
        class="absolute right-0 mt-2 w-52 sm:w-56 rounded-2xl bg-slate-900/95 backdrop-blur-md border border-slate-700/80 shadow-2xl p-1.5 z-50 text-slate-200"
        role="menu"
      >
        <div class="px-3 py-1.5 border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-400">
          Pilih Mata Uang / Currency
        </div>

        <div class="py-1 space-y-0.5">
          <button
            v-for="curr in CURRENCIES"
            :key="curr.code"
            @click="handleSelect(curr.code)"
            type="button"
            class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-colors"
            :class="currentCurrency === curr.code
              ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-bold'
              : 'hover:bg-slate-800/80 text-slate-300 hover:text-white'"
            role="menuitem"
          >
            <div class="flex items-center gap-2.5">
              <!-- Flag Circle in dropdown -->
              <span class="w-5 h-5 rounded-full overflow-hidden shrink-0 border border-white/20 flex items-center justify-center">
                <!-- Indonesia -->
                <svg v-if="curr.code === 'IDR'" viewBox="0 0 512 512" class="w-full h-full">
                  <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
                  <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#e11d48" />
                </svg>
                <!-- Singapore Flag (Bulan Sabit Tebal + 5 Bintang Putih Jelas) -->
                <svg v-else-if="curr.code === 'SGD'" viewBox="0 0 512 512" class="w-full h-full">
                  <circle cx="256" cy="256" r="256" fill="#ffffff" />
                  <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#ef4444" />
                  <path d="M95 40 A 88 88 0 1 0 95 216 A 72 72 0 1 1 95 40 Z" fill="#ffffff" />
                  <polygon points="195,65 198,75 208,75 200,81 203,91 195,85 187,91 190,81 182,75 192,75" fill="#ffffff" />
                  <polygon points="235,95 238,105 248,105 240,111 243,121 235,115 227,121 230,111 222,105 232,105" fill="#ffffff" />
                  <polygon points="220,145 223,155 233,155 225,161 228,171 220,165 212,171 215,161 207,155 217,155" fill="#ffffff" />
                  <polygon points="170,145 173,155 183,155 175,161 178,171 170,165 162,171 165,161 157,155 167,155" fill="#ffffff" />
                  <polygon points="155,95 158,105 168,105 160,111 163,121 155,115 147,121 150,111 142,105 152,105" fill="#ffffff" />
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
                <span class="block leading-tight">{{ curr.code }}</span>
                <span class="block text-[10px] text-slate-400 font-normal leading-tight">{{ curr.label }}</span>
              </div>
            </div>

            <!-- Active Checkmark -->
            <svg
              v-if="currentCurrency === curr.code"
              class="w-4 h-4 text-blue-400 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>

        <div class="px-2.5 py-1.5 border-t border-slate-800 text-[10px] text-slate-400 leading-tight">
          💡 Estimasi kurs untuk turis. Transaksi resmi mengacu pada IDR.
        </div>
      </div>
    </transition>
  </div>
</template>
