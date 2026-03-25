<template>
  <v-app>
    <v-main class="attendance-page">
      <div class="attendance-shell" :class="{ 'attendance-shell-dark': darkMode }">
        <v-navigation-drawer
          v-if="isCompactNav && mobileMenuOpen"
          v-model="mobileMenuOpen"
          temporary
          location="left"
          width="286"
          class="mobile-drawer"
          :class="{ 'mobile-drawer-dark': darkMode }"
        >
          <div class="mobile-drawer-inner">
            <div class="brand-block mobile-drawer-brand">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Coach workspace</div>
              </div>
            </div>

            <nav class="mobile-nav-list">
              <v-btn
                v-for="item in navItems"
                :key="`mobile-${item.label}`"
                :to="item.to"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/attendance' }"
                block
                @click="mobileMenuOpen = false"
              >
                <template #prepend>
                  <v-icon>{{ item.icon }}</v-icon>
                </template>
                {{ item.label }}
              </v-btn>
            </nav>

            <div class="mobile-drawer-profile">
              <v-avatar size="44">
                <img :src="avatarFor(profileSeed, profileName)" alt="Coach profile">
              </v-avatar>
              <div>
                <div class="profile-name">{{ profileName }}</div>
                <div class="profile-email">{{ profileEmail }}</div>
              </div>
            </div>

            <v-btn
              variant="outlined"
              class="mobile-logout-btn"
              prepend-icon="mdi-logout"
              @click="handleMobileLogout"
            >
              Log out
            </v-btn>
          </div>
        </v-navigation-drawer>

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
            <div class="mobile-header-card">
              <button type="button" class="mobile-menu-btn" @click="mobileMenuOpen = true">
                <v-icon size="24">mdi-menu</v-icon>
              </button>

              <div class="mobile-brand-inline">
                <div class="brand-icon mobile-brand-icon">
                  <v-icon color="white">mdi-school-outline</v-icon>
                </div>
                <div class="mobile-brand-copy">
                  <div class="brand-name">SportSystem</div>
                  <div class="brand-caption">Attendance</div>
                </div>
              </div>

              <div class="mobile-header-actions">
                <v-btn
                  icon
                  variant="text"
                  class="top-icon-btn mobile-theme-btn"
                  :class="{ 'top-icon-btn-active': darkMode }"
                  @click="darkMode = !darkMode"
                >
                  <v-icon>{{ darkMode ? 'mdi-weather-night' : 'mdi-white-balance-sunny' }}</v-icon>
                </v-btn>

                <div class="icon-badge-wrap">
                  <v-btn icon variant="text" class="top-icon-btn" @click="notificationsDialog = true">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">{{ unreadCount }}</span>
                </div>
              </div>
            </div>

            <div class="mobile-utility-card">
              <div class="mobile-profile-row">
                <div class="profile-pill mobile-profile-pill">
                  <v-avatar size="42">
                    <img :src="avatarFor(profileSeed, profileName)" alt="Coach profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="topbar-card">
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
                  <v-btn icon variant="text" class="top-icon-btn" @click="notificationsDialog = true">
                    <v-icon>mdi-bell-outline</v-icon>
                  </v-btn>
                  <span class="icon-badge">{{ unreadCount }}</span>
                </div>

                <v-btn
                  icon
                  variant="text"
                  class="top-icon-btn logout-btn"
                  @click="handleLogout"
                >
                  <v-icon>mdi-logout</v-icon>
                </v-btn>

                <div class="profile-pill">
                  <v-avatar size="48">
                    <img :src="avatarFor(profileSeed, profileName)" alt="Coach profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
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

                <v-btn
                  v-if="canManageAttendance"
                  color="primary"
                  prepend-icon="mdi-check-circle-outline"
                  @click="openMarkDialog"
                >
                  Mark attendance
                </v-btn>
              </div>

              <div class="attendance-overview">
                <div class="coach-card">
                  <v-avatar size="76" class="coach-avatar">
                    <img :src="avatarFor(overviewProfileSeed, overviewProfileName)" alt="User avatar">
                  </v-avatar>
                  <div class="coach-name">{{ overviewProfileName }}</div>
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
                      <div class="timeline-percent">{{ summary.attendance_rate ?? 0 }}%</div>
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
                      <span class="legend-dot legend-dot-blue"></span>
                      Planned
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
                        'calendar-cell-selected': day.id === focusedDate,
                        'calendar-cell-muted': day.statusType === 'no-training',
                        'calendar-cell-empty': !day.hasSessions,
                        'calendar-cell-planned': day.statusType === 'planned',
                        'calendar-cell-present': day.statusType === 'present',
                        'calendar-cell-absent': day.statusType === 'absent',
                        'calendar-cell-mixed': day.statusType === 'mixed',
                        'calendar-cell-no-training': day.statusType === 'no-training',
                        'calendar-cell-outside': !day.isCurrentMonth
                      }"
                      :style="day.cellStyle"
                    >
                      <div class="cell-date">{{ day.day }}</div>

                      <div v-if="day.hasSessions" class="cell-sessions">
                        <div
                          v-for="session in day.sessions"
                          :key="session.id"
                          class="cell-session-item"
                        >
                          <span
                            class="cell-dot"
                            :class="{
                              'cell-dot-green': session.statusType === 'present',
                              'cell-dot-red': session.statusType === 'absent',
                              'cell-dot-blue': session.statusType === 'planned' || session.statusType === 'mixed'
                            }"
                          ></span>
                          <span class="cell-session-name">{{ session.training }}</span>
                        </div>
                      </div>

                      <div v-else class="cell-status">
                        <span class="cell-dot cell-dot-gray"></span>
                        No training
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="markDialog" max-width="560">
          <v-card class="dialog-card">
            <v-card-title>Mark Attendance</v-card-title>
            <v-card-text>
              <v-select
                v-model="markForm.session_id"
                label="Session"
                :items="sessionOptions"
                item-title="label"
                item-value="id"
              />
              <v-select
                v-model="markForm.user_id"
                label="Child"
                :items="selectedSessionChildren"
                item-title="name"
                item-value="id"
              />
              <v-select
                v-model="markForm.status"
                label="Status"
                :items="attendanceStatusOptions"
              />
              <v-text-field v-model="markForm.comment" label="Comment" />
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn variant="outlined" @click="markDialog = false">Cancel</v-btn>
              <v-btn color="primary" @click="submitAttendance">Save</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <AppNotificationsDialog
          v-model="notificationsDialog"
          :dark-mode="darkMode"
          :notifications="notificationItems"
          :loading="notificationsLoading"
          @notification-click="handleNotificationClick"
        />
      </div>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppNotificationsDialog from '../components/AppNotificationsDialog.vue'
