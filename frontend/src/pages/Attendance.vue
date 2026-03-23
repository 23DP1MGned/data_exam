<template>
  <v-app>
    <v-main class="attendance-page">
      <div class="attendance-shell">
        <div class="attendance-panel">
          <aside class="sidebar-card">
            <div class="brand-block">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Coach workspace</div>
              </div>
            </div>

            <nav class="nav-list">
              <v-btn
                v-for="item in navItems"
                :key="item.label"
                :to="item.to"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/attendance' }"
                block
              >
                <template #prepend>
                  <v-icon>{{ item.icon }}</v-icon>
                </template>
                {{ item.label }}
              </v-btn>
            </nav>
          </aside>

          <section class="content-shell">
            <div class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    label="Search attendance"
                    variant="plain"
                    hide-details
                    density="comfortable"
                    class="search-field"
                  />
                </div>
              </div>

              <div class="topbar-tools">
                <div class="icon-badge-wrap">
                  <v-btn
                    icon
                    variant="text"
                    class="top-icon-btn"
                    :class="{ 'top-icon-btn-active': darkMode }"
                    @click="darkMode = !darkMode"
                  >
                    <v-icon>{{ darkMode ? 'mdi-weather-night' : 'mdi-white-balance-sunny' }}</v-icon>
                  </v-btn>
                </div>

                <div class="icon-badge-wrap">
                  <v-btn icon variant="text" class="top-icon-btn">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">4</span>
                </div>

                <div class="profile-pill">
                  <v-avatar size="48">
                    <img src="https://i.pravatar.cc/80?img=12" alt="Coach profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">Maksims Richards</div>
                    <div class="profile-email">maksims@sportsystem.app</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="attendance-card">
              <div class="attendance-header">
                <div>
                  <h1 class="attendance-title">Attendance Calendar</h1>
                  <div class="attendance-subtitle">
                    Track sessions, player presence and team workload in one view
                  </div>
                </div>
              </div>

              <div class="attendance-overview">
                <div class="coach-card">
                  <v-avatar size="76" class="coach-avatar">
                    <img src="https://i.pravatar.cc/100?img=12" alt="User avatar">
                  </v-avatar>
                  <div class="coach-name">Maksims Richards</div>
                  <div class="coach-groups">
                    <span v-for="group in memberGroups" :key="group" class="coach-group-pill">
                      {{ group }}
                    </span>
                  </div>
                </div>

                <div class="overview-main">
                  <div class="overview-top">
                    <div class="status-badge">
                      Attendance overview
                    </div>
                  </div>

                  <div class="stats-row">
                    <div v-for="item in stats" :key="item.label" class="stat-card">
                      <div class="stat-value">{{ item.value }}</div>
                      <div class="stat-label">{{ item.label }}</div>
                    </div>
                  </div>

                  <div class="timeline-wrap">
                    <div class="timeline-head">
                      <div>
                        <div class="timeline-title">Session attendance split</div>
                        <div class="timeline-caption">
                          This bar shows how all scheduled sessions are split between attended and missed sessions this month.
                        </div>
                      </div>
                      <div class="timeline-percent">72%</div>
                    </div>
                    <div class="timeline-bar">
                      <div
                        v-for="segment in timelineSegments"
                        :key="segment.label"
                        class="timeline-segment"
                        :style="{ background: segment.color, width: segment.width }"
                      ></div>
                    </div>
                    <div class="timeline-labels">
                      <span v-for="time in timelineLabels" :key="time">{{ time }}</span>
                    </div>
                    <div class="timeline-legend">
                      <span
                        v-for="segment in timelineSegments"
                        :key="`${segment.label}-legend`"
                        class="legend-item"
                      >
                        <span class="legend-dot" :style="{ background: segment.color }"></span>
                        {{ segment.label }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="attendance-toolbar">
                <div class="toolbar-label-wrap">
                  <div class="toolbar-section-label">Calendar</div>
                </div>

                <div class="toolbar-meta">
                  <div class="month-switcher">
                    <v-btn icon variant="text" class="month-nav-btn" @click="changeMonth(-1)">
                      <v-icon>mdi-chevron-left</v-icon>
                    </v-btn>
                    <div class="toolbar-date">
                      <v-icon size="18">mdi-calendar-month-outline</v-icon>
                      {{ currentMonthLabel }}
                    </div>
                    <v-btn icon variant="text" class="month-nav-btn" @click="changeMonth(1)">
                      <v-icon>mdi-chevron-right</v-icon>
                    </v-btn>
                  </div>
                </div>
              </div>

              <div class="calendar-card">
                <div class="calendar-header">
                  <div class="calendar-title">Recent attendance calendar</div>
                  <div class="calendar-legend">
                    <span class="legend-item">
                      <span class="legend-dot legend-dot-green"></span>
                      Present
                    </span>
                    <span class="legend-item">
                      <span class="legend-dot legend-dot-red"></span>
                      Absent
                    </span>
                    <span class="legend-item">
                      <span class="legend-dot legend-dot-gray"></span>
                      No training
                    </span>
                  </div>
                </div>

                <div class="calendar-grid">
                  <div class="calendar-days">
                    <div v-for="day in weekDays" :key="day" class="day-name">{{ day }}</div>
                  </div>

                  <div v-for="week in calendarWeeks" :key="week.id" class="calendar-week">
                    <div
                      v-for="day in week.days"
                      :key="day.id"
                      class="calendar-cell"
                      :class="{
                        'calendar-cell-muted': day.status === 'No training',
                        'calendar-cell-empty': !day.status,
                        'calendar-cell-present': day.status === 'Present',
                        'calendar-cell-absent': day.status === 'Absent',
                        'calendar-cell-no-training': day.status === 'No training',
                        'calendar-cell-outside': !day.isCurrentMonth
                      }"
                    >
                      <div class="cell-date">{{ day.day }}</div>

                      <div v-if="day.status" class="cell-status">
                        <span
                          class="cell-dot"
                          :class="{
                            'cell-dot-green': day.status === 'Present',
                            'cell-dot-red': day.status === 'Absent',
                            'cell-dot-gray': day.status === 'No training'
                          }"
                        ></span>
                        {{ day.status }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')
const darkMode = ref(false)
const currentMonth = ref(new Date('2026-07-01'))

const navItems = [
  { label: 'Home', icon: 'mdi-home-outline', to: '/dashboard' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: '/attendance' },
  { label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }
]

const memberGroups = ['Football U14', 'Running Club', 'Swimming Beginners']

const stats = [
  { label: 'Total training time', value: '12h 30m' },
  { label: 'Total sessions', value: '18' },
  { label: 'Attended sessions', value: '13' },
  { label: 'Missed sessions', value: '5' }
]

const timelineSegments = [
  { label: 'Attended sessions', color: '#39b980', width: '72%' },
  { label: 'Missed sessions', color: '#ef6b73', width: '28%' }
]

const timelineLabels = ['0%', '25%', '50%', '75%', '100%']

const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

const attendanceByDate = {
  '2026-07-01': 'Present',
  '2026-07-02': 'Present',
  '2026-07-03': 'Absent',
  '2026-07-06': 'Present',
  '2026-07-07': 'Present',
  '2026-07-08': 'Absent',
  '2026-07-09': 'Present',
  '2026-07-10': 'Present',
  '2026-07-13': 'Present',
  '2026-07-14': 'Absent',
  '2026-07-15': 'Present',
  '2026-07-16': 'Present',
  '2026-07-17': 'Present',
  '2026-07-20': 'Absent',
  '2026-07-21': 'Present',
  '2026-07-22': 'Present',
  '2026-07-23': 'Present',
  '2026-07-24': 'Absent',
  '2026-07-27': 'Present',
  '2026-07-28': 'Present',
  '2026-07-29': 'Absent',
  '2026-07-30': 'Present',
  '2026-07-31': 'Present',
  '2026-08-03': 'Present',
  '2026-08-04': 'Absent',
  '2026-08-05': 'Present',
  '2026-08-06': 'Present',
  '2026-08-07': 'Present'
}

const currentMonthLabel = computed(() =>
  currentMonth.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric'
  })
)

