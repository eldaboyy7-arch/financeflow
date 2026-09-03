import api from './axios'
import type { Goal, CreateGoalPayload, ContributeGoalPayload } from '@/types/goal'

export const goalsApi = {
  get: () => api.get<{ data: Goal[] }>('/goals'),
  create: (payload: CreateGoalPayload) => api.post<{ data: Goal }>('/goals', payload),
  update: (id: number, payload: Partial<CreateGoalPayload>) => api.put<{ data: Goal }>(`/goals/${id}`, payload),
  delete: (id: number) => api.delete(`/goals/${id}`),
  contribute: (id: number, payload: ContributeGoalPayload) =>
    api.post<{ message: string; data: any; goal: Goal }>(`/goals/${id}/contribute`, payload),
}
