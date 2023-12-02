<template>
    <div class="main-container">
        <div class="search-container">
            <v-card class="rounded-lg border main-card">
                <template v-slot:title>
                    <!-- <v-form ref="formRef" validate-on="submit lazy" @submit.prevent="findPlaces"> -->
                    <v-text-field :loading="loading" v-model.trim="searchPlace" density="compact" label="Search Place"
                        append-inner-icon="mdi-magnify" variant="outlined" hide-details="auto"
                        @keyup.enter="findPlaces"></v-text-field>
                    <!-- </v-form> -->
                </template>
                <template v-slot:text>
                    <v-divider class="border-opacity-100" color="#d4d4d4"></v-divider>
                    <v-row v-for="item in listPlaces">
                        <v-col cols="12">
                            <v-card class="list-place-container" @click="() => { searchPlace = item.name; findPlaces(); }">
                                <div>
                                    <v-row>
                                        <v-col :md="item.photos && item.photos?.length > 0 ? '9' : 12" cols="12">
                                            <div class="place-detail-container pa-2">
                                                <!-- <template v-slot:title> -->
                                                <div class="text-h6">{{ item.name }}</div>
                                                <!-- </template> -->
                                                <!-- <template v-slot:text> -->
                                                <div class="d-flex">
                                                    <div class="text-subtitle-1 mr-1">{{ item.rating }}</div>
                                                    <v-rating readonly :length="5" :size="22" :model-value="item.rating"
                                                        color="warning" active-color="warning" half-increments />
                                                </div>
                                                <div class="text-subtitle-1"><b class="text-capitalize">{{ item.types[0] ||
                                                    ''
                                                }}</b> {{ item.vicinity }}</div>
                                                <div class="text-subtitle-1"
                                                    :class="{ 'text-success': item.opening_hours?.open_now, 'text-error': !item.opening_hours?.open_now }">
                                                    {{ item.opening_hours?.open_now ? 'Open' : 'Closed' }}</div>
                                                <!-- </template> -->
                                            </div>

                                        </v-col>
                                        <v-col v-if="item.photos && item.photos?.length > 0" md="3" cols="12"
                                            class="d-flex align-center">
                                            <v-img :width="400" cover
                                                :src="`${config.public.baseURL}/api/restaurants/place-photo?photoRef=${item.photos[0].photo_reference}`"></v-img>
                                        </v-col>
                                    </v-row>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </template>
            </v-card>

        </div>
        <div class="map-container">
            <div ref="mapRef"></div>
        </div>

    </div>
    <Loading></Loading>
    <Dialog></Dialog>
</template>
  
<script setup lang="ts">
import Loading from '@/components/Loading.vue';
import Dialog from '@/components/Dialog.vue';
import { useReCaptcha } from 'vue-recaptcha-v3';

const mapRef = ref(null)
const zoom = 13
const defaultLocation: any = reactive({ lat: '', lng: '', title: '' })
const loading = ref(false)
const searchPlace = ref('Bang sue')
const noImagePicture = ref('https://t4.ftcdn.net/jpg/04/73/25/49/360_F_473254957_bxG9yf4ly7OBO5I0O5KABlN930GwaMQz.jpg')
const listPlaces = ref([])
const config = useRuntimeConfig()
const infoWindowRef: any = reactive({})
const route = useRoute()
const router = useRouter()
const recaptchaInstance = useReCaptcha();
// initMap is now async
let map: google.maps.Map;
let markers: google.maps.Marker[] = [];
async function initMap(): Promise<void> {
    const { Map } = await google.maps.importLibrary("maps") as google.maps.MapsLibrary;
    await getDefaultLocation()
    map = new Map(mapRef.value as HTMLElement, {
        center: defaultLocation,
        zoom: zoom,
        mapId: 'scgAssignment',
    });
}

async function addMarker(value: any, pin?: any): Promise<void> {
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
    const parser = new DOMParser();
    const pinSvgString = '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24"><title>silverware-fork-knife</title><path d="M11,9H9V2H7V9H5V2H3V9C3,11.12 4.66,12.84 6.75,12.97V22H9.25V12.97C11.34,12.84 13,11.12 13,9V2H11V9M16,6V14H18.5V22H21V2C18.24,2 16,4.24 16,6Z" /></svg>'
    const pinOptions = pin || new PinElement({
        background: '#FF4D4D',
        scale: .9,
        glyph: parser.parseFromString(pinSvgString, 'image/svg+xml').documentElement,
    });
    const marker = new AdvancedMarkerElement({
        map: map,
        position: value.geometry?.location,
        title: value.name,
        content: buildContent(pinOptions.element, value),
    });
    addMarkerEvent(marker, value)
    markers.push(marker)
}