const calendarWeeks = computed(() => {
  const startOfMonth = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), 1)
  const endOfMonth = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 0)

  const firstDayIndex = (startOfMonth.getDay() + 6) % 7
  const totalCells = Math.ceil((firstDayIndex + endOfMonth.getDate()) / 7) * 7
  const firstVisibleDay = new Date(startOfMonth)
  firstVisibleDay.setDate(startOfMonth.getDate() - firstDayIndex)

  const days = Array.from({ length: totalCells }, (_, index) => {
    const date = new Date(firstVisibleDay)
    date.setDate(firstVisibleDay.getDate() + index)

    const iso = date.toISOString().slice(0, 10)
    const isCurrentMonth = date.getMonth() === currentMonth.value.getMonth()

    return {
      id: iso,
      day: date.getDate(),
      status: attendanceByDate[iso] || 'No training',
      isCurrentMonth
    }
  })

  return Array.from({ length: days.length / 7 }, (_, index) => ({
    id: index + 1,
    days: days.slice(index * 7, index * 7 + 7)
  }))
})

function changeMonth(offset) {
  currentMonth.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() + offset,
    1
  )
}
</script>

<style scoped>
.attendance-page {
  padding: 24px;
}

.attendance-shell {
  max-width: 1440px;
  margin: 0 auto;
  border-radius: 34px;
  border: 1px solid rgba(255, 255, 255, 0.45);
  background: linear-gradient(135deg, rgba(236, 244, 255, 0.82), rgba(245, 237, 248, 0.78));
  box-shadow: 0 24px 70px rgba(95, 122, 168, 0.18);
  backdrop-filter: blur(18px);
}

