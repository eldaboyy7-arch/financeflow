<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useBudgetsStore } from '@/stores/budgets'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import { BanknotesIcon, ArrowRightIcon, PlusIcon, ExclamationCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const budgetsStore = useBudgetsStore()
const { formatCurrency } = useFormatCurrency()

onMounted(async () => {
  if (!budgetsStore.budgets.length) {
    await budgetsStore.fetchBudgets()
  }
})
</script>

<template>
  <div class="card p-4 sm:p-5 space-y-3.5">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
          <BanknotesIcon class="w-4 h-4" />
        </div>
        <div>
          <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-none">
            Anggaran Kamu
          </h2>
          <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
            Bulan ini
          </p>
        </div>
      </div>

      <RouterLink
        to="/anggaran"
        class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1"
      >
        <span>Kelola</span>
        <ArrowRightIcon class="w-3 h-3" />
      </RouterLink>
    </div>

    <!-- Loading State -->
    <div v-if="budgetsStore.loading && !budgetsStore.budgets.length" class="space-y-3">
      <div v-for="i in 2" :key="i" class="space-y-1.5 animate-pulse">
        <div class="flex justify-between">
          <div class="w-24 h-3 bg-slate-200 dark:bg-slate-700 rounded"></div>
          <div class="w-16 h-3 bg-slate-200 dark:bg-slate-700 rounded"></div>
        </div>
        <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full"></div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!budgetsStore.budgets.length" class="py-5 text-center space-y-2">
      <p class="text-xs text-slate-400 dark:text-slate-500">
        Belum ada batas anggaran yang diatur bulan ini.
      </p>
      <RouterLink
        to="/anggaran"
        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 rounded-lg hover:bg-primary-100 transition-colors"
      >
        <PlusIcon class="w-3.5 h-3.5" />
        <span>Atur Anggaran Sekarang</span>
      </RouterLink>
    </div>

    <!-- Budgets List (Top 3) -->
    <div v-else class="space-y-3.5">
      <div
        v-for="budget in budgetsStore.budgets.slice(0, 3)"
        :key="budget.id"
        class="space-y-1.5"
      >
        <!-- Title row -->
        <div class="flex items-center justify-between text-xs">
          <div class="flex items-center gap-1.5 font-semibold text-slate-800 dark:text-slate-200 truncate">
            <span>{{ budget.category.icon }}</span>
            <span class="truncate">{{ budget.category.name }}</span>
          </div>
          <div class="text-right shrink-0 tabular-nums">
            <span class="font-bold text-slate-900 dark:text-white">
              {{ formatCurrency(budget.spent) }}
            </span>
            <span class="text-slate-400 text-[11px]"> / {{ formatCurrency(budget.amount) }}</span>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
          <div
            class="h-full rounded-full transition-all duration-500"
            :class="[
              budget.status === 'exceeded'
                ? 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.4)]'
                : budget.status === 'warning'
                ? 'bg-amber-500'
                : 'bg-emerald-500',
            ]"
            :style="{ width: `${Math.min(100, budget.percentage)}%` }"
          ></div>
        </div>

        <!-- Status Caption -->
        <div class="flex items-center justify-between text-[11px]">
          <span
            v-if="budget.status === 'exceeded'"
            class="font-semibold text-rose-600 dark:text-rose-400 flex items-center gap-1"
          >
            <ExclamationCircleIcon class="w-3.5 h-3.5 shrink-0 text-rose-500" />
            <span>Melebihi limit (+{{ formatCurrency(budget.overspent) }})</span>
          </span>
          <span
            v-else-if="budget.status === 'warning'"
            class="font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1"
          >
            <ExclamationTriangleIcon class="w-3.5 h-3.5 shrink-0 text-amber-500" />
            <span>Mendekati limit (Sisa {{ formatCurrency(budget.remaining) }})</span>
          </span>
          <span v-else class="text-slate-400 dark:text-slate-500">
            Sisa {{ formatCurrency(budget.remaining) }}
          </span>

          <span
            class="font-bold tabular-nums"
            :class="[
              budget.status === 'exceeded'
                ? 'text-rose-600 dark:text-rose-400'
                : budget.status === 'warning'
                ? 'text-amber-600 dark:text-amber-400'
                : 'text-slate-500 dark:text-slate-400',
            ]"
          >
            {{ budget.percentage }}%
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
