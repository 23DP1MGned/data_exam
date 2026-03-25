<template>
  <v-app>
    <v-main class="coach-attendance-page">
      <div class="coach-attendance-shell" :class="{ 'coach-attendance-shell-dark': darkMode }">
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
                :class="{ 'nav-item-active': item.to === '/coach-attendance' }"
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

        <div class="coach-attendance-panel">
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
                :class="{ 'nav-item-active': item.to === '/coach-attendance' }"
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
                  <div class="brand-caption">Coach Attendance</div>
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

            <div class="coach-page-card">
              <div class="coach-page-header">
                <div>
                  <h1 class="coach-page-title">Coach Attendance</h1>
                  <div class="coach-page-subtitle">
                    Pick a group, open a day and mark attendance for each real session instance.
                  </div>
                </div>

                <v-menu
                  :disabled="coachGroups.length <= 1"
                  location="bottom end"
                  offset="12"
                >
                  <template #activator="{ props }">
                    <button
                      type="button"
                      class="group-selector"
                      :class="{ 'group-selector-static': coachGroups.length <= 1 }"
                      v-bind="props"
                    >
                      <v-avatar size="44" class="group-selector-avatar">
                        <img
                          :src="avatarFor(`coach-group-${selectedGroup?.id ?? 'none'}`, selectedGroup?.section ?? 'Group')"
                          :alt="selectedGroup?.section ?? 'Selected group'"
                        >
                      </v-avatar>
                      <div class="group-selector-copy">
                        <div class="group-selector-label">Selected group</div>
                        <div class="group-selector-name">{{ selectedGroup?.section || 'No groups assigned' }}</div>
                      </div>
                      <v-icon v-if="coachGroups.length > 1" size="22" class="group-selector-chevron">
                        mdi-chevron-down
                      </v-icon>
                    </button>
                  </template>

                  <v-list class="group-selector-menu">
                    <v-list-item
                      v-for="group in coachGroups"
                      :key="group.id"
                      @click="setSelectedCoachGroupId(group.id)"
                    >
                      <v-list-item-title>{{ group.section }}</v-list-item-title>
                      <v-list-item-subtitle>
                        {{ group.age_category ? `Age ${group.age_category}` : 'No age category' }}
                      </v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>

              <div v-if="errorMessage" class="state-wrap">
                <v-alert type="error" variant="tonal" border="start">
                  {{ errorMessage }}
                </v-alert>
              </div>

              <div v-else-if="loading" class="state-wrap loading-state">
                <v-progress-circular indeterminate color="primary" size="28" />
                <span>Loading coach attendance...</span>
              </div>

              <template v-else>
                <div class="coach-stats-grid">
                  <article v-for="item in overviewStats" :key="item.label" class="coach-stat-card">
                    <div class="summary-label">{{ item.label }}</div>
                    <div class="summary-value">{{ item.value }}</div>
                  </article>
                </div>

                <div class="workbench-grid">
                  <section class="workbench-card sessions-card">
                    <div class="section-head">
                      <div>
                        <div class="section-title">Sessions on {{ selectedDateLabel }}</div>
                        <div class="section-caption">
                          Click a day in the calendar to switch the training selector below.
                        </div>
                      </div>
                    </div>

                    <div v-if="selectedDaySessions.length" class="day-session-list">
                      <button
                        v-for="session in selectedDaySessions"
                        :key="session.id"
                        type="button"
                        class="day-session-pill"
                        :class="{
                          'day-session-pill-active': session.id === selectedSessionId,
                          'day-session-pill-completed': sessionMetaById[session.id]?.state === 'completed',
                          'day-session-pill-pending': sessionMetaById[session.id]?.state === 'pending',
                          'day-session-pill-cancelled': sessionMetaById[session.id]?.state === 'cancelled'
                        }"
                        @click="selectedSessionId = session.id"
                      >
                        <div class="day-session-title">{{ session.title }}</div>
                        <div class="day-session-meta">{{ session.start }}-{{ session.end }}</div>
                      </button>
                    </div>
                    <div v-else class="empty-block">
                      No trainings scheduled for this group on the selected day.
                    </div>

                    <template v-if="selectedSession">
                      <div class="selected-session-summary">
                        <div>
                          <div class="selected-session-title">{{ selectedSession.title }}</div>
                          <div class="selected-session-meta">
                            {{ selectedSession.group }} · {{ selectedSession.start }}-{{ selectedSession.end }}
                          </div>
                        </div>
                        <div class="selected-session-progress">
                          <div class="summary-label">Progress</div>
                          <div class="summary-value small-value">
                            {{ selectedSessionMeta.markedCount }}/{{ selectedSession.students.length }}
                          </div>
                        </div>
                      </div>
                    </template>
                  </section>

                  <section class="workbench-card roster-card">
                    <div class="section-head">
                      <div>
                        <div class="section-title">Students roster</div>
                        <div class="section-caption">Mark the selected training.</div>
                      </div>
                      <div class="roster-head-tools">
                        <div v-if="selectedSession" class="section-chip">{{ selectedSession.students.length }}</div>
                        <div class="roster-search-shell">
                          <v-icon size="18" class="roster-search-icon">mdi-magnify</v-icon>
                          <v-text-field
                            v-model="rosterSearch"
                            placeholder="Search student"
                            variant="plain"
                            hide-details
                            density="comfortable"
                            class="roster-search-field"
                          />
                        </div>
                      </div>
                    </div>

                    <template v-if="selectedSession">
                      <div v-if="selectedSession.status === 'cancelled'" class="state-wrap roster-state">
                        This session is cancelled. Attendance marking is disabled.
                      </div>

                      <template v-else>
                        <v-alert
                          v-if="saveError"
                          type="error"
                          variant="tonal"
                          border="start"
                          class="roster-alert"
                        >
                          {{ saveError }}
                        </v-alert>

                        <v-alert
                          v-if="saveSuccess"
                          type="success"
                          variant="tonal"
                          border="start"
                          class="roster-alert"
                        >
                          {{ saveSuccess }}
                        </v-alert>

                        <div class="roster-toolbar">
                          <v-btn
                            color="primary"
                            variant="outlined"
                            prepend-icon="mdi-check-all"
                            @click="markAllPresent"
                          >
                            Mark all present
                          </v-btn>
                          <v-btn
                            color="primary"
                            :loading="saving"
                            prepend-icon="mdi-content-save-outline"
                            @click="saveAttendance"
                          >
                            Save attendance
                          </v-btn>
                        </div>

                        <div class="roster-scroll">
                          <div class="roster-list">
                            <article
                              v-for="student in filteredRosterStudents"
                              :key="student.id"
                              class="roster-item"
                            >
                              <div class="roster-student">
                                <v-avatar size="42">
                                  <img :src="avatarFor(`student-${student.id}`, student.name)" :alt="student.name">
                                </v-avatar>
                                <div>
                                  <div class="roster-student-name">{{ student.name }}</div>
                                  <div class="roster-student-meta">
                                    {{ selectedGroup?.section }}
                                  </div>
                                </div>
                              </div>

                              <div class="status-toggle">
                                <button
                                  type="button"
                                  class="status-option"
                                  :class="{ 'status-option-active status-option-present': sessionDraft[student.id] === 'present' }"
                                  @click="setStudentStatus(student.id, 'present')"
                                >
                                  Present
                                </button>
                                <button
                                  type="button"
                                  class="status-option"
                                  :class="{ 'status-option-active status-option-absent': sessionDraft[student.id] === 'absent' }"
                                  @click="setStudentStatus(student.id, 'absent')"
                                >
                                  Absent
                                </button>
                              </div>
                            </article>
                          </div>
                        </div>
                        <div v-if="!filteredRosterStudents.length" class="empty-block roster-empty">
                          No students found for the current search.
                        </div>
                      </template>
                    </template>
                    <div v-else class="empty-block">
                      Select a training on the left to open the full roster.
                    </div>
                  </section>
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
                    <div class="calendar-title">Group attendance calendar</div>
                    <div class="calendar-legend">
                      <span class="legend-item">
                        <span class="legend-dot legend-dot-green"></span>
                        Marked
                      </span>
                      <span class="legend-item">
                      <span class="legend-dot legend-dot-blue"></span>
                        Planned
                      </span>
                      <span class="legend-item">
                        <span class="legend-dot legend-dot-gray"></span>
                        Cancelled / no sessions
                      </span>
                    </div>
                  </div>

                  <div class="calendar-grid">
                    <div class="calendar-days">
                      <div v-for="day in weekDays" :key="day" class="day-name">{{ day }}</div>
                    </div>

                    <div v-for="week in calendarWeeks" :key="week.id" class="calendar-week">
                      <button
                        v-for="day in week.days"
                        :key="day.id"
                        type="button"
                        class="calendar-cell"
                        :class="{
                          'calendar-cell-empty': !day.hasSessions,
                          'calendar-cell-selected': day.iso === selectedDate,
                          'calendar-cell-outside': !day.isCurrentMonth
                        }"
                        :style="day.cellStyle"
                        @click="selectDate(day.iso)"
                      >
                        <div class="cell-date">{{ day.day }}</div>

                        <div v-if="day.hasSessions" class="cell-sessions">
                          <div
                            v-for="session in day.sessions.slice(0, 2)"
                            :key="session.id"
                            class="cell-session-item"
                          >
                            <span class="cell-dot" :class="session.dotClass"></span>
                            <span class="cell-session-name">{{ session.title }}</span>
                          </div>
                          <div v-if="day.sessions.length > 2" class="cell-more">
                            +{{ day.sessions.length - 2 }} more
                          </div>
                        </div>

                        <div v-else class="cell-status">
                          <span class="cell-dot cell-dot-gray"></span>
                          No sessions
                        </div>
                      </button>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </section>
        </div>

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
import { useRouter } from 'vue-router'
import AppNotificationsDialog from '../components/AppNotificationsDialog.vue'
import { useNotifications } from '../composables/useNotifications'
import { useSelectedCoachGroup } from '../composables/useSelectedCoachGroup'
import { attendanceApi, groupsApi, sessionsApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const darkMode = ref(false)
const notificationsDialog = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const loading = ref(false)
const saving = ref(false)
const errorMessage = ref('')
const saveError = ref('')
const saveSuccess = ref('')
const rosterSearch = ref('')
const currentMonth = ref(new Date())
const selectedDate = ref(formatDateKey(new Date()))
const selectedSessionId = ref(null)
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
const {
  selectedCoachGroupId,
  setSelectedCoachGroupId,
  syncSelectedCoachGroup
} = useSelectedCoachGroup()

const coachGroups = ref([])
const sessions = ref([])
const attendanceRecords = ref([])
const sessionDrafts = ref({})

const profileName = computed(() => {
  if (!user.value) return 'Coach User'
  return `${user.value.name} ${user.value.surname}`.trim()
})

const profileEmail = computed(() => user.value?.email ?? 'coach@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)

const navItems = [
  { label: 'Home', icon: 'mdi-home-outline', to: '/home' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: '/coach-attendance' }
]

const selectedGroup = computed(() =>
  coachGroups.value.find((group) => group.id === selectedCoachGroupId.value) ?? null
)

const selectedGroupSessions = computed(() =>
  [...sessions.value].sort((first, second) =>
    `${first.date} ${first.start}`.localeCompare(`${second.date} ${second.start}`)
  )
)

const attendanceBySessionUser = computed(() =>
  attendanceRecords.value.reduce((map, record) => {
    if (!map[record.session_id]) {
      map[record.session_id] = {}
    }

    map[record.session_id][record.user_id] = record
    return map
  }, {})
)

const sessionMetaById = computed(() =>
  selectedGroupSessions.value.reduce((map, session) => {
    const sessionRecords = attendanceBySessionUser.value[session.id] ?? {}
    const totalStudents = session.students.length
    const markedCount = session.students.filter((student) => sessionRecords[student.id]).length
    const presentCount = session.students.filter((student) => sessionRecords[student.id]?.status === 'present').length
    const isCancelled = session.status === 'cancelled'
    const isMarked = totalStudents > 0 && markedCount === totalStudents
    const isPastOrToday = session.date <= todayIso()

    let state = 'empty'
    if (isCancelled) {
      state = 'cancelled'
    } else if (isMarked) {
      state = 'completed'
    } else if (isPastOrToday) {
      state = 'pending'
    } else {
      state = 'scheduled'
    }

    map[session.id] = {
      markedCount,
      presentCount,
      totalStudents,
      state
    }

    return map
  }, {})
)

const requiresAttendanceSessions = computed(() =>
  selectedGroupSessions.value.filter((session) => {
    const meta = sessionMetaById.value[session.id]
    return session.status !== 'cancelled'
      && session.date <= todayIso()
      && meta
      && meta.markedCount < meta.totalStudents
  })
)

const selectedDaySessions = computed(() =>
  selectedGroupSessions.value.filter((session) => session.date === selectedDate.value)
)

const selectedSession = computed(() =>
  selectedDaySessions.value.find((session) => session.id === selectedSessionId.value) ?? selectedDaySessions.value[0] ?? null
)

const selectedSessionMeta = computed(() => {
  if (!selectedSession.value) {
    return {
      markedCount: 0,
      totalStudents: 0
    }
  }

  return sessionMetaById.value[selectedSession.value.id] ?? {
    markedCount: 0,
    totalStudents: selectedSession.value.students.length
  }
})

const sessionDraft = computed(() =>
  selectedSession.value
    ? (sessionDrafts.value[selectedSession.value.id] ?? {})
    : {}
)

const filteredRosterStudents = computed(() => {
  if (!selectedSession.value) return []

  const sortedStudents = [...selectedSession.value.students].sort((first, second) =>
    first.name.localeCompare(second.name, 'en', { sensitivity: 'base' })
  )

  const query = rosterSearch.value.trim().toLowerCase()
  if (!query) return sortedStudents

  return sortedStudents.filter((student) =>
    student.name.toLowerCase().includes(query)
  )
})

const overviewStats = computed(() => {
  const markedRecords = attendanceRecords.value.length
  const presentRecords = attendanceRecords.value.filter((record) => record.status === 'present').length
  const averageAttendanceRate = markedRecords ? Math.round((presentRecords / markedRecords) * 100) : 0
  const sessionsThisMonth = selectedGroupSessions.value.filter((session) => {
    const sessionDate = new Date(`${session.date}T00:00:00`)
    return sessionDate.getMonth() === currentMonth.value.getMonth()
      && sessionDate.getFullYear() === currentMonth.value.getFullYear()
      && session.status !== 'cancelled'
  }).length

  return [
    { label: 'Average attendance rate', value: `${averageAttendanceRate}%` },
    { label: 'Sessions requiring attendance', value: String(requiresAttendanceSessions.value.length) },
    { label: 'Sessions this month', value: String(sessionsThisMonth) },
    { label: 'Marked attendance records', value: String(markedRecords) }
  ]
})

const currentMonthLabel = computed(() =>
  currentMonth.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric'
  })
)