.attendance-panel {
  display: grid;
  grid-template-columns: 232px minmax(0, 1fr);
  gap: 22px;
  padding: 22px;
}

.sidebar-card {
  display: flex;
  flex-direction: column;
  padding: 18px;
  border-radius: 28px;
  background: rgba(245, 250, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.brand-block {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 26px;
  padding: 10px 8px;
  min-width: 0;
}

.brand-icon {
  display: grid;
  place-items: center;
  width: 54px;
  height: 54px;
  flex-shrink: 0;
  border-radius: 18px;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 16px 30px rgba(22, 119, 255, 0.28);
}

.brand-text {
  min-width: 0;
  flex: 1;
}

.brand-name {
  font-size: 1.22rem;
  font-weight: 700;
  color: #172033;
  line-height: 1.1;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.nav-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.nav-item {
  justify-content: flex-start;
  min-height: 58px;
  border-radius: 18px;
  color: #172033;
  text-transform: none;
  letter-spacing: 0;
  font-size: 1rem;
  font-weight: 500;
}

:deep(.nav-item .v-btn__content) {
  justify-content: flex-start;
}

.nav-item-active {
  color: white;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 16px 34px rgba(22, 119, 255, 0.28);
}

.content-shell {
  display: flex;
  flex-direction: column;
  gap: 22px;
  min-width: 0;
}

.topbar-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 18px 20px;
  border-radius: 26px;
  background: rgba(255, 255, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.search-wrap {
  flex: 1;
  min-width: 0;
  max-width: 420px;
}

.search-shell {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 58px;
  padding: 0 18px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(223, 231, 243, 0.92);
}

.search-shell-icon {
  color: #6b7280;
  flex-shrink: 0;
}

.search-field {
  flex: 1;
}

:deep(.search-field .v-input__control),
:deep(.search-field .v-field),
:deep(.search-field .v-field__field) {
  min-height: auto;
  background: transparent;
  box-shadow: none;
}

.search-field :deep(.v-field__input) {
  min-height: 58px;
  padding-top: 0;
  padding-bottom: 0;
  display: flex;
  align-items: center;
}

.search-field :deep(input) {
  color: #172033;
}

.topbar-tools {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.top-icon-btn {
  width: 54px;
  height: 54px;
  border: 1px solid rgba(148, 163, 184, 0.18);
  background: rgba(255, 255, 255, 0.75);
}

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
}

.icon-badge-wrap {
  position: relative;
  color: #111827;
}

.icon-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  display: grid;
  place-items: center;
  min-width: 21px;
  height: 21px;
  padding: 0 6px;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 700;
  color: white;
  background: #1677ff;
}

.profile-pill {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
  padding: 10px 16px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.82);
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.attendance-card {
  padding: 28px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.attendance-header {
  margin-bottom: 22px;
}

.attendance-title {
  margin: 0;
  font-size: 2.2rem;
  font-weight: 700;
  color: #121826;
}

.attendance-subtitle {
  margin-top: 10px;
  color: #66748a;
  font-size: 1rem;
}

.attendance-overview {
  display: grid;
  grid-template-columns: 220px minmax(0, 1fr);
  gap: 18px;
  margin-bottom: 24px;
}

.coach-card,
.calendar-card {
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.88);
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
}

.coach-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 26px 20px;
  text-align: center;
  background: linear-gradient(180deg, #5e3b92 0%, #6f49a6 100%);
  color: white;
}

.coach-avatar {
  margin-bottom: 14px;
  border: 3px solid rgba(255, 255, 255, 0.45);
}

.coach-name {
  font-size: 1.1rem;
  font-weight: 700;
}

.coach-groups {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 8px;
  margin-top: 14px;
}

.coach-group-pill {
  padding: 7px 10px;
  border-radius: 999px;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.92);
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.overview-main {
  min-width: 0;
}

.overview-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 16px;
}

.status-badge {
  padding: 10px 14px;
  border-radius: 14px;
  color: #1677ff;
  background: rgba(22, 119, 255, 0.12);
  font-weight: 600;
  white-space: nowrap;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
  margin-bottom: 16px;
}

.stat-card {
  padding: 16px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.82);
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #172033;
}

.stat-label {
  margin-top: 6px;
  color: #7b8798;
}

.timeline-wrap {
  padding: 16px 18px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.82);
}

