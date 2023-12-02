<template>
        <v-dialog  v-model="dialog" persistent width="auto">
            <v-card color="primary">
                <v-card-text>
                    Please stand by
                    <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
</template>

<script setup lang="ts">
import { useLoadingStore } from '@/stores/loading.store'

const dialog = ref(false)
watch(
  () => useLoadingStore().isActive,
  function (current, prev) {
    dialog.value = current
  }
)
</script>