const selectedDateLabel = computed(() => formatPrettyDate(selectedDate.value))

const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

const calendarEntriesByDate = computed(() =>
  selectedGroupSessions.value.reduce((map, session) => {
    const meta = sessionMetaById.value[session.id]

    if (!map[session.date]) {
      map[session.date] = []
    }

    map[session.date].push({
      id: session.id,
      title: session.title,
      state: meta?.state ?? 'scheduled',
      dotClass: getSessionDotClass(meta?.state ?? 'scheduled')
    })

    return map
  }, {})
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

    const iso = formatDateKey(date)
    const sessionsForDay = calendarEntriesByDate.value[iso] ?? []
    const stateSignature = buildDayStateSignature(sessionsForDay.map((session) => session.state))

    return {
      id: iso,
      iso,
      day: date.getDate(),
      isCurrentMonth: date.getMonth() === currentMonth.value.getMonth(),
      hasSessions: sessionsForDay.length > 0,
      sessions: sessionsForDay,
      cellStyle: buildCalendarCellStyle(stateSignature)
    }
  })

  return Array.from({ length: days.length / 7 }, (_, index) => ({
    id: index + 1,
    days: days.slice(index * 7, index * 7 + 7)
  }))
})

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  initializePage()
  loadNotifications()
})

watch(darkMode, (value) => {
  localStorage.setItem(darkModeStorageKey, String(value))
})

