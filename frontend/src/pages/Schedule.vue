<template>
  <v-app>
    <v-main class="schedule-page">
      <div class="schedule-shell" :class="{ 'schedule-shell-dark': darkMode }">
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
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/schedule' }"
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

        <div class="schedule-panel">
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
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/schedule' }"
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
                  <div class="brand-caption">Schedule</div>
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

                <v-btn
                  v-if="canCreateTraining"
                  color="primary"
                  class="mobile-create-btn"
                  prepend-icon="mdi-plus"
                  @click="openCreate"
                >
                  Create training
                </v-btn>
              </div>
            </div>

            <div class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    placeholder="Search schedule"
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

            <div class="schedule-card">
              <div class="schedule-header">
                <div>
                  <h1 class="schedule-title">Schedule</h1>
                  <div class="schedule-subtitle">Here you can view the training schedule for the upcoming month</div>
                </div>

                <v-btn
                  v-if="canCreateTraining"
                  color="primary"
                  class="create-btn desktop-only-btn"
                  prepend-icon="mdi-plus"
                  @click="openCreate"
                >
                  Create training
                </v-btn>
              </div>

              <div class="toolbar-row">
                <div class="pill-tabs">
                  <button
                    v-for="item in tabs"
                    :key="item.value"
                    type="button"
                    class="pill-tab"
                    :class="{ 'pill-tab-active': tab === item.value }"
                    @click="tab = item.value"
                  >
                    {{ item.label }}
                  </button>
                </div>

                <div class="toolbar-actions">
                  <v-btn variant="outlined" class="toolbar-btn" @click="filterDialog = true">
                    <v-icon start size="18">mdi-tune-variant</v-icon>
                    Filters
                  </v-btn>
                </div>
              </div>

              <div
                v-for="group in pagedGroupedTrainings"
                :key="group.label"
                class="day-group"
              >
                <div class="day-label">
                  <span v-if="group.title" class="day-primary">{{ group.title }}</span>
                  <span class="day-secondary">{{ group.label }}</span>
                </div>

                <div
                  v-for="training in group.items"
                  :key="training.id"
                  class="schedule-item"
                  :class="{
                    'schedule-item-expanded': expandedId === training.id,
                    'schedule-item-cancelled': isCancelledTraining(training.status)
                  }"
                >
                  <div class="schedule-grid">
                    <div class="schedule-meta-grid">
                      <div class="slot-block">
                        <div class="meta-label">Date</div>
                        <div class="slot-value">{{ formatCardDate(training.date) }}</div>
                      </div>

                      <div class="slot-block">
                        <div class="meta-label">From</div>
                        <div class="slot-value">{{ training.start }}</div>
                      </div>

                      <div class="slot-block">
                        <div class="meta-label">To</div>
                        <div class="slot-value">{{ training.end }}</div>
                      </div>
                    </div>

                    <div class="course-block">
                      <div class="meta-label">Training</div>
                      <div class="course-header-row">
                        <div class="course-title">{{ training.title }}</div>
                        <v-chip
                          v-if="isCancelledTraining(training.status)"
                          size="small"
                          class="status-chip status-chip-cancelled"
                        >
                          Cancelled
                        </v-chip>
                      </div>

                      <template v-if="expandedId === training.id && canManageTrainingActions">
                        <div class="meta-label section-gap">Age</div>
                        <div class="course-subtitle">{{ training.description }}</div>

                        <div class="meta-label section-gap">Group</div>
                        <div class="meeting-link">{{ training.group }}</div>
                      </template>
                    </div>

                    <div
                      v-if="expandedId === training.id && !canManageTrainingActions"
                      class="schedule-detail-row"
                    >
                      <div class="schedule-detail-item">
                        <div class="meta-label">Day</div>
                        <div class="slot-value">{{ formatWeekday(training.date) }}</div>
                      </div>

                      <div class="schedule-detail-item">
                        <div class="meta-label">Duration</div>
                        <div class="slot-value">{{ formatDuration(training.start, training.end) }}</div>
                      </div>

                      <div class="schedule-detail-item">
                        <div class="meta-label">Status</div>
                        <div class="slot-value">{{ formatTrainingStatus(training.status) }}</div>
                      </div>

                      <div class="schedule-detail-item">
                        <div class="meta-label">Age</div>
                        <div class="slot-value">{{ training.description }}</div>
                      </div>

                      <div class="schedule-detail-item schedule-detail-item-wide">
                        <div class="meta-label">Group</div>
                        <div class="meeting-link">{{ training.group }}</div>
                      </div>
                    </div>

                    <div class="teacher-block">
                      <div class="teacher-summary">
                        <v-avatar size="46" class="teacher-avatar">
                          <img :src="training.avatar" :alt="training.trainer">
                        </v-avatar>
                        <div>
                          <div class="meta-label">Trainer</div>
                          <div class="teacher-name">{{ training.trainer }}</div>
                        </div>
                      </div>
                    </div>

                    <div class="expand-block">
                      <button
                        type="button"
                        class="expand-link"
                        @click="toggleExpanded(training.id)"
                      >
                        {{ expandedId === training.id ? 'Show less' : 'Show more' }}
                      </button>
                    </div>
                  </div>

                  <div v-if="expandedId === training.id" class="schedule-footer">
                    <div class="students-block">
                      <div class="meta-label">Students</div>
                      <div class="students-row">
                        <v-avatar
                          v-for="student in training.students.slice(0, 4)"
                          :key="student.id"
                          size="44"
                          class="student-avatar"
                        >
                          <img :src="student.avatar" :alt="student.name">
                        </v-avatar>
                        <div class="student-more">+{{ training.students.length }}</div>
                      </div>
                    </div>

                    <div v-if="canManageTrainingActions" class="action-row">
                      <v-btn variant="outlined" color="error" class="action-btn" @click="cancelTraining(training)">
                        Cancel
                      </v-btn>
                      <v-btn
                        variant="outlined"
                        color="primary"
                        class="action-btn"
                        @click="openEdit(training)"
                      >
                        Edit
                      </v-btn>
                      <v-btn color="primary" class="action-btn" to="/attendance">
                        Attendance
                      </v-btn>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="totalPages > 1" class="pagination-row">
                <v-btn
                  variant="text"
                  class="pagination-nav-btn"
                  :disabled="currentPage === 1"
                  @click="currentPage = Math.max(1, currentPage - 1)"
                >
                  Prev
                </v-btn>

                <div class="pagination-pages">
                  <button
                    v-for="page in totalPages"
                    :key="page"
                    type="button"
                    class="pagination-page-btn"
                    :class="{ 'pagination-page-btn-active': currentPage === page }"
                    @click="currentPage = page"
                  >
                    {{ page }}
                  </button>
                </div>

                <v-btn
                  variant="text"
                  class="pagination-nav-btn"
                  :disabled="currentPage === totalPages"
                  @click="currentPage = Math.min(totalPages, currentPage + 1)"
                >
                  Next
                </v-btn>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="dialog" max-width="560">
          <v-card class="dialog-card">
            <v-card-title>
              {{ isEditing ? 'Edit Training' : 'Create Training' }}
            </v-card-title>

            <v-card-text>
              <v-select
                v-model="newTraining.group_id"
                label="Group"
                :items="groupOptions"
                item-title="section"
                item-value="id"
              />
              <v-text-field label="Training title" v-model="newTraining.title" />
              <v-text-field label="Date (YYYY-MM-DD)" v-model="newTraining.date" />
              <v-text-field label="Start time" v-model="newTraining.start" />
              <v-text-field label="End time" v-model="newTraining.end" />
              <v-text-field label="Trainer" v-model="newTraining.trainer" readonly />
              <v-textarea label="Description" v-model="newTraining.description" readonly />
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn @click="dialog = false">Cancel</v-btn>
              <v-btn color="primary" @click="saveTraining">
                {{ isEditing ? 'Update' : 'Save' }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="filterDialog" max-width="520">
          <v-card class="dialog-card filter-dialog-card">
            <div class="filter-dialog-header">
              <div>
                <div class="filter-dialog-title">Filters</div>
                <div class="filter-dialog-subtitle">
                  Sort trainings by time or title without leaving the schedule view.
                </div>
              </div>

              <v-btn icon variant="text" @click="filterDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <div class="filter-options">
              <button
                type="button"
                class="filter-option"
                :class="{ 'filter-option-active': selectedSort === 'time' }"
                @click="selectedSort = 'time'"
              >
                <span class="filter-option-icon">
                  <v-icon size="18">mdi-clock-outline</v-icon>
                </span>
                <span>
                  <span class="filter-option-title">By time</span>
                  <span class="filter-option-text">Show trainings from the earliest start time.</span>
                </span>
              </button>

              <button
                type="button"
                class="filter-option"
                :class="{ 'filter-option-active': selectedSort === 'az' }"
                @click="selectedSort = 'az'"
              >
                <span class="filter-option-icon">
                  <v-icon size="18">mdi-sort-alphabetical-ascending</v-icon>
                </span>
                <span>
                  <span class="filter-option-title">A to Z</span>
                  <span class="filter-option-text">Order trainings alphabetically from A to Z.</span>
                </span>
              </button>

              <button
                type="button"
                class="filter-option"
                :class="{ 'filter-option-active': selectedSort === 'za' }"
                @click="selectedSort = 'za'"
              >
                <span class="filter-option-icon">
                  <v-icon size="18">mdi-sort-alphabetical-descending</v-icon>
                </span>
                <span>
                  <span class="filter-option-title">Z to A</span>
                  <span class="filter-option-text">Order trainings alphabetically from Z to A.</span>
                </span>
              </button>
            </div>

            <div class="filter-dialog-actions">
              <v-btn variant="outlined" class="reset-filter-btn" @click="resetSort">
                Reset
              </v-btn>
              <v-btn color="primary" class="apply-filter-btn" @click="applySelectedSort">
                Apply filters
              </v-btn>
            </div>
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
import { useRouter } from 'vue-router'
import AppNotificationsDialog from '../components/AppNotificationsDialog.vue'
import { useNotifications } from '../composables/useNotifications'
import { useSelectedChild } from '../composables/useSelectedChild'
import { groupsApi, sessionsApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const search = ref('')
const tab = ref('upcoming')
const dialog = ref(false)
const filterDialog = ref(false)
const notificationsDialog = ref(false)
const isEditing = ref(false)
const editingId = ref(null)
const expandedId = ref(1)
const selectedSort = ref('time')
const darkMode = ref(false)
const currentPage = ref(1)
const pageSize = 6
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const darkModeStorageKey = 'app-dark-mode'
const loading = ref(false)

const tabs = [
  { value: 'upcoming', label: 'Upcoming' },
  { value: 'past', label: 'Past' }
]

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
const trainings = ref([])
const groupOptions = ref([])
const profileName = computed(() => {
  if (!user.value) return 'SportSystem User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const isParent = computed(() => user.value?.role === 'parent')
const canCreateTraining = computed(() => ['admin', 'coach'].includes(user.value?.role ?? ''))
const canManageTrainingActions = computed(() => ['admin', 'coach'].includes(user.value?.role ?? ''))
const navItems = computed(() => [
  { label: 'Home', icon: 'mdi-home-outline', to: '/home' },
  { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
  { label: 'Attendance', icon: 'mdi-check-circle-outline', to: user.value?.role === 'coach' ? '/coach-attendance' : '/attendance' },
  ...(user.value?.role === 'parent'
    ? [{ label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }]
    : [])
])
const profileEmail = computed(() => user.value?.email ?? 'user@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)

const newTraining = ref({
  group_id: null,
  title: '',
  date: '',
  start: '',
  end: '',
  trainer: '',
  description: ''
})

const today = computed(() => {
  const now = new Date()
  now.setHours(0, 0, 0, 0)
  return now
})

const filteredTrainings = computed(() => {
  const query = search.value.trim().toLowerCase()

  return trainings.value.filter((training) => {
    const matchesSelectedChild = !isParent.value
      || !selectedChildId.value
      || training.students?.some((student) => student.id === selectedChildId.value)

    const matchesTab = tab.value === 'upcoming'
      ? !isPastTraining(training)
      : isPastTraining(training)

    if (!matchesSelectedChild) return false
    if (!matchesTab) return false
    if (!query) return true

    return [
      training.title,
      training.description,
      training.trainer,
      training.group
    ].some((value) => value.toLowerCase().includes(query))
  })
})

const sortedFilteredTrainings = computed(() =>
  [...filteredTrainings.value].sort((a, b) => {
    const first = parseTrainingDateTime(a.date, a.start).getTime()
    const second = parseTrainingDateTime(b.date, b.start).getTime()
    return tab.value === 'upcoming' ? first - second : second - first
  })
)

const groupedTrainings = computed(() => {
  const groups = new Map()

  sortedFilteredTrainings.value.forEach((training) => {
    if (!groups.has(training.date)) groups.set(training.date, [])
    groups.get(training.date).push(training)
  })

  return Array.from(groups.entries()).map(([date, items]) => ({
    title: getRelativeDayLabel(date),
    label: parseDateOnly(date).toLocaleDateString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    }),
    items
  }))
})

const paginatedTrainings = computed(() => {
  const startIndex = (currentPage.value - 1) * pageSize
  return sortedFilteredTrainings.value.slice(startIndex, startIndex + pageSize)
})

const pagedGroupedTrainings = computed(() => {
  const groups = new Map()

  paginatedTrainings.value.forEach((training) => {
    if (!groups.has(training.date)) groups.set(training.date, [])
    groups.get(training.date).push(training)
  })

  return Array.from(groups.entries()).map(([date, items]) => ({
    title: getRelativeDayLabel(date),
    label: parseDateOnly(date).toLocaleDateString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    }),
    items
  }))
})

const totalPages = computed(() => Math.max(1, Math.ceil(sortedFilteredTrainings.value.length / pageSize)))

watch([tab, search, selectedSort], () => {
  currentPage.value = 1
})

watch(totalPages, (value) => {
  if (currentPage.value > value) currentPage.value = value
})

watch(isCompactNav, (value) => {
  if (!value) mobileMenuOpen.value = false
})

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  loadGroups()
  loadSessions()
  loadNotifications()
})

