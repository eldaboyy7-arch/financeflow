import api from './axios'
import type { RecurringItem, CreateRecurringPayload, UpdateRecurringPayload } from '@/types/recurring'

export const recurringApi = {
  get: () => api.get<{ data: RecurringItem[] }>('/recurring'),
  upcoming: (days: number = 30) => api.get<{ data: RecurringItem[] }>(`/recurring/upcoming?days=${days}`),
  create: (payload: CreateRecurringPayload) => api.post<{ data: RecurringItem }>('/recurring', payload),
  update: (id: number, payload: UpdateRecurringPayload) => api.put<{ data: RecurringItem }>(`/recurring/${id}`, payload),
  delete: (id: number) => api.delete(`/recurring/${id}`),
  pay: (id: number) => api.post<{ message: string; transaction: any; recurring: RecurringItem }>(`/recurring/${id}/pay`),
}