watch(isCompactNav, (value) => {
  if (!value) mobileMenuOpen.value = false
})

watch(notificationsDialog, (value) => {
  if (value) {
    loadNotifications(true)
  }
})

watch(selectedCoachGroupId, async (value) => {
  if (!value) return
  await loadGroupAttendance(value)
})

watch(selectedDaySessions, (value) => {
  if (!value.length) {
    selectedSessionId.value = null
    return
  }

  if (!value.some((session) => session.id === selectedSessionId.value)) {
    selectedSessionId.value = value[0].id
  }
}, { immediate: true })

watch(selectedSession, (session) => {
  if (!session) return
  ensureSessionDraft(session)
  saveError.value = ''
  saveSuccess.value = ''
}, { immediate: true })

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

async function initializePage() {
  loading.value = true
  errorMessage.value = ''

  try {
    const groups = await groupsApi.list()
    coachGroups.value = groups
    const resolvedGroupId = syncSelectedCoachGroup(groups.map((group) => group.id))

    if (!resolvedGroupId && groups[0]?.id) {
      setSelectedCoachGroupId(groups[0].id)
      await loadGroupAttendance(groups[0].id)
      return
    }

    if (resolvedGroupId) {
      await loadGroupAttendance(resolvedGroupId)
    }
  } catch (error) {
    errorMessage.value = extractErrorMessage(error, 'Failed to load coach groups.')
  } finally {
    loading.value = false
  }
}

