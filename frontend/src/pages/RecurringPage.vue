<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRecurringStore } from '@/stores/recurring'
import { useAccountsStore } from '@/stores/accounts'
import { useCategoriesStore } from '@/stores/categories'
import { useUiStore } from '@/stores/ui'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import type { RecurringItem } from '@/types/recurring'
import RecurringModal from '@/components/RecurringModal.vue'
import MoneySpinner from '@/components/MoneySpinner.vue'
import {
  PlusIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  PencilSquareIcon,
  TrashIcon,
  CalendarDaysIcon,
  ClockIcon,
  ExclamationCircleIcon,
  BanknotesIcon,
  CreditCardIcon,
  BellAlertIcon,
} from '@heroicons/vue/24/outline'

const recurringStore = useRecurringStore()
const accountsStore = useAccountsStore()
const categoriesStore = useCategoriesStore()
const uiStore = useUiStore()
const { formatCurrency } = useFormatCurrency()

const showModal = ref(false)
const selectedRecurring = ref<RecurringItem | null>(null)
const payingId = ref<number | null>(null)

function openCreate() {
  selectedRecurring.value = null
  showModal.value = true
}

function openEdit(item: RecurringItem) {
  selectedRecurring.value = item
  showModal.value = true
}

async function handleDelete(item: RecurringItem) {
  if (confirm(`Hapus langganan / tagihan "${item.name}"?`)) {
    await recurringStore.deleteItem(item.id)
    uiStore.showToast('Tagihan rutin berhasil dihapus.', 'info')
  }
}

async function handlePay(item: RecurringItem) {
  if (confirm(`Bayar tagihan "${item.name}" sebesar ${formatCurrency(item.amount)} sekarang?`)) {
    payingId.value = item.id
    try {
      await recurringStore.payBill(item.id)
      uiStore.showToast(`Tagihan "${item.name}" berhasil dibayar!`)
    } finally {
      payingId.value = null
    }
  }
}

onMounted(() => {
  recurringStore.fetchAll()
  accountsStore.fetchAccounts()
  categoriesStore.fetchCategories()
})
</script>