import { useNotifications } from '../composables/useNotifications'
import { useSelectedChild } from '../composables/useSelectedChild'
import { attendanceApi, sessionsApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const route = useRoute()
const darkMode = ref(false)
const notificationsDialog = ref(false)
const markDialog = ref(false)
const currentMonth = ref(new Date())
const focusedDate = ref(null)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const darkModeStorageKey = 'app-dark-mode'
const avatarFor = (seed, label = seed) => createAvatarDataUri(seed, label)
const { user } = useAuth()
const {
  items: notificationItems,
  loading: notificationsLoading,
  unreadCount,
  loadNotifications,
  markNotificationRead
} = useNotifications()
const { selectedChildId, syncSelectedChild } = useSelectedChild()
const attendanceResponse = ref({
  records: [],
  summary: {
    total_sessions: 0,
    attended_sessions: 0,
    missed_sessions: 0,
    attendance_rate: 0,
    total_training_time_minutes: 0
  }
})
const calendarSessions = ref([])
const manageableSessions = ref([])
const markForm = ref({
  session_id: null,
  user_id: null,
  status: 'present',
  comment: ''
})
const attendanceStatusOptions = ['present', 'absent']

const profileName = computed(() => {
  if (!user.value) return 'SportSystem User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const navItems = computed(() => [
  { label: 'Home', icon: 'mdi-home-outline', to: '/home' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: '/attendance' },
  ...(user.value?.role === 'parent'
    ? [{ label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }]
    : [])
])
const profileEmail = computed(() => user.value?.email ?? 'user@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)
const isParent = computed(() => user.value?.role === 'parent')
const attendanceChildren = computed(() =>
  Array.from(
    new Map(
      (attendanceResponse.value.records ?? []).map((record) => [
        record.user_id,
        {
          id: record.user_id,
          name: record.child_name,
        }
      ])
    ).values()
  )
)
const selectedAttendanceChild = computed(() =>
  attendanceChildren.value.find((child) => child.id === selectedChildId.value) ?? null
)
const overviewProfileName = computed(() =>
  isParent.value && selectedAttendanceChild.value
    ? selectedAttendanceChild.value.name
    : profileName.value
)
const overviewProfileSeed = computed(() =>
  isParent.value && selectedAttendanceChild.value
    ? `child-${selectedAttendanceChild.value.id}-${selectedAttendanceChild.value.name}`
    : profileSeed.value
)
const records = computed(() =>
  (attendanceResponse.value.records ?? []).filter((record) =>
    !isParent.value || !selectedChildId.value || record.user_id === selectedChildId.value
  )
)
const summary = computed(() => {
  const totalSessions = records.value.length
  const attendedSessions = records.value.filter((item) => item.status === 'present').length
  const missedSessions = records.value.filter((item) => item.status === 'absent').length
  const totalTrainingTimeMinutes = records.value.reduce((total, item) => {
    if (item.status !== 'present') return total

    const start = Date.parse(`1970-01-01T${item.start_time}:00`)
    const end = Date.parse(`1970-01-01T${item.end_time}:00`)
    return total + (Number.isFinite(start) && Number.isFinite(end) && end > start ? Math.round((end - start) / 60000) : 0)
  }, 0)

  return {
    total_sessions: totalSessions,
    attended_sessions: attendedSessions,
    missed_sessions: missedSessions,
    attendance_rate: totalSessions > 0 ? Math.round((attendedSessions / totalSessions) * 100) : 0,
    total_training_time_minutes: totalTrainingTimeMinutes,
  }
})
const canManageAttendance = computed(() => ['admin', 'coach'].includes(user.value?.role ?? ''))
const memberGroups = computed(() => [...new Set(records.value.map((item) => item.training))])
const scopedCalendarSessions = computed(() =>
  calendarSessions.value.filter((session) => {
    if (user.value?.role === 'child') {
      return session.students?.some((student) => student.id === user.value?.id)
    }

    if (isParent.value) {
      return !selectedChildId.value || session.students?.some((student) => student.id === selectedChildId.value)
    }

    return true
  })
)

const stats = computed(() => [
  { label: 'Total training time', value: formatMinutes(summary.value.total_training_time_minutes ?? 0) },
  { label: 'Total sessions', value: String(summary.value.total_sessions ?? 0) },
  { label: 'Attended sessions', value: String(summary.value.attended_sessions ?? 0) },
  { label: 'Missed sessions', value: String(summary.value.missed_sessions ?? 0) }
])

const timelineSegments = computed(() => {
  const totalSessions = Number(summary.value.total_sessions ?? 0)
  const attendedSessions = Number(summary.value.attended_sessions ?? 0)
  const missedSessions = Number(summary.value.missed_sessions ?? 0)

  if (!totalSessions) {
    return [
      { label: 'Attended sessions', color: '#39b980', width: '0%' },
      { label: 'Missed sessions', color: '#ef6b73', width: '0%' }
    ]
  }

  return [
    { label: 'Attended sessions', color: '#39b980', width: `${Math.round((attendedSessions / totalSessions) * 100)}%` },
    { label: 'Missed sessions', color: '#ef6b73', width: `${Math.round((missedSessions / totalSessions) * 100)}%` }
  ]
})

const timelineLabels = ['0%', '25%', '50%', '75%', '100%']

const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
const attendanceStatusBySession = computed(() =>
  records.value.reduce((map, item) => {
    if (!map[item.session_id]) {
      map[item.session_id] = []
    }

    map[item.session_id].push(item.status)
    return map
  }, {})
)

const calendarEntriesByDate = computed(() =>
  scopedCalendarSessions.value.reduce((map, session) => {
    const sessionStatuses = attendanceStatusBySession.value[session.id] ?? []
    const uniqueStatuses = [...new Set(sessionStatuses)]

    let statusType = 'planned'
    if (uniqueStatuses.length > 1) {
      statusType = 'mixed'
    } else if (uniqueStatuses[0] === 'present') {
      statusType = 'present'
    } else if (uniqueStatuses[0] === 'absent') {
      statusType = 'absent'
    }

    if (!map[session.date]) {
      map[session.date] = []
    }

    map[session.date].push({
      id: session.id,
      training: session.title,
      statusType,
    })

    return map
  }, {})
)

const currentMonthLabel = computed(() =>
  currentMonth.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric'
  })
)

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  syncDateFromRoute()
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  loadCalendarSessions()
  loadAttendance()
  loadNotifications()
})