async function loadGroupAttendance(groupId) {
  loading.value = true
  errorMessage.value = ''
  saveError.value = ''
  saveSuccess.value = ''

  try {
    const [groupSessions, attendanceData] = await Promise.all([
      sessionsApi.list({ group_id: groupId }),
      attendanceApi.list({ group_id: groupId })
    ])

    sessions.value = groupSessions
    attendanceRecords.value = attendanceData.records ?? []
    sessionDrafts.value = {}
    syncSelectedDate(groupSessions)
  } catch (error) {
    errorMessage.value = extractErrorMessage(error, 'Failed to load attendance data.')
  } finally {
    loading.value = false
  }
}

function syncSelectedDate(groupSessions) {
  if (!groupSessions.length) {
    selectedDate.value = todayIso()
    selectedSessionId.value = null
    return
  }

  const sessionDates = groupSessions.map((session) => session.date).sort()
  if (!sessionDates.includes(selectedDate.value)) {
    selectedDate.value = sessionDates.find((date) => date >= todayIso()) ?? sessionDates[0]
  }

  currentMonth.value = new Date(`${selectedDate.value}T00:00:00`)
}

function ensureSessionDraft(session) {
  if (sessionDrafts.value[session.id]) return

  const existingRecords = attendanceBySessionUser.value[session.id] ?? {}
  sessionDrafts.value = {
    ...sessionDrafts.value,
    [session.id]: session.students.reduce((draft, student) => {
      draft[student.id] = existingRecords[student.id]?.status ?? null
      return draft
    }, {})
  }
}

function setStudentStatus(studentId, status) {
  if (!selectedSession.value) return

  ensureSessionDraft(selectedSession.value)
  sessionDrafts.value = {
    ...sessionDrafts.value,
    [selectedSession.value.id]: {
      ...sessionDrafts.value[selectedSession.value.id],
      [studentId]: status
    }
  }
}

function markAllPresent() {
  if (!selectedSession.value) return

  sessionDrafts.value = {
    ...sessionDrafts.value,
    [selectedSession.value.id]: selectedSession.value.students.reduce((draft, student) => {
      draft[student.id] = 'present'
      return draft
    }, {})
  }
}

async function saveAttendance() {
  if (!selectedSession.value) return

  const draft = sessionDraft.value
  const missingStudents = selectedSession.value.students.filter((student) => !draft[student.id])

  if (missingStudents.length) {
    saveSuccess.value = ''
    saveError.value = 'Please set a status for every student before saving.'
    return
  }

  saving.value = true
  saveError.value = ''
  saveSuccess.value = ''

  try {
    await attendanceApi.bulkSave({
      session_id: selectedSession.value.id,
      records: selectedSession.value.students.map((student) => ({
        user_id: student.id,
        status: draft[student.id]
      }))
    })

    await loadGroupAttendance(selectedCoachGroupId.value)
    saveSuccess.value = 'Attendance updated for this session.'
  } catch (error) {
    saveError.value = extractErrorMessage(error, 'Failed to save attendance.')
  } finally {
    saving.value = false
  }
}

function selectDate(date) {
  selectedDate.value = date
}

function openSessionFromList(session) {
  selectedDate.value = session.date
  currentMonth.value = new Date(`${session.date}T00:00:00`)
  selectedSessionId.value = session.id
}

function changeMonth(offset) {
  currentMonth.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth() + offset,
    1
  )
}

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function todayIso() {
  return formatDateKey(new Date())
}

function formatDateKey(date) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

