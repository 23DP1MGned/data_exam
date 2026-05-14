<template>
  <div
    class="stats-grid"
    :class="[`stats-grid-${variant}`, { 'stats-grid-dark': darkMode }]"
    :style="{ '--stats-columns': columnCount }"
  >
    <article
      v-for="item in items"
      :key="item.label"
      class="stats-card"
      :class="`stats-card-${item.tone || 'default'}`"
    >
      <div class="stats-card-main">
        <div class="stats-copy">
          <div class="stats-label">{{ item.label }}</div>
          <div v-if="showCaption && item.caption" class="stats-caption">{{ item.caption }}</div>
        </div>

        <div class="stats-side">
          <div class="stats-icon-wrap" :class="`stats-icon-wrap-${item.tone || 'default'}`">
            <v-icon size="20">{{ item.icon || 'mdi-chart-box-outline' }}</v-icon>
          </div>
          <div class="stats-value">{{ item.value }}</div>
        </div>
      </div>
    </article>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  darkMode: {
    type: Boolean,
    default: false
  },
  variant: {
    type: String,
    default: 'default'
  },
  hideCaptions: {
    type: Boolean,
    default: false
  }
})

const columnCount = computed(() => {
  switch (props.variant) {
    case 'users':
    case 'sessions':
      return 5
    case 'payments':
      return 3
    case 'compact':
      return 2
    case 'admin':
    default:
      return 4
  }
})

const showCaption = computed(() => !props.hideCaptions && !['users', 'sessions'].includes(props.variant))
</script>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(var(--stats-columns, 4), minmax(0, 1fr)) !important;
  gap: 16px;
  width: 100%;
}

.stats-card {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 132px;
  min-width: 0;
  padding: 16px;
  overflow: hidden;
  border-radius: 16px;
  border: 1px solid rgba(216, 227, 241, 0.96);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(246, 250, 255, 0.92));
  box-shadow:
    0 10px 22px rgba(94, 117, 151, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.76);
}

.stats-card::before {
  content: '';
  position: absolute;
  top: 14px;
  bottom: 14px;
  left: 0;
  width: 4px;
  border-radius: 0 8px 8px 0;
  background: linear-gradient(180deg, rgba(73, 131, 220, 0.94), rgba(125, 173, 238, 0.72));
}

.stats-card-main {
  display: flex;
  flex-direction: column;
  gap: 14px;
  align-items: stretch;
}

.stats-copy {
  min-width: 0;
}

.stats-side {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  width: 100%;
  min-width: 0;
  max-width: none;
  margin-top: auto;
  padding: 10px 12px;
  border-radius: 12px;
  background: rgba(242, 247, 253, 0.92);
  border: 1px solid rgba(223, 232, 243, 0.94);
}

.stats-label {
  color: #7b8798;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  line-height: 1.18;
  text-wrap: pretty;
  overflow-wrap: break-word;
}

.stats-caption {
  margin-top: 6px;
  color: #738196;
  font-size: 0.82rem;
  line-height: 1.25;
  text-wrap: pretty;
  overflow-wrap: break-word;
}

.stats-value {
  font-size: clamp(1.55rem, 1.7vw, 2.15rem);
  line-height: 1;
  font-weight: 760;
  color: #172033;
  text-align: right;
  white-space: nowrap;
  font-variant-numeric: tabular-nums;
}

.stats-icon-wrap {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  flex-shrink: 0;
  border-radius: 12px;
  border: 1px solid transparent;
}

.stats-card-users {
  background: linear-gradient(180deg, rgba(243, 248, 255, 0.98), rgba(233, 242, 255, 0.94));
}

.stats-card-coaches {
  background: linear-gradient(180deg, rgba(242, 250, 255, 0.98), rgba(232, 245, 255, 0.94));
}

.stats-card-groups {
  background: linear-gradient(180deg, rgba(244, 245, 255, 0.98), rgba(236, 239, 255, 0.94));
}

.stats-card-payments {
  background: linear-gradient(180deg, rgba(241, 247, 255, 0.98), rgba(231, 239, 250, 0.94));
}

.stats-card-attendance {
  background: linear-gradient(180deg, rgba(240, 251, 247, 0.98), rgba(231, 246, 240, 0.94));
}

.stats-card-time {
  background: linear-gradient(180deg, rgba(244, 250, 255, 0.98), rgba(235, 243, 252, 0.94));
}

.stats-card-alert {
  background: linear-gradient(180deg, rgba(247, 245, 255, 0.98), rgba(238, 236, 255, 0.94));
}

.stats-icon-wrap-users {
  color: #275eaa;
  background: rgba(228, 239, 255, 0.96);
  border-color: rgba(181, 203, 236, 0.94);
}

.stats-icon-wrap-coaches {
  color: #2e74b6;
  background: rgba(225, 244, 255, 0.96);
  border-color: rgba(176, 217, 240, 0.94);
}

.stats-icon-wrap-groups {
  color: #465fc4;
  background: rgba(233, 237, 255, 0.96);
  border-color: rgba(194, 202, 244, 0.92);
}

.stats-icon-wrap-payments {
  color: #1f4f8d;
  background: rgba(228, 238, 251, 0.96);
  border-color: rgba(181, 202, 231, 0.94);
}

.stats-icon-wrap-attendance {
  color: #25755f;
  background: rgba(229, 246, 239, 0.96);
  border-color: rgba(183, 225, 208, 0.92);
}

.stats-icon-wrap-time {
  color: #356fa4;
  background: rgba(233, 243, 252, 0.96);
  border-color: rgba(187, 210, 235, 0.92);
}

