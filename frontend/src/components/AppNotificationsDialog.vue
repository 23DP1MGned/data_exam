<template>
  <v-dialog :model-value="modelValue" max-width="720" @update:model-value="emit('update:modelValue', $event)">
    <v-card class="notifications-dialog-card" :class="{ 'notifications-dialog-card-dark': darkMode }">
      <div class="notifications-dialog-header">
        <div>
          <div class="notifications-dialog-title">All Notifications</div>
          <div class="notifications-dialog-subtitle">Recent updates about trainings, attendance and payments.</div>
        </div>

        <v-btn icon variant="text" @click="emit('update:modelValue', false)">
          <v-icon>mdi-close</v-icon>
        </v-btn>
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
          Loading notifications...
        </div>

        <div v-else-if="!normalizedNotifications.length" class="notifications-empty-state">
          No notifications yet.
        </div>
      </div>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  darkMode: {
    type: Boolean,
    default: false
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

const normalizedNotifications = computed(() => props.notifications ?? [])

function handleNotificationClick(item) {
  emit('notification-click', item)
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
}
</style>