function formatPrettyDate(value) {
  if (!value) return 'No date selected'

  return new Date(`${value}T00:00:00`).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

function buildDayStateSignature(states) {
  if (!states.length) return 'empty'

  const normalized = states.map((state) => {
    if (state === 'cancelled') return 'cancelled'
    if (state === 'completed') return 'completed'
    return 'pending'
  })

  return [...new Set(normalized)].sort().join(',')
}

function getSessionDotClass(state) {
  if (state === 'completed') return 'cell-dot-green'
  if (state === 'cancelled') return 'cell-dot-gray'
  return 'cell-dot-blue'
}

function buildCalendarCellStyle(signature) {
  const styles = {
    completed: {
      '--calendar-cell-fill': 'rgba(87, 214, 156, 0.16)',
      '--calendar-cell-border': 'rgba(57, 185, 128, 0.22)',
      '--calendar-cell-shadow': '0 12px 24px rgba(57, 185, 128, 0.12)'
    },
    pending: {
      '--calendar-cell-fill': 'rgba(89, 133, 255, 0.15)',
      '--calendar-cell-border': 'rgba(89, 133, 255, 0.24)',
      '--calendar-cell-shadow': '0 12px 24px rgba(89, 133, 255, 0.12)'
    },
    cancelled: {
      '--calendar-cell-fill': 'rgba(203, 213, 225, 0.16)',
      '--calendar-cell-border': 'rgba(148, 163, 184, 0.2)',
      '--calendar-cell-shadow': '0 12px 24px rgba(148, 163, 184, 0.08)'
    },
    'completed,pending': {
      '--calendar-cell-fill': 'linear-gradient(135deg, rgba(87, 214, 156, 0.16) 0%, rgba(87, 214, 156, 0.16) 49%, rgba(89, 133, 255, 0.15) 51%, rgba(89, 133, 255, 0.15) 100%)',
      '--calendar-cell-border': 'rgba(140, 171, 232, 0.28)',
      '--calendar-cell-shadow': '0 12px 24px rgba(120, 156, 236, 0.12)'
    },
    'cancelled,pending': {
      '--calendar-cell-fill': 'linear-gradient(135deg, rgba(203, 213, 225, 0.16) 0%, rgba(203, 213, 225, 0.16) 49%, rgba(89, 133, 255, 0.15) 51%, rgba(89, 133, 255, 0.15) 100%)',
      '--calendar-cell-border': 'rgba(156, 161, 226, 0.28)',
      '--calendar-cell-shadow': '0 12px 24px rgba(132, 145, 223, 0.12)'
    },
    'cancelled,completed': {
      '--calendar-cell-fill': 'linear-gradient(135deg, rgba(203, 213, 225, 0.16) 0%, rgba(203, 213, 225, 0.16) 49%, rgba(87, 214, 156, 0.16) 51%, rgba(87, 214, 156, 0.16) 100%)',
      '--calendar-cell-border': 'rgba(171, 180, 202, 0.26)',
      '--calendar-cell-shadow': '0 12px 24px rgba(148, 163, 184, 0.12)'
    },
    empty: {
      '--calendar-cell-fill': 'rgba(203, 213, 225, 0.12)',
      '--calendar-cell-border': 'rgba(226, 232, 240, 0.52)',
      '--calendar-cell-shadow': 'none'
    }
  }

  return styles[signature] ?? styles.empty
}

function extractErrorMessage(error, fallback) {
  const validationErrors = error?.response?.data?.errors

  if (validationErrors && typeof validationErrors === 'object') {
    const firstError = Object.values(validationErrors).flat()[0]
    if (firstError) return firstError
  }

  return error?.response?.data?.message || fallback
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
</script>

<style scoped>
.coach-attendance-page {
  padding: 24px;
}

.coach-attendance-shell {
  max-width: 1440px;
  margin: 0 auto;
  border-radius: 34px;
  border: 1px solid rgba(255, 255, 255, 0.45);
  background: linear-gradient(135deg, rgba(236, 244, 255, 0.82), rgba(245, 237, 248, 0.78));
  box-shadow: 0 24px 70px rgba(95, 122, 168, 0.18);
  backdrop-filter: blur(18px);
}

.coach-attendance-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.coach-attendance-panel {
  display: grid;
  grid-template-columns: 232px minmax(0, 1fr);
  gap: 22px;
  padding: 22px;
}

.content-shell {
  display: flex;
  flex-direction: column;
  gap: 22px;
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

.mobile-nav-list,
.nav-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.mobile-drawer-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 14px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
}

.coach-attendance-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
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

.sidebar-card {
  display: flex;
  flex-direction: column;
  min-height: 100%;
  padding: 18px;
  border-radius: 28px;
  background: rgba(245, 250, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.coach-attendance-shell-dark .sidebar-card {
  background: rgba(18, 27, 43, 0.88);
  border-color: rgba(74, 92, 126, 0.42);
}

.brand-block {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 26px;
  padding: 10px 8px;
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

.brand-name {
  font-size: 1.22rem;
  font-weight: 700;
  color: #172033;
  line-height: 1.1;
}

.coach-attendance-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.coach-attendance-shell-dark .brand-caption {
  color: #94a6c4;
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

.coach-attendance-shell-dark .nav-item {
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

.mobile-header-card {
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.coach-attendance-shell-dark .mobile-header-card {
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
}

.coach-attendance-shell-dark .mobile-menu-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-brand-inline {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.mobile-brand-icon {
  width: 46px;
  height: 46px;
  border-radius: 15px;
}

.mobile-header-actions,
.topbar-tools {
  display: flex;
  align-items: center;
}

.mobile-header-actions {
  gap: 12px;
}

.mobile-utility-card {
  padding: 16px 18px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.coach-attendance-shell-dark .mobile-utility-card,
.coach-attendance-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
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

.topbar-tools {
  gap: 14px;
  min-width: 0;
}

.coach-attendance-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-profile-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.icon-badge-wrap {
  position: relative;
}

.top-icon-btn {
  width: 54px;
  height: 54px;
  border: 1px solid rgba(148, 163, 184, 0.18);
  color: #111827;
  background: rgba(255, 255, 255, 0.75);
}

.coach-attendance-shell-dark .top-icon-btn {
  color: #dce6f7;
  background: rgba(18, 27, 43, 0.92);
  border-color: rgba(74, 92, 126, 0.46);
}

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
}

.coach-attendance-shell-dark .top-icon-btn-active {
  color: #7fbcff;
  background: rgba(22, 43, 76, 0.96);
  border-color: rgba(82, 156, 255, 0.44);
}

.logout-btn {
  color: #111827;
}

.coach-attendance-shell-dark .logout-btn {
  color: #eef4ff;
}

.icon-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  display: grid;
  place-items: center;
  border-radius: 999px;
  background: #1677ff;
  color: white;
  font-size: 0.72rem;
  font-weight: 700;
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

.coach-attendance-shell-dark .profile-pill {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.mobile-profile-pill {
  flex: 1;
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 700;
  color: #1c2438;
}

.coach-attendance-shell-dark .profile-name {
  color: #f3f7ff;
}

.profile-email {
  margin-top: 2px;
  font-size: 0.86rem;
  color: #78859a;
}

.coach-attendance-shell-dark .profile-email {
  color: #94a6c4;
}

.coach-page-card {
  padding: 26px;
  border-radius: 30px;
  background: rgba(249, 252, 255, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.72);
}

.coach-attendance-shell-dark .coach-page-card {
  background: rgba(18, 27, 43, 0.86);
  border-color: rgba(74, 92, 126, 0.42);
}

.coach-page-header,
.attendance-toolbar,
.calendar-header,
.section-head,
.selected-session-summary,
.roster-toolbar {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}

.coach-page-header {
  margin-bottom: 22px;
}

.coach-page-title {
  margin: 0;
  font-size: 2.1rem;
  line-height: 1.1;
  color: #172033;
}

.coach-attendance-shell-dark .coach-page-title,
.coach-attendance-shell-dark .summary-value,
.coach-attendance-shell-dark .section-title,
.coach-attendance-shell-dark .selected-session-title,
.coach-attendance-shell-dark .roster-student-name {
  color: #f3f7ff;
}

.coach-page-subtitle,
.summary-label,
.section-caption,
.toolbar-section-label,
.calendar-title,
.roster-student-meta,
.selected-session-meta,
.requires-item-meta,
.day-session-meta,
.cell-more {
  color: #7b8798;
}

.coach-attendance-shell-dark .coach-page-subtitle,
.coach-attendance-shell-dark .summary-label,
.coach-attendance-shell-dark .section-caption,
.coach-attendance-shell-dark .toolbar-section-label,
.coach-attendance-shell-dark .calendar-title,
.coach-attendance-shell-dark .roster-student-meta,
.coach-attendance-shell-dark .selected-session-meta,
.coach-attendance-shell-dark .requires-item-meta,
.coach-attendance-shell-dark .day-session-meta,
.coach-attendance-shell-dark .cell-more {
  color: #94a6c4;
}

.group-selector {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 12px;
  min-width: 280px;
  padding: 14px 18px;
  border: 1px solid rgba(223, 232, 246, 0.95);
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.86);
  cursor: pointer;
}

.coach-attendance-shell-dark .group-selector {
  background: rgba(17, 25, 40, 0.86);
  border-color: rgba(64, 82, 116, 0.72);
}

.group-selector-static {
  cursor: default;
}

.group-selector-avatar {
  flex-shrink: 0;
  box-shadow: 0 12px 26px rgba(22, 119, 255, 0.16);
}

.group-selector-copy {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  min-width: 0;
  flex: 1;
}

.group-selector-label {
  font-size: 0.82rem;
  line-height: 1.1;
  color: #7b8798;
}

.group-selector-name {
  margin-top: 4px;
  font-size: 1rem;
  font-weight: 700;
  line-height: 1.15;
  text-align: left;
  color: #172033;
}

.coach-attendance-shell-dark .group-selector-label {
  color: #94a6c4;
}

.coach-attendance-shell-dark .group-selector-name {
  color: #f3f7ff;
}

.group-selector-chevron {
  color: #6f7f96;
}

.coach-attendance-shell-dark .group-selector-chevron {
  color: #c8d7ef;
}

:deep(.group-selector-menu) {
  min-width: 300px;
  padding: 8px;
  border-radius: 22px;
  border: 1px solid rgba(223, 231, 243, 0.92);
  background: rgba(255, 255, 255, 0.98);
  box-shadow: 0 24px 48px rgba(79, 106, 154, 0.18);
}

:deep(.group-selector-menu .v-list-item) {
  min-height: 62px;
  border-radius: 16px;
}

:deep(.group-selector-menu .v-list-item-title) {
  color: #172033;
  font-weight: 600;
}

:deep(.group-selector-menu .v-list-item-subtitle) {
  color: #7b8798;
}

.coach-attendance-shell-dark :deep(.group-selector-menu) {
  border-color: rgba(74, 92, 126, 0.42);
  background: rgba(18, 27, 43, 0.98);
  box-shadow: 0 28px 52px rgba(4, 10, 24, 0.4);
}

.coach-attendance-shell-dark :deep(.group-selector-menu .v-list-item-title) {
  color: #f3f7ff;
}

.coach-attendance-shell-dark :deep(.group-selector-menu .v-list-item-subtitle) {
  color: #94a6c4;
}

.coach-stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 22px;
}

.coach-stat-card,
.workbench-card,
.calendar-card {
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(229, 236, 246, 0.94);
}

.coach-attendance-shell-dark .coach-stat-card,
.coach-attendance-shell-dark .workbench-card,
.coach-attendance-shell-dark .calendar-card {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.coach-stat-card {
  padding: 20px;
}

.summary-value {
  margin-top: 8px;
  font-size: 1.9rem;
  font-weight: 700;
  color: #172033;
}

.small-value {
  font-size: 1.35rem;
}

.workbench-grid {
  display: grid;
  grid-template-columns: minmax(320px, 0.78fr) minmax(0, 1.22fr);
  gap: 18px;
  margin-bottom: 22px;
  align-items: stretch;
}

.workbench-card {
  padding: 20px;
}

.roster-card {
  display: flex;
  flex-direction: column;
  min-height: 0;
}

.roster-head-tools {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-left: auto;
}

.section-title {
  font-size: 1.18rem;
  font-weight: 700;
  color: #172033;
}

.section-chip {
  min-width: 42px;
  height: 42px;
  padding: 0 14px;
  display: grid;
  place-items: center;
  border-radius: 999px;
  font-weight: 700;
  color: #0f5fe3;
  background: rgba(194, 225, 255, 0.5);
}

.roster-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 18px;
}

.requires-item,
.roster-item,
.day-session-pill {
  border: 1px solid rgba(223, 232, 246, 0.95);
  background: rgba(241, 246, 255, 0.8);
}

.coach-attendance-shell-dark .requires-item,
.coach-attendance-shell-dark .roster-item,
.coach-attendance-shell-dark .day-session-pill {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.requires-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  width: 100%;
  padding: 16px;
  border-radius: 18px;
  text-align: left;
  cursor: pointer;
}

.requires-item-title,
.day-session-title {
  font-weight: 700;
  color: #172033;
}

.coach-attendance-shell-dark .requires-item-title,
.coach-attendance-shell-dark .day-session-title {
  color: #f3f7ff;
}

.requires-item-side {
  text-align: right;
}

.requires-item-progress {
  font-size: 0.88rem;
  font-weight: 700;
  color: #0f5fe3;
}

.requires-item-open {
  margin-top: 6px;
  font-size: 0.86rem;
  color: #7b8798;
}

.day-session-list {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 18px;
}

.day-session-pill {
  min-width: 180px;
  padding: 14px 16px;
  border-radius: 18px;
  text-align: left;
  cursor: pointer;
}

.day-session-pill-active {
  border-color: rgba(22, 119, 255, 0.46);
  box-shadow: 0 0 0 3px rgba(22, 119, 255, 0.08);
}

.day-session-pill-completed {
  background: rgba(87, 214, 156, 0.1);
}

.day-session-pill-pending {
  background: rgba(89, 133, 255, 0.12);
}

.day-session-pill-cancelled {
  background: rgba(203, 213, 225, 0.18);
}

.selected-session-summary {
  margin-top: 18px;
  padding: 18px;
  border-radius: 20px;
  background: rgba(241, 246, 255, 0.8);
  border: 1px solid rgba(223, 232, 246, 0.95);
}

.coach-attendance-shell-dark .selected-session-summary {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.selected-session-title {
  font-size: 1.08rem;
  font-weight: 700;
  color: #172033;
}

.roster-alert {
  margin-top: 16px;
}

.roster-toolbar {
  margin-top: 18px;
  align-items: center;
}

.roster-search-shell {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 210px;
  min-height: 44px;
  padding: 0 14px;
  border-radius: 16px;
  background: rgba(241, 246, 255, 0.8);
  border: 1px solid rgba(223, 232, 246, 0.95);
}

.coach-attendance-shell-dark .roster-search-shell {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.roster-search-icon {
  color: #6f7f96;
}

.coach-attendance-shell-dark .roster-search-icon {
  color: #9eb1cf;
}

.roster-search-field {
  flex: 1;
}

.roster-search-field :deep(.v-field__input) {
  min-height: auto;
  padding-top: 0;
  padding-bottom: 0;
}

.roster-search-field :deep(input) {
  color: #172033;
  opacity: 1;
}

.coach-attendance-shell-dark .roster-search-field :deep(input) {
  color: #eef4ff;
}

.roster-search-field :deep(input::placeholder) {
  color: #7b8798;
  opacity: 1;
}

.coach-attendance-shell-dark .roster-search-field :deep(input::placeholder) {
  color: #94a6c4;
}

.roster-scroll {
  margin-top: 18px;
  max-height: 280px;
  overflow-y: auto;
  padding-right: 6px;
}

.roster-scroll .roster-list {
  margin-top: 0;
}

.roster-scroll::-webkit-scrollbar {
  width: 10px;
}

.roster-scroll::-webkit-scrollbar-track {
  border-radius: 999px;
  background: rgba(203, 213, 225, 0.16);
}

.roster-scroll::-webkit-scrollbar-thumb {
  border-radius: 999px;
  background: rgba(89, 133, 255, 0.36);
}

.coach-attendance-shell-dark .roster-scroll::-webkit-scrollbar-track {
  background: rgba(36, 52, 79, 0.68);
}

.coach-attendance-shell-dark .roster-scroll::-webkit-scrollbar-thumb {
  background: rgba(97, 155, 255, 0.42);
}

.roster-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding: 14px 16px;
  border-radius: 18px;
}

.roster-student {
  display: flex;
  align-items: center;
  gap: 12px;
}

.roster-student-name {
  font-weight: 700;
  color: #172033;
}

.status-toggle {
  display: inline-flex;
  gap: 8px;
  flex-wrap: wrap;
}

.status-option {
  min-width: 96px;
  padding: 10px 14px;
  border: 1px solid rgba(214, 223, 238, 0.95);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.88);
  color: #172033;
  font-weight: 600;
  cursor: pointer;
}

.coach-attendance-shell-dark .status-option {
  background: rgba(12, 20, 33, 0.88);
  color: #eef4ff;
  border-color: rgba(63, 80, 114, 0.58);
}

.status-option-active {
  border-color: transparent;
  color: white;
}

.status-option-present.status-option-active {
  background: linear-gradient(180deg, #31b46d 0%, #269e5c 100%);
}

.status-option-absent.status-option-active {
  background: linear-gradient(180deg, #f1646d 0%, #e54c57 100%);
}

.attendance-toolbar {
  margin-bottom: 14px;
  align-items: center;
}

.toolbar-date {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 52px;
  padding: 0 18px;
  border-radius: 18px;
  color: #172033;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.coach-attendance-shell-dark .toolbar-date {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.month-switcher {
  display: flex;
  align-items: center;
  gap: 10px;
}

.month-nav-btn {
  border: 1px solid rgba(230, 237, 246, 0.94);
  background: rgba(255, 255, 255, 0.92);
}

.coach-attendance-shell-dark .month-nav-btn {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.calendar-card {
  padding: 22px;
}

.calendar-header {
  margin-bottom: 18px;
  align-items: center;
}

.calendar-legend {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 14px;
}

.legend-item {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #6f7f96;
  font-size: 0.92rem;
}

.coach-attendance-shell-dark .legend-item {
  color: #a7bad7;
}

.legend-dot,
.cell-dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  flex-shrink: 0;
}

.legend-dot-green,
.cell-dot-green {
  background: #39b980;
}

.legend-dot-blue,
.cell-dot-blue {
  background: #4b7cff;
}

.legend-dot-gray,
.cell-dot-gray {
  background: #94a3b8;
}

.calendar-days,
.calendar-week {
  display: grid;
  grid-template-columns: repeat(7, minmax(0, 1fr));
  gap: 12px;
}

.calendar-days {
  margin-bottom: 12px;
}

.day-name {
  padding: 0 8px;
  text-align: center;
  font-size: 0.84rem;
  font-weight: 700;
  color: #7b8798;
}

.coach-attendance-shell-dark .day-name {
  color: #94a6c4;
}

.calendar-cell {
  min-height: 138px;
  padding: 12px;
  border-radius: 20px;
  border: 1px solid var(--calendar-cell-border);
  background: var(--calendar-cell-fill);
  box-shadow: var(--calendar-cell-shadow);
  text-align: left;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.calendar-cell:hover {
  transform: translateY(-1px);
}

.calendar-cell-selected {
  box-shadow: 0 0 0 2px rgba(22, 119, 255, 0.18), var(--calendar-cell-shadow);
}

.calendar-cell-empty {
  cursor: pointer;
}

.calendar-cell-outside {
  opacity: 0.55;
}

.cell-date {
  font-size: 0.9rem;
  font-weight: 700;
  color: #172033;
}

.coach-attendance-shell-dark .cell-date,
.coach-attendance-shell-dark .cell-session-name,
.coach-attendance-shell-dark .cell-status {
  color: #eef4ff;
}

.cell-sessions {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 12px;
}

.cell-session-item,
.cell-status {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.84rem;
  color: #172033;
}

.cell-session-name {
  display: block;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.state-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 160px;
  padding: 18px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.75);
  border: 1px dashed rgba(194, 206, 225, 0.92);
  color: #5f6f88;
}

.coach-attendance-shell-dark .state-wrap {
  background: rgba(13, 20, 34, 0.72);
  border-color: rgba(78, 97, 132, 0.58);
  color: #aac0df;
}

.loading-state {
  flex-direction: column;
  gap: 14px;
}

.empty-block {
  margin-top: 18px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(241, 246, 255, 0.8);
  border: 1px dashed rgba(223, 232, 246, 0.95);
  color: #6f7f96;
}

.coach-attendance-shell-dark .empty-block {
  background: rgba(17, 25, 40, 0.72);
  border-color: rgba(58, 75, 108, 0.62);
  color: #9eb1cf;
}

.roster-state {
  margin-top: 18px;
  min-height: 120px;
}

.roster-empty {
  margin-top: 14px;
}

@media (max-width: 1180px) {
  .coach-stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .workbench-grid {
    grid-template-columns: 1fr;
  }

  .roster-scroll {
    max-height: 240px;
  }
}

@media (max-width: 1024px) {
  .coach-attendance-page {
    padding: 16px;
  }

  .coach-attendance-panel {
    display: block;
    padding: 16px;
  }

  .sidebar-card {
    display: none;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: block;
  }

  .mobile-header-card {
    display: flex;
  }

  .topbar-card {
    justify-content: flex-end;
  }

  .topbar-tools .profile-pill,
  .logout-btn {
    display: none;
  }
}

@media (max-width: 768px) {
  .coach-page-card {
    padding: 20px;
  }

  .coach-page-header,
  .attendance-toolbar,
  .calendar-header,
  .selected-session-summary,
  .roster-toolbar,
  .roster-item {
    flex-direction: column;
    align-items: stretch;
  }

  .section-head {
    flex-direction: column;
    align-items: stretch;
  }

  .roster-head-tools {
    margin-left: 0;
    width: 100%;
    flex-wrap: wrap;
  }

  .roster-search-shell {
    min-width: 0;
    width: 100%;
  }

  .coach-stats-grid {
    grid-template-columns: 1fr;
  }

  .calendar-days,
  .calendar-week {
    gap: 8px;
  }

  .calendar-cell {
    min-height: 116px;
    padding: 10px;
  }

  .group-selector {
    min-width: 0;
    width: 100%;
  }
}

@media (max-width: 560px) {
  .coach-attendance-page {
    padding: 10px;
  }

  .coach-attendance-panel {
    padding: 10px;
  }

  .coach-page-title {
    font-size: 1.8rem;
  }

  .summary-value {
    font-size: 1.6rem;
  }

  .calendar-cell {
    min-height: 104px;
  }
}
</style>
