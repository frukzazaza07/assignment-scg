<script lang="ts">
import { useReCaptcha } from 'vue-recaptcha-v3';

export default defineComponent({
    setup() {
        // initialize a instance
        const recaptchaInstance = useReCaptcha();

        const recaptcha = async () => {
            // optional you can await for the reCaptcha load
            await recaptchaInstance?.recaptchaLoaded();

            // get the token, a custom action could be added as argument to the method
            const token = await recaptchaInstance?.executeRecaptcha('');

            return token;
        };

        return {
            recaptcha,
        };
    },
});
</script>