watch(darkMode, (value) => {
  localStorage.setItem(darkModeStorageKey, String(value))
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

watch(notificationsDialog, (value) => {
  if (value) {
    loadNotifications(true)
  }
})

watch(() => newTraining.value.group_id, () => {
  syncTrainingGroupDetails()
})

function toggleExpanded(id) {
  expandedId.value = expandedId.value === id ? null : id
}

function openCreate() {
  if (!canCreateTraining.value) return

  isEditing.value = false
  editingId.value = null
  newTraining.value = {
    group_id: groupOptions.value[0]?.id ?? null,
    title: '',
    date: '',
    start: '',
    end: '',
    trainer: '',
    description: '',
    group: ''
  }
  syncTrainingGroupDetails()
  dialog.value = true
}

function openEdit(training) {
  isEditing.value = true
  editingId.value = training.id
  newTraining.value = {
    group_id: training.group_id,
    title: training.title,
    date: training.date,
    start: training.start,
    end: training.end,
    trainer: training.trainer,
    description: training.description,
    group: training.group
  }
  dialog.value = true
}

async function loadGroups() {
  const response = await groupsApi.list()
  groupOptions.value = response
}

async function loadSessions() {
  loading.value = true

  try {
    const response = await sessionsApi.list()
    trainings.value = response.map(withTrainingAvatars)
    if (isParent.value) {
      syncSelectedChild(
        trainings.value.flatMap((training) => (training.students || []).map((student) => student.id)),
        { preserveExisting: true }
      )
    }
  } finally {
    loading.value = false
  }
}

async function saveTraining() {
  const payload = {
    group_id: newTraining.value.group_id,
    title: newTraining.value.title,
    date: newTraining.value.date,
    start_time: newTraining.value.start,
    end_time: newTraining.value.end,
    status: new Date(`${newTraining.value.date}T${newTraining.value.end}:00`).getTime() < Date.now() ? 'completed' : 'planned'
  }

  if (isEditing.value && editingId.value) {
    await sessionsApi.update(editingId.value, payload)
  } else {
    await sessionsApi.create(payload)
  }

  await loadSessions()
  dialog.value = false
}

function parseTime(value) {
  return new Date(`1970-01-01 ${value}`).getTime()
}

function parseTrainingDateTime(date, time) {
  return new Date(`${date}T${time}:00`)
}

function parseDateOnly(value) {
  return new Date(`${value}T00:00:00`)
}

function isPastTraining(training) {
  return parseTrainingDateTime(training.date, training.end).getTime() < Date.now()
}

function getRelativeDayLabel(date) {
  const target = new Date(`${date}T00:00:00`)
  const diffDays = Math.round((target.getTime() - today.value.getTime()) / 86400000)

  if (diffDays === 0) return 'Today'
  if (diffDays === 1) return 'Tomorrow'
  return ''
}

function formatCardDate(value) {
  return parseDateOnly(value).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

function formatWeekday(value) {
  return parseDateOnly(value).toLocaleDateString('en-US', {
    weekday: 'long'
  })
}

function formatDuration(start, end) {
  const startValue = parseTime(start)
  const endValue = parseTime(end)
  const minutes = Math.max(0, Math.round((endValue - startValue) / 60000))

  if (minutes < 60) return `${minutes} min`

  const hours = Math.floor(minutes / 60)
  const remainder = minutes % 60

  if (!remainder) return `${hours} h`

  return `${hours} h ${remainder} min`
}

function formatTrainingStatus(status) {
  return status ? `${status.charAt(0).toUpperCase()}${status.slice(1)}` : 'Planned'
}

function isCancelledTraining(status) {
  return String(status || '').toLowerCase() === 'cancelled'
}

function sortAZ() {
  trainings.value.sort((a, b) => a.title.localeCompare(b.title))
}

function sortZA() {
  trainings.value.sort((a, b) => b.title.localeCompare(a.title))
}

function sortTime() {
  trainings.value.sort((a, b) => parseTime(a.start) - parseTime(b.start))
}

function applySelectedSort() {
  if (selectedSort.value === 'time') sortTime()
  if (selectedSort.value === 'az') sortAZ()
  if (selectedSort.value === 'za') sortZA()

  filterDialog.value = false
}

function resetSort() {
  selectedSort.value = 'time'
  sortTime()
  filterDialog.value = false
}

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function syncTrainingGroupDetails() {
  const selectedGroup = groupOptions.value.find((group) => group.id === newTraining.value.group_id)
  if (!selectedGroup) return

  newTraining.value.trainer = selectedGroup.trainer
  newTraining.value.description = selectedGroup.age_category || 'Training session'
  newTraining.value.group = selectedGroup.section
}

function withTrainingAvatars(training) {
  return {
    ...training,
    avatar: avatarFor(`trainer-${training.trainer}`, training.trainer),
    students: (training.students || []).map((student) => ({
      ...student,
      avatar: avatarFor(`student-${student.name}`, student.name)
    }))
  }
}

async function cancelTraining(training) {
  await sessionsApi.remove(training.id)
  await loadSessions()
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
.schedule-page {
  padding: 24px;
}

.schedule-shell {
  position: relative;
  max-width: 1440px;
  margin: 0 auto;
  border-radius: 34px;
  overflow: visible;
  border: 1px solid rgba(255, 255, 255, 0.45);
  background: linear-gradient(135deg, rgba(236, 244, 255, 0.82), rgba(245, 237, 248, 0.78));
  box-shadow: 0 24px 70px rgba(95, 122, 168, 0.18);
  backdrop-filter: blur(18px);
}

.schedule-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.schedule-panel {
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

.mobile-drawer-dark :deep(.v-navigation-drawer__content) {
  background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(22, 31, 48, 0.98));
}

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

.schedule-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.schedule-shell-dark .mobile-logout-btn {
  color: #eef4ff;
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

.schedule-shell-dark .sidebar-card {
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

.schedule-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.schedule-shell-dark .brand-caption {
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

.schedule-shell-dark .nav-item {
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

.schedule-shell-dark .mobile-header-card {
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

.schedule-shell-dark .mobile-menu-btn {
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

.schedule-shell-dark .mobile-utility-card {
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

.mobile-create-btn {
  min-height: 56px;
  padding: 0 18px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  box-shadow: 0 18px 34px rgba(22, 119, 255, 0.22);
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

.schedule-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
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

.schedule-shell-dark .search-shell {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.search-shell-icon {
  color: #6b7280;
  flex-shrink: 0;
}

.schedule-shell-dark .search-shell-icon {
  color: #8ea3c7;
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

.search-field :deep(.v-label),
.search-field :deep(input::placeholder) {
  color: #111827;
  opacity: 1;
}

.schedule-shell-dark .search-field :deep(input),
.schedule-shell-dark .search-field :deep(.v-label),
.schedule-shell-dark .search-field :deep(input::placeholder) {
  color: #e7eefb;
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

.schedule-shell-dark .top-icon-btn {
  color: #dce6f7;
  background: rgba(18, 27, 43, 0.92);
  border-color: rgba(74, 92, 126, 0.46);
}

.logout-btn {
  color: #111827;
}

.schedule-shell-dark .logout-btn {
  color: #dce6f7;
}

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
}

.schedule-shell-dark .top-icon-btn-active {
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

.schedule-shell-dark .profile-pill {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.schedule-shell-dark .profile-name {
  color: #f2f7ff;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.schedule-shell-dark .profile-email {
  color: #93a5c3;
}

.schedule-card {
  min-width: 0;
  padding: 26px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.schedule-shell-dark .schedule-card {
  background: rgba(22, 31, 48, 0.72);
  border-color: rgba(74, 92, 126, 0.42);
}

.schedule-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.schedule-title {
  margin: 0;
  font-size: 2.3rem;
  line-height: 1.1;
  font-weight: 700;
  color: #121826;
}

.schedule-shell-dark .schedule-title {
  color: #f3f7ff;
}

.schedule-subtitle {
  margin-top: 10px;
  font-size: 1rem;
  color: #66748a;
}

.schedule-shell-dark .schedule-subtitle {
  color: #8fa3c1;
}

.create-btn {
  min-height: 54px;
  padding: 0 24px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-size: 1rem;
  box-shadow: 0 18px 34px rgba(22, 119, 255, 0.28);
}

.desktop-only-btn {
  display: inline-flex;
}

.toolbar-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 28px;
}

.pill-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.pill-tab {
  min-height: 48px;
  padding: 0 18px;
  border: 1px solid rgba(191, 203, 224, 0.84);
  border-radius: 999px;
  color: #6b7280;
  background: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pill-tab-active {
  border-color: transparent;
  color: white;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 14px 26px rgba(22, 119, 255, 0.24);
}

.toolbar-actions {
  display: flex;
  gap: 12px;
}

.toolbar-btn {
  min-height: 48px;
  padding: 0 18px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  color: #162033;
  background: rgba(255, 255, 255, 0.8);
}

.pagination-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-top: 28px;
  padding-top: 8px;
}

.pagination-pages {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

.pagination-page-btn {
  width: 44px;
  height: 44px;
  border: 1px solid rgba(191, 203, 224, 0.84);
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.86);
  color: #6b7280;
  font-size: 0.98rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-page-btn-active {
  border-color: transparent;
  color: white;
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  box-shadow: 0 14px 26px rgba(22, 119, 255, 0.24);
}

.pagination-nav-btn {
  min-height: 44px;
  padding: 0 8px;
  border-radius: 14px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
  color: #111827;
}

.day-group + .day-group {
  margin-top: 34px;
}

.day-label {
  margin-bottom: 16px;
  font-size: 1.22rem;
}

.day-primary {
  margin-right: 10px;
  font-weight: 700;
  color: #111827;
}

.schedule-shell-dark .day-primary {
  color: #f3f7ff;
}

.day-secondary {
  color: #6b7280;
}

.schedule-shell-dark .day-secondary {
  color: #8ea3c7;
}

.schedule-item {
  margin-bottom: 16px;
  padding: 24px 26px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid transparent;
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
}

.schedule-item-cancelled {
  border-color: rgba(239, 107, 115, 0.48);
  background: linear-gradient(180deg, rgba(255, 247, 247, 0.98), rgba(255, 255, 255, 0.92));
  box-shadow: 0 16px 34px rgba(239, 107, 115, 0.08);
}

.schedule-shell-dark .schedule-item {
  background: rgba(18, 27, 43, 0.9);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.26);
}

.schedule-shell-dark .schedule-item-cancelled {
  border-color: rgba(239, 107, 115, 0.38);
  background: linear-gradient(180deg, rgba(57, 24, 31, 0.84), rgba(18, 27, 43, 0.9));
  box-shadow: 0 18px 38px rgba(90, 18, 30, 0.24);
}

.schedule-item-expanded {
  border-color: rgba(22, 119, 255, 0.7);
  box-shadow: 0 18px 36px rgba(22, 119, 255, 0.1);
}

.schedule-item-expanded.schedule-item-cancelled {
  border-color: rgba(239, 107, 115, 0.7);
  box-shadow: 0 18px 36px rgba(239, 107, 115, 0.16);
}

.schedule-grid {
  display: grid;
  grid-template-columns: 90px 90px 90px minmax(220px, 1fr) minmax(180px, 220px) 100px;
  gap: 18px;
  align-items: start;
}

.schedule-meta-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
  grid-column: 1 / span 3;
  align-self: start;
}

.schedule-detail-row {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 18px;
  grid-column: 1 / span 5;
  grid-row: 2;
  align-self: start;
}

.schedule-detail-item {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.schedule-detail-item-wide {
  min-width: 0;
}

.schedule-detail-row .slot-value,
.schedule-detail-row .meeting-link {
  font-weight: 500;
}

.meta-label {
  margin-bottom: 6px;
  font-size: 0.95rem;
  color: #7d8899;
}

.schedule-shell-dark .meta-label {
  color: #8fa3c1;
}

.slot-value,
.course-title,
.teacher-name {
  font-size: 1.05rem;
  font-weight: 600;
  color: #111827;
}

.schedule-shell-dark .slot-value,
.schedule-shell-dark .course-title,
.schedule-shell-dark .teacher-name,
.schedule-shell-dark .course-subtitle,
.schedule-shell-dark .meeting-link {
  color: #eef4ff;
}

.schedule-shell-dark .course-title,
.schedule-shell-dark .teacher-name {
  color: #f5f9ff;
}

.course-title {
  font-size: 1.1rem;
}

.course-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.course-subtitle,
.meeting-link {
  color: #172033;
  font-size: 0.96rem;
  overflow-wrap: anywhere;
}

.meeting-link {
  word-break: break-word;
}

.status-chip {
  flex-shrink: 0;
  font-weight: 700;
}

.status-chip-cancelled {
  color: #c93f4c;
  background: rgba(255, 223, 226, 0.92);
  border: 1px solid rgba(239, 107, 115, 0.34);
}

.schedule-shell-dark .status-chip-cancelled {
  color: #ffd9dd;
  background: rgba(126, 37, 49, 0.56);
  border-color: rgba(239, 107, 115, 0.34);
}

.section-gap {
  margin-top: 22px;
}

.teacher-summary {
  display: flex;
  align-items: center;
  gap: 12px;
}

.teacher-avatar {
  border: 2px solid rgba(255, 255, 255, 0.82);
  box-shadow: 0 10px 20px rgba(110, 136, 173, 0.18);
}

.expand-block {
  display: flex;
  justify-content: flex-end;
}

.expand-link {
  padding: 0;
  border: none;
  background: transparent;
  color: #1677ff;
  font-size: 1.02rem;
  font-weight: 600;
  cursor: pointer;
}

.schedule-shell-dark .student-avatar,
.schedule-shell-dark .student-more {
  border-color: rgba(18, 27, 43, 0.92);
}

.schedule-footer {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 24px;
  margin-top: 26px;
}

.students-row {
  display: flex;
  align-items: center;
  margin-top: 10px;
}

.student-avatar {
  margin-right: -10px;
  border: 3px solid white;
}

.student-more {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  margin-left: 8px;
  border-radius: 999px;
  font-weight: 700;
  color: white;
  background: #1677ff;
  border: 3px solid white;
}

.action-row {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 12px;
}

.action-btn {
  min-height: 50px;
  padding: 0 20px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

.dialog-card {
  border-radius: 24px;
}

.schedule-shell-dark :deep(.v-overlay__content .dialog-card) {
  background: linear-gradient(180deg, rgba(22, 31, 48, 0.98), rgba(16, 24, 38, 0.96));
  color: #eff5ff;
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.filter-dialog-card {
  padding: 26px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.96), rgba(238, 245, 255, 0.92));
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow: 0 20px 45px rgba(76, 104, 148, 0.18);
}

.schedule-shell-dark :deep(.v-overlay__content .filter-dialog-card) {
  background: linear-gradient(180deg, rgba(22, 31, 48, 0.98), rgba(16, 24, 38, 0.96));
  box-shadow: 0 24px 48px rgba(4, 10, 24, 0.42);
}

.filter-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 20px;
}

.filter-dialog-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #172033;
}

.schedule-shell-dark .filter-dialog-title {
  color: #f3f7ff;
}

.filter-dialog-subtitle {
  margin-top: 8px;
  color: #64748b;
  line-height: 1.5;
}

.schedule-shell-dark .filter-dialog-subtitle {
  color: #8fa3c1;
}

.filter-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.filter-option {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  width: 100%;
  padding: 16px 18px;
  border: 1px solid rgba(199, 210, 224, 0.9);
  border-radius: 20px;
  text-align: left;
  background: rgba(255, 255, 255, 0.84);
  cursor: pointer;
  transition: all 0.2s ease;
}

.schedule-shell-dark .filter-option {
  background: rgba(12, 19, 32, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.filter-option:hover {
  transform: translateY(-1px);
  border-color: rgba(22, 119, 255, 0.35);
}

.filter-option-active {
  border-color: rgba(22, 119, 255, 0.55);
  background: linear-gradient(180deg, rgba(236, 244, 255, 0.98), rgba(227, 238, 255, 0.96));
  box-shadow: 0 14px 28px rgba(22, 119, 255, 0.12);
}

.filter-option-icon {
  display: grid;
  place-items: center;
  width: 38px;
  height: 38px;
  border-radius: 12px;
  color: #1677ff;
  background: rgba(22, 119, 255, 0.1);
  flex-shrink: 0;
}

.filter-option-title {
  display: block;
  font-size: 1rem;
  font-weight: 600;
  color: #172033;
}

.schedule-shell-dark .filter-option-title {
  color: #eef4ff;
}

.filter-option-text {
  display: block;
  margin-top: 4px;
  color: #6b7280;
  line-height: 1.45;
}

.schedule-shell-dark .filter-option-text {
  color: #8ea3c7;
}

.filter-dialog-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 22px;
}

.reset-filter-btn {
  min-height: 46px;
  padding: 0 18px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
  color: #1677ff;
  background: rgba(255, 255, 255, 0.82);
  border-color: rgba(22, 119, 255, 0.28);
  font-weight: 600;
}

.schedule-shell-dark .reset-filter-btn {
  background: rgba(18, 27, 43, 0.92);
  color: #7fbcff;
  border-color: rgba(82, 156, 255, 0.32);
}

.apply-filter-btn {
  min-height: 46px;
  padding: 0 18px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

@media (max-width: 1280px) {
  .schedule-panel {
    grid-template-columns: 210px minmax(0, 1fr);
  }

  .brand-name {
    font-size: 1.08rem;
  }

  .brand-caption {
    font-size: 0.76rem;
  }

  .schedule-grid {
    grid-template-columns: repeat(3, minmax(90px, 110px)) minmax(220px, 1fr);
  }

  .schedule-meta-grid,
  .teacher-block,
  .expand-block {
    grid-column: span 4;
  }

  .schedule-detail-row {
    grid-column: 1 / span 4;
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .expand-block {
    justify-content: flex-start;
  }

  .schedule-footer {
    flex-direction: column;
    align-items: flex-start;
  }

  .action-row {
    justify-content: flex-start;
  }
}

@media (max-width: 1024px) {
  .desktop-only-btn {
    display: none !important;
  }

  .schedule-shell {
    width: 100%;
    max-width: none;
    overflow: hidden;
  }

  .schedule-panel {
    grid-template-columns: 1fr;
    padding: 18px;
    gap: 18px;
  }

  .sidebar-card {
    display: none;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: flex;
  }

  .topbar-card {
    display: none;
  }

  .schedule-header {
    display: block;
  }

  .toolbar-row {
    flex-direction: column;
    align-items: stretch;
  }

  .pill-tabs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    width: 100%;
  }

  .toolbar-actions {
    width: 100%;
  }

  .toolbar-btn {
    width: 100%;
  }

  .toolbar-actions {
    flex-wrap: wrap;
  }

  .schedule-card {
    padding: 22px;
  }

  .schedule-grid {
    grid-template-columns: minmax(0, 1fr) 180px;
    gap: 16px 20px;
  }

  .schedule-meta-grid {
    grid-column: 1 / span 2;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
  }

  .course-block {
    grid-column: 1;
    grid-row: 2;
  }

  .schedule-detail-row {
    grid-column: 1 / span 2;
    grid-row: 3;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
  }

  .teacher-block {
    grid-column: 1;
    grid-row: 4;
  }

  .expand-block {
    grid-column: 1;
    grid-row: 5;
  }

  .pagination-row {
    flex-wrap: wrap;
  }

  .schedule-footer {
    flex-direction: column;
    align-items: stretch;
  }
}

@media (max-width: 768px) {
  .schedule-page {
    padding: 10px;
  }

  .schedule-panel {
    padding: 12px;
    gap: 12px;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: flex;
  }

  .schedule-card {
    padding: 18px;
  }

  .schedule-title {
    font-size: 1.9rem;
  }

  .pagination-row {
    flex-direction: column;
  }

  .toolbar-row {
    gap: 14px;
  }

  .pill-tabs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .toolbar-actions {
    width: 100%;
  }

  .toolbar-btn {
    width: 100%;
  }

  .schedule-grid {
    grid-template-columns: 1fr;
  }

  .schedule-meta-grid {
    grid-column: 1;
    grid-template-columns: 1fr;
    gap: 14px;
  }

  .course-block {
    grid-column: 1;
    grid-row: auto;
  }

  .schedule-detail-row {
    grid-column: 1;
    grid-row: auto;
    grid-template-columns: 1fr;
    gap: 14px;
  }

  .teacher-block {
    grid-column: 1;
    grid-row: auto;
  }

  .expand-block {
    grid-column: 1;
    grid-row: auto;
    justify-content: flex-start;
  }

  .schedule-item {
    padding: 18px;
  }

  .action-row {
    width: 100%;
    flex-direction: column;
  }

  .action-btn {
    flex: 1 1 100%;
  }

  .mobile-profile-row {
    flex-direction: column;
    align-items: stretch;
  }

  .mobile-create-btn {
    width: 100%;
    min-height: 50px;
  }
}

@media (max-width: 560px) {
  .schedule-shell {
    border-radius: 24px;
    width: 100%;
    max-width: none;
    overflow: hidden;
  }

  .schedule-panel {
    padding: 10px;
    gap: 10px;
  }

  .sidebar-card,
  .topbar-card,
  .schedule-card {
    border-radius: 20px;
  }

  .mobile-header-card,
  .mobile-utility-card,
  .schedule-card {
    padding: 14px;
  }

  .mobile-header-card {
    gap: 10px;
  }

  .mobile-menu-btn,
  .mobile-header-actions .top-icon-btn {
    width: 42px;
    height: 42px;
    min-width: 42px;
  }

  .mobile-brand-icon {
    width: 40px;
    height: 40px;
  }

  .mobile-header-actions {
    gap: 8px;
  }

  .brand-name {
    font-size: 1rem;
  }

  .brand-caption {
    font-size: 0.72rem;
  }

  .schedule-title {
    font-size: 1.65rem;
  }

  .schedule-subtitle {
    font-size: 0.92rem;
  }

  .toolbar-btn,
  .pill-tab {
    min-height: 44px;
  }

  .pagination-pages {
    gap: 8px;
  }

  .pagination-page-btn {
    width: 40px;
    height: 40px;
  }

  .filter-dialog-card {
    padding: 18px;
  }

  .filter-dialog-header,
  .filter-dialog-actions {
    align-items: stretch;
    flex-direction: column;
  }
}
</style>
