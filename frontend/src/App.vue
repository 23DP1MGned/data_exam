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
</style>
