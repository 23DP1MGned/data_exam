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
</style>
