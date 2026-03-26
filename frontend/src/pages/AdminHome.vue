<template>
  <v-app>
    <v-main class="admin-home-page">
      <v-theme-provider :theme="darkMode ? 'adminDark' : 'adminLight'">
      <div class="admin-home-shell admin-theme-root" :class="{ 'admin-home-shell-dark': darkMode, 'admin-theme-root-dark': darkMode }">
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
              <div class="topbar-spacer" aria-hidden="true"></div>

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

        <AppPageFooter :dark-mode="darkMode" />

        <AppNotificationsDialog
          v-model="notificationsDialog"
          :dark-mode="darkMode"
          accent="admin"
          :notifications="notificationItems"
          :loading="notificationsLoading"
          @notification-click="handleNotificationClick"
        />

      </div>
      </v-theme-provider>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppNotificationsDialog from '../components/AppNotificationsDialog.vue'
import AppPageFooter from '../components/AppPageFooter.vue'
import { useNotifications } from '../composables/useNotifications'
import { dashboardApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'

const router = useRouter()
const darkMode = ref(false)
const notificationsDialog = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const darkModeStorageKey = 'app-dark-mode'

const navItems = [
  { label: 'Admin Panel', icon: 'mdi-shield-crown-outline', to: '/admin' },
  { label: 'Users', icon: 'mdi-account-multiple-outline', to: '/admin-users' },
  { label: 'Groups', icon: 'mdi-account-group-outline', to: '/manage-groups' },
  { label: 'Sessions', icon: 'mdi-calendar-clock-outline', to: '/manage-sessions' },
  { label: 'Payments', icon: 'mdi-credit-card-outline', to: '/admin-payments' }
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

const profileName = computed(() => {
  if (!user.value) return 'Admin User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const profileEmail = computed(() => user.value?.email ?? 'admin@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)

const notifications = computed(() => notificationItems.value.slice(0, 3))
const filteredAdminGroups = computed(() => adminGroups.value.slice(0, 5))

const filteredAdminSessions = computed(() => adminSessions.value.slice(0, 5))

const filteredAdminPayments = computed(() => adminPayments.value.slice(0, 5))

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
  return new Date(`${value}T00:00:00`).toLocaleDateString('en-US', {
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

.admin-home-footer {
  position: relative;
  margin: 0 22px 22px;
  border-radius: 28px;
  overflow: hidden;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-home-shell-dark .admin-home-footer {
  background: rgba(22, 31, 48, 0.72);
  border-color: rgba(74, 92, 126, 0.4);
}

.admin-home-footer-decor {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(184, 216, 255, 0.14) 46%, rgba(255, 255, 255, 0.08)),
    linear-gradient(180deg, rgba(245, 250, 255, 0.14), rgba(225, 237, 252, 0.08));
  pointer-events: none;
}

.admin-home-shell-dark .admin-home-footer-decor {
  background:
    linear-gradient(135deg, rgba(96, 136, 208, 0.16), rgba(41, 58, 92, 0.1) 46%, rgba(255, 255, 255, 0.04)),
    linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.015));
}

.admin-home-footer-glow {
  position: absolute;
  border-radius: 999px;
  filter: blur(28px);
  opacity: 0.78;
}

.admin-home-footer-glow-left {
  top: -12px;
  left: 40px;
  width: 220px;
  height: 96px;
  background: rgba(150, 203, 255, 0.34);
}

.admin-home-footer-glow-right {
  right: 70px;
  bottom: -20px;
  width: 260px;
  height: 102px;
  background: rgba(182, 216, 255, 0.28);
}

.admin-home-shell-dark .admin-home-footer-glow-left {
  background: rgba(78, 120, 190, 0.24);
}

.admin-home-shell-dark .admin-home-footer-glow-right {
  background: rgba(54, 93, 156, 0.22);
}

.admin-home-footer-content {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
  align-items: center;
  gap: 16px;
  padding: 18px 24px;
}

.admin-home-footer-meta {
  color: #7b8798;
  font-size: 0.82rem;
}

.admin-home-shell-dark .admin-home-footer-meta {
  color: #94a6c4;
}

.admin-home-footer-nav {
  display: flex;
  align-items: center;
  gap: 12px;
  justify-content: center;
  justify-self: center;
}

.admin-home-footer-socials {
  display: flex;
  align-items: center;
  gap: 10px;
  justify-self: end;
}

.admin-home-footer-side {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 14px;
  justify-self: end;
}

.admin-home-footer-link {
  min-height: 30px;
  padding: 0 8px;
  border: 1px solid transparent;
  border-radius: 999px;
  background: transparent;
  color: #4e6689;
  font-size: 0.86rem;
  font-weight: 600;
  letter-spacing: 0.01em;
  cursor: default;
  transition:
    color 0.18s ease,
    transform 0.18s ease,
    background 0.18s ease,
    border-color 0.18s ease,
    box-shadow 0.18s ease;
}

.admin-home-shell-dark .admin-home-footer-link {
  color: #b3c4e2;
}

.admin-home-footer-link:hover {
  color: #315f9d;
  transform: translateY(-1px);
  border-color: rgba(173, 196, 229, 0.7);
  background: rgba(255, 255, 255, 0.36);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.38),
    0 8px 18px rgba(160, 184, 218, 0.18);
}

.admin-home-shell-dark .admin-home-footer-link:hover {
  color: #dce9ff;
  border-color: rgba(106, 129, 171, 0.56);
  background: rgba(255, 255, 255, 0.06);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 8px 18px rgba(5, 10, 24, 0.24);
}

.admin-home-footer-link-icon {
  width: 34px;
  min-width: 34px;
  min-height: 34px;
  padding: 0;
  justify-content: center;
  border-radius: 999px;
  border: 1px solid rgba(170, 193, 225, 0.42);
  background: rgba(255, 255, 255, 0.34);
  color: #4e6689;
}

.admin-home-shell-dark .admin-home-footer-link-icon {
  border-color: rgba(93, 109, 145, 0.42);
  background: rgba(255, 255, 255, 0.06);
  color: #b3c4e2;
}

.admin-home-footer-link-icon:hover {
  background: rgba(255, 255, 255, 0.5);
}

.admin-home-shell-dark .admin-home-footer-link-icon:hover {
  background: rgba(255, 255, 255, 0.1);
}

.footer-dialog-card {
  padding: 24px;
  border-radius: 28px;
  background: linear-gradient(180deg, rgba(250, 252, 255, 0.98), rgba(239, 246, 255, 0.98));
  border: 1px solid rgba(223, 232, 246, 0.94);
  box-shadow: 0 24px 56px rgba(79, 106, 154, 0.22);
}

.footer-dialog-card-dark {
  background: linear-gradient(180deg, rgba(16, 23, 37, 0.99), rgba(22, 31, 48, 0.98));
  border-color: rgba(64, 82, 116, 0.62);
  box-shadow: 0 24px 56px rgba(3, 8, 20, 0.55);
}

.footer-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}

.footer-dialog-eyebrow {
  color: #5d7db1;
  font-size: 0.84rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.footer-dialog-card-dark .footer-dialog-eyebrow {
  color: #8eb8ff;
}

.footer-dialog-title {
  margin: 8px 0 0;
  color: #172033;
  font-size: 1.55rem;
  line-height: 1.15;
}

.footer-dialog-card-dark .footer-dialog-title {
  color: #eef4ff;
}

.footer-dialog-close {
  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border: 1px solid rgba(223, 231, 243, 0.92);
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.92);
  color: #172033;
  cursor: pointer;
}

.footer-dialog-card-dark .footer-dialog-close {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.footer-dialog-body {
  margin-top: 22px;
}

.footer-dialog-copy {
  margin: 0;
  color: #6f819d;
  line-height: 1.7;
}

.footer-dialog-card-dark .footer-dialog-copy {
  color: #94a6c4;
}

.footer-dialog-grid,
.footer-contact-list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
  margin-top: 20px;
}

.footer-dialog-tabs {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px;
  border-radius: 16px;
  background: rgba(244, 248, 255, 0.9);
  border: 1px solid rgba(224, 232, 243, 0.92);
}

.footer-dialog-card-dark .footer-dialog-tabs {
  background: rgba(12, 19, 32, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.footer-dialog-tab {
  min-height: 38px;
  padding: 0 14px;
  border: 1px solid transparent;
  border-radius: 12px;
  background: transparent;
  color: #6f819d;
  font-size: 0.92rem;
  font-weight: 600;
  cursor: pointer;
}

.footer-dialog-card-dark .footer-dialog-tab {
  color: #94a6c4;
}

.footer-dialog-tab-active {
  background: rgba(255, 255, 255, 0.92);
  border-color: rgba(223, 231, 243, 0.92);
  color: #172033;
}

.footer-dialog-card-dark .footer-dialog-tab-active {
  background: rgba(20, 30, 46, 0.96);
  border-color: rgba(63, 80, 114, 0.58);
  color: #eef4ff;
}

.footer-dialog-info-card,
.footer-contact-card {
  padding: 16px 18px;
  border-radius: 20px;
  background: rgba(244, 248, 255, 0.9);
  border: 1px solid rgba(224, 232, 243, 0.92);
}

.footer-dialog-card-dark .footer-dialog-info-card,
.footer-dialog-card-dark .footer-contact-card {
  background: rgba(12, 19, 32, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.footer-dialog-info-label,
.footer-contact-label {
  color: #7b8798;
  font-size: 0.82rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.footer-dialog-card-dark .footer-dialog-info-label,
.footer-dialog-card-dark .footer-contact-label {
  color: #8ea3c7;
}

.footer-dialog-info-value,
.footer-contact-value,
.footer-contact-link {
  display: block;
  margin-top: 8px;
  color: #172033;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  overflow-wrap: anywhere;
}

.footer-dialog-card-dark .footer-dialog-info-value,
.footer-dialog-card-dark .footer-contact-value,
.footer-dialog-card-dark .footer-contact-link {
  color: #eef4ff;
}

.footer-support-wrap {
  margin-top: 20px;
}

.footer-support-success {
  margin-bottom: 14px;
  padding: 14px 16px;
  border-radius: 18px;
  background: rgba(221, 245, 228, 0.9);
  border: 1px solid rgba(170, 223, 184, 0.92);
  color: #236340;
  font-weight: 600;
}

.footer-dialog-card-dark .footer-support-success {
  background: rgba(22, 61, 42, 0.84);
  border-color: rgba(54, 121, 84, 0.72);
  color: #9ce5b5;
}

.footer-support-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.footer-support-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.footer-support-field-full {
  margin-top: 14px;
}

.footer-support-input,
.footer-support-textarea {
  width: 100%;
  border: 1px solid rgba(223, 231, 243, 0.92);
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.92);
  color: #172033;
  font: inherit;
  outline: none;
}

.footer-support-input {
  min-height: 50px;
  padding: 0 14px;
}

.footer-support-textarea {
  padding: 14px;
  resize: vertical;
  min-height: 140px;
}

.footer-dialog-card-dark .footer-support-input,
.footer-dialog-card-dark .footer-support-textarea {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.footer-support-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 16px;
}

.footer-support-btn {
  min-height: 44px;
  padding: 0 16px;
  border-radius: 14px;
  font: inherit;
  font-weight: 700;
  cursor: pointer;
  transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease;
}

.footer-support-btn:hover {
  transform: translateY(-1px);
}

.footer-support-btn-secondary {
  border: 1px solid rgba(223, 231, 243, 0.92);
  background: rgba(255, 255, 255, 0.92);
  color: #172033;
}

.footer-dialog-card-dark .footer-support-btn-secondary {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.footer-support-btn-primary {
  border: 1px solid rgba(14, 103, 244, 0.92);
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  color: white;
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
  background: linear-gradient(180deg, var(--admin-accent) 0%, var(--admin-accent-strong) 100%);
  box-shadow: 0 16px 30px var(--admin-accent-shadow);
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
  background: linear-gradient(180deg, var(--admin-accent) 0%, var(--admin-accent-strong) 100%);
  box-shadow: 0 16px 34px var(--admin-accent-shadow);
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
  padding: 18px 22px;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-home-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.topbar-spacer {
  flex: 1;
  min-width: 0;
}

.topbar-tools {
  display: flex;
  align-items: center;
  gap: 12px;
}

.top-icon-btn {
  width: 54px;
  height: 54px;
  border-radius: 18px;
  color: #111827;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-home-shell-dark .top-icon-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.top-icon-btn-active {
  color: var(--admin-accent-text);
}

.admin-home-shell-dark .top-icon-btn-active {
  color: var(--admin-accent-dark-text);
}

.icon-badge-wrap {
  position: relative;
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
  background: var(--admin-accent);
  color: white;
  font-size: 0.72rem;
  font-weight: 700;
}

.profile-pill {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.profile-pill > div {
  min-width: 0;
}

.admin-home-shell-dark .profile-pill {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 700;
  color: #1c2438;
}

.admin-home-shell-dark .profile-name {
  color: #f3f7ff;
}

.profile-email {
  margin-top: 2px;
  font-size: 0.9rem;
  color: #78859a;
  word-break: break-word;
}

.admin-home-shell-dark .profile-email {
  color: #94a6c4;
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
  font-size: 2.1rem;
  line-height: 1.1;
  color: #172033;
}

.admin-home-shell-dark .overview-title {
  color: #f3f7ff;
}

.overview-subtitle {
  color: #7b8798;
}

.admin-home-shell-dark .overview-subtitle {
  color: #94a6c4;
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
  color: #7b8798;
}

.admin-home-shell-dark .summary-label {
  color: #94a6c4;
}

.summary-value {
  margin-top: 8px;
  font-size: 1.9rem;
  font-weight: 700;
  color: #172033;
}

.admin-home-shell-dark .summary-value {
  color: #f3f7ff;
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

  .admin-home-footer {
    margin: 0 18px 18px;
  }

  .admin-home-footer-content {
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: start;
    row-gap: 14px;
  }

  .admin-home-footer-nav {
    justify-self: end;
  }

  .admin-home-footer-side {
    grid-column: 1 / -1;
    width: 100%;
    justify-content: space-between;
    justify-self: stretch;
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

  .admin-home-footer {
    margin: 0 12px 12px;
  }

  .admin-home-footer-content {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
    padding: 16px 18px;
  }

  .admin-home-footer-meta {
    text-align: center;
  }

  .admin-home-footer-nav {
    justify-content: center;
    justify-self: auto;
  }

  .admin-home-footer-side {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    justify-self: auto;
  }

  .footer-dialog-card {
    padding: 18px;
    border-radius: 22px;
  }

  .footer-dialog-title {
    font-size: 1.32rem;
  }

  .footer-dialog-grid,
  .footer-contact-list,
  .footer-support-grid {
    grid-template-columns: 1fr;
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

  .admin-home-footer {
    margin: 0 10px 10px;
    border-radius: 20px;
  }

  .admin-home-footer-content {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    padding: 14px;
  }

  .admin-home-footer-nav {
    flex-wrap: wrap;
    justify-content: center;
    justify-self: auto;
  }

  .admin-home-footer-socials {
    align-self: center;
    justify-self: auto;
  }

  .admin-home-footer-side {
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    justify-self: auto;
  }

  .footer-dialog-close {
    width: 40px;
    height: 40px;
  }

  .footer-support-actions {
    flex-direction: column-reverse;
    align-items: stretch;
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
