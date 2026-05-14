<template>
  <v-app>
    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<style>
html,
body,
#app {
  min-height: 100%;
  margin: 0;
  background:
    radial-gradient(circle at top left, rgba(37, 99, 235, 0.28), transparent 32%),
    radial-gradient(circle at top right, rgba(79, 70, 229, 0.18), transparent 30%),
    radial-gradient(circle at bottom center, rgba(56, 189, 248, 0.16), transparent 34%),
    linear-gradient(180deg, #c9e0fb 0%, #bfd9f8 42%, #b1cef3 100%);
}

body {
  color: #162033;
}

.v-application {
  background:
    radial-gradient(circle at top left, rgba(37, 99, 235, 0.28), transparent 32%),
    radial-gradient(circle at top right, rgba(79, 70, 229, 0.18), transparent 30%),
    radial-gradient(circle at bottom center, rgba(56, 189, 248, 0.16), transparent 34%),
    linear-gradient(180deg, #c9e0fb 0%, #bfd9f8 42%, #b1cef3 100%) !important;
}

.v-application__wrap,
.v-main {
  background: transparent;
}

.app-select-menu {
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(223, 232, 246, 0.94);
  background: linear-gradient(180deg, rgba(250, 252, 255, 0.98), rgba(239, 246, 255, 0.98));
  box-shadow: 0 22px 48px rgba(79, 106, 154, 0.22);
}

.app-select-menu .v-list {
  padding: 8px;
  background: transparent;
}

.app-select-menu .v-list-item {
  min-height: 46px;
  border-radius: 14px;
  color: #172033;
}

.app-select-menu .v-list-item:hover {
  background: rgba(207, 226, 252, 0.56);
}

.app-select-menu .v-list-item--active {
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  color: white;
}

.app-select-menu-dark {
  border-color: rgba(64, 82, 116, 0.62);
  background: linear-gradient(180deg, rgba(16, 23, 37, 0.99), rgba(22, 31, 48, 0.98));
  box-shadow: 0 24px 56px rgba(3, 8, 20, 0.55);
}

.app-select-menu-dark .v-list {
  background: transparent;
}

.app-select-menu-dark .v-list-item {
  color: #eef4ff;
}

.app-select-menu-dark .v-list-item:hover {
  background: rgba(36, 52, 79, 0.78);
}

.app-select-menu-dark .v-list-item--active {
  background: linear-gradient(180deg, #1677ff 0%, #0f5fe3 100%);
  color: white;
}

.admin-select-menu {
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(246, 199, 147, 0.92);
  background: linear-gradient(180deg, rgba(255, 251, 246, 0.98), rgba(255, 244, 232, 0.98));
  box-shadow: 0 22px 48px rgba(182, 103, 24, 0.18);
}

.admin-select-menu .v-list {
  padding: 8px;
  background: transparent;
}

.admin-select-menu .v-list-item {
  min-height: 46px;
  border-radius: 14px;
  color: #2a1a0d;
}

.admin-select-menu .v-list-item-title {
  color: inherit;
  font-weight: 600;
}

.admin-select-menu .v-list-item-subtitle {
  color: #85542a;
}

.admin-select-menu .v-list-item:hover {
  background: rgba(255, 229, 198, 0.72);
}

.admin-select-menu .v-list-item--active {
  background: linear-gradient(180deg, #f28c28 0%, #de6f12 100%);
  color: white;
}

.admin-select-menu .v-list-item--active .v-list-item__overlay {
  opacity: 0 !important;
}

.admin-select-menu .v-selection-control,
.admin-select-menu .v-selection-control__wrapper,
.admin-select-menu .v-selection-control__input,
.admin-select-menu .v-checkbox-btn {
  color: #bf6515;
}

.admin-select-menu .v-selection-control .v-icon,
.admin-select-menu .v-checkbox-btn .v-icon {
  color: inherit;
}

.admin-select-menu-dark {
  border-color: rgba(113, 79, 42, 0.68);
  background: linear-gradient(180deg, rgba(24, 19, 14, 0.99), rgba(35, 26, 17, 0.98));
  box-shadow: 0 24px 56px rgba(8, 4, 1, 0.52);
}

.admin-select-menu-dark .v-list {
  background: transparent;
}

.admin-select-menu-dark .v-list-item {
  color: #fff1e3;
}

.admin-select-menu-dark .v-list-item-title {
  color: inherit;
}

.admin-select-menu-dark .v-list-item-subtitle {
  color: #d2b08f;
}

.admin-select-menu-dark .v-list-item:hover {
  background: rgba(83, 49, 18, 0.54);
}

.admin-select-menu-dark .v-list-item--active {
  background: linear-gradient(180deg, #ff9e3d 0%, #f08219 100%);
  color: white;
}

.admin-select-menu-dark .v-list-item--active .v-list-item__overlay {
  opacity: 0 !important;
}

.admin-select-menu-dark .v-selection-control,
.admin-select-menu-dark .v-selection-control__wrapper,
.admin-select-menu-dark .v-selection-control__input,
.admin-select-menu-dark .v-checkbox-btn {
  color: #ffc78f;
}

.admin-select-menu-dark .v-selection-control .v-icon,
.admin-select-menu-dark .v-checkbox-btn .v-icon {
  color: inherit;
}

.admin-theme-root {
  --admin-accent: #f28c28;
  --admin-accent-strong: #de6f12;
  --admin-accent-soft: #fff2e3;
  --admin-accent-soft-strong: #ffe4c2;
  --admin-accent-border: rgba(242, 140, 40, 0.32);
  --admin-accent-shadow: rgba(242, 140, 40, 0.28);
  --admin-accent-shadow-soft: rgba(242, 140, 40, 0.16);
  --admin-accent-ring: rgba(242, 140, 40, 0.12);
  --admin-accent-text: #bf6515;
  --admin-accent-muted: #9a5a1d;
  --admin-accent-dark-text: #ffc78f;
}

.admin-theme-root.admin-theme-root-dark {
  --admin-accent: #ff9e3d;
  --admin-accent-strong: #f08219;
  --admin-accent-soft: rgba(96, 56, 17, 0.32);
  --admin-accent-soft-strong: rgba(122, 69, 18, 0.38);
  --admin-accent-border: rgba(255, 158, 61, 0.3);
  --admin-accent-shadow: rgba(255, 158, 61, 0.24);
  --admin-accent-shadow-soft: rgba(255, 158, 61, 0.14);
  --admin-accent-ring: rgba(255, 158, 61, 0.14);
  --admin-accent-text: #ffbe7b;
  --admin-accent-muted: #ffcf98;
  --admin-accent-dark-text: #ffd5a8;
}

.coach-theme-root {
  --coach-accent: #9c7cff;
  --coach-accent-strong: #7f5cff;
  --coach-accent-soft: #f4efff;
  --coach-accent-soft-strong: #e6dcff;
  --coach-accent-border: rgba(156, 124, 255, 0.32);
  --coach-accent-border-strong: rgba(156, 124, 255, 0.54);
  --coach-accent-shadow: rgba(156, 124, 255, 0.28);
  --coach-accent-shadow-soft: rgba(156, 124, 255, 0.16);
  --coach-accent-ring: rgba(156, 124, 255, 0.12);
  --coach-accent-text: #7654df;
  --coach-accent-muted: #8a6af0;
  --coach-accent-dark-text: #d8c7ff;
}

.coach-theme-root.coach-theme-root-dark {
  --coach-accent: #b79cff;
  --coach-accent-strong: #9879ff;
  --coach-accent-soft: rgba(58, 43, 99, 0.34);
  --coach-accent-soft-strong: rgba(73, 56, 123, 0.42);
  --coach-accent-border: rgba(183, 156, 255, 0.32);
  --coach-accent-border-strong: rgba(183, 156, 255, 0.5);
  --coach-accent-shadow: rgba(183, 156, 255, 0.24);
  --coach-accent-shadow-soft: rgba(183, 156, 255, 0.14);
  --coach-accent-ring: rgba(183, 156, 255, 0.14);
  --coach-accent-text: #cfbdff;
  --coach-accent-muted: #b89df8;
  --coach-accent-dark-text: #e0d3ff;
}

.dashboard-page,
.schedule-page,
.groups-page,
.attendance-page,
.payments-page,
.coach-attendance-page,
.admin-home-page,
.users-page,
.admin-groups-page,
.admin-sessions-page,
.admin-payments-page {
  min-height: 100dvh;
  box-sizing: border-box;
}

.dashboard-shell,
.schedule-shell,
.groups-shell,
.attendance-shell,
.payments-shell,
.coach-attendance-shell,
.admin-home-shell,
.users-shell,
.admin-groups-shell,
.admin-sessions-shell,
.admin-payments-shell {
  min-height: 100%;
  display: flex;
  flex-direction: column;
}

.dashboard-panel,
.schedule-panel,
.groups-panel,
.attendance-panel,
.payments-panel,
.coach-attendance-panel,
.admin-home-panel,
.users-panel,
.admin-groups-panel,
.admin-sessions-panel,
.admin-payments-panel {
  flex: 1;
}

.mobile-brand-inline,
.brand-block,
.brand-text,
.mobile-brand-copy {
  min-width: 0;
}

.mobile-brand-inline,
.brand-block,
.brand-text,
.mobile-brand-copy {
  overflow: hidden;
}

.mobile-brand-inline {
  flex: 1 1 0;
  width: 0;
}

.brand-text,
.mobile-brand-copy {
  flex: 1 1 auto;
  width: 0;
}

.brand-name,
.brand-caption,
.mobile-brand-copy .brand-name,
.mobile-brand-copy .brand-caption {
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

@media (max-width: 560px) {
  .dashboard-page,
  .schedule-page,
  .groups-page,
  .attendance-page,
  .payments-page,
  .coach-attendance-page,
  .admin-home-page,
  .users-page,
  .admin-groups-page,
  .admin-sessions-page,
  .admin-payments-page {
    padding: 10px !important;
  }

  .dashboard-shell,
  .schedule-shell,
  .groups-shell,
  .attendance-shell,
  .payments-shell,
  .coach-attendance-shell,
  .admin-home-shell,
  .users-shell,
  .admin-groups-shell,
  .admin-sessions-shell,
  .admin-payments-shell {
    border-radius: 24px !important;
  }

  .dashboard-panel,
  .schedule-panel,
  .groups-panel,
  .attendance-panel,
  .payments-panel,
  .coach-attendance-panel,
  .admin-home-panel,
  .users-panel,
  .admin-groups-panel,
  .admin-sessions-panel,
  .admin-payments-panel {
    gap: 12px !important;
    padding: 10px !important;
  }

  .topbar-card,
  .overview-shell-card,
  .overview-card,
  .users-card,
  .groups-card,
  .sessions-card,
  .payments-card,
  .attendance-card,
  .coach-page-card,
  .calendar-card,
  .workbench-card,
  .list-card,
  .finder-card {
    padding: 16px !important;
    border-radius: 22px !important;
  }

  .overview-stat-card,
  .stat-card,
  .coach-stat-card,
  .group-card,
  .session-card,
  .payment-item,
  .overview-item,
  .user-card,
  .requires-item,
  .roster-item,
  .day-session-pill {
    padding: 14px !important;
    border-radius: 18px !important;
  }

  .users-header,
  .groups-header,
  .sessions-header,
  .payments-header,
  .attendance-header,
  .coach-page-header,
  .toolbar-row {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 12px !important;
  }

  .mobile-create-btn,
  .session-filter-btn,
  .toolbar-btn,
  .create-btn {
    width: 100% !important;
  }

  .overview-title,
  .users-title,
  .groups-title,
  .sessions-title,
  .payments-title,
  .attendance-title,
  .coach-page-title {
    font-size: clamp(1.8rem, 7vw, 2.2rem) !important;
    line-height: 1.08 !important;
  }

  .overview-subtitle,
  .users-subtitle,
  .groups-subtitle,
  .sessions-subtitle,
  .payments-subtitle,
  .attendance-subtitle,
  .coach-page-subtitle {
    font-size: 0.95rem !important;
    line-height: 1.55 !important;
  }
}

@media (max-width: 380px) {
  .dashboard-page,
  .schedule-page,
  .groups-page,
  .attendance-page,
  .payments-page,
  .coach-attendance-page,
  .admin-home-page,
  .users-page,
  .admin-groups-page,
  .admin-sessions-page,
  .admin-payments-page {
    padding: 8px !important;
  }

  .dashboard-shell,
  .schedule-shell,
  .groups-shell,
  .attendance-shell,
  .payments-shell,
  .coach-attendance-shell,
  .admin-home-shell,
  .users-shell,
  .admin-groups-shell,
  .admin-sessions-shell,
  .admin-payments-shell {
    border-radius: 20px !important;
  }

  .dashboard-panel,
  .schedule-panel,
  .groups-panel,
  .attendance-panel,
  .payments-panel,
  .coach-attendance-panel,
  .admin-home-panel,
  .users-panel,
  .admin-groups-panel,
  .admin-sessions-panel,
  .admin-payments-panel {
    gap: 8px !important;
    padding: 8px !important;
  }

  .content-shell {
    gap: 8px !important;
  }

  .topbar-card,
  .overview-shell-card,
  .overview-card,
  .users-card,
  .groups-card,
  .sessions-card,
  .payments-card,
  .attendance-card,
  .coach-page-card,
  .calendar-card,
  .workbench-card,
  .list-card,
  .finder-card {
    padding: 14px !important;
    border-radius: 18px !important;
  }

  .overview-stat-card,
  .stat-card,
  .coach-stat-card,
  .group-card,
  .session-card,
  .payment-item,
  .overview-item,
  .user-card,
  .requires-item,
  .roster-item,
  .day-session-pill {
    padding: 12px !important;
    border-radius: 16px !important;
  }

  .mobile-header-card,
  .mobile-utility-card {
    padding: 10px !important;
    border-radius: 18px !important;
  }

  .mobile-header-card {
    gap: 8px !important;
  }

  .mobile-profile-row {
    flex-direction: column !important;
    align-items: stretch !important;
  }

  .profile-pill,
  .mobile-profile-pill {
    width: 100%;
    gap: 10px !important;
    padding: 8px 10px !important;
    border-radius: 18px !important;
  }

  .mobile-brand-inline {
    gap: 8px !important;
    min-width: 0;
  }

  .mobile-brand-icon {
    width: 36px !important;
    height: 36px !important;
    border-radius: 12px !important;
  }

  .mobile-brand-copy .brand-name {
    font-size: 0.92rem !important;
  }

  .mobile-brand-copy .brand-caption {
    font-size: 0.68rem !important;
  }

  .mobile-menu-btn,
  .mobile-header-actions .top-icon-btn {
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
  }

  .mobile-header-actions {
    gap: 8px !important;
  }

  .profile-name {
    font-size: 0.88rem !important;
  }

  .profile-email {
    font-size: 0.78rem !important;
  }

  .overview-title,
  .users-title,
  .groups-title,
  .sessions-title,
  .payments-title,
  .attendance-title,
  .coach-page-title {
    font-size: clamp(1.55rem, 7vw, 1.9rem) !important;
  }

  .overview-subtitle,
  .users-subtitle,
  .groups-subtitle,
  .sessions-subtitle,
  .payments-subtitle,
  .attendance-subtitle,
  .coach-page-subtitle {
    font-size: 0.9rem !important;
    line-height: 1.5 !important;
  }

  .summary-value,
  .stat-value {
    font-size: 1.7rem !important;
  }

  .icon-badge {
    top: -1px !important;
    right: -1px !important;
    min-width: 18px !important;
    height: 18px !important;
    padding: 0 4px !important;
    font-size: 0.64rem !important;
  }
}

@media (max-width: 320px) {
  .dashboard-page,
  .schedule-page,
  .groups-page,
  .attendance-page,
  .payments-page,
  .coach-attendance-page,
  .admin-home-page,
  .users-page,
  .admin-groups-page,
  .admin-sessions-page,
  .admin-payments-page {
    padding: 6px !important;
  }

  .dashboard-panel,
  .schedule-panel,
  .groups-panel,
  .attendance-panel,
  .payments-panel,
  .coach-attendance-panel,
  .admin-home-panel,
  .users-panel,
  .admin-groups-panel,
  .admin-sessions-panel,
  .admin-payments-panel {
    padding: 6px !important;
  }

  .content-shell {
    gap: 6px !important;
  }

  .topbar-card,
  .overview-shell-card,
  .overview-card,
  .users-card,
  .groups-card,
  .sessions-card,
  .payments-card,
  .attendance-card,
  .coach-page-card,
  .calendar-card,
  .workbench-card,
  .list-card,
  .finder-card {
    padding: 12px !important;
    border-radius: 16px !important;
  }

  .mobile-header-card,
  .mobile-utility-card {
    padding: 8px !important;
    border-radius: 16px !important;
  }

  .mobile-brand-inline {
    min-width: 0;
  }

  .mobile-brand-copy .brand-name {
    font-size: 0.86rem !important;
  }

  .mobile-brand-copy .brand-caption {
    font-size: 0.64rem !important;
  }

  .overview-title,
  .users-title,
  .groups-title,
  .sessions-title,
  .payments-title,
  .attendance-title,
  .coach-page-title {
    font-size: 1.42rem !important;
  }

  .summary-value,
  .stat-value {
    font-size: 1.55rem !important;
  }

  .mobile-header-actions {
    gap: 6px !important;
  }

  .mobile-menu-btn,
  .mobile-header-actions .top-icon-btn {
    width: 38px !important;
    height: 38px !important;
    min-width: 38px !important;
  }
}
</style>
