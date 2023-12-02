<template>
    <v-dialog v-model="dialog" color="red" width="450px">
        <v-card class="py-4">
            <div class="d-flex justify-center">
                <v-icon :color="useDialogStore().dialog.icon.color" size="100">{{ useDialogStore().dialog.icon.icon
                }}</v-icon>
            </div>
            <v-card-title class="d-flex justify-center">
                
                <div>{{ useDialogStore().dialog.title }}</div>
            </v-card-title>
            <!-- eslint-disable vue/no-v-html -->
            <v-card-text class="d-flex justify-center">
                {{ useDialogStore().dialog.message }}
            </v-card-text>

            <v-card-actions class="justify-center">
                <v-btn variant="flat" color="error" @click="useDialogStore().close()">{{ useDialogStore().dialog.btnMessage }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script lang="ts" setup>
import { useDialogStore } from '@/stores/dialog.store'
const dialog = ref(false)
watch(
    () => useDialogStore().dialog.active,
    function (current, prev) {
        dialog.value = current
    }
)
</script>