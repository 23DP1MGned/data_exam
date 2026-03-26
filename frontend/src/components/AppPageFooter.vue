<template>
  <footer
    class="app-page-footer"
    :class="{ 'app-page-footer-dark': darkMode }"
  >
    <div class="app-page-footer-decor" aria-hidden="true">
      <span class="app-page-footer-glow app-page-footer-glow-left"></span>
      <span class="app-page-footer-glow app-page-footer-glow-right"></span>
    </div>

    <div class="app-page-footer-content">
      <div class="app-page-footer-meta">© 2026 SportSystem. All rights reserved.</div>

      <nav class="app-page-footer-nav" aria-label="Footer navigation">
        <button type="button" class="app-page-footer-link" @click="aboutDialog = true">About</button>
        <button type="button" class="app-page-footer-link" @click="contactsDialog = true">Contacts</button>
      </nav>

      <div class="app-page-footer-side">
        <div class="app-page-footer-meta">Created by Maksims Gnedovs</div>

        <div class="app-page-footer-socials" aria-label="Social links">
          <button type="button" class="app-page-footer-link app-page-footer-link-icon" aria-label="Facebook">
            <v-icon size="18">mdi-facebook</v-icon>
          </button>
          <button type="button" class="app-page-footer-link app-page-footer-link-icon" aria-label="Instagram">
            <v-icon size="18">mdi-instagram</v-icon>
          </button>
          <button type="button" class="app-page-footer-link app-page-footer-link-icon" aria-label="LinkedIn">
            <v-icon size="18">mdi-linkedin</v-icon>
          </button>
        </div>
      </div>
    </div>

    <v-dialog v-model="aboutDialog" max-width="620">
      <div
        class="footer-dialog-card"
        :class="{ 'footer-dialog-card-dark': darkMode }"
      >
        <div class="footer-dialog-header">
          <div>
            <div class="footer-dialog-eyebrow">About</div>
            <h2 class="footer-dialog-title">SportSystem keeps the whole sports workflow together.</h2>
          </div>

          <button type="button" class="footer-dialog-close" @click="aboutDialog = false">
            <v-icon size="20">mdi-close</v-icon>
          </button>
        </div>

        <div class="footer-dialog-body">
          <p class="footer-dialog-copy">
            SportSystem is designed for sports schools and training clubs that need one place for
            managing groups, dated sessions, attendance tracking and parent payments.
          </p>

          <div class="footer-dialog-grid">
            <article class="footer-dialog-info-card">
              <div class="footer-dialog-info-label">Built for</div>
              <div class="footer-dialog-info-value">Admins, coaches, parents and children</div>
            </article>

            <article class="footer-dialog-info-card">
              <div class="footer-dialog-info-label">Core modules</div>
              <div class="footer-dialog-info-value">Groups, sessions, attendance, payments</div>
            </article>

            <article class="footer-dialog-info-card">
              <div class="footer-dialog-info-label">Workflow</div>
              <div class="footer-dialog-info-value">Scheduling, attendance tracking and payment management</div>
            </article>

            <article class="footer-dialog-info-card">
              <div class="footer-dialog-info-label">Created by</div>
              <div class="footer-dialog-info-value">Maksims Gnedovs</div>
            </article>
          </div>
        </div>
      </div>
    </v-dialog>

    <v-dialog v-model="contactsDialog" max-width="560">
      <div
        class="footer-dialog-card"
        :class="{ 'footer-dialog-card-dark': darkMode }"
      >
        <div class="footer-dialog-header">
          <div>
            <div class="footer-dialog-eyebrow">Contacts</div>
            <h2 class="footer-dialog-title">Contact information</h2>
          </div>

          <button type="button" class="footer-dialog-close" @click="contactsDialog = false">
            <v-icon size="20">mdi-close</v-icon>
          </button>
        </div>

        <div class="footer-dialog-body">
          <div class="footer-dialog-tabs">
            <button
              type="button"
              class="footer-dialog-tab"
              :class="{ 'footer-dialog-tab-active': contactsTab === 'details' }"
              @click="contactsTab = 'details'"
            >
              Contacts
            </button>
            <button
              type="button"
              class="footer-dialog-tab"
              :class="{ 'footer-dialog-tab-active': contactsTab === 'support' }"
              @click="contactsTab = 'support'"
            >
              Support
            </button>
          </div>

          <div v-if="contactsTab === 'details'" class="footer-contact-list">
            <article class="footer-contact-card">
              <div class="footer-contact-label">Email</div>
              <a class="footer-contact-link" href="mailto:maksims.gnedovs@sportsystem.app">
                maksims.gnedovs@sportsystem.app
              </a>
            </article>

            <article class="footer-contact-card">
              <div class="footer-contact-label">Phone</div>
              <a class="footer-contact-link" href="tel:+37120000000">+371 20 000 000</a>
            </article>

            <article class="footer-contact-card">
              <div class="footer-contact-label">Location</div>
              <div class="footer-contact-value">Riga, Latvia</div>
            </article>

            <article class="footer-contact-card">
              <div class="footer-contact-label">Availability</div>
              <div class="footer-contact-value">Mon-Fri, 09:00-18:00</div>
            </article>
          </div>

          <div v-else class="footer-support-wrap">
            <div v-if="supportSuccess" class="footer-support-success">
              Support request is ready to send.
            </div>

            <div class="footer-support-grid">
              <label class="footer-support-field">
                <span class="footer-contact-label">Your email</span>
                <input
                  v-model="supportForm.email"
                  type="email"
                  class="footer-support-input"
                  placeholder="you@example.com"
                >
              </label>

              <label class="footer-support-field">
                <span class="footer-contact-label">Subject</span>
                <input
                  v-model="supportForm.subject"
                  type="text"
                  class="footer-support-input"
                  placeholder="Describe the issue briefly"
                >
              </label>
            </div>

            <label class="footer-support-field footer-support-field-full">
              <span class="footer-contact-label">Message</span>
              <textarea
                v-model="supportForm.message"
                class="footer-support-textarea"
                rows="5"
                placeholder="Write your support request here..."
              ></textarea>
            </label>

            <div class="footer-support-actions">
              <button type="button" class="footer-support-btn footer-support-btn-secondary" @click="resetSupportForm">
                Clear
              </button>
              <button type="button" class="footer-support-btn footer-support-btn-primary" @click="handleSupportSubmit">
                Send request
              </button>
            </div>
          </div>
        </div>
      </div>
    </v-dialog>
  </footer>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useAuth } from '../services/auth'

