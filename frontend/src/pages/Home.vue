<template>
  <v-app>
    <v-main class="dashboard-page">
      <div class="dashboard-shell" :class="{ 'dashboard-shell-dark': darkMode }">
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
                :class="{ 'nav-item-active': item.to === '/home' }"
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

        <div class="dashboard-panel">
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
                :class="{ 'nav-item-active': item.to === '/home' }"
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
                  <div class="brand-caption">Home</div>
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

            <div class="mobile-search-card">
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

                <v-menu
                  v-if="isParent && selectedChild"
                  :disabled="linkedChildren.length <= 1"
                  location="bottom end"
                  offset="10"
                >
                  <template #activator="{ props }">
                    <button
                      type="button"
                      class="child-profile-selector child-profile-selector-mobile"
                      :class="{ 'child-profile-selector-static': linkedChildren.length <= 1 }"
                      v-bind="props"
                    >
                      <v-avatar size="40">
                        <img :src="avatarFor(`child-${selectedChild.id}`, selectedChild.name)" :alt="selectedChild.name">
                      </v-avatar>
                      <div class="child-profile-copy">
                        <div class="child-profile-name">{{ selectedChild.name }}</div>
                        <div class="child-profile-email">{{ selectedChild.email }}</div>
                      </div>
                      <v-icon v-if="linkedChildren.length > 1" size="20" class="child-profile-chevron">
                        mdi-chevron-down
                      </v-icon>
                    </button>
                  </template>

                  <v-list class="child-profile-menu">
                    <v-list-item
                      v-for="child in linkedChildren"
                      :key="child.id"
                      @click="setSelectedChildId(child.id)"
                    >
                      <template #prepend>
                        <v-avatar size="36">
                          <img :src="avatarFor(`child-${child.id}`, child.name)" :alt="child.name">
                        </v-avatar>
                      </template>
                      <v-list-item-title>{{ child.name }}</v-list-item-title>
                      <v-list-item-subtitle>{{ child.email }}</v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-menu>

                <v-btn
                  v-else-if="!isAdmin"
                  color="primary"
                  class="mobile-schedule-btn"
                  prepend-icon="mdi-calendar-month-outline"
                  to="/schedule"
                >
                  Schedule
                </v-btn>
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

            <div class="schedule-card">
              <div class="schedule-header">
                <div>
                  <h1 class="schedule-title">{{ isAdmin ? 'Admin Panel' : 'Home Overview' }}</h1>
                  <div class="schedule-subtitle">
                    {{ isAdmin
                      ? 'System-wide admin overview with users, groups, sessions and payment activity'
                      : 'Quick summary of trainings, groups, notifications and attendance' }}
                  </div>
                </div>

                <v-menu
                  v-if="isParent && selectedChild"
                  :disabled="linkedChildren.length <= 1"
                  location="bottom end"
                  offset="12"
                >
                  <template #activator="{ props }">
                    <button
                      type="button"
                      class="child-profile-selector child-profile-selector-desktop"
                      :class="{ 'child-profile-selector-static': linkedChildren.length <= 1 }"
                      v-bind="props"
                    >
                      <v-avatar size="52">
                        <img :src="avatarFor(`child-${selectedChild.id}`, selectedChild.name)" :alt="selectedChild.name">
                      </v-avatar>
                      <div class="child-profile-copy">
                        <div class="child-profile-name">{{ selectedChild.name }}</div>
                        <div class="child-profile-email">{{ selectedChild.email }}</div>
                      </div>
                      <v-icon v-if="linkedChildren.length > 1" size="22" class="child-profile-chevron">
                        mdi-chevron-down
                      </v-icon>
                    </button>
                  </template>

                  <v-list class="child-profile-menu">
                    <v-list-item
                      v-for="child in linkedChildren"
                      :key="child.id"
                      @click="setSelectedChildId(child.id)"
                    >
                      <template #prepend>
                        <v-avatar size="36">
                          <img :src="avatarFor(`child-${child.id}`, child.name)" :alt="child.name">
                        </v-avatar>
                      </template>
                      <v-list-item-title>{{ child.name }}</v-list-item-title>
                      <v-list-item-subtitle>{{ child.email }}</v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-menu>

                <v-btn
                  v-else-if="!isAdmin"
                  color="primary"
                  class="create-btn desktop-only-btn"
                  prepend-icon="mdi-calendar-month-outline"
                  to="/schedule"
                >
                  View full schedule
                </v-btn>
              </div>

              <div class="overview-stats-grid">
                <article v-for="item in displayedOverviewStats" :key="item.label" class="overview-stat-card">
                  <div class="summary-label">{{ item.label }}</div>
                  <div class="summary-value">{{ item.value }}</div>
                </article>
              </div>

              <div v-if="isAdmin" class="overview-grid">
                <section class="overview-card">
                  <div class="overview-card-header">
                    <div class="list-title">Latest Groups</div>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="group in filteredAdminGroups"
                      :key="group.id"
                      class="overview-item"
                    >
                      <div>
                        <div class="payment-name">{{ group.name }}</div>
                        <div class="payment-meta">{{ group.coach }} • {{ group.age_category || 'No age category' }}</div>
                      </div>
                      <div class="payment-side">
                        <div class="payment-amount">{{ group.students }} students</div>
                      </div>
                    </article>

                    <div v-if="!filteredAdminGroups.length" class="empty-state">
                      No groups found.
                    </div>
                  </div>
                </section>

                <section class="overview-card">
                  <div class="overview-card-header">
                    <div class="list-title">Upcoming Sessions</div>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="session in filteredAdminSessions"
                      :key="session.id"
                      class="overview-item"
                    >
                      <div>
                        <div class="payment-name">{{ session.title }}</div>
                        <div class="payment-meta">{{ session.trainer }} • {{ formatOverviewDate(session.date) }}</div>
                      </div>
                      <div class="payment-side">
                        <div class="payment-amount">{{ session.start }} - {{ session.end }}</div>
                      </div>
                    </article>

                    <div v-if="!filteredAdminSessions.length" class="empty-state">
                      No sessions found.
                    </div>
                  </div>
                </section>

                <section class="overview-card">
                  <div class="overview-card-header">
                    <div class="list-title">Recent Payments</div>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="payment in filteredAdminPayments"
                      :key="payment.id"
                      class="overview-item"
                    >
                      <div>
                        <div class="payment-name">{{ payment.child }}</div>
                        <div class="payment-meta">{{ payment.parent }} • {{ payment.method }}</div>
                        <div class="payment-secondary">{{ payment.created_at }}</div>
                      </div>
                      <div class="payment-side">
                        <div class="payment-amount">{{ formatCurrency(payment.amount) }}</div>
                        <div class="payment-meta">{{ payment.status }}</div>
                      </div>
                    </article>

                    <div v-if="!filteredAdminPayments.length" class="empty-state">
                      No payments found.
                    </div>
                  </div>
                </section>

                <section class="overview-card">
                  <div class="overview-card-header">
                    <div class="list-title">System Notifications</div>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="item in notifications"
                      :key="item.id"
                      class="overview-item"
                    >
                      <div>
                        <div class="payment-name">{{ item.title }}</div>
                        <div class="payment-meta">{{ item.time }}</div>
                      </div>
                    </article>

                    <div v-if="!notifications.length" class="empty-state">
                      No notifications available.
                    </div>
                  </div>
                </section>
              </div>

              <div v-else class="overview-grid">
                <section class="overview-card">
                  <div class="overview-card-header">
                    <div class="list-title">{{ trainingsBlockTitle }}</div>
                    <v-btn
                      v-if="!isParent"
                      variant="text"
                      color="primary"
                      class="desktop-only-btn"
                      to="/schedule"
                    >
                      View full schedule
                    </v-btn>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="training in homeTrainings.slice(0, 5)"
                      :key="training.id"
                      class="overview-item"
                    >
                      <div>
                        <div class="payment-name">{{ training.title }}</div>
                        <div class="payment-meta">{{ trainingMeta(training) }}</div>
                      </div>
                      <div class="payment-side">
                        <div class="payment-amount">{{ training.start }} - {{ training.end }}</div>
                      </div>
                    </article>

                    <div v-if="!homeTrainings.length" class="empty-state">
                      {{ trainingsBlockEmptyState }}
                    </div>
                  </div>
                </section>

                <section class="overview-card">
                  <div class="overview-card-header">
                    <div class="list-title">Notifications</div>
                  </div>

                  <div class="list-wrap">
                    <article
                      v-for="item in notifications"
                      :key="item.id"
                      class="overview-item"
                    >
                      <div>
                        <div class="payment-name">{{ item.title }}</div>
                        <div class="payment-meta">{{ item.time }}</div>
                      </div>
                    </article>
                  </div>
                </section>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="dialog" max-width="560">
          <v-card class="dialog-card base-dialog-card">
            <v-card-title class="base-dialog-title">
              {{ isEditing ? 'Edit Training' : 'Create Training' }}
            </v-card-title>

            <v-card-text class="base-dialog-content">
              <v-text-field label="Title" v-model="newTraining.title" class="base-dialog-field" />
              <v-text-field label="Date (YYYY-MM-DD)" v-model="newTraining.date" class="base-dialog-field" />
              <v-text-field label="Start time" v-model="newTraining.start" class="base-dialog-field" />
              <v-text-field label="End time" v-model="newTraining.end" class="base-dialog-field" />
              <v-text-field label="Trainer" v-model="newTraining.trainer" class="base-dialog-field" />
              <v-textarea label="Description" v-model="newTraining.description" class="base-dialog-field" />
            </v-card-text>

            <v-card-actions class="base-dialog-actions">
              <v-spacer></v-spacer>
              <v-btn variant="outlined" class="base-dialog-cancel-btn" @click="dialog = false">Cancel</v-btn>
              <v-btn color="primary" class="base-dialog-save-btn" @click="saveTraining">
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
                  Sort trainings by time or title without leaving the home overview.
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
import { dashboardApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const dialog = ref(false)
const filterDialog = ref(false)
const notificationsDialog = ref(false)
const isEditing = ref(false)
const selectedSort = ref('time')
const darkMode = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const darkModeStorageKey = 'app-dark-mode'
const loading = ref(false)
const dashboardMode = ref('standard')

const navItems = computed(() => {
  if (user.value?.role === 'admin') {
    return [
      { label: 'Admin Panel', icon: 'mdi-shield-crown-outline', to: '/admin' },
      { label: 'Users', icon: 'mdi-account-multiple-outline', to: '/admin-users' },
      { label: 'Groups', icon: 'mdi-account-group-outline', to: '/manage-groups' },
      { label: 'Sessions', icon: 'mdi-calendar-clock-outline', to: '/manage-sessions' },
      { label: 'Payments', icon: 'mdi-credit-card-outline', to: '/admin-payments' }
    ]
  }

  return [
    { label: 'Home', icon: 'mdi-home-outline', to: '/home' },
    { label: 'Schedule', icon: 'mdi-calendar-month-outline', to: '/schedule' },
    { label: 'Groups', icon: 'mdi-account-group-outline', to: '/groups' },
    { label: 'Attendance', icon: 'mdi-check-circle-outline', to: user.value?.role === 'coach' ? '/coach-attendance' : '/attendance' },
    ...(user.value?.role === 'parent'
      ? [{ label: 'Payments', icon: 'mdi-credit-card-outline', to: '/payments' }]
      : [])
  ]
})

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
  selectedChildId,
  setSelectedChildId,
  syncSelectedChild
} = useSelectedChild()

