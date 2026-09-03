import api from './axios'
import type { NotificationResponse } from '@/types/notification'

export const notificationsApi = {
  get: () => api.get<NotificationResponse>('/notifications'),
  markAsRead: (id: string | number) => api.put(`/notifications/${id}/read`),
  markAllAsRead: () => api.post('/notifications/read-all'),
}
