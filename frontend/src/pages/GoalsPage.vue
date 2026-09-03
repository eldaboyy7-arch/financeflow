<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useGoalsStore } from '@/stores/goals'
import { useAccountsStore } from '@/stores/accounts'
import { useUiStore } from '@/stores/ui'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import type { Goal } from '@/types/goal'
import GoalModal from '@/components/GoalModal.vue'
import GoalDepositModal from '@/components/GoalDepositModal.vue'
import MoneySpinner from '@/components/MoneySpinner.vue'
import EmptyState from '@/components/EmptyState.vue'
import {
  PlusIcon,
  SparklesIcon,
  CheckCircleIcon,
  PencilSquareIcon,
  TrashIcon,
  BanknotesIcon,
  CalendarDaysIcon,
  ClockIcon,
  ArrowTrendingUpIcon,
  TrophyIcon,
} from '@heroicons/vue/24/outline'

const goalsStore = useGoalsStore()
const accountsStore = useAccountsStore()
const uiStore = useUiStore()
const { formatCurrency } = useFormatCurrency()

const showModal = ref(false)
const showDepositModal = ref(false)
const selectedGoal = ref<Goal | null>(null)

function openCreate() {
  selectedGoal.value = null
  showModal.value = true
}

function openEdit(goal: Goal) {
  selectedGoal.value = goal
  showModal.value = true
}

function openDeposit(goal: Goal) {
  selectedGoal.value = goal
  showDepositModal.value = true
}

async function handleDelete(goal: Goal) {
  if (confirm(`Yakin ingin menghapus target "${goal.name}"?`)) {
    await goalsStore.deleteGoal(goal.id)
    uiStore.showToast('Target impian berhasil dihapus.', 'info')
  }
}

onMounted(() => {
  goalsStore.fetchGoals()
  accountsStore.fetchAccounts()
})
</script>

