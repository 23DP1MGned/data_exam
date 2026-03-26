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
</style>