.timeline-title {
  font-size: 0.98rem;
  font-weight: 700;
  color: #172033;
}

.timeline-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}

.timeline-caption {
  margin-top: 6px;
  margin-bottom: 14px;
  color: #64748b;
  line-height: 1.45;
  font-size: 0.9rem;
}

.timeline-percent {
  font-size: 1.8rem;
  line-height: 1;
  font-weight: 800;
  color: #172033;
  white-space: nowrap;
}

.timeline-bar {
  display: flex;
  height: 18px;
  overflow: hidden;
  border-radius: 999px;
}

.timeline-segment:first-child {
  border-radius: 999px 0 0 999px;
}

.timeline-segment:last-child {
  border-radius: 0 999px 999px 0;
}

.timeline-labels {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  margin-top: 10px;
  color: #94a3b8;
  font-size: 0.74rem;
}

.timeline-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 14px;
}

.attendance-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 20px;
}

.toolbar-label-wrap {
  display: flex;
  align-items: center;
}

.toolbar-section-label {
  font-size: 1.02rem;
  font-weight: 700;
  color: #172033;
}

.toolbar-meta {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  color: #64748b;
}

.month-switcher,
.toolbar-date {
  display: flex;
  align-items: center;
  gap: 8px;
}

.month-switcher {
  padding: 6px 8px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.86);
  border: 1px solid rgba(223, 231, 243, 0.9);
}

.month-nav-btn {
  width: 38px;
  height: 38px;
}

.toolbar-date {
  min-width: 160px;
  justify-content: center;
  font-weight: 600;
  color: #475569;
}

.calendar-card {
  padding: 20px;
}

.calendar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 18px;
}

.calendar-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #172033;
}

.calendar-legend {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #64748b;
  font-size: 0.9rem;
}

.legend-dot,
.cell-dot {
  width: 9px;
  height: 9px;
  border-radius: 999px;
}

.legend-dot-green,
.cell-dot-green {
  background: #39b980;
}

