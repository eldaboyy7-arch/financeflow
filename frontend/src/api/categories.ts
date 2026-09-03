import api from './axios'
import type { Category, CategoryPayload } from '@/types/category'

export const categoriesApi = {
  list: (params?: { type?: 'income' | 'expense'; mode?: string } | 'income' | 'expense') => {
    const queryParams = typeof params === 'string' ? { type: params } : params
    return api.get<Category[]>('/categories', { params: queryParams })
  },

  create: (payload: CategoryPayload) =>
    api.post<Category>('/categories', payload),

  update: (id: number, payload: Partial<CategoryPayload>) =>
    api.put<Category>(`/categories/${id}`, payload),

  delete: (id: number) =>
    api.delete<{ message: string }>(`/categories/${id}`),
}
