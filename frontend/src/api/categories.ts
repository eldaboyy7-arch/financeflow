import api from './axios'
import type { Category, CategoryPayload } from '@/types/category'

export const categoriesApi = {
  list: (type?: 'income' | 'expense') =>
    api.get<Category[]>('/categories', { params: type ? { type } : {} }),

  create: (payload: CategoryPayload) =>
    api.post<Category>('/categories', payload),

  update: (id: number, payload: Partial<CategoryPayload>) =>
    api.put<Category>(`/categories/${id}`, payload),

  delete: (id: number) =>
    api.delete<{ message: string }>(`/categories/${id}`),
}
