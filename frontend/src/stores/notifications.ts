import { defineStore } from 'pinia'
import { ref } from 'vue'
import { notificationsApi } from '@/api/notifications'
import type { NotificationItem } from '@/types/notification'

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref<NotificationItem[]>([])
  const unreadCount = ref<number>(0)
  const loading = ref(false)

  async function fetchNotifications() {
    loading.value = true
    try {
      const { data } = await notificationsApi.get()
      notifications.value = data.items
      unreadCount.value = data.unread_count
    } catch (e) {
      console.error('Failed to fetch notifications:', e)
    } finally {
      loading.value = false
    }
  }

  async function markAsRead(id: string | number) {
    // If numeric, update database
    if (typeof id === 'number' || !isNaN(Number(id))) {
      await notificationsApi.markAsRead(id)
    }
    const target = notifications.value.find((n) => String(n.id) === String(id))
    if (target && !target.read) {
      target.read = true
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  }

  async function markAllAsRead() {
    await notificationsApi.markAllAsRead()
    notifications.value.forEach((n) => (n.read = true))
    unreadCount.value = 0
  }

  return {
    notifications,
    unreadCount,
    loading,
    fetchNotifications,
    markAsRead,
    markAllAsRead,
  }
})
