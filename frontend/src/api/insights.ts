import api from './axios'
import type { SmartInsightsResponse } from '@/types/insight'

export const insightsApi = {
  get: (params?: { month?: number; year?: number }) =>
    api.get<SmartInsightsResponse>('/insights', { params }),
}
