import { defineStore } from 'pinia'

export const useLoadingStore = defineStore({
  id: 'loading',
  state: () => ({
    isActive: false
  }),
  actions: {
    start() {
      this.isActive = true
    },
    finish() {
      this.isActive = false
    }
  }
})