watch(darkMode, (value) => {
  localStorage.setItem(darkModeStorageKey, String(value))
})

watch(isCompactNav, (value) => {
  if (!value) mobileMenuOpen.value = false
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

watch(notificationsDialog, (value) => {
  if (value) {
    loadNotifications(true)
  }
})

watch(() => route.query.date, () => {
  syncDateFromRoute()
})

watch(() => markForm.value.session_id, () => {
  markForm.value.user_id = selectedSessionChildren.value[0]?.id ?? null
})

watch(records, (value) => {
  if (focusedDate.value) return

  const firstRecordDate = value[0]?.date
  if (firstRecordDate) {
    currentMonth.value = new Date(`${firstRecordDate}T00:00:00`)
  }
}, { immediate: true })

watch(scopedCalendarSessions, (value) => {
  if (focusedDate.value) return
  if (records.value.length) return

  const firstSessionDate = value[0]?.date
  if (firstSessionDate) {
    currentMonth.value = new Date(`${firstSessionDate}T00:00:00`)
  }
}, { immediate: true })

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

    const iso = formatDateKey(date)
    const isCurrentMonth = date.getMonth() === currentMonth.value.getMonth()
    const sessions = calendarEntriesByDate.value[iso] ?? []
    const statusSignature = sessions.length
      ? [...new Set(sessions.map((session) => session.statusType))].sort().join(',')
      : 'no-training'
    const statusType = sessions.length > 0
      ? (statusSignature.includes(',') ? 'mixed' : sessions[0].statusType)
      : 'no-training'

    return {
      id: iso,
      day: date.getDate(),
      hasSessions: sessions.length > 0,
      sessions,
      statusType,
      cellStyle: buildCalendarCellStyle(statusSignature),
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

function syncDateFromRoute() {
  const routeDate = requestedRouteDate()
  focusedDate.value = routeDate

  if (routeDate) {
    currentMonth.value = new Date(`${routeDate}T00:00:00`)
  }
}

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function requestedRouteDate() {
  const value = typeof route.query.date === 'string' ? route.query.date : ''
  return /^\d{4}-\d{2}-\d{2}$/.test(value) ? value : null
}

const sessionOptions = computed(() =>
  manageableSessions.value.map((session) => ({
    id: session.id,
    label: `${session.title} · ${session.date} · ${session.start}-${session.end}`
  }))
)

const selectedSessionChildren = computed(() => {
  const selectedSession = manageableSessions.value.find((session) => session.id === markForm.value.session_id)
  return selectedSession?.students ?? []
})

async function loadAttendance() {
  const data = await attendanceApi.list()
  attendanceResponse.value = data
  if (isParent.value) {
    syncSelectedChild((data?.records ?? []).map((record) => record.user_id), { preserveExisting: true })
  }
}

async function loadCalendarSessions() {
  const sessions = await sessionsApi.list()
  calendarSessions.value = sessions

  if (canManageAttendance.value) {
    manageableSessions.value = sessions
  }
}

function openMarkDialog() {
  markForm.value = {
    session_id: manageableSessions.value[0]?.id ?? null,
    user_id: manageableSessions.value[0]?.students?.[0]?.id ?? null,
    status: 'present',
    comment: ''
  }
  markDialog.value = true
}

async function submitAttendance() {
  await attendanceApi.create({
    session_id: markForm.value.session_id,
    user_id: markForm.value.user_id,
    status: markForm.value.status,
    comment: markForm.value.comment
  })

  await loadAttendance()
  markDialog.value = false
}

async function handleNotificationClick(item) {
  if (item?.unread) {
    await markNotificationRead(item.id)
  }
}

async function handleLogout() {
  await logout()
  router.push('/login')
}

async function handleMobileLogout() {
  mobileMenuOpen.value = false
  await handleLogout()
}

function formatMinutes(totalMinutes) {
  const hours = Math.floor(totalMinutes / 60)
  const minutes = totalMinutes % 60
  return `${hours}h ${String(minutes).padStart(2, '0')}m`
}

function buildCalendarCellStyle(statusSignature) {
  const styles = {
    present: {
      '--calendar-cell-fill': 'rgba(87, 214, 156, 0.16)',
      '--calendar-cell-border': 'rgba(57, 185, 128, 0.22)',
      '--calendar-cell-shadow': '0 12px 24px rgba(57, 185, 128, 0.12)',
    },
    absent: {
      '--calendar-cell-fill': 'rgba(255, 133, 140, 0.15)',
      '--calendar-cell-border': 'rgba(239, 107, 115, 0.22)',
      '--calendar-cell-shadow': '0 12px 24px rgba(239, 107, 115, 0.1)',
    },
    planned: {
      '--calendar-cell-fill': 'rgba(89, 133, 255, 0.15)',
      '--calendar-cell-border': 'rgba(89, 133, 255, 0.24)',
      '--calendar-cell-shadow': '0 12px 24px rgba(89, 133, 255, 0.12)',
    },
    'absent,present': {
      '--calendar-cell-fill': 'linear-gradient(135deg, rgba(87, 214, 156, 0.16) 0%, rgba(87, 214, 156, 0.16) 49%, rgba(255, 133, 140, 0.15) 51%, rgba(255, 133, 140, 0.15) 100%)',
      '--calendar-cell-border': 'rgba(171, 180, 202, 0.26)',
      '--calendar-cell-shadow': '0 12px 24px rgba(148, 163, 184, 0.12)',
    },
    'planned,present': {
      '--calendar-cell-fill': 'linear-gradient(135deg, rgba(89, 133, 255, 0.15) 0%, rgba(89, 133, 255, 0.15) 49%, rgba(87, 214, 156, 0.16) 51%, rgba(87, 214, 156, 0.16) 100%)',
      '--calendar-cell-border': 'rgba(140, 171, 232, 0.28)',
      '--calendar-cell-shadow': '0 12px 24px rgba(120, 156, 236, 0.12)',
    },
    'absent,planned': {
      '--calendar-cell-fill': 'linear-gradient(135deg, rgba(89, 133, 255, 0.15) 0%, rgba(89, 133, 255, 0.15) 49%, rgba(255, 133, 140, 0.15) 51%, rgba(255, 133, 140, 0.15) 100%)',
      '--calendar-cell-border': 'rgba(156, 161, 226, 0.28)',
      '--calendar-cell-shadow': '0 12px 24px rgba(132, 145, 223, 0.12)',
    },
    'absent,planned,present': {
      '--calendar-cell-fill': 'linear-gradient(135deg, rgba(89, 133, 255, 0.15) 0%, rgba(89, 133, 255, 0.15) 33%, rgba(87, 214, 156, 0.16) 33%, rgba(87, 214, 156, 0.16) 66%, rgba(255, 133, 140, 0.15) 66%, rgba(255, 133, 140, 0.15) 100%)',
      '--calendar-cell-border': 'rgba(171, 180, 202, 0.3)',
      '--calendar-cell-shadow': '0 12px 24px rgba(148, 163, 184, 0.14)',
    },
    'no-training': {
      '--calendar-cell-fill': 'rgba(203, 213, 225, 0.16)',
      '--calendar-cell-border': 'rgba(148, 163, 184, 0.2)',
      '--calendar-cell-shadow': '0 12px 24px rgba(148, 163, 184, 0.08)',
    },
  }

  return styles[statusSignature] ?? styles['no-training']
}

function formatDateKey(date) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
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

.attendance-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.attendance-panel {
  display: grid;
  grid-template-columns: 232px minmax(0, 1fr);
  gap: 22px;
  padding: 22px;
}

.mobile-header-card,
.mobile-utility-card {
  display: none;
}

.mobile-drawer-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 18px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.98), rgba(238, 245, 255, 0.96));
}