.legend-dot-red,
.cell-dot-red {
  background: #ef6b73;
}

.legend-dot-gray,
.cell-dot-gray {
  background: #94a3b8;
}

.calendar-days,
.calendar-week {
  display: grid;
  grid-template-columns: repeat(7, minmax(0, 1fr));
}

.calendar-days {
  margin-bottom: 10px;
}

.day-name {
  padding: 0 10px 8px;
  font-weight: 700;
  color: #475569;
}

.calendar-cell {
  position: relative;
  min-height: 112px;
  padding: 10px;
  border-top: 1px solid rgba(226, 232, 240, 0.92);
  transition: background 0.2s ease, box-shadow 0.2s ease;
}

.calendar-cell::before {
  content: '';
  position: absolute;
  inset: 6px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.44);
  border: 1px solid rgba(255, 255, 255, 0.48);
  box-shadow: 0 10px 22px rgba(148, 163, 184, 0.08);
  backdrop-filter: blur(8px);
  opacity: 0;
  transition: opacity 0.2s ease, background 0.2s ease, border-color 0.2s ease;
  pointer-events: none;
}

.calendar-cell > * {
  position: relative;
  z-index: 1;
}

.calendar-cell-empty {
  opacity: 0.75;
}

.calendar-cell-outside {
  opacity: 0.52;
}

.calendar-cell-present {
  background: transparent;
}

.calendar-cell-absent {
  background: transparent;
}

.calendar-cell-no-training {
  background: transparent;
}

.calendar-cell-muted {
  color: #64748b;
}

.calendar-cell-present::before {
  opacity: 1;
  background: rgba(87, 214, 156, 0.16);
  border-color: rgba(57, 185, 128, 0.22);
  box-shadow: 0 12px 24px rgba(57, 185, 128, 0.12);
}

.calendar-cell-absent::before {
  opacity: 1;
  background: rgba(255, 133, 140, 0.15);
  border-color: rgba(239, 107, 115, 0.22);
  box-shadow: 0 12px 24px rgba(239, 107, 115, 0.1);
}

.calendar-cell-no-training::before {
  opacity: 1;
  background: rgba(203, 213, 225, 0.16);
  border-color: rgba(148, 163, 184, 0.2);
  box-shadow: 0 12px 24px rgba(148, 163, 184, 0.08);
}

.cell-date {
  font-weight: 600;
  color: #172033;
}

.cell-status {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 10px;
  color: #64748b;
  font-size: 0.82rem;
}

@media (max-width: 1280px) {
  .attendance-panel {
    grid-template-columns: 210px minmax(0, 1fr);
  }

  .brand-name {
    font-size: 1.08rem;
  }

  .brand-caption {
    font-size: 0.76rem;
  }

  .stats-row {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .attendance-panel {
    grid-template-columns: 1fr;
  }

  .nav-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .topbar-card,
  .attendance-toolbar,
  .overview-top {
    flex-direction: column;
    align-items: stretch;
  }

  .search-wrap {
    max-width: none;
  }

  .topbar-tools {
    flex-wrap: wrap;
  }

  .profile-pill {
    width: 100%;
  }

  .attendance-overview {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .attendance-page {
    padding: 12px;
  }

  .attendance-card {
    padding: 18px;
  }

  .attendance-title {
    font-size: 1.85rem;
  }

  .nav-list {
    grid-template-columns: 1fr;
  }

  .stats-row {
    grid-template-columns: 1fr;
  }

  .calendar-days,
  .calendar-week {
    min-width: 760px;
  }

  .calendar-grid {
    overflow-x: auto;
  }
}

@media (max-width: 560px) {
  .attendance-shell {
    border-radius: 24px;
  }

  .attendance-panel {
    padding: 14px;
    gap: 14px;
  }

  .sidebar-card,
  .topbar-card,
  .attendance-card {
    border-radius: 20px;
  }

  .brand-name {
    font-size: 1rem;
  }

  .brand-caption {
    font-size: 0.72rem;
  }
}
</style>
