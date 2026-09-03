<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { useBudgetsStore } from '@/stores/budgets'
import { useUiStore } from '@/stores/ui'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import type { Budget } from '@/types/budget'
import type { SelectOption } from '@/components/SelectInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import BudgetModal from '@/components/BudgetModal.vue'
import MoneySpinner from '@/components/MoneySpinner.vue'
import EmptyState from '@/components/EmptyState.vue'
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  BanknotesIcon,
  ScaleIcon,
  ExclamationTriangleIcon,
  ExclamationCircleIcon,
  CheckCircleIcon,
  InboxIcon,
  CalendarDaysIcon,
} from '@heroicons/vue/24/outline'

const budgetsStore = useBudgetsStore()
const uiStore = useUiStore()
const { formatCurrency, formatAmount } = useFormatCurrency()

const selectedMonth = ref<number>(new Date().getMonth() + 1)
const selectedYear = ref<number>(new Date().getFullYear())

const showModal = ref(false)
const selectedBudget = ref<Budget | null>(null)

const monthOptions: SelectOption[] = [
  { value: 1, label: 'Januari' },
  { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' },
  { value: 4, label: 'April' },
  { value: 5, label: 'Mei' },
  { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' },
  { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' },
  { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },
  { value: 12, label: 'Desember' },
]

const currentYear = new Date().getFullYear()
const yearOptions: SelectOption[] = [
  { value: currentYear - 1, label: String(currentYear - 1) },
  { value: currentYear,     label: String(currentYear) },
  { value: currentYear + 1, label: String(currentYear + 1) },
]

const monthName = computed(() => {
  const m = monthOptions.find((o) => o.value === selectedMonth.value)
  return m ? m.label : 'Bulan'
})

async function loadBudgets() {
  await budgetsStore.fetchBudgets(selectedMonth.value, selectedYear.value)
}

watch([selectedMonth, selectedYear], () => {
  loadBudgets()
})

onMounted(() => {
  loadBudgets()
})

function openCreateModal() {
  selectedBudget.value = null
  showModal.value = true
}

function openEditModal(budget: Budget) {
  selectedBudget.value = budget
  showModal.value = true
}

async function handleDelete(budget: Budget) {
  if (confirm(`Yakin ingin menghapus budget untuk kategori "${budget.category.name}"?`)) {
    await budgetsStore.deleteBudget(budget.id)
    uiStore.showToast('Anggaran berhasil dihapus.', 'info')
  }
}
</script>

<template>
  <div class="space-y-5 sm:space-y-6">
    <!-- Header & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Smart Budget</h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
          Kelola dan pantau batas pengeluaran per kategori secara cerdas
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <!-- Month & Year Selects -->
        <div class="w-32 sm:w-36">
          <SelectInput v-model="selectedMonth" :options="monthOptions" />
        </div>
        <div class="w-24 sm:w-28">
          <SelectInput v-model="selectedYear" :options="yearOptions" />
        </div>

        <button
          @click="openCreateModal"
          class="flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-md transition-all active:scale-95"
        >
          <PlusIcon class="w-4 h-4" />
          <span>Buat Budget</span>
        </button>
      </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- 1. Total Anggaran -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-800/40 flex items-center justify-center text-[#0066FF] dark:text-blue-400 shrink-0">
              <BanknotesIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Total Anggaran</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-[#0066FF] dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded-full border border-blue-100 dark:border-blue-800/30">
            Plafon
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ formatCurrency(budgetsStore.summary?.total_budgeted ?? 0) }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>Periode {{ monthName }} {{ selectedYear }}</span>
          <span class="text-[#0066FF] dark:text-blue-400 font-medium">Batas</span>
        </div>
      </div>

      <!-- 2. Total Terpakai -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-rose-200 dark:hover:border-rose-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-100 dark:border-rose-800/40 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
              <ScaleIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Total Terpakai</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-1.5 py-0.5 rounded-full border border-rose-100 dark:border-rose-800/30">
            {{ budgetsStore.summary?.overall_percentage ?? 0 }}%
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ formatCurrency(budgetsStore.summary?.total_spent ?? 0) }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>{{ budgetsStore.summary?.overall_percentage ?? 0 }}% dari limit</span>
          <span class="text-rose-500 font-medium">Realisasi</span>
        </div>
      </div>

      <!-- 3. Sisa Anggaran -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-800/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
              <CheckCircleIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Sisa Anggaran</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded-full border border-emerald-100 dark:border-emerald-800/30">
            Tersedia
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight tabular-nums truncate">
            {{ formatCurrency(budgetsStore.summary?.total_remaining ?? 0) }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>Sisa yang boleh dibelanjakan</span>
          <span class="text-emerald-600 dark:text-emerald-400 font-medium">Aman</span>
        </div>
      </div>

      <!-- 4. Status Anggaran -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center text-slate-600 dark:text-slate-300 shrink-0">
              <CalendarDaysIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Status Anggaran</span>
          </div>
          <span
            class="text-[9px] sm:text-[10px] font-semibold px-1.5 py-0.5 rounded-full border"
            :class="(budgetsStore.summary?.exceeded_count ?? 0) > 0
              ? 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/30'
              : (budgetsStore.summary?.warning_count ?? 0) > 0
              ? 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/30'
              : 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/30'"
          >
            {{ (budgetsStore.summary?.exceeded_count ?? 0) > 0 ? 'Overlimit' : (budgetsStore.summary?.warning_count ?? 0) > 0 ? 'Waspada' : 'Aman' }}
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p
            class="text-base sm:text-xl font-black tracking-tight truncate"
            :class="(budgetsStore.summary?.exceeded_count ?? 0) > 0
              ? 'text-rose-600 dark:text-rose-400'
              : (budgetsStore.summary?.warning_count ?? 0) > 0
              ? 'text-amber-600 dark:text-amber-400'
              : 'text-emerald-600 dark:text-emerald-400'"
          >
            {{ (budgetsStore.summary?.exceeded_count ?? 0) > 0 ? `${budgetsStore.summary?.exceeded_count} Overlimit` : (budgetsStore.summary?.warning_count ?? 0) > 0 ? `${budgetsStore.summary?.warning_count} Waspada` : 'Semua Terkendali' }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>{{ budgetsStore.budgets.length }} kategori aktif</span>
          <span class="text-slate-500 dark:text-slate-400 font-medium">Tracking</span>
        </div>
      </div>
    </div>

    <!-- Overall Progress Card -->
    <div v-if="budgetsStore.budgets.length" class="card p-4 sm:p-5 space-y-2.5">
      <div class="flex items-center justify-between text-xs">
        <span class="font-bold text-slate-800 dark:text-slate-200">Progress Anggaran Keseluruhan</span>
        <span class="font-bold tabular-nums text-slate-900 dark:text-white">
          {{ formatCurrency(budgetsStore.summary?.total_spent ?? 0) }} / {{ formatCurrency(budgetsStore.summary?.total_budgeted ?? 0) }} ({{ budgetsStore.summary?.overall_percentage ?? 0 }}%)
        </span>
      </div>

      <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
        <div
          class="h-full rounded-full transition-all duration-500"
          :class="[
            (budgetsStore.summary?.overall_percentage ?? 0) >= 100
              ? 'bg-rose-500'
              : (budgetsStore.summary?.overall_percentage ?? 0) >= 75
              ? 'bg-amber-500'
              : 'bg-emerald-500',
          ]"
          :style="{ width: `${Math.min(100, budgetsStore.summary?.overall_percentage ?? 0)}%` }"
        ></div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="budgetsStore.loading" class="card p-10 flex items-center justify-center">
      <MoneySpinner size="lg" text="Menganalisis alokasi anggaran..." subtext="Memeriksa batas dan realisasi belanja" />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="!budgetsStore.budgets.length"
      class="card p-8 sm:p-12 text-center"
    >
      <EmptyState
        type="budgets"
        :title="`Belum Ada Anggaran untuk ${monthName} ${selectedYear}`"
        description="Tetapkan batas pengeluaran untuk kategori favoritmu seperti Makanan, Transportasi, atau Belanja agar keuangan tetap hemat."
        action-text="Buat Anggaran Sekarang"
        @action="openCreateModal"
      />
    </div>

    <!-- Budgets Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="b in budgetsStore.budgets"
        :key="b.id"
        class="card p-5 space-y-4 hover:shadow-md transition-all border relative group"
        :class="[
          b.status === 'exceeded'
            ? 'border-rose-200/80 dark:border-rose-900/50'
            : b.status === 'warning'
            ? 'border-amber-200/80 dark:border-amber-900/50'
            : 'border-slate-200/60 dark:border-slate-700/60',
        ]"
      >
        <!-- Card Header -->
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3">
            <span class="text-2xl shrink-0 p-2 bg-slate-100 dark:bg-slate-800 rounded-xl">
              {{ b.category.icon }}
            </span>
            <div>
              <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                {{ b.category.name }}
              </h3>
              <p class="text-[11px] text-slate-400">
                Budget: {{ formatCurrency(b.amount) }}
              </p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 opacity-90 sm:opacity-0 group-hover:opacity-100 transition-opacity">
            <button
              @click="openEditModal(b)"
              class="p-1.5 text-slate-400 hover:text-primary-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
              title="Edit Budget"
            >
              <PencilSquareIcon class="w-4 h-4" />
            </button>
            <button
              @click="handleDelete(b)"
              class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
              title="Hapus Budget"
            >
              <TrashIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="space-y-1.5">
          <div class="flex justify-between text-xs font-semibold tabular-nums">
            <span class="text-slate-600 dark:text-slate-300">
              Terpakai: {{ formatCurrency(b.spent) }}
            </span>
            <span
              :class="[
                b.status === 'exceeded'
                  ? 'text-rose-600 dark:text-rose-400 font-bold'
                  : b.status === 'warning'
                  ? 'text-amber-600 dark:text-amber-400 font-bold'
                  : 'text-slate-600 dark:text-slate-400',
              ]"
            >
              {{ b.percentage }}%
            </span>
          </div>

          <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-500"
              :class="[
                b.status === 'exceeded'
                  ? 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]'
                  : b.status === 'warning'
                  ? 'bg-amber-500'
                  : 'bg-emerald-500',
              ]"
              :style="{ width: `${Math.min(100, b.percentage)}%` }"
            ></div>
          </div>
        </div>

        <!-- Card Footer / Status Note -->
        <div class="pt-1 flex items-center justify-between text-xs border-t border-slate-100 dark:border-slate-800">
          <div v-if="b.status === 'exceeded'" class="text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1.5">
            <ExclamationCircleIcon class="w-4 h-4 shrink-0 text-rose-500" />
            <span>Melebihi budget (+{{ formatCurrency(b.overspent) }})</span>
          </div>
          <div v-else-if="b.status === 'warning'" class="text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1.5">
            <ExclamationTriangleIcon class="w-4 h-4 shrink-0 text-amber-500" />
            <span>Mendekati limit (Sisa {{ formatCurrency(b.remaining) }})</span>
          </div>
          <div v-else class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
            <CheckCircleIcon class="w-4 h-4 shrink-0 text-emerald-500" />
            <span>Sisa {{ formatCurrency(b.remaining) }}</span>
          </div>

          <span
            class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md"
            :class="[
              b.status === 'exceeded'
                ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400'
                : b.status === 'warning'
                ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400'
                : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400',
            ]"
          >
            {{ b.status === 'exceeded' ? 'Exceeded' : b.status === 'warning' ? 'Warning' : 'Normal' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Budget Modal -->
    <BudgetModal
      v-model="showModal"
      :budget="selectedBudget"
      :initial-month="selectedMonth"
      :initial-year="selectedYear"
      @saved="loadBudgets"
    />
  </div>
</template>
