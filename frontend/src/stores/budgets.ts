import { defineStore } from 'pinia'
import { ref } from 'vue'
import { budgetsApi } from '@/api/budgets'
import type { Budget, BudgetSummary, BudgetPayload } from '@/types/budget'

export const useBudgetsStore = defineStore('budgets', () => {
  const budgets = ref<Budget[]>([])
  const summary = ref<BudgetSummary | null>(null)
  const loading = ref(false)
  const currentMonth = ref(new Date().getMonth() + 1)
  const currentYear = ref(new Date().getFullYear())
  const hasLoaded = ref(false)

  async function fetchBudgets(month?: number, year?: number, force = false) {
    const m = month ?? currentMonth.value
    const y = year ?? currentYear.value
    const isPeriodChanged = m !== currentMonth.value || y !== currentYear.value

    currentMonth.value = m
    currentYear.value = y

    if (!hasLoaded.value || isPeriodChanged || force) {
      loading.value = true
    }

    try {
      const { data } = await budgetsApi.list({ month: m, year: y })
      budgets.value = data.data
      summary.value = data.summary
      hasLoaded.value = true
    } catch (e) {
      console.error('Failed to fetch budgets:', e)
    } finally {
      loading.value = false
    }
  }

  async function createBudget(payload: BudgetPayload) {
    const res = await budgetsApi.create(payload)
    await fetchBudgets(currentMonth.value, currentYear.value, true)
    return res.data
  }

  async function updateBudget(id: number, amount: number) {
    const res = await budgetsApi.update(id, { amount })
    await fetchBudgets(currentMonth.value, currentYear.value, true)
    return res.data
  }

  async function deleteBudget(id: number) {
    const res = await budgetsApi.delete(id)
    await fetchBudgets(currentMonth.value, currentYear.value, true)
    return res.data
  }

  return {
    budgets,
    summary,
    loading,
    currentMonth,
    currentYear,
    hasLoaded,
    fetchBudgets,
    createBudget,
    updateBudget,
    deleteBudget,
  }
})
