import { defineStore } from 'pinia'

export const useDialogStore = defineStore({
  id: 'dialog',
  state: () => ({
    dialog: {
      active: false,
      title: 'Warning',
      message: 'Some thing went wrong',
      btnMessage: 'close',
      icon: {
        icon: 'mdi-alert-circle-outline',
        color: 'warning',
      }
    }
  }),
  actions: {
    open() {
      this.dialog.active = true
    },
    close() {
      this.dialog.active = false
      clearError()
    }
  }
})