const trainings = ref([])
const overviewStats = ref([])
const adminGroups = ref([])
const adminSessions = ref([])
const adminPayments = ref([])
const linkedChildren = ref([])
const newTraining = ref({
  title: '',
  date: '',
  start: '',
  end: '',
  trainer: '',
  description: ''
})

const profileName = computed(() => {
  if (!user.value) return 'SportSystem User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const profileEmail = computed(() => user.value?.email ?? 'user@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)
const isAdmin = computed(() => (user.value?.role === 'admin') || dashboardMode.value === 'admin')
const isParent = computed(() => user.value?.role === 'parent')
const isCoach = computed(() => user.value?.role === 'coach')
const selectedChild = computed(() =>
  linkedChildren.value.find((child) => child.id === selectedChildId.value) ?? linkedChildren.value[0] ?? null
)

const nextThreeDaysTrainings = computed(() => {
  if (!isParent.value || !selectedChild.value) return trainings.value

  return trainings.value.filter((training) =>
    Array.isArray(training.child_ids) && training.child_ids.includes(selectedChild.value.id)
  )
})

const homeTrainings = computed(() => {
  if (!isCoach.value) return nextThreeDaysTrainings.value

  const limitTimestamp = countdownNow.value + (7 * 24 * 60 * 60 * 1000)

  return trainings.value.filter((training) => {
    const startAt = trainingStartTimestamp(training)
    return Number.isFinite(startAt) && startAt <= limitTimestamp
  })
})

const trainingsBlockTitle = computed(() =>
  isCoach.value ? 'My next trainings' : 'Next 3 Days Trainings'
)

const trainingsBlockEmptyState = computed(() =>
  isCoach.value
    ? 'No trainings scheduled for the next seven days.'
    : 'No trainings scheduled for the next three days.'
)

const notifications = computed(() => notificationItems.value.slice(0, 3))
const filteredAdminGroups = computed(() => adminGroups.value.slice(0, 5))
const filteredAdminSessions = computed(() => adminSessions.value.slice(0, 5))
const filteredAdminPayments = computed(() => adminPayments.value.slice(0, 5))
const countdownNow = ref(Date.now())
let countdownTimer = null

const countdownTrainings = computed(() => {
  if (!isParent.value || !selectedChild.value) return trainings.value

  return trainings.value.filter((training) =>
    Array.isArray(training.child_ids) && training.child_ids.includes(selectedChild.value.id)
  )
})

const coachThreeDayTrainingHours = computed(() => {
  if (!isCoach.value) return null

  const limitTimestamp = countdownNow.value + (3 * 24 * 60 * 60 * 1000)
  const totalMinutes = trainings.value
    .filter((training) => {
      const startAt = trainingStartTimestamp(training)
      return Number.isFinite(startAt) && startAt >= countdownNow.value && startAt <= limitTimestamp
    })
    .reduce((sum, training) => sum + trainingDurationMinutes(training), 0)

  return formatDuration(totalMinutes)
})

const nextTrainingCountdown = computed(() => {
  const nextTraining = countdownTrainings.value
    .map((training) => ({
      ...training,
      startAt: trainingStartTimestamp(training)
    }))
    .filter((training) => Number.isFinite(training.startAt) && training.startAt >= countdownNow.value)
    .sort((left, right) => left.startAt - right.startAt)[0]

  if (!nextTraining) return 'No upcoming'

  const diffMs = nextTraining.startAt - countdownNow.value
  const weekMs = 7 * 24 * 60 * 60 * 1000

  if (diffMs > weekMs) return 'More than a week'

  const totalMinutes = Math.max(0, Math.floor(diffMs / 60000))
  const totalHours = Math.floor(totalMinutes / 60)
  const days = Math.floor(totalHours / 24)
  const hours = totalHours % 24
  const minutes = totalMinutes % 60

  if (days > 0) return `${days}d ${hours}h`
  if (totalHours > 0) return `${totalHours}h ${minutes}m`
  if (minutes > 0) return `${minutes}m`

  return 'Now'
})

const displayedOverviewStats = computed(() =>
  overviewStats.value.map((item) =>
    item.label === 'Next training countdown'
      ? { ...item, value: nextTrainingCountdown.value }
      : isCoach.value && item.label === 'Training hours in 3 days'
        ? { ...item, value: coachThreeDayTrainingHours.value }
        : item
  )
)

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  countdownNow.value = Date.now()
  countdownTimer = window.setInterval(() => {
    countdownNow.value = Date.now()
  }, 60000)
  loadDashboard()
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
  if (countdownTimer) {
    window.clearInterval(countdownTimer)
    countdownTimer = null
  }
})