.mobile-drawer :deep(.v-navigation-drawer__scrim),
.mobile-drawer :deep(.v-navigation-drawer__content),
.mobile-drawer :deep(.v-navigation-drawer__prepend),
.mobile-drawer :deep(.v-navigation-drawer__append) {
  background: transparent;
}

.mobile-drawer-dark {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.98)) !important;
  color: #eef4ff;
}

.mobile-drawer-dark :deep(.v-navigation-drawer__content),
.mobile-drawer-dark .mobile-drawer-inner {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.98));
}

.mobile-drawer-brand {
  margin-bottom: 20px;
}

.mobile-nav-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.mobile-drawer-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: auto;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
}

.mobile-logout-btn {
  justify-content: flex-start;
  margin-top: 12px;
  min-height: 56px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
}

.attendance-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.attendance-shell-dark .mobile-logout-btn {
  color: #eef4ff;
}

.sidebar-card {
  display: flex;
  flex-direction: column;
  padding: 18px;
  border-radius: 28px;
  background: rgba(245, 250, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.attendance-shell-dark .sidebar-card {
  background: rgba(18, 27, 43, 0.88);
  border-color: rgba(74, 92, 126, 0.42);
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

.attendance-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.attendance-shell-dark .brand-caption {
  color: #94a6c4;
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

.attendance-shell-dark .nav-item {
  color: #d8e2f2;
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

.mobile-header-card {
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.attendance-shell-dark .mobile-header-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-menu-btn {
  display: grid;
  place-items: center;
  width: 46px;
  height: 46px;
  padding: 0;
  border: 1px solid rgba(223, 231, 243, 0.92);
  border-radius: 14px;
  color: #111827;
  background: rgba(255, 255, 255, 0.92);
  cursor: pointer;
  flex-shrink: 0;
}

.attendance-shell-dark .mobile-menu-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-brand-inline {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 1;
}

.mobile-brand-icon {
  width: 44px;
  height: 44px;
  border-radius: 14px;
}

.mobile-brand-copy {
  min-width: 0;
}

.mobile-brand-copy .brand-name,
.mobile-brand-copy .brand-caption {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.mobile-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.mobile-theme-btn {
  color: #111827;
}

.mobile-utility-card {
  flex-direction: column;
  gap: 0;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.attendance-shell-dark .mobile-utility-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-profile-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mobile-profile-pill {
  flex: 1;
  min-width: 0;
  min-height: 56px;
  padding: 10px 12px;
}

.profile-pill > div {
  min-width: 0;
}

.topbar-card {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 18px;
  padding: 18px 20px;
  border-radius: 26px;
  background: rgba(255, 255, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.6);
}

.attendance-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
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

.attendance-shell-dark .top-icon-btn {
  color: #dce6f7;
  background: rgba(18, 27, 43, 0.92);
  border-color: rgba(74, 92, 126, 0.46);
}

.logout-btn {
  color: #111827;
}

.attendance-shell-dark .logout-btn {
  color: #dce6f7;
}

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
}

.attendance-shell-dark .top-icon-btn-active {
  color: #7fbcff;
  background: rgba(22, 43, 76, 0.96);
  border-color: rgba(82, 156, 255, 0.44);
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

.attendance-shell-dark .profile-pill {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.attendance-shell-dark .profile-name {
  color: #f2f7ff;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.attendance-shell-dark .profile-email {
  color: #93a5c3;
}

.attendance-card {
  padding: 28px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.attendance-shell-dark .attendance-card {
  background: rgba(22, 31, 48, 0.72);
  border-color: rgba(74, 92, 126, 0.42);
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

.attendance-shell-dark .attendance-title {
  color: #f3f7ff;
}

.attendance-subtitle {
  margin-top: 10px;
  color: #66748a;
  font-size: 1rem;
}

.attendance-shell-dark .attendance-subtitle {
  color: #8fa3c1;
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

.attendance-shell-dark .calendar-card,
.attendance-shell-dark .stat-card,
.attendance-shell-dark .timeline-wrap,
.attendance-shell-dark .month-switcher {
  background: rgba(18, 27, 43, 0.9);
  border-color: rgba(74, 92, 126, 0.42);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.26);
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

.attendance-shell-dark .stat-value,
.attendance-shell-dark .timeline-title,
.attendance-shell-dark .timeline-percent,
.attendance-shell-dark .toolbar-section-label,
.attendance-shell-dark .calendar-title,
.attendance-shell-dark .cell-date {
  color: #eef4ff;
}

.stat-label {
  margin-top: 6px;
  color: #7b8798;
}

.attendance-shell-dark .stat-label,
.attendance-shell-dark .timeline-caption,
.attendance-shell-dark .toolbar-meta,
.attendance-shell-dark .toolbar-date,
.attendance-shell-dark .legend-item,
.attendance-shell-dark .cell-status,
.attendance-shell-dark .day-name {
  color: #8fa3c1;
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

.legend-dot-blue,
.cell-dot-blue {
  background: #5985ff;
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

.attendance-shell-dark .calendar-cell {
  border-top-color: rgba(63, 80, 114, 0.58);
}

.calendar-cell::before {
  content: '';
  position: absolute;
  inset: 6px;
  border-radius: 18px;
  background: var(--calendar-cell-fill, rgba(255, 255, 255, 0.44));
  border: 1px solid var(--calendar-cell-border, rgba(255, 255, 255, 0.48));
  box-shadow: var(--calendar-cell-shadow, 0 10px 22px rgba(148, 163, 184, 0.08));
  backdrop-filter: blur(8px);
  opacity: 0;
  transition: opacity 0.2s ease, background 0.2s ease, border-color 0.2s ease;
  pointer-events: none;
}

.attendance-shell-dark .calendar-cell::before {
  background: var(--calendar-cell-fill, rgba(12, 19, 32, 0.46));
  border-color: var(--calendar-cell-border, rgba(63, 80, 114, 0.4));
  box-shadow: var(--calendar-cell-shadow, 0 10px 22px rgba(4, 10, 24, 0.18));
}

.calendar-cell > * {
  position: relative;
  z-index: 1;
}

.calendar-cell-selected::before {
  opacity: 1;
  background: linear-gradient(180deg, rgba(67, 120, 255, 0.14), rgba(67, 120, 255, 0.08));
  border-color: rgba(67, 120, 255, 0.48);
  box-shadow:
    0 0 0 2px rgba(67, 120, 255, 0.14),
    0 12px 24px rgba(67, 120, 255, 0.12);
}

.attendance-shell-dark .calendar-cell-selected::before {
  background: linear-gradient(180deg, rgba(67, 120, 255, 0.2), rgba(67, 120, 255, 0.1));
  border-color: rgba(103, 151, 255, 0.58);
  box-shadow:
    0 0 0 2px rgba(67, 120, 255, 0.18),
    0 12px 24px rgba(12, 24, 56, 0.3);
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

.calendar-cell-planned {
  background: transparent;
}

.calendar-cell-absent {
  background: transparent;
}

.calendar-cell-mixed {
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
}

.calendar-cell-planned::before {
  opacity: 1;
}

.calendar-cell-absent::before {
  opacity: 1;
}

.calendar-cell-mixed::before {
  opacity: 1;
}

.calendar-cell-no-training::before {
  opacity: 1;
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

.cell-sessions {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: 10px;
}

.cell-session-item {
  display: flex;
  align-items: center;
  gap: 7px;
  min-width: 0;
  color: #64748b;
  font-size: 0.78rem;
  line-height: 1.25;
}

.attendance-shell-dark .cell-session-item,
.attendance-shell-dark .cell-status {
  color: #a8b7cf;
}

.cell-session-name {
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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
  .attendance-page {
    padding: 16px;
  }

  .attendance-shell {
    width: 100%;
    max-width: none;
    overflow: hidden;
    border-radius: 28px;
  }

  .attendance-panel {
    grid-template-columns: 1fr;
    padding: 18px;
    gap: 18px;
  }

  .sidebar-card,
  .topbar-card {
    display: none;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: flex;
  }

  .attendance-toolbar,
  .overview-top {
    flex-direction: column;
    align-items: stretch;
  }

  .attendance-card {
    padding: 24px;
  }

  .toolbar-meta {
    width: 100%;
    justify-content: flex-start;
  }

  .month-switcher {
    width: 100%;
    justify-content: space-between;
  }

  .attendance-overview {
    grid-template-columns: 1fr;
  }

  .coach-card {
    align-items: flex-start;
    text-align: left;
    padding: 22px;
  }

  .coach-groups {
    justify-content: flex-start;
  }

  .timeline-head,
  .calendar-header {
    flex-direction: column;
    align-items: stretch;
  }

  .calendar-grid {
    overflow: visible;
    padding-bottom: 0;
  }

  .calendar-days,
  .calendar-week {
    min-width: 0;
  }

  .day-name {
    padding: 0 6px 8px;
    font-size: 0.8rem;
  }

  .calendar-cell {
    min-height: 88px;
    padding: 8px 6px;
  }

  .calendar-cell::before {
    inset: 4px;
    border-radius: 16px;
  }

  .cell-date {
    font-size: 0.88rem;
  }

  .cell-status {
    margin-top: 8px;
    font-size: 0.72rem;
    line-height: 1.2;
    gap: 5px;
  }
}

@media (max-width: 768px) {
  .attendance-page {
    padding: 12px;
  }

  .attendance-shell {
    border-radius: 24px;
  }

  .attendance-panel {
    padding: 14px;
    gap: 14px;
  }

  .mobile-header-card,
  .mobile-utility-card,
  .attendance-card,
  .calendar-card,
  .coach-card,
  .stat-card,
  .timeline-wrap {
    border-radius: 20px;
  }

  .attendance-card {
    padding: 18px;
  }

  .attendance-title {
    font-size: 1.85rem;
  }

  .attendance-subtitle {
    font-size: 0.95rem;
  }

  .stats-row {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }

  .timeline-percent {
    font-size: 1.55rem;
  }

  .toolbar-date {
    min-width: 0;
    font-size: 0.95rem;
  }

  .calendar-card {
    padding: 16px;
  }

  .calendar-title {
    font-size: 1rem;
  }

  .legend-item {
    font-size: 0.84rem;
  }

  .calendar-days,
  .calendar-week {
    min-width: 620px;
  }

  .calendar-grid {
    overflow-x: auto;
    overflow-y: hidden;
    padding-bottom: 4px;
  }

  .day-name {
    padding: 0 4px 6px;
    font-size: 0.72rem;
  }

  .calendar-cell {
    min-height: 74px;
    padding: 6px 5px;
  }

  .calendar-cell::before {
    inset: 3px;
    border-radius: 13px;
  }

  .cell-date {
    font-size: 0.78rem;
  }

  .cell-status {
    margin-top: 6px;
    gap: 4px;
    font-size: 0.62rem;
    line-height: 1.15;
  }
}

@media (max-width: 560px) {
  .attendance-panel {
    padding: 12px;
    gap: 12px;
  }

  .mobile-header-card,
  .mobile-utility-card {
    padding: 12px 14px;
  }

  .mobile-menu-btn,
  .top-icon-btn {
    width: 44px;
    height: 44px;
  }

  .mobile-brand-icon {
    width: 40px;
    height: 40px;
    border-radius: 13px;
  }

  .mobile-brand-copy .brand-name {
    font-size: 0.96rem;
  }

  .mobile-brand-copy .brand-caption {
    font-size: 0.72rem;
  }

  .mobile-profile-pill {
    min-height: 52px;
    padding: 8px 10px;
  }

  .profile-name {
    font-size: 0.92rem;
  }

  .profile-email {
    font-size: 0.82rem;
  }

  .attendance-card {
    padding: 16px;
  }

  .attendance-title {
    font-size: 1.65rem;
  }

  .coach-card {
    padding: 18px;
  }

  .coach-avatar {
    width: 68px !important;
    height: 68px !important;
  }

  .coach-name {
    font-size: 1rem;
  }

  .coach-group-pill {
    font-size: 0.76rem;
    padding: 6px 9px;
  }

  .stat-card,
  .timeline-wrap,
  .calendar-card {
    padding: 14px;
  }

  .stats-row {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .stat-value {
    font-size: 1.3rem;
  }

  .stat-label,
  .timeline-caption,
  .cell-status {
    font-size: 0.8rem;
  }

  .month-switcher {
    padding: 6px;
    border-radius: 16px;
  }

  .month-nav-btn {
    width: 34px;
    height: 34px;
  }

  .toolbar-date {
    font-size: 0.88rem;
  }

  .day-name {
    font-size: 0.72rem;
    padding: 0 4px 6px;
  }

  .calendar-cell {
    min-height: 64px;
    padding: 5px 4px;
  }

  .calendar-cell::before {
    inset: 2px;
    border-radius: 11px;
  }

  .cell-date {
    font-size: 0.72rem;
  }

  .cell-status {
    margin-top: 5px;
    font-size: 0.56rem;
    gap: 3px;
  }
}
</style>
