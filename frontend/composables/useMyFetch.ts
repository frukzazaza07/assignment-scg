import type { UseFetchOptions } from "nuxt/app"
import { useLoadingStore } from '@/stores/loading.store'
import { useDialogStore } from '@/stores/dialog.store'
export const useMyFetch: typeof useFetch = (request, opts?) => {
    const config = useRuntimeConfig()
    return useFetch(request, {
        baseURL: config.public.baseURL,
        key: `${new Date().getTime()}`,
        onRequest({ request, options }) {
            useLoadingStore().start()
        },
        onRequestError({ request, response, options }) {
            useLoadingStore().finish()
            useDialogStore().open()
        },
        onResponse({ request, response, options }) {
            useLoadingStore().finish()
        },
        onResponseError({ request, response, options }) {
            useLoadingStore().finish()
            useDialogStore().open()

        }, ...opts
    })
}