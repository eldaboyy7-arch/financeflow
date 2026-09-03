import { defineStore } from 'pinia'
import { ref } from 'vue'
import { dashboardApi, type DashboardSummary, type ChartData, type ExpenseBreakdownItem } from '@/api/dashboard'
import type { Transaction } from '@/types/transaction'

export const useDashboardStore = defineStore('dashboard', () => {
  const summary = ref<DashboardSummary | null>(null)
  const recentTransactions = ref<Transaction[]>([])
  const chartData = ref<ChartData | null>(null)
  const breakdown = ref<ExpenseBreakdownItem[]>([])
  const chartPeriod = ref<'monthly' | 'daily'>('monthly')
  const loading = ref(false)
  const hasLoaded = ref(false)

  async function fetchDashboard(force = false) {
    if (!hasLoaded.value || force) {
      loading.value = true
    }
    try {
      const [dashRes, chartRes, breakdownRes] = await Promise.all([
        dashboardApi.index(),
        dashboardApi.incomeExpenseChart(chartPeriod.value),
        dashboardApi.expenseBreakdown(),
      ])

      summary.value = dashRes.data.summary
      recentTransactions.value = (dashRes.data.recent_transactions as any).data ?? dashRes.data.recent_transactions
      chartData.value = chartRes.data
      breakdown.value = breakdownRes.data
      hasLoaded.value = true
    } catch (e) {
      console.error('Failed to load dashboard:', e)
    } finally {
      loading.value = false
    }
  }

  async function switchPeriod(period: 'monthly' | 'daily') {
    chartPeriod.value = period
    const { data } = await dashboardApi.incomeExpenseChart(period)
    chartData.value = data
  }

  return {
    summary,
    recentTransactions,
    chartData,
    breakdown,
    chartPeriod,
    loading,
    hasLoaded,
    fetchDashboard,
    switchPeriod,
  }
})