watch(notificationsDialog, (value) => {
  if (value) {
    loadNotifications(true)
  }
})

async function loadDashboard() {
  loading.value = true

  try {
    const data = await dashboardApi.get()
    dashboardMode.value = data?.mode ?? 'standard'
    trainings.value = (data?.next_trainings ?? []).map((training) => withTrainingAvatars({
      ...training,
      group: training.title,
      students: []
    }))
    linkedChildren.value = data?.linked_children ?? []
    if (isParent.value) {
      syncSelectedChild(linkedChildren.value.map((child) => child.id))
    }
    overviewStats.value = data?.overview_stats ?? []
    adminGroups.value = data?.latest_groups ?? []
    adminSessions.value = data?.recent_sessions ?? []
    adminPayments.value = data?.recent_payments ?? []
  } finally {
    loading.value = false
  }
}

function saveTraining() {
  if (!newTraining.value.title) {
    dialog.value = false
    return
  }

  trainings.value.unshift(withTrainingAvatars({
    id: Date.now(),
    ...newTraining.value,
    group: newTraining.value.title,
    students: []
  }))
  dialog.value = false
}

function parseTime(value) {
  return new Date(`1970-01-01 ${value}`).getTime()
}

function trainingStartTimestamp(training) {
  return new Date(`${training.date}T${training.start}:00`).getTime()
}

