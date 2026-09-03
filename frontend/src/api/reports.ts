import api from './axios'

export interface MonthlyReport {
  year: number
  month: number
  total_income: number
  total_expense: number
  net_cash_flow: number
  saving_rate: number
  income_breakdown: Array<{ category: string; icon: string; color: string; total: number }>
  expense_breakdown: Array<{ category: string; icon: string; color: string; total: number }>
  account_balances: Array<{ name: string; type: string; balance: number; icon: string; color: string }>
  comparison: {
    prev_income: number
    prev_expense: number
    income_change: number
    expense_change: number
  }
}

export const reportsApi = {
  monthly: (year: number, month: number) =>
    api.get<MonthlyReport>('/reports/monthly', { params: { year, month } }),
}