<template>
  <div class="space-y-5 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Tagihan & Langganan Rutin</h1>
        <p class="text-xs sm:text-sm text-slate-400 dark:text-slate-500 mt-0.5">
          Pantau tagihan bulanan dan jadwal jatuh tempo otomatis
        </p>
      </div>

      <button
        @click="openCreate"
        class="btn-primary flex items-center justify-center gap-2 self-start sm:self-auto py-2.5 px-4 rounded-xl shadow-md"
      >
        <PlusIcon class="w-4 h-4 stroke-2" />
        <span class="text-xs sm:text-sm font-semibold">Tambah Tagihan</span>
      </button>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Total Komitmen Bulanan -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-rose-200 dark:hover:border-rose-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-100 dark:border-rose-800/40 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
              <CreditCardIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Komitmen Bulanan</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-1.5 py-0.5 rounded-full border border-rose-100 dark:border-rose-800/30">
            Rutin
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ formatCurrency(recurringStore.totalMonthlyCommitment) }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>Estimasi biaya tetap</span>
          <span class="text-rose-500 font-medium">Beban bulanan</span>
        </div>
      </div>

      <!-- Tagihan Jatuh Tempo -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-amber-200 dark:hover:border-amber-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-100 dark:border-amber-800/40 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
              <ClockIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Jatuh Tempo (30 Hari)</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full border border-amber-100 dark:border-amber-800/30 shrink-0 whitespace-nowrap">
            Mendatang
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ recurringStore.upcomingBills.length }} <span class="text-xs font-bold text-slate-400">Tagihan</span>
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>Perlu disiapkan</span>
          <span class="text-amber-600 dark:text-amber-400 font-medium">Prioritas</span>
        </div>
      </div>

      <!-- Total Langganan Aktif -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-800/40 flex items-center justify-center text-[#0066FF] dark:text-blue-400 shrink-0">
              <ArrowPathIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Langganan Aktif</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-[#0066FF] dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded-full border border-blue-100 dark:border-blue-800/30">
            Aktif
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ recurringStore.activeItems.length }} <span class="text-xs font-bold text-slate-400">Layanan</span>
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>Rutin terdaftar</span>
          <span class="text-[#0066FF] dark:text-blue-400 font-medium">Otomatis</span>
        </div>
      </div>

      <!-- Auto Create / Status -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-800/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
              <BellAlertIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Status Pengingat</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded-full border border-emerald-100 dark:border-emerald-800/30">
            Online
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight truncate">
            Aktif
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>Alert in-app menyala</span>
          <span class="text-emerald-600 dark:text-emerald-400 font-medium">Real-time</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="recurringStore.loading" class="card p-10 flex items-center justify-center">
      <MoneySpinner size="lg" text="Memuat jadwal tagihan & langganan..." subtext="Memeriksa tanggal jatuh tempo terdekat" />
    </div>

    <!-- Section 1: Upcoming Bills (Jatuh Tempo Terdekat) -->
    <div v-else-if="recurringStore.upcomingBills.length" class="space-y-3">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
        <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
          Jadwal Pembayaran Mendatang (Upcoming Bills)
        </h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <div
          v-for="bill in recurringStore.upcomingBills"
          :key="bill.id"
          class="card p-4 space-y-3 hover:shadow-md transition-all"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-2.5">
              <span class="text-xl">{{ bill.category?.icon || '📦' }}</span>
              <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm leading-tight">
                  {{ bill.name }}
                </h3>
                <p class="text-[11px] text-slate-400">
                  {{ bill.account?.name }}
                </p>
              </div>
            </div>
            <span
              class="px-2 py-0.5 rounded-full text-[10px] font-bold"
              :class="bill.is_overdue ? 'bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400' : bill.is_due_today ? 'bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
            >
              {{ bill.is_overdue ? 'Lewat Jatuh Tempo' : bill.is_due_today ? 'Jatuh Tempo Hari Ini' : `${bill.days_until_due} hari lagi` }}
            </span>
          </div>

          <div class="flex items-center justify-between pt-1">
            <span class="text-base font-extrabold text-slate-900 dark:text-white tabular-nums">
              {{ formatCurrency(bill.amount) }}
            </span>

            <button
              @click="handlePay(bill)"
              :disabled="payingId === bill.id"
              class="py-1.5 px-3 rounded-xl bg-primary-600 text-white hover:bg-primary-700 text-xs font-bold transition-all shadow-sm flex items-center gap-1"
            >
              <CheckCircleIcon class="w-4 h-4" />
              <span>{{ payingId === bill.id ? 'Memproses...' : 'Bayar Tagihan' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 2: All Subscriptions List -->
    <div class="space-y-3">
      <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
        Daftar Semua Langganan & Tagihan Rutin
      </h2>

      <!-- Skeleton Loading -->
      <div v-if="recurringStore.loading" class="card p-5 animate-pulse space-y-3">
        <div v-for="i in 3" :key="i" class="h-10 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
      </div>

      <!-- Table / Cards List -->
      <div v-else-if="recurringStore.items.length" class="card overflow-hidden">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
          <div
            v-for="item in recurringStore.items"
            :key="item.id"
            class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors"
          >
            <div class="flex items-center gap-3 min-w-0">
              <div
                class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shrink-0 shadow-inner"
                :style="{ backgroundColor: (item.category?.color || '#6366F1') + '1A' }"
              >
                {{ item.category?.icon || '📦' }}
              </div>
              <div class="min-w-0">
                <div class="flex items-center gap-2">
                  <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">
                    {{ item.name }}
                  </h3>
                  <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 capitalize">
                    {{ item.frequency }}
                  </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1.5">
                  <span>{{ item.account?.name }}</span>
                  <span>•</span>
                  <span>Jatuh tempo: {{ item.next_due_date }}</span>
                </p>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
              <span class="text-base font-extrabold text-slate-900 dark:text-white tabular-nums">
                {{ formatCurrency(item.amount) }}
              </span>

              <div class="flex items-center gap-1">
                <button
                  @click="handlePay(item)"
                  :disabled="payingId === item.id"
                  class="py-1.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-600 dark:hover:text-primary-400 text-xs font-bold transition-all flex items-center gap-1"
                  title="Bayar 1-Klik"
                >
                  <CheckCircleIcon class="w-3.5 h-3.5" />
                  <span>Bayar</span>
                </button>
                <button
                  @click="openEdit(item)"
                  class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"
                  title="Edit Tagihan"
                >
                  <PencilSquareIcon class="w-4 h-4" />
                </button>
                <button
                  @click="handleDelete(item)"
                  class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"
                  title="Hapus Tagihan"
                >
                  <TrashIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="card p-12 text-center text-slate-400 dark:text-slate-500 flex flex-col items-center gap-3"
      >
        <div class="w-16 h-16 rounded-3xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center">
          <ArrowPathIcon class="w-8 h-8 stroke-[1.5]" />
        </div>
        <div class="space-y-1">
          <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base">Belum Ada Tagihan Rutin</h3>
          <p class="text-xs max-w-sm mx-auto">
            Catat langganan Netflix, WiFi, tagihan listrik, atau sewa kost agar tidak pernah terlewat!
          </p>
        </div>
        <button
          @click="openCreate"
          class="btn-primary py-2 px-4 text-xs font-semibold rounded-xl shadow-md mt-2"
        >
          + Tambah Tagihan Pertama
        </button>
      </div>
    </div>

    <!-- Modal -->
    <RecurringModal v-model="showModal" :recurring="selectedRecurring" />
  </div>
</template>