.stats-icon-wrap-alert {
  color: #6052bf;
  background: rgba(237, 235, 255, 0.96);
  border-color: rgba(203, 198, 246, 0.92);
}

.stats-grid-users .stats-side,
.stats-grid-sessions .stats-side {
  padding: 9px 10px;
}

.stats-grid-users .stats-value,
.stats-grid-sessions .stats-value {
  font-size: clamp(1.45rem, 1.45vw, 1.95rem);
}

.stats-grid-dark .stats-card {
  border-color: rgba(73, 91, 126, 0.5);
  background: linear-gradient(180deg, rgba(18, 28, 46, 0.96), rgba(15, 24, 40, 0.94));
  box-shadow:
    0 16px 34px rgba(4, 10, 24, 0.22),
    inset 0 1px 0 rgba(255, 255, 255, 0.06);
}

.stats-grid-dark .stats-card-users {
  background: linear-gradient(180deg, rgba(20, 33, 56, 0.98), rgba(17, 31, 55, 0.95));
}

.stats-grid-dark .stats-card-coaches {
  background: linear-gradient(180deg, rgba(18, 36, 58, 0.98), rgba(15, 32, 53, 0.95));
}

.stats-grid-dark .stats-card-groups {
  background: linear-gradient(180deg, rgba(24, 29, 59, 0.98), rgba(20, 26, 54, 0.95));
}

.stats-grid-dark .stats-card-payments {
  background: linear-gradient(180deg, rgba(18, 30, 52, 0.98), rgba(15, 27, 48, 0.95));
}

.stats-grid-dark .stats-card-attendance {
  background: linear-gradient(180deg, rgba(17, 39, 47, 0.98), rgba(14, 31, 39, 0.95));
}

.stats-grid-dark .stats-card-time {
  background: linear-gradient(180deg, rgba(19, 33, 52, 0.98), rgba(15, 28, 46, 0.95));
}

.stats-grid-dark .stats-card-alert {
  background: linear-gradient(180deg, rgba(28, 30, 58, 0.98), rgba(20, 23, 49, 0.95));
}

.stats-grid-dark .stats-side {
  background: rgba(26, 37, 56, 0.9);
  border-color: rgba(63, 80, 114, 0.54);
}

.stats-grid-dark .stats-label {
  color: #94a6c4;
}

.stats-grid-dark .stats-caption {
  color: #a1b3cf;
}

.stats-grid-dark .stats-value {
  color: #f3f7ff;
}

.stats-grid-dark .stats-icon-wrap-users {
  color: #d7e6ff;
  background: rgba(33, 55, 94, 0.82);
  border-color: rgba(81, 109, 161, 0.62);
}

.stats-grid-dark .stats-icon-wrap-coaches {
  color: #d4efff;
  background: rgba(28, 66, 98, 0.82);
  border-color: rgba(66, 120, 160, 0.62);
}

.stats-grid-dark .stats-icon-wrap-groups {
  color: #dcdfff;
  background: rgba(42, 49, 103, 0.82);
  border-color: rgba(87, 101, 181, 0.56);
}

.stats-grid-dark .stats-icon-wrap-payments {
  color: #d3e7ff;
  background: rgba(27, 48, 82, 0.82);
  border-color: rgba(71, 100, 148, 0.58);
}

.stats-grid-dark .stats-icon-wrap-attendance {
  color: #c8f0df;
  background: rgba(24, 70, 57, 0.82);
  border-color: rgba(58, 122, 102, 0.56);
}

.stats-grid-dark .stats-icon-wrap-time {
  color: #d6e8ff;
  background: rgba(30, 54, 86, 0.82);
  border-color: rgba(71, 102, 146, 0.56);
}

.stats-grid-dark .stats-icon-wrap-alert {
  color: #e0dcff;
  background: rgba(48, 47, 95, 0.82);
  border-color: rgba(89, 96, 167, 0.56);
}

@media (max-width: 1200px) {
  .stats-grid-users,
  .stats-grid-sessions {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
  }
}

@media (max-width: 960px) {
  .stats-grid,
  .stats-grid-admin,
  .stats-grid-users,
  .stats-grid-sessions,
  .stats-grid-payments,
  .stats-grid-compact {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 14px;
  }

  .stats-card {
    min-height: 124px;
    padding: 14px;
  }

  .stats-card-main {
    gap: 12px;
  }

  .stats-side {
    padding: 9px 10px;
  }
}

@media (max-width: 640px) {
  .stats-grid,
  .stats-grid-admin,
  .stats-grid-users,
  .stats-grid-sessions,
  .stats-grid-payments,
  .stats-grid-compact {
    grid-template-columns: 1fr !important;
    gap: 12px;
  }

  .stats-card {
    min-height: 116px;
    padding: 13px;
    border-radius: 14px;
  }

  .stats-card::before {
    top: 12px;
    bottom: 12px;
  }

  .stats-card-main {
    gap: 12px;
  }

  .stats-side {
    padding: 9px 10px;
    border-radius: 11px;
  }

  .stats-icon-wrap {
    width: 36px;
    height: 36px;
    border-radius: 11px;
  }

  .stats-value {
    font-size: clamp(1.35rem, 6vw, 1.9rem);
  }

  .stats-label {
    font-size: 0.74rem;
  }

  .stats-caption {
    font-size: 0.78rem;
  }
}

@media (max-width: 420px) {
  .stats-side {
    gap: 10px;
    padding: 8px 9px;
  }

  .stats-icon-wrap {
    width: 34px;
    height: 34px;
  }

  .stats-value {
    font-size: clamp(1.2rem, 5.8vw, 1.7rem);
  }

  .stats-label {
    font-size: 0.72rem;
  }
}
</style>