defineProps({
  darkMode: {
    type: Boolean,
    default: false
  }
})

const { user } = useAuth()
const profileEmail = computed(() => user.value?.email ?? '')

const aboutDialog = ref(false)
const contactsDialog = ref(false)
const contactsTab = ref('details')
const supportSuccess = ref(false)
const supportForm = ref({
  email: profileEmail.value,
  subject: '',
  message: ''
})

watch(contactsDialog, (value) => {
  if (value) {
    contactsTab.value = 'details'
    supportSuccess.value = false
    if (!supportForm.value.email) {
      supportForm.value.email = profileEmail.value
    }
  }
})

watch(profileEmail, (value) => {
  if (!supportForm.value.email) {
    supportForm.value.email = value
  }
})

function resetSupportForm() {
  supportForm.value = {
    email: profileEmail.value,
    subject: '',
    message: ''
  }
  supportSuccess.value = false
}

function handleSupportSubmit() {
  supportSuccess.value = true
}
</script>

<style scoped>
.app-page-footer {
  position: relative;
  margin: 0 22px 22px;
  border-radius: 28px;
  overflow: hidden;
  background: rgba(249, 251, 255, 0.58);
  border: 1px solid rgba(255, 255, 255, 0.62);
}

.app-page-footer-dark {
  background: rgba(22, 31, 48, 0.72);
  border-color: rgba(74, 92, 126, 0.4);
}