<template>
  <div class="space-y-5 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Target Impian & Tabungan</h1>
        <p class="text-xs sm:text-sm text-slate-400 dark:text-slate-500 mt-0.5">
          Wujudkan wishlist dan rencana finansial masa depanmu
        </p>
      </div>

      <button
        @click="openCreate"
        class="btn-primary flex items-center justify-center gap-2 self-start sm:self-auto py-2.5 px-4 rounded-xl shadow-md"
      >
        <PlusIcon class="w-4 h-4 stroke-2" />
        <span class="text-xs sm:text-sm font-semibold">Buat Target Baru</span>
      </button>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Total Terkumpul -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-800/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
              <ArrowTrendingUpIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2.5]" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Total Terkumpul</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded-full border border-emerald-100 dark:border-emerald-800/30">
            Tersimpan
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ formatCurrency(goalsStore.totalSaved) }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>Dari semua target</span>
          <span class="text-emerald-600 dark:text-emerald-400 font-medium">Akumulasi</span>
        </div>
      </div>

      <!-- Total Target -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-800/40 flex items-center justify-center text-[#0066FF] dark:text-blue-400 shrink-0">
              <TrophyIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Total Target</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-[#0066FF] dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded-full border border-blue-100 dark:border-blue-800/30">
            Wishlist
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ formatCurrency(goalsStore.totalTarget) }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>Target keseluruhan</span>
          <span class="text-[#0066FF] dark:text-blue-400 font-medium">Goal impian</span>
        </div>
      </div>

      <!-- Progress Keseluruhan -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-800/40 flex items-center justify-center text-[#0066FF] dark:text-blue-400 shrink-0">
              <SparklesIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Pencapaian</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-[#0066FF] dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-1.5 py-0.5 rounded-full border border-blue-100 dark:border-blue-800/30">
            {{ goalsStore.overallPercentage }}%
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ goalsStore.overallPercentage }}%
          </p>
        </div>

        <div class="space-y-1 pt-2 border-t border-slate-100 dark:border-slate-700/60 mt-1">
          <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
            <div
              class="h-full bg-[#0066FF] rounded-full transition-all duration-500"
              :style="{ width: `${Math.min(goalsStore.overallPercentage, 100)}%` }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Target Aktif -->
      <div class="bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3.5 sm:p-5 shadow-sm hover:shadow-md hover:border-amber-200 dark:hover:border-amber-800/40 transition-all flex flex-col justify-between">
        <div class="flex items-center justify-between gap-1.5 mb-2.5">
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-100 dark:border-amber-800/40 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
              <ClockIcon class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-2" />
            </div>
            <span class="text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Target Aktif</span>
          </div>
          <span class="text-[9px] sm:text-[10px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-1.5 py-0.5 rounded-full border border-amber-100 dark:border-amber-800/30">
            {{ goalsStore.activeGoals.length }} Aktif
          </span>
        </div>

        <div class="my-0.5 sm:my-1">
          <p class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums truncate">
            {{ goalsStore.activeGoals.length }} <span class="text-xs font-bold text-slate-400">Impian</span>
          </p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700/60 text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1">
          <span>{{ goalsStore.completedGoals.length }} tercapai</span>
          <span class="text-emerald-600 dark:text-emerald-400 font-medium">Selesai</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="goalsStore.loading" class="card p-4 sm:p-8 flex items-center justify-center">
      <MoneySpinner size="md" text="Memuat target impian & tabungan..." subtext="Mempersiapkan progres finansialmu" />
    </div>

    <!-- Goals Grid -->
    <div v-else-if="goalsStore.goals.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
      <div
        v-for="g in goalsStore.goals"
        :key="g.id"
        class="card p-4 sm:p-5 space-y-4 hover:shadow-md transition-all flex flex-col justify-between"
      >
        <div class="space-y-3">
          <!-- Top Row -->
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div
                class="w-11 h-11 rounded-2xl flex items-center justify-center text-2xl shadow-inner"
                :style="{ backgroundColor: (g.color || '#6366F1') + '1A' }"
              >
                {{ g.icon || '🎯' }}
              </div>
              <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm sm:text-base leading-tight">
                  {{ g.name }}
                </h3>
                <span
                  v-if="g.is_completed"
                  class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5"
                >
                  <CheckCircleIcon class="w-3.5 h-3.5" />
                  Target Tercapai!
                </span>
                <span
                  v-else-if="g.days_remaining !== null"
                  class="inline-flex items-center gap-1 text-[10px] text-slate-400 mt-0.5"
                >
                  <ClockIcon class="w-3.5 h-3.5" />
                  {{ g.is_overdue ? 'Melewati batas target' : `${g.days_remaining} hari lagi` }}
                </span>
              </div>
            </div>

            <!-- Actions Menu -->
            <div class="flex items-center gap-1">
              <button
                @click="openEdit(g)"
                class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"
                title="Edit Target"
              >
                <PencilSquareIcon class="w-4 h-4" />
              </button>
              <button
                @click="handleDelete(g)"
                class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"
                title="Hapus Target"
              >
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Progress Bar & Amounts -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between text-xs">
              <span class="font-extrabold text-slate-900 dark:text-white tabular-nums">
                {{ formatCurrency(g.current_amount) }}
              </span>
              <span class="text-slate-400 tabular-nums">
                Target: {{ formatCurrency(g.target_amount) }}
              </span>
            </div>

            <div class="h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :style="{
                  width: `${Math.min(g.percentage, 100)}%`,
                  backgroundColor: g.is_completed ? '#10B981' : (g.color || '#6366F1'),
                }"
              ></div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-400">
              <span>{{ g.percentage }}% tercapai</span>
              <span v-if="!g.is_completed">Kurang {{ formatCurrency(g.remaining_amount) }}</span>
            </div>
          </div>
        </div>

        <!-- Deposit CTA Button -->
        <button
          @click="openDeposit(g)"
          class="w-full py-2 px-3 text-xs font-bold rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-600 dark:hover:text-primary-400 transition-colors flex items-center justify-center gap-1.5 shadow-2xs mt-2"
        >
          <BanknotesIcon class="w-4 h-4" />
          <span>+ Tabung Sekarang</span>
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="card p-8 sm:p-12 text-center"
    >
      <EmptyState
        type="goals"
        title="Belum Ada Target Impian"
        description="Mulai rencanakan tabungan untuk dana darurat, wishlist gadget, atau liburan impianmu!"
        action-text="Buat Target Impian"
        @action="openCreate"
      />
    </div>

    <!-- Modals -->
    <GoalModal v-model="showModal" :goal="selectedGoal" />
    <GoalDepositModal v-model="showDepositModal" :goal="selectedGoal" />
  </div>
</template>
