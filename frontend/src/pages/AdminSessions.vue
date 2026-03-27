<template>
  <v-app>
    <v-main class="admin-sessions-page">
      <v-theme-provider :theme="darkMode ? 'adminDark' : 'adminLight'">
      <div class="admin-sessions-shell admin-theme-root" :class="{ 'admin-sessions-shell-dark': darkMode, 'admin-theme-root-dark': darkMode }">
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
                <div class="brand-caption">{{ t('workspace.admin') }}</div>
              </div>
            </div>

            <nav class="mobile-nav-list">
              <v-btn
                v-for="item in navItems"
                :key="`mobile-${item.label}`"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/manage-sessions' }"
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
              {{ t('common.logout') }}
            </v-btn>
          </div>
        </v-navigation-drawer>

        <div class="admin-sessions-panel">
          <aside class="sidebar-card">
            <div class="brand-block">
              <div class="brand-icon">
                <v-icon color="white">mdi-school-outline</v-icon>
              </div>
              <div class="brand-text">
                <div class="brand-name">SportSystem</div>
                <div class="brand-caption">{{ t('workspace.admin') }}</div>
              </div>
            </div>

            <nav class="nav-list">
              <v-btn
                v-for="item in navItems"
                :key="item.label"
                :to="item.to || undefined"
                variant="text"
                class="nav-item"
                :class="{ 'nav-item-active': item.to === '/manage-sessions' }"
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
                  <div class="brand-caption">{{ t('pages.adminSessions.caption') }}</div>
                </div>
              </div>

              <div class="mobile-header-actions">
                <AppLanguageSwitch :dark-mode="darkMode" accent="admin" />

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

                <v-btn color="primary" class="mobile-create-btn" prepend-icon="mdi-plus" @click="openCreateDialog">
                  {{ t('common.createSession') }}
                </v-btn>
              </div>
            </div>

            <div v-if="!isCompactNav" class="topbar-card">
              <div class="search-wrap">
                <div class="search-shell">
                  <v-icon size="20" class="search-shell-icon">mdi-magnify</v-icon>
                  <v-text-field
                    v-model="search"
                    placeholder="Search sessions"
                    variant="plain"
                    hide-details
                    density="comfortable"
                    class="search-field"
                  />
                </div>
              </div>

              <div class="topbar-tools">
                <AppLanguageSwitch :dark-mode="darkMode" accent="admin" />

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

            <div class="sessions-card">
              <div class="sessions-header">
                <div>
                  <h1 class="sessions-title">{{ t('pages.adminSessions.title') }}</h1>
                  <div class="sessions-subtitle">
                    {{ t('pages.adminSessions.subtitle') }}
                  </div>
                </div>

                <v-btn color="primary" class="desktop-only-btn" prepend-icon="mdi-plus" @click="openCreateDialog">
                  {{ t('common.createSession') }}
                </v-btn>
              </div>

              <div class="overview-stats-grid">
                <article v-for="item in overviewStats" :key="item.label" class="overview-stat-card">
                  <div class="summary-label">{{ item.label }}</div>
                  <div class="summary-value">{{ item.value }}</div>
                </article>
              </div>

              <div class="session-view-switch">
                <div class="session-view-tabs">
                  <v-btn
                    variant="text"
                    class="view-switch-btn"
                    :class="{ 'view-switch-btn-active': activeView === 'plans' }"
                    @click="activeView = 'plans'"
                  >
                    {{ t('pages.adminSessions.statWeeklyTrainings') }}
                  </v-btn>
                  <v-btn
                    variant="text"
                    class="view-switch-btn"
                    :class="{ 'view-switch-btn-active': activeView === 'dates' }"
                    @click="activeView = 'dates'"
                  >
                    {{ t('pages.adminSessions.statSessionDates') }}
                  </v-btn>
                </div>

                <v-btn
                  v-if="activeView === 'dates'"
                  variant="text"
                  class="session-filter-btn"
                  prepend-icon="mdi-tune-variant"
                  @click="sessionDatesFilterDialog = true"
                >
                  {{ t('pages.adminSessions.filterButton') }}
                </v-btn>
              </div>

              <div v-if="errorMessage" class="state-wrap">
                <v-alert type="error" variant="tonal" border="start">
                  {{ errorMessage }}
                </v-alert>
              </div>

              <div v-else-if="loading" class="state-wrap loading-state">
                <v-progress-circular indeterminate color="primary" size="28" />
                <span>{{ t('pages.adminSessions.loadingSessions') }}</span>
              </div>

              <div v-else-if="activeView === 'plans' && !filteredWeeklyTrainings.length" class="state-wrap empty-state">
                {{ t('pages.adminSessions.noWeeklyTrainingsFound') }}
              </div>

              <div v-else-if="activeView === 'dates' && !filteredSessionDates.length" class="state-wrap empty-state">
                {{ t('pages.adminSessions.noSessionsFound') }}
              </div>

              <div v-else-if="activeView === 'plans'" class="sessions-grid">
                <article
                  v-for="session in filteredWeeklyTrainings"
                  :key="`plan-${session.template_id}`"
                  class="session-card"
                >
                  <div class="session-card-top">
                    <div class="session-card-main">
                      <v-avatar size="56" class="session-avatar">
                        <img :src="avatarFor(`session-${session.template_id}-${session.title}`, session.title)" :alt="session.title">
                      </v-avatar>

                      <div class="session-copy">
                        <div class="session-name">{{ session.title }}</div>
                        <div class="session-trainer">{{ session.group }}</div>
                      </div>
                    </div>
                  </div>

                  <div class="session-info-grid">
                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.labelDays') }}</span>
                      <span class="info-value">{{ formatSessionWeekdays(session) }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.nearestDate') }}</span>
                      <span class="info-value">{{ formatNearestDate(session.nearest_date) }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.labelCoach') }}</span>
                      <span class="info-value">{{ session.trainer || t('pages.adminSessions.coachNotAssigned') }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.start') }}</span>
                      <span class="info-value">{{ session.start }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.end') }}</span>
                      <span class="info-value">{{ session.end }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.pricePerTraining') }}</span>
                      <span class="info-value">{{ formatPrice(session.price) }}</span>
                    </div>
                  </div>

                  <div class="session-card-actions">
                    <v-btn
                      color="primary"
                      class="action-btn action-btn-edit"
                      prepend-icon="mdi-pencil-outline"
                      @click="openEditDialog(session)"
                    >
                      {{ t('common.edit') }}
                    </v-btn>
                    <v-btn
                      variant="outlined"
                      color="error"
                      class="action-btn action-btn-delete"
                      @click="deleteSession(session)"
                    >
                      {{ t('common.delete') }}
                    </v-btn>
                  </div>
                </article>
              </div>

              <div v-else class="sessions-grid">
                <article
                  v-for="session in filteredSessionDates"
                  :key="`date-${session.id}`"
                  class="session-card"
                >
                  <div class="session-card-top">
                    <div class="session-card-main">
                      <v-avatar size="56" class="session-avatar">
                        <img :src="avatarFor(`session-date-${session.id}-${session.title}`, session.title)" :alt="session.title">
                      </v-avatar>

                      <div class="session-copy">
                        <div class="session-name">{{ session.title }}</div>
                        <div class="session-trainer">{{ session.group }}</div>
                      </div>
                    </div>

                    <v-chip size="small" class="status-chip" :class="statusChipClass(session.status)">
                      {{ formatStatus(session.status) }}
                    </v-chip>
                  </div>

                  <div class="session-info-grid">
                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.dateLabel') }}</span>
                      <span class="info-value">{{ formatDate(session.date) }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.dayLabel') }}</span>
                      <span class="info-value">{{ formatDay(session.date) }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.labelCoach') }}</span>
                      <span class="info-value">{{ session.trainer || t('pages.adminSessions.coachNotAssigned') }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.start') }}</span>
                      <span class="info-value">{{ session.start }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.end') }}</span>
                      <span class="info-value">{{ session.end }}</span>
                    </div>

                    <div class="info-item">
                      <span class="info-label">{{ t('pages.adminSessions.pricePerTraining') }}</span>
                      <span class="info-value">{{ formatPrice(session.price) }}</span>
                    </div>
                  </div>

                  <div class="session-card-actions">
                    <v-btn
                      variant="outlined"
                      class="action-btn action-btn-manage"
                      prepend-icon="mdi-account-plus-outline"
                      @click="openChildrenDialog(session)"
                    >
                      {{ t('common.manageChildren') }}
                    </v-btn>
                    <v-btn
                      color="primary"
                      class="action-btn action-btn-edit"
                      prepend-icon="mdi-flag-outline"
                      @click="openStatusDialog(session)"
                    >
                      {{ t('common.changeStatus') }}
                    </v-btn>
                  </div>
                </article>
              </div>
            </div>
          </section>
        </div>

        <v-dialog v-model="sessionDialog" max-width="760">
          <v-card class="dialog-card create-dialog-card" :class="{ 'create-dialog-card-dark': darkMode }">
            <div class="create-dialog-header" :class="{ 'create-dialog-header-dark': darkMode }">
              <div>
                <div class="create-dialog-title" :class="{ 'create-dialog-title-dark': darkMode }">
                  {{ editingSessionId ? t('common.editSession') : t('common.createSession') }}
                </div>
                <div class="create-dialog-subtitle" :class="{ 'create-dialog-subtitle-dark': darkMode }">
                  {{
                    editingSessionId
                      ? t('pages.adminSessions.editSubtitle')
                      : t('pages.adminSessions.createSubtitle')
                  }}
                </div>
              </div>

              <v-btn icon variant="text" @click="closeDialog">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <v-card-text class="create-dialog-content">
              <v-alert
                v-if="formError"
                type="error"
                variant="tonal"
                border="start"
                class="dialog-alert"
              >
                {{ formError }}
              </v-alert>

              <div class="create-fields-grid">
                <v-text-field
                  v-model="form.title"
                  :label="t('pages.adminSessions.trainingTitle')"
                  variant="outlined"
                  class="create-field create-field-full"
                />

                <v-autocomplete
                  v-model="form.group_id"
                  :items="groupOptions"
                  item-title="label"
                  item-value="value"
                  :label="t('pages.adminSessions.groupLabel')"
                  variant="outlined"
                  class="create-field create-field-full"
                  :menu-props="selectMenuProps"
                  :placeholder="t('pages.adminSessions.groupPlaceholder')"
                  clearable
                />

                <v-autocomplete
                  v-model="form.weekdays"
                  :items="weekdayOptions"
                  item-title="label"
                  item-value="value"
                  :label="t('pages.adminSessions.weekdays')"
                  variant="outlined"
                  class="create-field create-field-full weekdays-field"
                  :menu-props="selectMenuProps"
                  :hint="resolvedDateHint"
                  persistent-hint
                  multiple
                  chips
                  closable-chips
                  clearable
                />

                <v-text-field
                  v-model="form.start_time"
                  :label="t('pages.adminSessions.startTime')"
                  type="time"
                  variant="outlined"
                  class="create-field time-field"
                />

                <v-text-field
                  v-model="form.end_time"
                  :label="t('pages.adminSessions.endTime')"
                  type="time"
                  variant="outlined"
                  class="create-field time-field"
                />

                <v-text-field
                  v-model="form.price"
                  :label="t('pages.adminSessions.pricePerTraining')"
                  type="number"
                  variant="outlined"
                  class="create-field price-field"
                  prepend-inner-icon="mdi-currency-eur"
                  min="0"
                  step="0.01"
                />
              </div>
            </v-card-text>

            <v-card-actions class="create-dialog-actions" :class="{ 'create-dialog-actions-dark': darkMode }">
              <v-spacer></v-spacer>
              <v-btn color="primary" class="apply-filter-btn" :loading="saving" @click="saveSession">
                {{ editingSessionId ? t('common.saveChanges') : t('common.createSession') }}
              </v-btn>
              <v-btn variant="text" class="reset-filter-btn" :class="{ 'reset-filter-btn-dark': darkMode }" @click="closeDialog">{{ t('common.cancel') }}</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="statusDialog" max-width="560">
          <v-card class="dialog-card create-dialog-card" :class="{ 'create-dialog-card-dark': darkMode }">
            <div class="create-dialog-header" :class="{ 'create-dialog-header-dark': darkMode }">
              <div>
                <div class="create-dialog-title" :class="{ 'create-dialog-title-dark': darkMode }">{{ t('common.changeStatus') }}</div>
                <div class="create-dialog-subtitle" :class="{ 'create-dialog-subtitle-dark': darkMode }">
                  {{ t('pages.adminSessions.statusSubtitle') }}
                </div>
              </div>

              <v-btn icon variant="text" @click="closeStatusDialog">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <v-card-text class="create-dialog-content">
              <v-alert
                v-if="statusError"
                type="error"
                variant="tonal"
                border="start"
                class="dialog-alert"
              >
                {{ statusError }}
              </v-alert>

              <div class="status-instance-summary">
                <div class="payment-name">{{ selectedInstanceSession?.title || t('pages.adminSessions.sessionInstance') }}</div>
                <div class="payment-meta">{{ selectedInstanceSession?.group || t('pages.adminSessions.groupNotSet') }}</div>
                <div class="payment-secondary">
                  {{ selectedInstanceSession?.date ? formatDate(selectedInstanceSession.date) : '' }}
                </div>
              </div>

              <v-select
                v-model="statusForm.status"
                :items="statusOptions"
                item-title="label"
                item-value="value"
                :label="t('pages.adminSessions.statusLabel')"
                variant="outlined"
                class="create-field"
                :menu-props="selectMenuProps"
              />
            </v-card-text>

            <v-card-actions class="create-dialog-actions" :class="{ 'create-dialog-actions-dark': darkMode }">
              <v-spacer></v-spacer>
              <v-btn color="primary" class="apply-filter-btn" :loading="statusSaving" @click="saveInstanceStatus">
                {{ t('common.saveStatus') }}
              </v-btn>
              <v-btn variant="text" class="reset-filter-btn" :class="{ 'reset-filter-btn-dark': darkMode }" @click="closeStatusDialog">{{ t('common.cancel') }}</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="sessionDatesFilterDialog" max-width="620">
          <v-card class="dialog-card create-dialog-card" :class="{ 'create-dialog-card-dark': darkMode }">
            <div class="create-dialog-header" :class="{ 'create-dialog-header-dark': darkMode }">
              <div>
                <div class="create-dialog-title" :class="{ 'create-dialog-title-dark': darkMode }">{{ t('pages.adminSessions.filterSessionDates') }}</div>
                <div class="create-dialog-subtitle" :class="{ 'create-dialog-subtitle-dark': darkMode }">
                  {{ t('pages.adminSessions.filterSessionDatesSubtitle') }}
                </div>
              </div>

              <v-btn icon variant="text" @click="sessionDatesFilterDialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <v-card-text class="create-dialog-content">
              <div class="session-date-filters-dialog">
                <v-text-field
                  v-model="sessionDateFrom"
                  :label="t('pages.adminSessions.fromDate')"
                  type="date"
                  variant="outlined"
                  hide-details
                  class="create-field session-date-filter-field"
                />

                <v-text-field
                  v-model="sessionDateTo"
                  :label="t('pages.adminSessions.toDate')"
                  type="date"
                  variant="outlined"
                  hide-details
                  class="create-field session-date-filter-field"
                />

                <v-select
                  v-model="sessionDatesPeriod"
                  :items="sessionDatePeriodOptions"
                  item-title="label"
                  item-value="value"
                  :label="t('pages.adminSessions.sessionPeriod')"
                  variant="outlined"
                  hide-details
                  class="create-field session-date-filter-field session-date-filter-full"
                  :menu-props="selectMenuProps"
                />

                <v-select
                  v-model="sessionDatesSort"
                  :items="sessionDateSortOptions"
                  item-title="label"
                  item-value="value"
                  :label="t('pages.adminSessions.sortBy')"
                  variant="outlined"
                  hide-details
                  class="create-field session-date-filter-field session-date-filter-full"
                  :menu-props="selectMenuProps"
                />
              </div>
            </v-card-text>

            <v-card-actions class="create-dialog-actions" :class="{ 'create-dialog-actions-dark': darkMode }">
              <v-spacer></v-spacer>
              <v-btn color="primary" class="apply-filter-btn" @click="sessionDatesFilterDialog = false">
                {{ t('common.apply') }}
              </v-btn>
              <v-btn variant="text" class="reset-filter-btn" :class="{ 'reset-filter-btn-dark': darkMode }" @click="resetSessionDateFilters">
                {{ t('common.reset') }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="sessionChildrenDialog" max-width="760">
          <v-card class="dialog-card create-dialog-card" :class="{ 'create-dialog-card-dark': darkMode }">
            <div class="create-dialog-header" :class="{ 'create-dialog-header-dark': darkMode }">
              <div>
                <div class="create-dialog-title" :class="{ 'create-dialog-title-dark': darkMode }">{{ t('pages.adminSessions.manageSessionChildren') }}</div>
                <div class="create-dialog-subtitle" :class="{ 'create-dialog-subtitle-dark': darkMode }">
                  {{ t('pages.adminSessions.manageChildrenSubtitle') }}
                </div>
              </div>

              <v-btn icon variant="text" @click="closeChildrenDialog">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>

            <v-card-text class="create-dialog-content">
              <v-alert
                v-if="sessionChildrenError"
                type="error"
                variant="tonal"
                border="start"
                class="dialog-alert"
              >
                {{ sessionChildrenError }}
              </v-alert>

              <div class="status-instance-summary session-children-summary">
                <div class="payment-name">{{ selectedChildrenSession?.title || t('pages.adminSessions.sessionInstance') }}</div>
                <div class="payment-meta">{{ selectedChildrenSession?.group || t('pages.adminSessions.groupNotSet') }}</div>
                <div class="payment-secondary">
                  {{ selectedChildrenSession?.date ? `${formatDate(selectedChildrenSession.date)} · ${selectedChildrenSession.start}-${selectedChildrenSession.end}` : '' }}
                </div>
              </div>

              <div class="session-children-add-row">
                <v-autocomplete
                  v-model="sessionChildForm.child_id"
                  :items="availableSessionChildOptions"
                  item-title="label"
                  item-value="value"
                  :label="t('pages.adminSessions.addChildToSession')"
                  variant="outlined"
                  class="create-field"
                  :menu-props="selectMenuProps"
                  :placeholder="t('pages.adminSessions.addChildPlaceholder')"
                  clearable
                />

                <v-btn
                  color="primary"
                  class="apply-filter-btn session-children-add-btn"
                  prepend-icon="mdi-plus"
                  :loading="childAssignmentSaving"
                  @click="addChildToSession"
                >
                  {{ t('pages.adminSessions.addChild') }}
                </v-btn>
              </div>

              <div class="session-children-list">
                <article
                  v-for="student in selectedChildrenSession?.students || []"
                  :key="`session-child-${student.id}`"
                  class="session-child-item"
                >
                  <div class="session-child-main">
                    <v-avatar size="42">
                      <img :src="avatarFor(`session-child-${student.id}`, student.name)" :alt="student.name">
                    </v-avatar>
                    <div>
                      <div class="session-child-name">{{ student.name }}</div>
                      <div class="session-child-meta">
                        {{ student.is_session_specific ? t('pages.adminSessions.addedOnlyForDate') : t('pages.adminSessions.comesFromGroupRoster') }}
                      </div>
                    </div>
                  </div>

                  <div class="session-child-actions">
                    <v-chip
                      size="small"
                      class="session-child-chip"
                      :class="{ 'session-child-chip-extra': student.is_session_specific }"
                    >
                      {{ student.is_session_specific ? t('pages.adminSessions.oneTimeChild') : t('pages.adminSessions.groupChild') }}
                    </v-chip>

                    <v-btn
                      v-if="student.is_session_specific"
                      variant="text"
                      color="error"
                      class="session-child-remove-btn"
                      @click="removeChildFromSession(student)"
                    >
                      Remove
                    </v-btn>
                  </div>
                </article>
              </div>
            </v-card-text>

            <v-card-actions class="create-dialog-actions" :class="{ 'create-dialog-actions-dark': darkMode }">
              <v-spacer></v-spacer>
              <v-btn variant="text" class="reset-filter-btn" :class="{ 'reset-filter-btn-dark': darkMode }" @click="closeChildrenDialog">{{ t('common.close') }}</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

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
import AppLanguageSwitch from '../components/AppLanguageSwitch.vue'
import { useNotifications } from '../composables/useNotifications'
import { useLocale } from '../i18n'
import { groupsApi, sessionsApi, usersApi } from '../services/api'
import { logout, useAuth } from '../services/auth'
import { createAvatarDataUri } from '../utils/avatar'
import { isSameNotificationTarget, resolveNotificationTarget } from '../utils/notifications'

const router = useRouter()
const search = ref('')
const darkMode = ref(false)
const activeView = ref('plans')
const sessionDateFrom = ref('')
const sessionDateTo = ref('')
const sessionDatesPeriod = ref('all')
const sessionDatesSort = ref('date-asc')
const sessionDatesFilterDialog = ref(false)
const sessionDialog = ref(false)
const statusDialog = ref(false)
const sessionChildrenDialog = ref(false)
const notificationsDialog = ref(false)
const mobileMenuOpen = ref(false)
const isCompactNav = ref(false)
const loading = ref(false)
const saving = ref(false)
const statusSaving = ref(false)
const childAssignmentSaving = ref(false)
const errorMessage = ref('')
const formError = ref('')
const statusError = ref('')
const sessionChildrenError = ref('')
const editingSessionId = ref(null)
const selectedInstanceId = ref(null)
const selectedChildrenSessionId = ref(null)
const darkModeStorageKey = 'app-dark-mode'
const { t, currentLocale } = useLocale()

const navItems = computed(() => [
  { label: t('pages.adminHome.caption'), icon: 'mdi-shield-crown-outline', to: '/admin' },
  { label: t('common.users'), icon: 'mdi-account-multiple-outline', to: '/admin-users' },
  { label: t('common.groups'), icon: 'mdi-account-group-outline', to: '/manage-groups' },
  { label: t('common.sessions'), icon: 'mdi-calendar-clock-outline', to: '/manage-sessions' },
  { label: t('common.payments'), icon: 'mdi-credit-card-outline', to: '/admin-payments' }
])

const statusOptions = computed(() => [
  { label: t('pages.attendance.planned'), value: 'planned' },
  { label: t('pages.adminSessions.statCompleted'), value: 'completed' },
  { label: t('pages.adminSessions.statCancelled'), value: 'cancelled' }
])

const sessionDateSortOptions = computed(() => [
  { label: currentSortLabel('date-asc'), value: 'date-asc' },
  { label: currentSortLabel('date-desc'), value: 'date-desc' },
  { label: currentSortLabel('alpha-asc'), value: 'alpha-asc' },
  { label: currentSortLabel('alpha-desc'), value: 'alpha-desc' },
  { label: currentSortLabel('time-asc'), value: 'time-asc' },
  { label: currentSortLabel('time-desc'), value: 'time-desc' }
])

const sessionDatePeriodOptions = computed(() => [
  { label: currentPeriodLabel('all'), value: 'all' },
  { label: currentPeriodLabel('past'), value: 'past' },
  { label: currentPeriodLabel('upcoming'), value: 'upcoming' }
])

const weekdayOptions = [
  { label: 'Monday', value: 'Mon' },
  { label: 'Tuesday', value: 'Tue' },
  { label: 'Wednesday', value: 'Wed' },
  { label: 'Thursday', value: 'Thu' },
  { label: 'Friday', value: 'Fri' },
  { label: 'Saturday', value: 'Sat' },
  { label: 'Sunday', value: 'Sun' }
]

const selectMenuProps = computed(() => ({
  contentClass: darkMode.value ? 'admin-select-menu admin-select-menu-dark' : 'admin-select-menu',
  theme: darkMode.value ? 'dark' : 'light'
}))

const avatarFor = (seed, label = seed) => createAvatarDataUri(seed, label)
const { user } = useAuth()
const {
  items: notificationItems,
  loading: notificationsLoading,
  unreadCount,
  loadNotifications,
  markNotificationRead
} = useNotifications()

const sessions = ref([])
const groups = ref([])
const users = ref([])
const form = ref(getDefaultForm())
const statusForm = ref(getDefaultStatusForm())
const sessionChildForm = ref({
  child_id: null
})

const profileName = computed(() => {
  if (!user.value) return 'Admin User'
  return `${user.value.name} ${user.value.surname}`.trim()
})
const profileEmail = computed(() => user.value?.email ?? 'admin@sportsystem.app')
const profileSeed = computed(() => user.value?.email ?? profileName.value)

const groupOptions = computed(() =>
  groups.value.map((group) => ({
    label: `${group.section}${group.trainer ? ` • ${group.trainer}` : ''}`,
    value: group.id,
    price: Number(group.price ?? 0)
  }))
)

const childOptions = computed(() =>
  users.value
    .filter((item) => ['child', 'adult'].includes(item.role))
    .map((item) => ({
      label: item.full_name ?? `${item.name} ${item.surname}`.trim(),
      value: item.id
    }))
)

const resolvedDateHint = computed(() => {
  const selectedWeekdays = sortWeekdays(form.value.weekdays || [])
  if (!selectedWeekdays.length) {
    return 'Select one or more weekdays for these recurring trainings.'
  }

  if (editingSessionId.value) {
    return `Repeats every week on ${formatWeekdayLabels(selectedWeekdays)}`
  }

  return `Will create recurring trainings every week on ${formatWeekdayLabels(selectedWeekdays)}`
})

const searchableSessions = computed(() => {
  const query = search.value.trim().toLowerCase()

  return sessions.value.filter((session) => {
    if (!query) return true

    return [
      session.title,
      session.group,
      session.trainer,
      session.status,
      session.date
    ]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(query))
  })
})

const filteredWeeklyTrainings = computed(() => {
  const grouped = new Map()
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  searchableSessions.value
    .filter((session) => session.template_id && session.template_active)
    .forEach((session) => {
      const existing = grouped.get(session.template_id)

      if (!existing) {
        grouped.set(session.template_id, {
          ...session,
          series_session_id: session.id,
          nearest_date: session.date,
          weekdays: sortWeekdays(session.weekdays || []),
        })
        return
      }

      const mergedWeekdays = sortWeekdays([
        ...(existing.weekdays || []),
        ...(session.weekdays || []),
      ])
      const candidateDates = [existing.nearest_date, session.date]
      const datedCandidates = candidateDates
        .map((date) => ({
          value: date,
          date: new Date(`${date}T00:00:00`)
        }))
        .sort((left, right) => left.date - right.date)
      const upcomingCandidate = datedCandidates.find((item) => item.date >= today)
      const nearestDate = (upcomingCandidate ?? datedCandidates[0]).value

      grouped.set(session.template_id, {
        ...existing,
        weekdays: mergedWeekdays,
        nearest_date: nearestDate,
      })
    })

  return Array.from(grouped.values()).sort((left, right) => {
    const dateDiff = new Date(`${left.nearest_date}T00:00:00`) - new Date(`${right.nearest_date}T00:00:00`)

    if (dateDiff !== 0) return dateDiff

    return left.title.localeCompare(right.title)
  })
})

const filteredSessionDates = computed(() => {
  const from = sessionDateFrom.value ? new Date(sessionDateFrom.value) : null
  const to = sessionDateTo.value ? new Date(sessionDateTo.value) : null
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  const filtered = searchableSessions.value.filter((session) => {
    const sessionDate = new Date(session.date)
    sessionDate.setHours(0, 0, 0, 0)

    if (from && sessionDate < from) return false
    if (to && sessionDate > to) return false
    if (sessionDatesPeriod.value === 'past' && sessionDate >= today) return false
    if (sessionDatesPeriod.value === 'upcoming' && sessionDate < today) return false

    return true
  })

  return [...filtered].sort((left, right) => {
    if (sessionDatesSort.value === 'date-desc') {
      return new Date(right.date) - new Date(left.date)
    }

    if (sessionDatesSort.value === 'alpha-asc') {
      return left.title.localeCompare(right.title)
    }

    if (sessionDatesSort.value === 'alpha-desc') {
      return right.title.localeCompare(left.title)
    }

    if (sessionDatesSort.value === 'time-asc') {
      return String(left.start).localeCompare(String(right.start))
    }

    if (sessionDatesSort.value === 'time-desc') {
      return String(right.start).localeCompare(String(left.start))
    }

    return new Date(left.date) - new Date(right.date)
  })
})

const selectedInstanceSession = computed(() =>
  sessions.value.find((item) => item.id === selectedInstanceId.value) ?? null
)

const selectedChildrenSession = computed(() =>
  sessions.value.find((item) => item.id === selectedChildrenSessionId.value) ?? null
)

const availableSessionChildOptions = computed(() => {
  const currentIds = new Set((selectedChildrenSession.value?.students ?? []).map((student) => student.id))

  return childOptions.value.filter((item) => !currentIds.has(item.value))
})

const overviewStats = computed(() => [
  { label: t('pages.adminSessions.statWeeklyTrainings'), value: filteredWeeklyTrainings.value.length },
  { label: t('pages.adminSessions.statSessionDates'), value: sessions.value.length },
  { label: t('pages.adminSessions.statPlanned'), value: sessions.value.filter((item) => item.status === 'planned').length },
  { label: t('pages.adminSessions.statCompleted'), value: sessions.value.filter((item) => item.status === 'completed').length },
  { label: t('pages.adminSessions.statCancelled'), value: sessions.value.filter((item) => item.status === 'cancelled').length }
])

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

watch(
  () => form.value.group_id,
  (groupId) => {
    if (editingSessionId.value || !groupId) return

    const selectedGroup = groupOptions.value.find((item) => item.value === groupId)
    if (!selectedGroup) return

    if (form.value.price === '' || form.value.price === null || Number(form.value.price) === 0) {
      form.value.price = selectedGroup.price
    }
  }
)

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateViewportState)
})

function getDefaultForm() {
  return {
    group_id: null,
    title: '',
    weekdays: [],
    start_time: '',
    end_time: '',
    price: '',
    status: 'planned'
  }
}

function getDefaultStatusForm() {
  return {
    status: 'planned'
  }
}

function updateViewportState() {
  isCompactNav.value = window.innerWidth <= 1024
}

function currentSortLabel(value) {
  const labels = {
    'date-asc': { en: 'Date: nearest first', lv: 'Datums: tuvākie vispirms' },
    'date-desc': { en: 'Date: latest first', lv: 'Datums: jaunākie vispirms' },
    'alpha-asc': { en: 'Alphabet: A-Z', lv: 'Alfabēts: A-Z' },
    'alpha-desc': { en: 'Alphabet: Z-A', lv: 'Alfabēts: Z-A' },
    'time-asc': { en: 'Time: earliest first', lv: 'Laiks: agrākie vispirms' },
    'time-desc': { en: 'Time: latest first', lv: 'Laiks: vēlākie vispirms' },
  }

  return labels[value]?.[currentLocale.value === 'en' ? 'en' : 'lv'] ?? value
}

function currentPeriodLabel(value) {
  const labels = {
    all: { en: 'All sessions', lv: 'Visas nodarbības' },
    past: { en: 'Past sessions', lv: 'Iepriekšējās nodarbības' },
    upcoming: { en: 'Upcoming sessions', lv: 'Tuvākās nodarbības' },
  }

  return labels[value]?.[currentLocale.value === 'en' ? 'en' : 'lv'] ?? value
}

function formatStatus(status) {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

function statusChipClass(status) {
  return `status-chip-${status}`
}

function formatDate(value) {
  return new Date(`${value}T00:00:00`).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

function formatDay(value) {
  return new Date(`${value}T00:00:00`).toLocaleDateString('en-US', {
    weekday: 'long'
  })
}

function formatNearestDate(value) {
  return formatDate(value)
}

function sortWeekdays(weekdays) {
  const weekdayOrder = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

  return [...new Set(weekdays)].sort((left, right) => weekdayOrder.indexOf(left) - weekdayOrder.indexOf(right))
}

function formatWeekdayLabels(weekdays) {
  const labelMap = Object.fromEntries(weekdayOptions.map((item) => [item.value, item.label]))

  return weekdays
    .map((weekday) => labelMap[weekday] ?? weekday)
    .join(', ')
}

function formatSessionWeekdays(session) {
  return formatWeekdayLabels(session.weekdays?.length ? session.weekdays : [new Date(`${session.date}T00:00:00`).toLocaleDateString('en-US', { weekday: 'short' })])
}

function resetSessionDateFilters() {
  sessionDateFrom.value = ''
  sessionDateTo.value = ''
  sessionDatesPeriod.value = 'all'
  sessionDatesSort.value = 'date-asc'
}

function formatPrice(value) {
  const numericValue = Number(value ?? 0)

  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: numericValue % 1 === 0 ? 0 : 2,
    maximumFractionDigits: 2
  }).format(numericValue)
}

function closeDialog() {
  sessionDialog.value = false
  editingSessionId.value = null
  formError.value = ''
  form.value = getDefaultForm()
}

function closeStatusDialog() {
  statusDialog.value = false
  selectedInstanceId.value = null
  statusError.value = ''
  statusForm.value = getDefaultStatusForm()
}

function closeChildrenDialog() {
  sessionChildrenDialog.value = false
  selectedChildrenSessionId.value = null
  sessionChildrenError.value = ''
  sessionChildForm.value = {
    child_id: null
  }
}

function openCreateDialog() {
  editingSessionId.value = null
  formError.value = ''
  form.value = getDefaultForm()
  sessionDialog.value = true
}

function openEditDialog(session) {
  editingSessionId.value = session.id
  formError.value = ''
  form.value = {
    group_id: session.group_id ?? null,
    title: session.title ?? '',
    weekdays: sortWeekdays(session.weekdays || []),
    start_time: session.start ?? '',
    end_time: session.end ?? '',
    price: session.price ?? '',
    status: session.status ?? 'planned'
  }
  sessionDialog.value = true
}

function openStatusDialog(session) {
  selectedInstanceId.value = session.id
  statusError.value = ''
  statusForm.value = {
    status: session.status ?? 'planned'
  }
  statusDialog.value = true
}

function openChildrenDialog(session) {
  selectedChildrenSessionId.value = session.id
  sessionChildrenError.value = ''
  sessionChildForm.value = {
    child_id: null
  }
  sessionChildrenDialog.value = true
}

function extractErrorMessage(error, fallback) {
  const validationErrors = error?.response?.data?.errors

  if (validationErrors && typeof validationErrors === 'object') {
    const firstError = Object.values(validationErrors).flat()[0]
    if (firstError) return firstError
  }

  return error?.response?.data?.message || fallback
}

async function initializePage() {
  loading.value = true
  errorMessage.value = ''

  try {
    const [sessionsResponse, groupsResponse, usersResponse] = await Promise.all([
      sessionsApi.list(),
      groupsApi.list(),
      usersApi.list()
    ])

    sessions.value = sessionsResponse
    groups.value = groupsResponse
    users.value = usersResponse
  } catch (error) {
    errorMessage.value = extractErrorMessage(error, t('pages.adminSessions.loadFailed'))
  } finally {
    loading.value = false
  }
}

async function refreshSessions() {
  sessions.value = await sessionsApi.list()
}

function buildPayload() {
  return {
    group_id: form.value.group_id || null,
    title: form.value.title?.trim() || '',
    weekdays: sortWeekdays(form.value.weekdays || []),
    start_time: form.value.start_time || null,
    end_time: form.value.end_time || null,
    price: form.value.price === '' || form.value.price === null ? 0 : Number(form.value.price)
  }
}

async function saveSession() {
  saving.value = true
  formError.value = ''

  try {
    const payload = buildPayload()

    if (!payload.group_id) {
      formError.value = t('pages.adminSessions.chooseGroupRequired')
      return
    }

    if (!payload.title) {
      formError.value = t('pages.adminSessions.titleRequired')
      return
    }

    if (!payload.weekdays.length || !payload.start_time || !payload.end_time) {
      formError.value = t('pages.adminSessions.scheduleRequired')
      return
    }

    if (Number.isNaN(payload.price) || payload.price < 0) {
      formError.value = t('pages.adminSessions.priceInvalid')
      return
    }

    if (editingSessionId.value) {
      await sessionsApi.update(editingSessionId.value, payload)
    } else {
      await sessionsApi.create(payload)
    }

    await initializePage()
    closeDialog()
  } catch (error) {
    formError.value = extractErrorMessage(error, t('pages.adminSessions.saveFailed'))
  } finally {
    saving.value = false
  }
}

async function deleteSession(session) {
  const isWeeklyTraining = activeView.value === 'plans' && session.template_id
  const message = isWeeklyTraining
    ? t('pages.adminSessions.deleteRecurringConfirm', { title: session.title, group: session.group })
    : t('pages.adminSessions.deleteSessionConfirm', { group: session.group, date: formatDate(session.date) })
  const confirmed = window.confirm(message)
  if (!confirmed) return

  try {
    if (isWeeklyTraining) {
      await sessionsApi.removeSeries(session.series_session_id || session.id)
    } else {
      await sessionsApi.remove(session.id)
    }
    await initializePage()
  } catch (error) {
    errorMessage.value = extractErrorMessage(
      error,
      isWeeklyTraining ? t('pages.adminSessions.deleteRecurringFailed') : t('pages.adminSessions.deleteFailed')
    )
  }
}

async function saveInstanceStatus() {
  statusSaving.value = true
  statusError.value = ''

  try {
    if (!selectedInstanceId.value) {
      statusError.value = t('pages.adminSessions.chooseInstanceRequired')
      return
    }

    if (!statusForm.value.status) {
      statusError.value = t('pages.adminSessions.statusRequired')
      return
    }

    await sessionsApi.updateStatus(selectedInstanceId.value, {
      status: statusForm.value.status
    })

    await initializePage()
    closeStatusDialog()
  } catch (error) {
    statusError.value = extractErrorMessage(error, t('pages.adminSessions.statusUpdateFailed'))
  } finally {
    statusSaving.value = false
  }
}

async function addChildToSession() {
  childAssignmentSaving.value = true
  sessionChildrenError.value = ''

  try {
    if (!selectedChildrenSessionId.value) {
      sessionChildrenError.value = t('pages.adminSessions.chooseDatedSessionRequired')
      return
    }

    if (!sessionChildForm.value.child_id) {
      sessionChildrenError.value = t('pages.adminSessions.selectChildRequired')
      return
    }

    await sessionsApi.addChild(selectedChildrenSessionId.value, {
      child_id: sessionChildForm.value.child_id
    })

    await refreshSessions()
    sessionChildForm.value.child_id = null
  } catch (error) {
    sessionChildrenError.value = extractErrorMessage(error, t('pages.adminSessions.addChildFailed'))
  } finally {
    childAssignmentSaving.value = false
  }
}

async function removeChildFromSession(student) {
  childAssignmentSaving.value = true
  sessionChildrenError.value = ''

  try {
    if (!selectedChildrenSessionId.value) {
      sessionChildrenError.value = t('pages.adminSessions.chooseDatedSessionRequired')
      return
    }

    await sessionsApi.removeChild(selectedChildrenSessionId.value, student.id)
    await refreshSessions()
  } catch (error) {
    sessionChildrenError.value = extractErrorMessage(error, t('pages.adminSessions.removeChildFailed'))
  } finally {
    childAssignmentSaving.value = false
  }
}

async function handleNotificationClick(item) {
  if (item?.unread) {
    await markNotificationRead(item.id)
  }

  const target = resolveNotificationTarget(item, 'admin')

  if (target && !isSameNotificationTarget(router.currentRoute.value, target)) {
    await router.push(target)
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
.admin-sessions-page {
  padding: 24px;
}

.admin-sessions-shell {
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

.admin-sessions-shell-dark {
  border-color: rgba(91, 109, 145, 0.4);
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(28, 36, 54, 0.94));
  box-shadow: 0 28px 80px rgba(4, 10, 24, 0.45);
}

.price-field :deep(input[type='number']) {
  -moz-appearance: textfield;
}

.price-field :deep(input[type='number']::-webkit-outer-spin-button),
.price-field :deep(input[type='number']::-webkit-inner-spin-button) {
  margin: 0;
  -webkit-appearance: none;
}

.create-dialog-card :deep(.time-field input[type='time']::-webkit-calendar-picker-indicator) {
  opacity: 1;
  filter: brightness(0);
}

.create-dialog-card :deep(.price-field .v-field__prepend-inner),
.create-dialog-card :deep(.price-field .v-field__prepend-inner .v-icon) {
  color: #172033;
}

.create-dialog-card :deep(.price-field .v-field__prepend-inner .v-icon) {
  font-size: 18px;
}

.admin-sessions-panel {
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
  margin-top: 14px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
}

.admin-sessions-shell-dark .mobile-drawer-profile {
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

.admin-sessions-shell-dark .sidebar-card {
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

.admin-sessions-shell-dark .brand-name {
  color: #f3f7ff;
}

.brand-caption {
  margin-top: 2px;
  font-size: 0.82rem;
  color: #7b8798;
}

.admin-sessions-shell-dark .brand-caption {
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

.admin-sessions-shell-dark .nav-item {
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

.admin-sessions-shell-dark .mobile-header-card {
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

.admin-sessions-shell-dark .mobile-menu-btn {
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

.mobile-brand-copy {
  min-width: 0;
}

.mobile-header-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.mobile-utility-card {
  padding: 16px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.62);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.admin-sessions-shell-dark .mobile-utility-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.mobile-profile-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
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

.admin-sessions-shell-dark .topbar-card {
  background: rgba(22, 31, 48, 0.82);
  border-color: rgba(74, 92, 126, 0.42);
}

.search-wrap {
  flex: 1;
  min-width: 0;
}

.search-shell {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 58px;
  padding: 0 18px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-sessions-shell-dark .search-shell {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.search-shell-icon {
  color: #111827;
}

.admin-sessions-shell-dark .search-shell-icon {
  color: #edf4ff;
}

.search-field {
  flex: 1;
}

.search-field :deep(input) {
  color: #111827;
  opacity: 1;
}

.admin-sessions-shell-dark .search-field :deep(input) {
  color: #edf4ff;
}

.search-field :deep(input::placeholder) {
  color: #111827;
  opacity: 1;
}

.admin-sessions-shell-dark .search-field :deep(input::placeholder) {
  color: #edf4ff;
}

.search-field :deep(.v-field__input) {
  padding-top: 0;
  padding-bottom: 0;
  min-height: auto;
}

.topbar-tools {
  display: flex;
  align-items: center;
  gap: 12px;
}

.icon-badge-wrap {
  position: relative;
}

.top-icon-btn {
  width: 54px;
  height: 54px;
  border-radius: 18px;
  color: #111827;
  background: rgba(255, 255, 255, 0.92);
  border: 1px solid rgba(230, 237, 246, 0.94);
}

.admin-sessions-shell-dark .top-icon-btn {
  color: #eef4ff;
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.top-icon-btn-active {
  color: var(--admin-accent-text);
}

.logout-btn {
  color: #111827;
}

.admin-sessions-shell-dark .logout-btn {
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

.admin-sessions-shell-dark .profile-pill {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.mobile-profile-pill {
  flex: 1;
}

.profile-name {
  font-size: 0.98rem;
  font-weight: 700;
  color: #1c2438;
}

.admin-sessions-shell-dark .profile-name {
  color: #f3f7ff;
}

.profile-email {
  margin-top: 2px;
  font-size: 0.86rem;
  color: #78859a;
}

.admin-sessions-shell-dark .profile-email {
  color: #94a6c4;
}

.sessions-card {
  padding: 26px;
  border-radius: 30px;
  background: rgba(249, 252, 255, 0.72);
  border: 1px solid rgba(255, 255, 255, 0.72);
}

.admin-sessions-shell-dark .sessions-card {
  background: rgba(18, 27, 43, 0.86);
  border-color: rgba(74, 92, 126, 0.42);
}

.sessions-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 24px;
}

.sessions-title {
  margin: 0;
  font-size: 2.1rem;
  line-height: 1.1;
  color: #172033;
}

.admin-sessions-shell-dark .sessions-title {
  color: #f3f7ff;
}

.sessions-subtitle,
.summary-label,
.info-label {
  color: #7b8798;
}

.admin-sessions-shell-dark .sessions-subtitle,
.admin-sessions-shell-dark .summary-label,
.admin-sessions-shell-dark .info-label {
  color: #94a6c4;
}

.overview-stats-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 22px;
}

.session-view-switch {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 22px;
  padding: 8px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.74);
  border: 1px solid rgba(229, 236, 246, 0.94);
}

.admin-sessions-shell-dark .session-view-switch {
  background: rgba(13, 20, 34, 0.86);
  border-color: rgba(63, 80, 114, 0.58);
}

.session-view-tabs {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.view-switch-btn {
  min-height: 46px;
  padding-inline: 18px;
  border-radius: 16px;
  color: #6e7f97;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 700;
}

.admin-sessions-shell-dark .view-switch-btn {
  color: #9eb1cf;
}

.view-switch-btn-active {
  color: white;
  background: linear-gradient(180deg, var(--admin-accent) 0%, var(--admin-accent-strong) 100%);
  box-shadow: 0 14px 28px var(--admin-accent-shadow);
}

.session-filter-btn {
  min-height: 46px;
  margin-left: auto;
  padding-inline: 16px;
  border-radius: 16px;
  color: #6e7f97;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 700;
}

.admin-sessions-shell-dark .session-filter-btn {
  color: #9eb1cf;
}

.session-date-filters-dialog {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.session-date-filter-full {
  grid-column: 1 / -1;
}

.session-date-filter-field :deep(.v-field) {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.82);
  box-shadow: inset 0 0 0 1px rgba(223, 232, 246, 0.95);
}

.session-date-filter-field :deep(.v-field--focused) {
  box-shadow:
    inset 0 0 0 1px rgba(74, 144, 255, 0.72),
    0 0 0 4px var(--admin-accent-ring);
}

.session-date-filter-field :deep(.v-field__outline) {
  --v-field-border-opacity: 0;
}

.session-date-filter-field :deep(input),
.session-date-filter-field :deep(.v-select__selection-text),
.session-date-filter-field :deep(.v-select__selection) {
  color: #172033;
}

.session-date-filter-field :deep(.v-label),
.session-date-filter-field :deep(.v-field__append-inner) {
  color: #6f7f96;
}

.create-dialog-card-dark .session-date-filter-field :deep(.v-field) {
  background: rgba(17, 25, 40, 0.86);
  box-shadow: inset 0 0 0 1px rgba(64, 82, 116, 0.72);
}

.create-dialog-card-dark .session-date-filter-field :deep(input),
.create-dialog-card-dark .session-date-filter-field :deep(.v-select__selection-text),
.create-dialog-card-dark .session-date-filter-field :deep(.v-select__selection) {
  color: #eef4ff;
}

.create-dialog-card-dark .session-date-filter-field :deep(.v-label),
.create-dialog-card-dark .session-date-filter-field :deep(.v-field__append-inner),
.create-dialog-card-dark .session-date-filter-field :deep(input::placeholder) {
  color: #94a6c4;
}

.create-dialog-card-dark .session-date-filter-field :deep(input[type='date']::-webkit-calendar-picker-indicator) {
  filter: invert(1);
}

.overview-stat-card {
  padding: 20px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(229, 236, 246, 0.94);
}

.admin-sessions-shell-dark .overview-stat-card {
  background: rgba(13, 20, 34, 0.88);
  border-color: rgba(63, 80, 114, 0.58);
}

.summary-value {
  margin-top: 8px;
  font-size: 1.9rem;
  font-weight: 700;
  color: #172033;
}

.admin-sessions-shell-dark .summary-value {
  color: #f3f7ff;
}

.state-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 180px;
  padding: 18px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.75);
  border: 1px dashed rgba(194, 206, 225, 0.92);
  color: #5f6f88;
}

.admin-sessions-shell-dark .state-wrap {
  background: rgba(13, 20, 34, 0.72);
  border-color: rgba(78, 97, 132, 0.58);
  color: #aac0df;
}

.loading-state {
  flex-direction: column;
  gap: 14px;
}

.sessions-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
}

.session-card {
  display: flex;
  flex-direction: column;
  gap: 16px;
  height: 100%;
  padding: 22px;
  border-radius: 26px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(245, 249, 255, 0.9));
  border: 1px solid rgba(229, 236, 246, 0.94);
  box-shadow: 0 14px 34px rgba(125, 148, 190, 0.09);
}

.admin-sessions-shell-dark .session-card {
  background: linear-gradient(180deg, rgba(13, 20, 34, 0.94), rgba(18, 27, 43, 0.92));
  border-color: rgba(63, 80, 114, 0.58);
  box-shadow: 0 18px 38px rgba(4, 10, 24, 0.3);
}

.session-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
}

.session-card-main {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.session-copy {
  min-width: 0;
}

.session-name {
  font-size: 1.08rem;
  font-weight: 700;
  color: #172033;
}

.admin-sessions-shell-dark .session-name,
.admin-sessions-shell-dark .info-value {
  color: #f3f7ff;
}

.session-trainer {
  margin-top: 4px;
  color: #7b8798;
}

.admin-sessions-shell-dark .session-trainer {
  color: #94a6c4;
}

.status-chip {
  border-radius: 999px;
  font-weight: 700;
}

.status-chip-planned {
  background: rgba(194, 225, 255, 0.5);
  color: var(--admin-accent-text);
}

.status-chip-completed {
  background: rgba(203, 241, 223, 0.52);
  color: #22764d;
}

.status-chip-cancelled {
  background: rgba(255, 217, 217, 0.74);
  color: #b42318;
}

.session-info-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding: 14px;
  border-radius: 20px;
  background: rgba(241, 246, 255, 0.8);
  border: 1px solid rgba(223, 232, 246, 0.95);
}

.admin-sessions-shell-dark .session-info-grid {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.info-value {
  font-weight: 600;
  color: #172033;
}

.session-card-actions {
  display: flex;
  gap: 10px;
  margin-top: auto;
  padding-top: 4px;
}

.action-btn {
  flex: 1;
  min-height: 48px;
  border-radius: 18px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 700;
}

.action-btn-edit {
  box-shadow: 0 14px 28px var(--admin-accent-shadow-soft);
}

.action-btn-manage {
  border-color: rgba(242, 140, 40, 0.48);
  background: rgba(242, 140, 40, 0.14);
  color: #c76612;
}

.action-btn-manage:hover {
  background: rgba(242, 140, 40, 0.2);
  border-color: rgba(242, 140, 40, 0.62);
}

.admin-sessions-shell-dark .action-btn-manage {
  border-color: rgba(242, 140, 40, 0.5);
  background: rgba(242, 140, 40, 0.16);
  color: #ffb56e;
}

.admin-sessions-shell-dark .action-btn-manage:hover {
  background: rgba(242, 140, 40, 0.22);
}

.action-btn-delete {
  background: rgba(255, 255, 255, 0.68);
}

.admin-sessions-shell-dark .action-btn-delete {
  background: rgba(17, 25, 40, 0.56);
}

.session-children-summary {
  margin-bottom: 18px;
}

.session-children-add-row {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 14px;
  align-items: start;
}

.session-children-add-btn {
  min-width: 150px;
}

.session-children-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 18px;
}

.session-child-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding: 14px 16px;
  border-radius: 18px;
  border: 1px solid rgba(223, 232, 246, 0.95);
  background: rgba(241, 246, 255, 0.8);
}

.create-dialog-card-dark .session-child-item {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.session-child-main,
.session-child-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.session-child-name {
  font-weight: 700;
  color: #172033;
}

.create-dialog-card-dark .session-child-name {
  color: #f3f7ff;
}

.session-child-meta {
  margin-top: 4px;
  color: #7b8798;
}

.create-dialog-card-dark .session-child-meta {
  color: #94a6c4;
}

.session-child-chip {
  font-weight: 700;
  color: #52718f;
  background: rgba(226, 234, 245, 0.9);
}

.session-child-chip-extra {
  color: var(--admin-accent-text);
  background: rgba(194, 225, 255, 0.5);
}

.dialog-card {
  border-radius: 28px;
  overflow: hidden;
  max-height: min(88vh, 820px);
}

.create-dialog-card {
  border: 1px solid rgba(223, 232, 246, 0.95);
  background:
    radial-gradient(circle at top left, rgba(147, 197, 253, 0.22), transparent 34%),
    radial-gradient(circle at top right, rgba(244, 200, 255, 0.18), transparent 30%),
    linear-gradient(180deg, rgba(247, 251, 255, 0.99), rgba(235, 243, 255, 0.98));
  box-shadow: 0 28px 70px rgba(79, 106, 154, 0.22);
}

.create-dialog-card-dark {
  border-color: rgba(66, 84, 118, 0.64);
  background:
    radial-gradient(circle at top left, rgba(55, 116, 255, 0.18), transparent 34%),
    radial-gradient(circle at top right, rgba(190, 120, 255, 0.12), transparent 30%),
    linear-gradient(180deg, rgba(16, 23, 37, 0.99), rgba(22, 31, 48, 0.98));
  box-shadow: 0 28px 80px rgba(3, 8, 20, 0.55);
  color: #eef4ff;
}

.create-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 24px 24px 18px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.38), rgba(255, 255, 255, 0.08));
  border-bottom: 1px solid rgba(219, 230, 246, 0.78);
}

.create-dialog-header-dark {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0));
  border-bottom-color: rgba(64, 82, 116, 0.68);
}

.create-dialog-title {
  font-size: 1.55rem;
  font-weight: 700;
  color: #172033;
}

.create-dialog-title-dark {
  color: #f3f7ff;
}

.create-dialog-subtitle {
  margin-top: 6px;
  color: #74849a;
  max-width: 430px;
}

.create-dialog-subtitle-dark {
  color: #94a6c4;
}

.create-dialog-content {
  padding: 18px 24px 14px !important;
  overflow-y: auto;
}

.dialog-alert {
  margin-bottom: 16px;
}

.status-instance-summary {
  margin-bottom: 18px;
  padding: 16px 18px;
  border-radius: 18px;
  background: rgba(241, 246, 255, 0.8);
  border: 1px solid rgba(223, 232, 246, 0.95);
}

.status-instance-summary .payment-name {
  color: #172033;
}

.status-instance-summary .payment-meta,
.status-instance-summary .payment-secondary {
  color: #6f7f96;
}

.create-dialog-card-dark .status-instance-summary {
  background: rgba(17, 25, 40, 0.88);
  border-color: rgba(58, 75, 108, 0.62);
}

.create-dialog-card-dark .status-instance-summary .payment-name {
  color: #eef4ff;
}

.create-dialog-card-dark .status-instance-summary .payment-meta,
.create-dialog-card-dark .status-instance-summary .payment-secondary {
  color: #94a6c4;
}

.create-fields-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.create-field-full {
  grid-column: 1 / -1;
}

.create-dialog-actions {
  padding: 18px 24px 24px;
  border-top: 1px solid rgba(219, 230, 246, 0.72);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.28));
}

.create-dialog-actions-dark {
  border-top-color: rgba(64, 82, 116, 0.62);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.03));
}

.reset-filter-btn,
.apply-filter-btn,
.desktop-only-btn,
.mobile-create-btn {
  min-height: 46px;
  border-radius: 16px;
  text-transform: none;
  letter-spacing: 0;
}

.desktop-only-btn {
  min-height: 52px;
  font-weight: 600;
}

.reset-filter-btn {
  color: #6e7f97;
  background: transparent !important;
}

.reset-filter-btn-dark {
  color: #9eb1cf;
}

.create-dialog-card :deep(.v-btn--icon) {
  color: #172033;
  background: rgba(255, 255, 255, 0.66);
  border: 1px solid rgba(219, 230, 246, 0.92);
}

.create-dialog-card-dark :deep(.v-btn--icon) {
  color: #eef4ff;
  background: rgba(18, 27, 43, 0.72);
  border-color: rgba(64, 82, 116, 0.62);
}

.create-dialog-card :deep(.create-field .v-field) {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.7);
  box-shadow: inset 0 0 0 1px rgba(223, 232, 246, 0.95);
}

.create-dialog-card :deep(.create-field .v-field--focused) {
  background: rgba(255, 255, 255, 0.92);
  box-shadow:
    inset 0 0 0 1px rgba(74, 144, 255, 0.72),
    0 0 0 4px var(--admin-accent-ring);
}

.create-dialog-card :deep(.create-field .v-field__outline) {
  --v-field-border-opacity: 0;
}

.create-dialog-card :deep(.create-field input),
.create-dialog-card :deep(.create-field textarea),
.create-dialog-card :deep(.create-field .v-field__input),
.create-dialog-card :deep(.create-field .v-autocomplete__selection),
.create-dialog-card :deep(.create-field .v-select__selection-text),
.create-dialog-card :deep(.create-field .v-select__selection) {
  color: #172033;
}

.create-dialog-card :deep(.create-field .v-chip),
.create-dialog-card :deep(.create-field .v-chip__content),
.create-dialog-card :deep(.create-field .v-chip .v-icon),
.create-dialog-card :deep(.create-field .v-chip .v-chip__close) {
  color: #172033;
}

.create-dialog-card :deep(.weekdays-field .v-chip) {
  background: rgba(207, 226, 252, 0.72);
  border: 1px solid rgba(147, 184, 243, 0.82);
}

.create-dialog-card :deep(.create-field .v-label),
.create-dialog-card :deep(.create-field .v-field__append-inner),
.create-dialog-card :deep(.create-field .v-messages__message) {
  color: #6f7f96;
}

.create-dialog-card-dark :deep(.create-field .v-field) {
  background: rgba(17, 25, 40, 0.86);
  box-shadow: inset 0 0 0 1px rgba(64, 82, 116, 0.72);
}

.create-dialog-card-dark :deep(.create-field .v-field--focused) {
  background: rgba(22, 31, 48, 0.98);
  box-shadow:
    inset 0 0 0 1px rgba(97, 155, 255, 0.74),
    0 0 0 4px var(--admin-accent-ring);
}

.create-dialog-card-dark :deep(.create-field input),
.create-dialog-card-dark :deep(.create-field textarea),
.create-dialog-card-dark :deep(.create-field .v-field__input),
.create-dialog-card-dark :deep(.create-field .v-autocomplete__selection),
.create-dialog-card-dark :deep(.create-field .v-select__selection-text),
.create-dialog-card-dark :deep(.create-field .v-select__selection) {
  color: #eef4ff;
}

.create-dialog-card-dark :deep(.create-field .v-chip),
.create-dialog-card-dark :deep(.create-field .v-chip__content),
.create-dialog-card-dark :deep(.create-field .v-chip .v-icon),
.create-dialog-card-dark :deep(.create-field .v-chip .v-chip__close) {
  color: #eef4ff;
}

.create-dialog-card-dark :deep(.weekdays-field .v-chip) {
  background: rgba(31, 72, 133, 0.42);
  border: 1px solid rgba(83, 122, 188, 0.62);
}

.create-dialog-card-dark :deep(.create-field .v-label),
.create-dialog-card-dark :deep(.create-field .v-field__append-inner),
.create-dialog-card-dark :deep(.create-field .v-messages__message),
.create-dialog-card-dark :deep(.create-field input::placeholder),
.create-dialog-card-dark :deep(.create-field textarea::placeholder) {
  color: #94a6c4;
}

.create-dialog-card-dark :deep(.time-field input[type='time']::-webkit-calendar-picker-indicator) {
  filter: invert(1);
}

.create-dialog-card-dark :deep(.price-field .v-field__prepend-inner),
.create-dialog-card-dark :deep(.price-field .v-field__prepend-inner .v-icon) {
  color: #eef4ff;
}

:deep(.admin-sessions-select-menu) {
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(223, 232, 246, 0.94);
  background: linear-gradient(180deg, rgba(250, 252, 255, 0.98), rgba(239, 246, 255, 0.98));
  box-shadow: 0 22px 48px rgba(79, 106, 154, 0.22);
}

:deep(.admin-sessions-select-menu .v-list) {
  padding: 8px;
  background: transparent;
}

:deep(.admin-sessions-select-menu .v-list-item) {
  min-height: 46px;
  border-radius: 14px;
  color: #172033;
}

:deep(.admin-sessions-select-menu .v-list-item:hover) {
  background: rgba(255, 229, 198, 0.62);
}

:deep(.admin-sessions-select-menu .v-list-item--active) {
  background: linear-gradient(180deg, var(--admin-accent) 0%, var(--admin-accent-strong) 100%);
  color: white;
}

:deep(.admin-sessions-select-menu-dark) {
  border-color: rgba(64, 82, 116, 0.62);
  background: linear-gradient(180deg, rgba(16, 23, 37, 0.99), rgba(22, 31, 48, 0.98));
  box-shadow: 0 24px 56px rgba(3, 8, 20, 0.55);
}

:deep(.admin-sessions-select-menu-dark .v-list) {
  background: transparent;
}

:deep(.admin-sessions-select-menu-dark .v-list-item) {
  color: #eef4ff;
}

:deep(.admin-sessions-select-menu-dark .v-list-item:hover) {
  background: rgba(83, 49, 18, 0.54);
}

:deep(.admin-sessions-select-menu-dark .v-list-item--active) {
  background: linear-gradient(180deg, var(--admin-accent) 0%, var(--admin-accent-strong) 100%);
  color: white;
}

@media (max-width: 1180px) {
  .sessions-grid,
  .overview-stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .admin-sessions-page {
    padding: 16px;
  }

  .admin-sessions-shell {
    overflow: hidden;
  }

  .admin-sessions-panel {
    display: block;
    padding: 16px;
  }

  .sidebar-card,
  .topbar-card .search-wrap {
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
    padding: 16px;
  }

  .topbar-tools .profile-pill,
  .logout-btn {
    display: none;
  }
}

@media (max-width: 768px) {
  .sessions-card {
    padding: 20px;
  }

  .sessions-header,
  .mobile-profile-row {
    flex-direction: column;
    align-items: stretch;
  }

  .overview-stats-grid,
  .sessions-grid,
  .create-fields-grid,
  .session-info-grid,
  .session-date-filters-dialog {
    grid-template-columns: 1fr;
  }

  .session-view-switch {
    flex-direction: column;
    align-items: stretch;
  }

  .session-view-tabs {
    flex-direction: column;
    align-items: stretch;
  }

  .session-filter-btn {
    margin-left: 0;
    justify-content: flex-start;
  }

  .desktop-only-btn {
    display: none;
  }

  .session-card-top,
  .session-card-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .session-children-add-row {
    grid-template-columns: 1fr;
  }

  .session-child-item {
    flex-direction: column;
    align-items: stretch;
  }

  .session-child-actions {
    justify-content: space-between;
  }

  .status-chip {
    align-self: flex-start;
  }
}

@media (max-width: 560px) {
  .admin-sessions-page {
    padding: 10px;
  }

  .admin-sessions-panel {
    padding: 10px;
  }

  .sessions-card {
    padding: 16px;
    border-radius: 24px;
  }

  .topbar-card,
  .mobile-header-card,
  .mobile-utility-card {
    border-radius: 20px;
  }

  .sessions-title {
    font-size: 1.75rem;
  }

  .summary-value {
    font-size: 1.6rem;
  }
}
</style>
