<template>
  <v-app>
    <v-main class="admin-home-page">
      <div class="admin-home-shell" :class="{ 'admin-home-shell-dark': darkMode }">
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
                <div class="brand-caption">Admin workspace</div>
              </div>
            </div>

            <nav class="mobile-nav-list">
              <v-btn
                v-for="item in navItems"
                :key="`mobile-${item.label}`"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/admin' }"
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
                <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
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

        <div class="admin-home-panel">
          <aside class="sidebar-card">
            <div class="brand-block">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">Admin workspace</div>
              </div>
            </div>

            <nav class="nav-list">
              <v-btn
                v-for="item in navItems"
                :key="item.label"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/admin' }"
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
                  <div class="brand-caption">Admin Panel</div>
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
                    <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    placeholder="Search admin panel"
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
                    <img :src="avatarFor(profileSeed, profileName)" alt="Admin profile">
                  </v-avatar>
                  <div>
                    <div class="profile-name">{{ profileName }}</div>
                    <div class="profile-email">{{ profileEmail }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="overview-shell-card">
              <div class="overview-header">
                <div>
                  <h1 class="overview-title">Admin Panel</h1>
                  <div class="overview-subtitle">
                    System-wide overview with users, groups, sessions, payments and notifications.
                  </div>
                </div>
              </div>

              <div class="overview-stats-grid">
                <article v-for="item in overviewStats" :key="item.label" class="overview-stat-card">
                  <div class="summary-label">{{ item.label }}</div>
                  <div class="summary-value">{{ item.value }}</div>
                </article>
              </div>

              <div class="overview-grid">
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
import { dashboardApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const search = ref('')
const darkMode = ref(false)
const notificationsDialog = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const darkModeStorageKey = 'app-dark-mode'

const navItems = [
  { label: 'Admin Panel', icon: 'mdi-shield-crown-outline', to: '/admin' },
  { label: 'Admin Users', icon: 'mdi-account-multiple-outline', to: '/admin-users' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/manage-groups' },
  { label: 'Sessions', icon: 'mdi-calendar-clock-outline', to: '/manage-sessions' }
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

const overviewStats = ref([])
const adminGroups = ref([])
const adminSessions = ref([])
const adminPayments = ref([])

const overviewQuery = computed(() => search.value.trim().toLowerCase())
const profileName = computed(() => {
  if (!user.value) return 'Admin User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const profileEmail = computed(() => user.value?.email ?? 'admin@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)

const notifications = computed(() => notificationItems.value.slice(0, 3))
const filteredAdminGroups = computed(() => {
  if (!overviewQuery.value) return adminGroups.value.slice(0, 5)
  return adminGroups.value
    .filter((group) =>
      [group.name, group.coach, group.age_category].filter(Boolean).some((value) =>
        value.toLowerCase().includes(overviewQuery.value)
      )
    )
    .slice(0, 5)
})

const filteredAdminSessions = computed(() => {
  if (!overviewQuery.value) return adminSessions.value.slice(0, 5)
  return adminSessions.value
    .filter((session) =>
      [session.title, session.trainer, session.status].filter(Boolean).some((value) =>
        value.toLowerCase().includes(overviewQuery.value)
      )
    )
    .slice(0, 5)
})

const filteredAdminPayments = computed(() => {
  if (!overviewQuery.value) return adminPayments.value.slice(0, 5)
  return adminPayments.value
    .filter((payment) =>
      [payment.parent, payment.child, payment.method, payment.status].filter(Boolean).some((value) =>
        value.toLowerCase().includes(overviewQuery.value)
      )
    )
    .slice(0, 5)
})

onMounted(() => {
  darkMode.value = localStorage.getItem(darkModeStorageKey) === 'true'
  updateViewportState()
  window.addEventListener('resize', updateViewportState)
  loadDashboard()
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

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

async function loadDashboard() {
  const data = await dashboardApi.get()
  overviewStats.value = data?.overview_stats ?? []
  adminGroups.value = data?.latest_groups ?? []
  adminSessions.value = data?.recent_sessions ?? []
  adminPayments.value = data?.recent_payments ?? []
}

function formatOverviewDate(value) {
  return new Date(value).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

function formatCurrency(value) {
  return `€${Number(value ?? 0)}`
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

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}
</script>

<style scoped>
.admin-home-page {
  padding: 24px;
}

.admin-home-shell {
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

.admin-home-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.admin-home-panel {
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

.admin-home-shell-dark .mobile-drawer-profile {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.admin-home-shell-dark .mobile-logout-btn {
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

.admin-home-shell-dark .sidebar-card {
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

.admin-home-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.admin-home-shell-dark .brand-caption {
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

.admin-home-shell-dark .nav-item {
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

.admin-home-shell-dark .mobile-header-card {
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

.admin-home-shell-dark .mobile-menu-btn {
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

.mobile-utility-card {
  flex-direction: column;
  gap: 0;
  padding: 14px 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-home-shell-dark .mobile-utility-card {
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

.admin-home-shell-dark .topbar-card {
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

.admin-home-shell-dark .search-shell {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.search-shell-icon {
  color: #6b7280;
  flex-shrink: 0;
}

.admin-home-shell-dark .search-shell-icon {
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

.search-field :deep(input::placeholder) {
  color: #111827;
  opacity: 1;
}

.admin-home-shell-dark .search-field :deep(input),
.admin-home-shell-dark .search-field :deep(input::placeholder) {
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

.admin-home-shell-dark .top-icon-btn {
  color: #dce6f7;
  background: rgba(18, 27, 43, 0.92);
  border-color: rgba(74, 92, 126, 0.46);
}

.top-icon-btn-active {
  color: #1677ff;
  background: rgba(232, 242, 255, 0.96);
  border-color: rgba(22, 119, 255, 0.28);
}

.admin-home-shell-dark .top-icon-btn-active {
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

.admin-home-shell-dark .profile-pill {
  background: rgba(18, 27, 43, 0.92);
  border: 1px solid rgba(74, 92, 126, 0.42);
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 600;
  color: #172033;
}

.admin-home-shell-dark .profile-name {
  color: #f2f7ff;
}

.profile-email {
  font-size: 0.9rem;
  color: #7b8798;
  word-break: break-word;
}

.admin-home-shell-dark .profile-email {
  color: #93a5c3;
}

.overview-shell-card {
  min-width: 0;
  padding: 26px;
  border-radius: 28px;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-home-shell-dark .overview-shell-card {
  background: rgba(22, 31, 48, 0.72);
  border-color: rgba(74, 92, 126, 0.42);
}

.overview-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.overview-title {
  margin: 0;
  font-size: 2.3rem;
  line-height: 1.1;
  font-weight: 700;
  color: #121826;
}

.admin-home-shell-dark .overview-title {
  color: #f3f7ff;
}

.overview-subtitle {
  margin-top: 10px;
  font-size: 1rem;
  color: #66748a;
}

.admin-home-shell-dark .overview-subtitle {
  color: #8fa3c1;
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

.admin-home-shell-dark .overview-stat-card,
.admin-home-shell-dark .overview-card {
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

.admin-home-shell-dark .summary-label {
  color: #8fa3c1;
}

.summary-value {
  margin-top: 10px;
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  line-height: 1;
}

.admin-home-shell-dark .summary-value {
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

.admin-home-shell-dark .list-title {
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

.admin-home-shell-dark .overview-item {
  background: rgba(12, 19, 32, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.payment-name {
  font-size: 1rem;
  font-weight: 600;
  color: #172033;
  overflow-wrap: anywhere;
}

.admin-home-shell-dark .payment-name,
.admin-home-shell-dark .payment-amount {
  color: #eef4ff;
}

.payment-meta {
  margin-top: 4px;
  font-size: 0.92rem;
  color: #718096;
  overflow-wrap: anywhere;
}

.payment-secondary {
  margin-top: 4px;
  font-size: 0.85rem;
  color: #8fa0b8;
}

.admin-home-shell-dark .payment-meta,
.admin-home-shell-dark .payment-secondary {
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

.admin-home-shell-dark .empty-state {
  color: #99acc8;
  background: rgba(12, 19, 32, 0.76);
  border-color: rgba(63, 80, 114, 0.58);
}

@media (max-width: 1280px) {
  .admin-home-panel {
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
  .admin-home-panel {
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

  .mobile-utility-card {
    display: flex;
  }

  .topbar-card {
    display: none;
  }

  .overview-shell-card {
    padding: 22px;
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

  .overview-item {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
  }
}

@media (max-width: 768px) {
  .admin-home-shell {
    width: 100%;
    max-width: none;
    overflow: hidden;
  }

  .admin-home-page {
    padding: 10px;
  }

  .admin-home-panel {
    padding: 12px;
    gap: 12px;
    grid-template-columns: 1fr;
  }

  .mobile-header-card,
  .mobile-utility-card {
    display: flex;
  }

  .topbar-card {
    display: none;
  }

  .overview-header {
    margin-bottom: 20px;
    gap: 14px;
  }

  .overview-title {
    font-size: 2rem;
  }

  .overview-subtitle {
    max-width: 34ch;
  }

  .overview-card-header {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
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

@media (max-width: 560px) {
  .admin-home-shell {
    border-radius: 24px;
    width: 100%;
    max-width: none;
    overflow: hidden;
  }

  .admin-home-panel {
    padding: 10px;
    gap: 10px;
  }

  .sidebar-card,
  .topbar-card,
  .overview-shell-card {
    border-radius: 20px;
  }

  .overview-title {
    font-size: 1.65rem;
  }

  .overview-subtitle {
    font-size: 0.92rem;
  }

  .overview-shell-card,
  .topbar-card,
  .sidebar-card {
    padding: 14px;
  }

  .search-shell {
    min-height: 52px;
    padding: 0 14px;
  }

  .search-field :deep(.v-field__input) {
    min-height: 52px;
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

  .mobile-header-card {
    padding: 12px 14px;
    border-radius: 20px;
  }

  .mobile-utility-card {
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

  .overview-header {
    gap: 14px;
  }

  .profile-pill {
    width: 100%;
    gap: 10px;
    border-radius: 18px;
  }

  .mobile-drawer-inner {
    padding: 14px;
  }
}
</style>
