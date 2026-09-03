import api from './axios'
import type { Budget, BudgetSummary, BudgetPayload, BudgetImpact } from '@/types/budget'

export const budgetsApi = {
  list: (params?: { month?: number; year?: number }) =>
    api.get<{ data: Budget[]; summary: BudgetSummary }>('/budgets', { params }),

  summary: (params?: { month?: number; year?: number }) =>
    api.get<{ summary: BudgetSummary }>('/budgets/summary', { params }),

  impact: (payload: { category_id: number; amount: number; month?: number; year?: number }) =>
    api.post<{ impact: BudgetImpact | null }>('/budgets/impact', payload),

  create: (payload: BudgetPayload) =>
    api.post<{ message: string; data: Budget }>('/budgets', payload),

  update: (id: number, payload: { amount: number }) =>
    api.put<{ message: string; data: Budget }>(`/budgets/${id}`, payload),

  delete: (id: number) =>
    api.delete<{ message: string }>(`/budgets/${id}`),
}
