export interface NotificationItem {
  id: string
  type: string
  title: string
  message: string
  created_at: string
  read: boolean
  link?: string | null
}

export interface NotificationResponse {
  unread_count: number
  items: NotificationItem[]
}