.app-page-footer-decor {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(184, 216, 255, 0.14) 46%, rgba(255, 255, 255, 0.08)),
    linear-gradient(180deg, rgba(245, 250, 255, 0.14), rgba(225, 237, 252, 0.08));
  pointer-events: none;
}

.app-page-footer-dark .app-page-footer-decor {
  background:
    linear-gradient(135deg, rgba(96, 136, 208, 0.16), rgba(41, 58, 92, 0.1) 46%, rgba(255, 255, 255, 0.04)),
    linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.015));
}

.app-page-footer-glow {
  position: absolute;
  border-radius: 999px;
  filter: blur(28px);
  opacity: 0.78;
}

.app-page-footer-glow-left {
  top: -12px;
  left: 40px;
  width: 220px;
  height: 96px;
  background: rgba(150, 203, 255, 0.34);
}

.app-page-footer-glow-right {
  right: 70px;
  bottom: -20px;
  width: 260px;
  height: 102px;
  background: rgba(182, 216, 255, 0.28);
}

.app-page-footer-dark .app-page-footer-glow-left {
  background: rgba(78, 120, 190, 0.24);
}

.app-page-footer-dark .app-page-footer-glow-right {
  background: rgba(54, 93, 156, 0.22);
}

.app-page-footer-content {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
  align-items: center;
  gap: 16px;
  padding: 18px 24px;
}

.app-page-footer-meta {
  color: #7b8798;
  font-size: 0.82rem;
}

.app-page-footer-dark .app-page-footer-meta {
  color: #94a6c4;
}

.app-page-footer-nav {
  display: flex;
  align-items: center;
  gap: 12px;
  justify-content: center;
  justify-self: center;
}

.app-page-footer-socials {
  display: flex;
  align-items: center;
  gap: 10px;
  justify-self: end;
}

.app-page-footer-side {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 14px;
  justify-self: end;
}

.app-page-footer-link {
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

.app-page-footer-dark .app-page-footer-link {
  color: #b3c4e2;
}

.app-page-footer-link:hover {
  color: #315f9d;
  transform: translateY(-1px);
  border-color: rgba(173, 196, 229, 0.7);
  background: rgba(255, 255, 255, 0.36);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.38),
    0 8px 18px rgba(160, 184, 218, 0.18);
}

.app-page-footer-dark .app-page-footer-link:hover {
  color: #dce9ff;
  border-color: rgba(106, 129, 171, 0.56);
  background: rgba(255, 255, 255, 0.06);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 8px 18px rgba(5, 10, 24, 0.24);
}

.app-page-footer-link-icon {
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

.app-page-footer-dark .app-page-footer-link-icon {
  border-color: rgba(93, 109, 145, 0.42);
  background: rgba(255, 255, 255, 0.06);
  color: #b3c4e2;
}

.app-page-footer-link-icon:hover {
  background: rgba(255, 255, 255, 0.5);
}

.app-page-footer-dark .app-page-footer-link-icon:hover {
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

@media (max-width: 1024px) {
  .app-page-footer {
    margin: 0 18px 18px;
  }

  .app-page-footer-content {
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: start;
    row-gap: 14px;
  }

  .app-page-footer-nav {
    justify-self: end;
  }

  .app-page-footer-side {
    grid-column: 1 / -1;
    width: 100%;
    justify-content: space-between;
    justify-self: stretch;
  }
}

@media (max-width: 768px) {
  .app-page-footer {
    margin: 0 12px 12px;
  }

  .app-page-footer-content {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
    padding: 16px 18px;
  }

  .app-page-footer-meta {
    text-align: center;
  }

  .app-page-footer-nav {
    justify-content: center;
    justify-self: auto;
  }

  .app-page-footer-side {
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
}

@media (max-width: 560px) {
  .app-page-footer {
    margin: 0 10px 10px;
    border-radius: 20px;
  }

  .app-page-footer-content {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    padding: 14px;
  }

  .app-page-footer-nav {
    flex-wrap: wrap;
    justify-content: center;
    justify-self: auto;
  }

  .app-page-footer-socials {
    align-self: center;
    justify-self: auto;
  }

  .app-page-footer-side {
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
}
</style>