function trainingDurationMinutes(training) {
  const startAt = trainingStartTimestamp(training)
  const endAt = new Date(`${training.date}T${training.end}:00`).getTime()

  if (!Number.isFinite(startAt) || !Number.isFinite(endAt)) return 0

  return Math.max(0, Math.round((endAt - startAt) / 60000))
}

function formatDuration(totalMinutes) {
  const minutes = Math.max(0, Number(totalMinutes) || 0)
  const hours = Math.floor(minutes / 60)
  const remainingMinutes = minutes % 60

  if (hours > 0 && remainingMinutes > 0) return `${hours}h ${remainingMinutes}m`
  if (hours > 0) return `${hours}h`
  return `${remainingMinutes}m`
}

function formatOverviewDate(value) {
  return new Date(`${value}T00:00:00`).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

function trainingMeta(training) {
  const date = formatOverviewDate(training.date)

  if (isCoach.value) {
    const count = Number(training.students_count ?? 0)
    const label = count === 1 ? 'student' : 'students'
    return `${count} ${label} • ${date}`
  }

  return `${training.trainer} • ${date}`
}

function formatCurrency(value) {
  return `€${Number(value ?? 0)}`
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
</script>

<style scoped>
.dashboard-page {
  padding: 24px;
}

.dashboard-shell {
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

.dashboard-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.dashboard-panel {
  display: grid;
  grid-template-columns: 232px minmax(0, 1fr);
  gap: 22px;
  padding: 22px;
}

.mobile-header-card,
.mobile-search-card {
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
  margin-top: 12px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
}

.mobile-logout-btn {
  margin-top: 12px;
  min-height: 52px;
  border-radius: 18px;
  color: #111827;
  text-transform: none;
  letter-spacing: 0;
  border-color: rgba(148, 163, 184, 0.26);
  background: rgba(255, 255, 255, 0.72);
}

.dashboard-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.dashboard-shell-dark .mobile-logout-btn {
  color: #eef4ff;
  border-color: rgba(74, 92, 126, 0.42);
  background: rgba(18, 27, 43, 0.92);
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

.dashboard-shell-dark .sidebar-card {
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

.dashboard-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.dashboard-shell-dark .brand-caption {
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

.dashboard-shell-dark .nav-item {
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

.nav-item-settings {
  margin-top: auto;
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

.dashboard-shell-dark .mobile-header-card {
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

.dashboard-shell-dark .mobile-menu-btn {
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

.mobile-search-card {
  flex-direction: column;
  gap: 0;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.dashboard-shell-dark .mobile-search-card {
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

.mobile-schedule-btn {
  min-height: 56px;
  padding: 0 18px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  box-shadow: 0 18px 34px rgba(22, 119, 255, 0.22);
}

.child-profile-selector {
  display: flex;
  align-items: center;
  gap: 14px;
  width: 100%;
  padding: 14px 18px;
  border: 1px solid rgba(231, 238, 247, 0.95);
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 14px 28px rgba(110, 136, 173, 0.08);
  text-align: left;
  cursor: pointer;
}

.dashboard-shell-dark .child-profile-selector {
  border-color: rgba(74, 92, 126, 0.42);
  background: rgba(18, 27, 43, 0.9);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.26);
}

.child-profile-selector-static {
  cursor: default;
}

.child-profile-selector-desktop {
  min-width: 320px;
  max-width: 360px;
}

.child-profile-selector-mobile {
  min-height: 76px;
  flex: 1;
}

.child-profile-copy {
  min-width: 0;
  flex: 1;
}

.child-profile-name {
  font-size: 1rem;
  font-weight: 700;
  color: #172033;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dashboard-shell-dark .child-profile-name {
  color: #f3f7ff;
}

.child-profile-email {
  margin-top: 4px;
  font-size: 0.92rem;
  color: #7b8798;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dashboard-shell-dark .child-profile-email {
  color: #94a6c4;
}

.child-profile-chevron {
  color: #7b8798;
  flex-shrink: 0;
}

.dashboard-shell-dark .child-profile-chevron {
  color: #94a6c4;
}

:deep(.child-profile-menu) {
  min-width: 300px;
  padding: 8px;
  border-radius: 22px;
  border: 1px solid rgba(223, 231, 243, 0.92);
  background: rgba(255, 255, 255, 0.98);
  box-shadow: 0 24px 48px rgba(79, 106, 154, 0.18);
}

:deep(.child-profile-menu .v-list-item) {
  min-height: 62px;
  border-radius: 16px;
}

:deep(.child-profile-menu .v-list-item-title) {
  color: #172033;
  font-weight: 600;
}

:deep(.child-profile-menu .v-list-item-subtitle) {
  color: #7b8798;
}

.dashboard-shell-dark :deep(.child-profile-menu) {
  border-color: rgba(74, 92, 126, 0.42);
  background: rgba(18, 27, 43, 0.98);
  box-shadow: 0 28px 52px rgba(4, 10, 24, 0.4);
}

.dashboard-shell-dark :deep(.child-profile-menu .v-list-item-title) {
  color: #f3f7ff;
}

.dashboard-shell-dark :deep(.child-profile-menu .v-list-item-subtitle) {
  color: #94a6c4;
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

.dashboard-shell-dark .topbar-card {
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

.dashboard-shell-dark .top-icon-btn {
  color: #dce6f7;
  background: rgba(18, 27, 43, 0.92);
  border-color: rgba(74, 92, 126, 0.46);
}

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
}

.logout-btn {
  color: #111827;
}

.dashboard-shell-dark .top-icon-btn-active {
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

.profile-pill > div {
  min-width: 0;
}

.dashboard-shell-dark .profile-pill {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.dashboard-shell-dark .profile-name {
  color: #f2f7ff;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.dashboard-shell-dark .profile-email {
  color: #93a5c3;
}

.schedule-card {
  min-width: 0;
  padding: 26px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.dashboard-shell-dark .schedule-card {
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

.dashboard-shell-dark .schedule-title {
  color: #f3f7ff;
}

.schedule-subtitle {
  margin-top: 10px;
  font-size: 1rem;
  color: #66748a;
}

.dashboard-shell-dark .schedule-subtitle {
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

.overview-stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.overview-stat-card,
.overview-card {
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.72);
  background: rgba(255, 255, 255, 0.84);
  box-shadow: 0 12px 28px rgba(110, 136, 173, 0.08);
}

.dashboard-shell-dark .overview-stat-card,
.dashboard-shell-dark .overview-card {
  background: rgba(18, 27, 43, 0.9);
  border-color: rgba(74, 92, 126, 0.42);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.26);
}

.overview-stat-card {
  padding: 22px;
}

.summary-label {
  font-size: 0.94rem;
  color: #6f7d90;
}

.dashboard-shell-dark .summary-label {
  color: #8fa3c1;
}

.summary-value {
  margin-top: 10px;
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  line-height: 1;
}

.dashboard-shell-dark .summary-value {
  color: #f4f8ff;
}

.overview-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
  gap: 18px;
  margin-bottom: 18px;
}

.overview-card {
  padding: 22px;
}

.overview-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 16px;
}

.list-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #111827;
}

.dashboard-shell-dark .list-title {
  color: #f2f7ff;
}

.list-wrap {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.overview-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 18px;
  border-radius: 18px;
  background: rgba(244, 248, 255, 0.9);
  border: 1px solid rgba(224, 232, 243, 0.92);
}

.overview-item > div:first-child {
  min-width: 0;
  flex: 1;
}

.dashboard-shell-dark .overview-item {
  background: rgba(12, 19, 32, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.payment-name {
  font-size: 1rem;
  font-weight: 600;
  color: #172033;
  overflow-wrap: anywhere;
}

.dashboard-shell-dark .payment-name,
.dashboard-shell-dark .payment-amount {
  color: #eef4ff;
}

.payment-meta {
  margin-top: 4px;
  font-size: 0.92rem;
  color: #718096;
  overflow-wrap: anywhere;
}

.dashboard-shell-dark .payment-meta {
  color: #8ea3c7;
}

.payment-side {
  text-align: right;
}

.payment-amount {
  font-size: 0.95rem;
  font-weight: 600;
  color: #172033;
}

.empty-state {
  padding: 18px;
  border-radius: 18px;
  color: #708196;
  background: rgba(244, 248, 255, 0.8);
  border: 1px dashed rgba(188, 199, 218, 0.9);
}

.dashboard-shell-dark .empty-state {
  color: #99acc8;
  background: rgba(12, 19, 32, 0.76);
  border-color: rgba(63, 80, 114, 0.58);
}

.dialog-card {
  border-radius: 24px;
}

.dashboard-shell-dark :deep(.v-overlay__content .dialog-card) {
  background: linear-gradient(180deg, rgba(22, 31, 48, 0.98), rgba(16, 24, 38, 0.96));
  color: #eff5ff;
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.base-dialog-card {
  padding: 8px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.98), rgba(238, 245, 255, 0.94));
  border: 1px solid rgba(255, 255, 255, 0.74);
  box-shadow: 0 22px 48px rgba(76, 104, 148, 0.18);
}

.base-dialog-title {
  font-size: 1.45rem;
  font-weight: 700;
  color: #172033;
}

.dashboard-shell-dark .base-dialog-title {
  color: #f3f7ff;
}

.base-dialog-content {
  display: grid;
  gap: 8px;
}

.base-dialog-card :deep(.base-dialog-field .v-field) {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.84);
  box-shadow: inset 0 0 0 1px rgba(223, 232, 246, 0.95);
}

.base-dialog-card :deep(.base-dialog-field .v-field--focused) {
  background: rgba(255, 255, 255, 0.96);
  box-shadow:
    inset 0 0 0 1px rgba(74, 144, 255, 0.72),
    0 0 0 4px rgba(22, 119, 255, 0.08);
}

.base-dialog-card :deep(.base-dialog-field .v-field__outline) {
  --v-field-border-opacity: 0;
}

.base-dialog-card :deep(.base-dialog-field input),
.base-dialog-card :deep(.base-dialog-field textarea),
.base-dialog-card :deep(.base-dialog-field .v-field__input),
.base-dialog-card :deep(.base-dialog-field .v-label),
.base-dialog-card :deep(.base-dialog-field .v-field__append-inner) {
  color: #172033;
}

.dashboard-shell-dark .base-dialog-card :deep(.base-dialog-field .v-field) {
  background: rgba(17, 25, 40, 0.88);
  box-shadow: inset 0 0 0 1px rgba(64, 82, 116, 0.72);
}

.dashboard-shell-dark .base-dialog-card :deep(.base-dialog-field .v-field--focused) {
  background: rgba(22, 31, 48, 0.98);
  box-shadow:
    inset 0 0 0 1px rgba(97, 155, 255, 0.74),
    0 0 0 4px rgba(22, 119, 255, 0.12);
}

.dashboard-shell-dark .base-dialog-card :deep(.base-dialog-field input),
.dashboard-shell-dark .base-dialog-card :deep(.base-dialog-field textarea),
.dashboard-shell-dark .base-dialog-card :deep(.base-dialog-field .v-field__input) {
  color: #eef4ff;
}

.dashboard-shell-dark .base-dialog-card :deep(.base-dialog-field .v-label),
.dashboard-shell-dark .base-dialog-card :deep(.base-dialog-field .v-field__append-inner) {
  color: #94a6c4;
}

.base-dialog-actions {
  padding: 8px 16px 16px;
  gap: 10px;
}

.base-dialog-cancel-btn,
.base-dialog-save-btn {
  min-height: 44px;
  padding: 0 18px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

.base-dialog-cancel-btn {
  color: #1677ff;
  background: rgba(255, 255, 255, 0.82);
  border-color: rgba(22, 119, 255, 0.28);
  font-weight: 600;
}

.dashboard-shell-dark .base-dialog-cancel-btn {
  background: rgba(18, 27, 43, 0.92);
  color: #7fbcff;
  border-color: rgba(82, 156, 255, 0.32);
}

.filter-dialog-card {
  padding: 26px;
  background: linear-gradient(180deg, rgba(247, 251, 255, 0.96), rgba(238, 245, 255, 0.92));
  border: 1px solid rgba(255, 255, 255, 0.72);
  box-shadow: 0 20px 45px rgba(76, 104, 148, 0.18);
}

.dashboard-shell-dark :deep(.v-overlay__content .filter-dialog-card) {
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

.dashboard-shell-dark .filter-dialog-title {
  color: #f3f7ff;
}

.filter-dialog-subtitle {
  margin-top: 8px;
  color: #64748b;
  line-height: 1.5;
}

.dashboard-shell-dark .filter-dialog-subtitle {
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

.dashboard-shell-dark .filter-option {
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

.dashboard-shell-dark .filter-option-title {
  color: #eef4ff;
}

.filter-option-text {
  display: block;
  margin-top: 4px;
  color: #6b7280;
  line-height: 1.45;
}

.dashboard-shell-dark .filter-option-text {
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

.dashboard-shell-dark .reset-filter-btn {
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
  .dashboard-panel {
    grid-template-columns: 210px minmax(0, 1fr);
  }

  .brand-name {
    font-size: 1.08rem;
  }

  .brand-caption {
    font-size: 0.76rem;
  }

  .overview-stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .desktop-only-btn {
    display: none !important;
  }

  .mobile-schedule-btn {
    min-width: 168px;
    padding: 0 22px;
  }

  .dashboard-panel {
    grid-template-columns: 1fr;
    padding: 18px;
    gap: 18px;
  }

  .sidebar-card {
    display: none;
  }

  .mobile-header-card {
    display: flex;
  }

  .mobile-search-card {
    display: flex;
  }

  .topbar-card {
    display: none;
  }

  .schedule-card {
    padding: 22px;
  }

  .schedule-header {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: end;
    gap: 16px;
  }

  .create-btn {
    min-width: 220px;
  }

  .overview-stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .overview-grid {
    grid-template-columns: 1fr;
  }

  .overview-card {
    padding: 20px;
  }

  .overview-card-header {
    align-items: center;
  }

  .overview-item {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
  }
}

@media (max-width: 768px) {
  .dashboard-shell {
    width: 100%;
    max-width: none;
    overflow: hidden;
  }

  .dashboard-page {
    padding: 10px;
  }

  .dashboard-panel {
    padding: 12px;
    gap: 12px;
    grid-template-columns: 1fr;
  }

  .mobile-header-card,
  .mobile-search-card {
    display: flex;
  }

  .overview-stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .topbar-card {
    display: none;
  }

  .schedule-header {
    margin-bottom: 20px;
    gap: 14px;
  }

  .create-btn {
    width: 100%;
  }

  .schedule-title {
    font-size: 2rem;
  }

  .schedule-subtitle {
    max-width: 34ch;
  }

  .overview-card-header {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }

  .overview-card-header :deep(.v-btn) {
    justify-content: flex-start;
    align-self: flex-start;
    padding-left: 0;
  }

  .overview-item {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }

  .payment-side {
    text-align: left;
  }
}

@media (min-width: 769px) and (max-width: 900px) {
  .dashboard-panel {
    padding: 16px;
    gap: 16px;
  }

  .nav-item {
    flex: 1 1 calc(50% - 6px);
    min-width: 0;
  }

  .topbar-card {
    grid-template-columns: 1fr;
  }

  .topbar-tools {
    grid-template-columns: 54px 54px minmax(0, 1fr);
  }

  .schedule-header {
    grid-template-columns: 1fr;
    align-items: stretch;
  }

  .create-btn {
    width: 100%;
    min-width: 0;
  }
}

@media (max-width: 560px) {
  .dashboard-shell {
    border-radius: 24px;
    width: 100%;
    max-width: none;
    overflow: hidden;
  }

  .dashboard-panel {
    padding: 10px;
    gap: 10px;
  }

  .sidebar-card,
  .topbar-card,
  .schedule-card {
    border-radius: 20px;
  }

  .brand-block {
    margin-bottom: 14px;
    padding: 6px 4px;
  }

  .brand-name {
    font-size: 1rem;
  }

  .brand-caption {
    font-size: 0.72rem;
  }

  .schedule-header {
    margin-bottom: 18px;
  }

  .schedule-title {
    font-size: 1.65rem;
  }

  .schedule-subtitle {
    font-size: 0.92rem;
  }

  .schedule-card,
  .topbar-card,
  .sidebar-card {
    padding: 14px;
  }

  .overview-stat-card,
  .overview-card {
    padding: 16px;
    border-radius: 20px;
  }

  .summary-value {
    font-size: 1.65rem;
  }

  .overview-item,
  .empty-state {
    padding: 14px;
  }

  .topbar-tools {
    grid-template-columns: 1fr;
  }

  .mobile-header-card {
    padding: 12px 14px;
    border-radius: 20px;
  }

  .mobile-search-card {
    padding: 12px;
    border-radius: 20px;
  }

  .mobile-menu-btn,
  .mobile-header-actions .top-icon-btn {
    width: 42px;
    height: 42px;
  }

  .mobile-brand-inline {
    gap: 10px;
  }

  .mobile-brand-icon {
    width: 40px;
    height: 40px;
  }

  .mobile-header-actions {
    gap: 8px;
  }

  .mobile-profile-row {
    flex-direction: column;
    align-items: stretch;
  }

  .overview-stats-grid {
    grid-template-columns: 1fr;
  }

  .schedule-header {
    gap: 14px;
  }

  .overview-card-header :deep(.v-btn) {
    width: 100%;
    justify-content: flex-start;
  }

  .mobile-schedule-btn {
    width: 100%;
    min-height: 50px;
  }

  .profile-pill {
    width: 100%;
    gap: 10px;
    border-radius: 18px;
  }

  .mobile-drawer-inner {
    padding: 14px;
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

@media (max-width: 380px) {
  .dashboard-page {
    padding: 8px;
  }

  .dashboard-shell {
    border-radius: 20px;
  }

  .dashboard-panel {
    padding: 8px;
    gap: 8px;
  }

  .mobile-header-card,
  .mobile-search-card,
  .schedule-card,
  .sidebar-card {
    padding: 10px;
    border-radius: 18px;
  }

  .mobile-header-card {
    gap: 8px;
  }

  .mobile-brand-inline {
    gap: 8px;
  }

  .mobile-brand-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
  }

  .mobile-brand-copy .brand-name {
    font-size: 0.92rem;
  }

  .mobile-brand-copy .brand-caption {
    font-size: 0.68rem;
  }

  .mobile-menu-btn,
  .mobile-header-actions .top-icon-btn {
    width: 40px;
    height: 40px;
    min-width: 40px;
  }

  .icon-badge {
    top: -1px;
    right: -1px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    font-size: 0.64rem;
  }

  .schedule-title {
    font-size: 1.45rem;
  }

  .schedule-subtitle {
    font-size: 0.88rem;
  }

  .create-btn,
  .mobile-schedule-btn {
    min-height: 46px;
    padding: 0 14px;
    font-size: 0.92rem;
  }

  .overview-stat-card,
  .overview-card,
  .overview-item,
  .empty-state {
    padding: 12px;
    border-radius: 18px;
  }

  .summary-label,
  .payment-meta {
    font-size: 0.84rem;
  }

  .summary-value {
    font-size: 1.45rem;
  }

  .list-title,
  .payment-name,
  .payment-amount {
    font-size: 0.92rem;
  }

  .profile-pill,
  .mobile-profile-pill {
    padding: 8px 10px;
  }

  .profile-name {
    font-size: 0.88rem;
  }

  .profile-email {
    font-size: 0.78rem;
  }
}

@media (max-width: 320px) {
  .dashboard-page {
    padding: 6px;
  }

  .dashboard-panel {
    padding: 6px;
    gap: 6px;
  }

  .mobile-header-card,
  .mobile-search-card,
  .schedule-card {
    padding: 8px;
    border-radius: 16px;
  }

  .mobile-brand-inline {
    min-width: 0;
  }

  .mobile-brand-copy .brand-name {
    font-size: 0.86rem;
  }

  .mobile-brand-copy .brand-caption {
    font-size: 0.64rem;
  }

  .mobile-header-actions {
    gap: 6px;
  }

  .mobile-menu-btn,
  .mobile-header-actions .top-icon-btn {
    width: 38px;
    height: 38px;
    min-width: 38px;
  }

  .overview-stats-grid,
  .overview-grid,
  .list-wrap {
    gap: 10px;
  }

  .schedule-header,
  .overview-card-header {
    gap: 8px;
  }

  .schedule-title {
    font-size: 1.34rem;
  }

  .schedule-subtitle {
    font-size: 0.82rem;
  }

  .summary-value {
    font-size: 1.28rem;
  }
}
</style>