function buildContent(iconElem: any, place: any) {
    const content = document.createElement("div")
    content.style.cursor = 'pointer'
    content.setAttribute("id", place.place_id);
    content.appendChild(iconElem)
    return content
}

function addMarkerEvent(marker: any, place: any) {
    const contentString = `
    <div><img width="100%" style="max-height: 160px;" src="${place.photos && place.photos.length > 0 ? config.public.baseURL + '/api/restaurants/place-photo?photoRef=' + place.photos[0].photo_reference : noImagePicture.value}"></img></div>
    <div>${place.name}</div>
    `;

    const infoWindow = new google.maps.InfoWindow({
        content: contentString
    });
    infoWindowRef[place.place_id] = infoWindow
    google.maps.event.addListener(marker, 'click', function () {
        for (const [key, value] of Object.entries(infoWindowRef)) {
            infoWindowRef[key].close()
        }
        infoWindowRef[place.place_id].open(map, marker);
    });
}

function setMapOnAll(map: google.maps.Map | null) {
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

function hideMarkers(): void {
    setMapOnAll(null);
}

function deleteMarkers(): void {
    hideMarkers();
    markers = [];
}

async function getDefaultLocation() {
    try {
        const dataDefaultLocation: any = await useMyFetch('api/restaurants/default-location')
        const dataDefaultLocationValue = dataDefaultLocation.data.value
        Object.assign(defaultLocation, dataDefaultLocationValue)
    } catch (e) {
        console.log(e)
    }
}

async function findPlaces() {
    try {
        const recaptchaToken = await getRecaptchaToken()
        router.push({ query: { searchPlace: searchPlace.value } })
        const places: any = await useMyFetch('api/restaurants/find-places', {
            headers: { 'g-recaptcha-response': recaptchaToken },
            method: 'post',
            body: { searchPlace: searchPlace.value !== '' ? searchPlace.value : undefined }
        })
        console.log(typeof places.data.value)
        listPlaces.value = places.data.value.results
        deleteMarkers()
        for (const value of places.data.value.results) {
            await addMarker(value)
        }
    } catch (e) {
        console.log(e)
    }
}

async function textSearch() {
    try {
        const places: any = await useMyFetch('api/restaurants/text-search', { method: 'post', body: { searchPlace: searchPlace.value !== '' ? searchPlace.value : undefined } })
        listPlaces.value = places.data.value.results
        deleteMarkers()
        for (const value of places.data.value.results) {
            await addMarker(value)
        }
    } catch (e) {
        console.log(e)
    }
}

async function getPlacePhoto(item: any) {
    try {
        const places: any = await useMyFetch('api/restaurants/place-photo', { method: 'post', body: { photoRef: item.photo_reference, maxWidth: item.width } })
        listPlaces.value = places.data.value.results
        deleteMarkers()
        for (const value of places.data.value.results) {
            await addMarker(value)
        }
    } catch (e) {
        console.log(e)
    }
}

const getRecaptchaToken = async () => {
    // optional you can await for the reCaptcha load
    await recaptchaInstance?.recaptchaLoaded();

    // get the token, a custom action could be added as argument to the method
    const token = await recaptchaInstance?.executeRecaptcha();

    return token;
};

onMounted(async () => {
    await initMap()
    await findPlaces()
});
</script>

<style lang="scss" scoped>
.main-container {
    position: relative;
    height: 100vh;
    overflow: hidden;
}

.search-container {
    position: relative;
    width: 100%;
    max-width: 400px;
    top: 6px;
    left: 10px;
    z-index: 9;

    @media only screen and (max-width: 460px) {
        left: 0px;
        top: 0px;
    }

    :deep(.main-card) {
        max-height: 500px;
        overflow-y: scroll;
    }

    :deep(.v-card-item__content),
    :deep(.v-card-title) {
        overflow: visible;
    }

    :deep(.v-field--variant-outlined.v-field--focused .v-field__outline) {
        --v-field-border-width: 1px;
        --v-field-border-opacity: 1;
    }
}

.map-container {
    div {
        width: 100%;
        height: 100%;
        position: inherit !important;
    }
}

.list-place-container {
    cursor: pointer;

    :hover {
        background-color: #004D40;
        color: white;
    }
}
</style>