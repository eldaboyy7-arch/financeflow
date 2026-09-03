import api from './axios'
import type { Transaction } from '@/types/transaction'

export interface DashboardSummary {
  total_balance: number
  income_this_month: number
  expense_this_month: number
  net_cash_flow: number
}

export interface ChartData {
  labels: string[]
  income: number[]
  expense: number[]
  range_label?: string
}

export interface ExpenseBreakdownItem {
  category: string
  icon: string
  color: string
  total: number
}

export const dashboardApi = {
  index: () =>
    api.get<{ summary: DashboardSummary; recent_transactions: { data: Transaction[] } }>('/dashboard'),

  incomeExpenseChart: (period: 'daily' | 'monthly' = 'monthly') =>
    api.get<ChartData>('/dashboard/income-expense', { params: { period } }),

  expenseBreakdown: () =>
    api.get<ExpenseBreakdownItem[]>('/dashboard/expense-breakdown'),
}
