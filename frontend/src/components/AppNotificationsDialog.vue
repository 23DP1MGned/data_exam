<template>
  <v-dialog :model-value="modelValue" max-width="720" @update:model-value="emit('update:modelValue', $event)">
    <v-card
      class="notifications-dialog-card"
      :class="{
        'notifications-dialog-card-dark': darkMode,
        'notifications-dialog-card-admin': accent === 'admin',
        'notifications-dialog-card-coach': accent === 'coach'
      }"
    >
      <div class="notifications-dialog-header">
        <div>
          <div class="notifications-dialog-title">{{ t('notifications.title') }}</div>
          <div class="notifications-dialog-subtitle">{{ t('notifications.subtitle') }}</div>
        </div>

        <div class="notifications-dialog-actions">
          <v-btn
            class="notifications-mark-all-btn"
            variant="text"
            :disabled="!hasUnreadNotifications || markAllLoading"
            :loading="markAllLoading"
            @click="handleMarkAllRead"
          >
            {{ t('notifications.markAllRead') }}
          </v-btn>

          <v-btn icon variant="text" @click="emit('update:modelValue', false)">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </div>
      </div>

      <div class="notifications-list">
        <article
          v-for="item in normalizedNotifications"
          :key="item.id"
          class="notification-item"
          :class="{ 'notification-item-unread': item.unread }"
          @click="handleNotificationClick(item)"
        >
          <div class="notification-dot" :class="{ 'notification-dot-active': item.unread }"></div>

          <div class="notification-copy">
            <div class="notification-title">{{ item.title }}</div>
            <div class="notification-text">{{ item.text }}</div>
            <div class="notification-time">{{ item.time }}</div>
          </div>
        </article>

        <div v-if="loading" class="notifications-empty-state">
          {{ t('notifications.loading') }}
        </div>

        <div v-else-if="!normalizedNotifications.length" class="notifications-empty-state">
          {{ t('notifications.empty') }}
        </div>
      </div>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useNotifications } from '../composables/useNotifications'
import { useLocale } from '../i18n'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  darkMode: {
    type: Boolean,
    default: false
  },
  accent: {
    type: String,
    default: 'default'
  },
  notifications: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'notification-click'])

const { markAllNotificationsRead } = useNotifications()
const { t } = useLocale()
const markAllLoading = ref(false)
const normalizedNotifications = computed(() => props.notifications ?? [])
const hasUnreadNotifications = computed(() => normalizedNotifications.value.some((item) => item.unread))

function handleNotificationClick(item) {
  emit('notification-click', item)
}

async function handleMarkAllRead() {
  if (markAllLoading.value || !hasUnreadNotifications.value) return

  markAllLoading.value = true

  try {
    await markAllNotificationsRead()
  } finally {
    markAllLoading.value = false
  }
}
</script>

<style scoped>
.notifications-dialog-card {
  padding: 24px;
  border-radius: 24px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.96), rgba(238, 245, 255, 0.92));
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow: 0 20px 45px rgba(76, 104, 148, 0.18);
}

.notifications-dialog-card-dark {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.96));
  border-color: rgba(74, 92, 126, 0.42);
  box-shadow: 0 24px 54px rgba(4, 10, 24, 0.34);
}

.notifications-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 18px;
}

.notifications-dialog-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.notifications-mark-all-btn {
  border-radius: 999px;
  text-transform: none;
  font-weight: 600;
}

.notifications-dialog-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #172033;
}

.notifications-dialog-card-dark .notifications-dialog-title {
  color: #eef4ff;
}

.notifications-dialog-subtitle {
  margin-top: 8px;
  color: #64748b;
  line-height: 1.5;
}

.notifications-dialog-card-dark .notifications-dialog-subtitle {
  color: #8fa3c1;
}

.notifications-list {
  display: flex;
  flex-direction: column;
  max-height: 440px;
  overflow: auto;
  padding-right: 4px;
  scrollbar-width: thin;
  scrollbar-color: rgba(22, 119, 255, 0.45) rgba(226, 232, 240, 0.5);
}

.notifications-dialog-card-admin .notifications-list {
  scrollbar-color: rgba(242, 140, 40, 0.55) rgba(226, 232, 240, 0.5);
}

.notifications-dialog-card-coach .notifications-list {
  scrollbar-color: rgba(156, 124, 255, 0.55) rgba(226, 232, 240, 0.5);
}

.notifications-list::-webkit-scrollbar {
  width: 10px;
}

.notifications-list::-webkit-scrollbar-track {
  border-radius: 999px;
  background: rgba(226, 232, 240, 0.5);
}

.notifications-list::-webkit-scrollbar-thumb {
  border: 2px solid rgba(226, 232, 240, 0.5);
  border-radius: 999px;
  background: linear-gradient(180deg, rgba(22, 119, 255, 0.7), rgba(15, 95, 227, 0.55));
}

.notifications-dialog-card-admin .notifications-list::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, rgba(242, 140, 40, 0.78), rgba(222, 111, 18, 0.58));
}

.notifications-dialog-card-coach .notifications-list::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, rgba(156, 124, 255, 0.78), rgba(127, 92, 255, 0.58));
}

.notification-item {
  display: flex;
  gap: 14px;
  padding: 16px 0;
}

.notification-item + .notification-item {
  border-top: 1px solid rgba(15, 23, 42, 0.08);
}

.notifications-dialog-card-dark .notification-item + .notification-item {
  border-top-color: rgba(74, 92, 126, 0.32);
}

.notification-dot {
  width: 10px;
  height: 10px;
  margin-top: 7px;
  border-radius: 999px;
  flex-shrink: 0;
  background: rgba(148, 163, 184, 0.4);
}

.notification-dot-active {
  background: #1677ff;
  box-shadow: 0 0 0 6px rgba(22, 119, 255, 0.12);
}

.notifications-dialog-card-admin .notification-dot-active {
  background: #f28c28;
  box-shadow: 0 0 0 6px rgba(242, 140, 40, 0.14);
}

.notifications-dialog-card-coach .notification-dot-active {
  background: #9c7cff;
  box-shadow: 0 0 0 6px rgba(156, 124, 255, 0.14);
}

.notification-copy {
  min-width: 0;
}

.notification-title {
  font-weight: 700;
  color: #172033;
}

.notifications-dialog-card-dark .notification-title {
  color: #eef4ff;
}

.notification-text {
  margin-top: 6px;
  color: #64748b;
  line-height: 1.45;
}

.notifications-dialog-card-dark .notification-text,
.notifications-dialog-card-dark .notification-time {
  color: #8fa3c1;
}

.notification-time {
  margin-top: 8px;
  font-size: 0.84rem;
  color: #7b8798;
}

.notifications-empty-state {
  padding: 18px 0 8px;
  color: #7b8798;
}

.notifications-dialog-card-dark .notifications-empty-state {
  color: #8fa3c1;
}

@media (max-width: 560px) {
  .notifications-dialog-card {
    padding: 18px;
  }

  .notifications-dialog-header {
    flex-direction: column;
    align-items: stretch;
  }

  .notifications-dialog-actions {
    justify-content: space-between;
  }
}
</style>
