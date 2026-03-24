import { computed, ref } from 'vue'
import { notificationsApi } from '../services/api'

const items = ref([])
const loading = ref(false)
const loadedOnce = ref(false)

export const useNotifications = () => {
  const loadNotifications = async (force = false) => {
    if (loading.value || (loadedOnce.value && !force)) return items.value

    loading.value = true

    try {
      items.value = await notificationsApi.list()
      loadedOnce.value = true
      return items.value
    } finally {
      loading.value = false
    }
  }

  const markNotificationRead = async (notificationId) => {
    const target = items.value.find((item) => item.id === notificationId)
    if (!target || target.is_read) return

    await notificationsApi.update(notificationId, { is_read: true })
    target.is_read = true
    target.unread = false
  }

  return {
    items,
    loading,
    unreadCount: computed(() => items.value.filter((item) => item.unread).length),
    loadNotifications,
    markNotificationRead
  }
}
