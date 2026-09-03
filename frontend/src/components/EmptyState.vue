<script setup lang="ts">
import { computed } from 'vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

const props = withDefaults(
  defineProps<{
    type?: 'transactions' | 'budgets' | 'goals' | 'recurring' | 'reports' | 'accounts' | 'custom'
    title?: string
    description?: string
    actionText?: string
    image?: string
  }>(),
  {
    type: 'transactions',
    title: '',
    description: '',
    actionText: '',
  }
)

const emit = defineEmits<{
  (e: 'action'): void
}>()

const imageSrc = computed(() => {
  if (props.image) return props.image
  switch (props.type) {
    case 'transactions':
      return '/assets/3d/trans_receive.png'
    case 'budgets':
      return '/assets/3d/budget_jar_boy.png'
    case 'goals':
      return '/assets/3d/goal_vacation_boy.png'
    case 'recurring':
      return '/assets/3d/alert_bell_boy.png'
    case 'accounts':
      return '/assets/3d/prop_wallet.png'
    case 'reports':
      return '/assets/3d/analytics_insight_boy.png'
    default:
      return '/assets/3d/trans_transfer.png'
  }
})

const defaultTitle = computed(() => {
  if (props.title) return props.title
  switch (props.type) {
    case 'transactions':
      return 'Belum ada transaksi nih'
    case 'budgets':
      return 'Belum ada anggaran bulanan'
    case 'goals':
      return 'Belum ada target impian'
    case 'recurring':
      return 'Belum ada tagihan rutin'
    case 'accounts':
      return 'Belum ada rekening aktif'
    case 'reports':
      return 'Data laporan belum tersedia'
    default:
      return 'Belum ada data'
  }
})

const defaultDescription = computed(() => {
  if (props.description) return props.description
  switch (props.type) {
    case 'transactions':
      return 'Yuk catat transaksi pertamamu untuk mulai melacak arus keuangan secara rapi.'
    case 'budgets':
      return 'Buat batasan anggaran untuk pos pengeluaranmu agar keuangan tetap terkendali dan hemat.'
    case 'goals':
      return 'Tentukan barang idaman, dana darurat, atau liburan impianmu dan mulai menabung hari ini.'
    case 'recurring':
      return 'Catat langganan dan tagihan bulanan agar tidak pernah lupa membayar tepat waktu.'
    case 'accounts':
      return 'Tambahkan rekening bank, dompet digital, atau uang tunai untuk mengelola saldo.'
    case 'reports':
      return 'Lakukan beberapa transaksi untuk melihat analisis grafik dan tren keuangan.'
    default:
      return 'Silakan tambahkan data baru untuk memulai.'
  }
})
</script>

<template>
  <div class="flex flex-col items-center justify-center p-8 sm:p-12 text-center max-w-md mx-auto select-none">
    <!-- 3D Contextual Character Illustration -->
    <div class="relative w-40 sm:w-48 h-32 sm:h-40 mb-5 flex items-center justify-center">
      <!-- Ambient Glow -->
      <div class="absolute inset-0 m-auto w-28 sm:w-32 h-28 sm:h-32 bg-blue-100/60 dark:bg-blue-950/40 rounded-full blur-xl pointer-events-none"></div>
      
      <img
        :src="imageSrc"
        :alt="defaultTitle"
        class="relative z-10 max-h-full max-w-full object-contain drop-shadow-md animate-float-subtle rounded-2xl"
      />
    </div>

    <!-- Title & Description -->
    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">
      {{ defaultTitle }}
    </h3>
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
      {{ defaultDescription }}
    </p>

    <!-- Action Button (Optional) -->
    <div v-if="actionText" class="mt-5">
      <button
        type="button"
        @click="emit('action')"
        class="inline-flex items-center gap-2 py-2.5 px-5 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-[#0066FF]/20 transition-all"
      >
        <PlusIcon class="w-4 h-4 stroke-[2.5]" />
        <span>{{ actionText }}</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
@keyframes floatSubtle {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.animate-float-subtle {
  animation: floatSubtle 3.2s ease-in-out infinite;
}
</style>
