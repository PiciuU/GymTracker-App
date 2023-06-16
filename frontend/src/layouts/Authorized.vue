<template>
    <TheHeader />

    <main>
        <div class="container" v-if="!isFetching && authStore.isAuthenticated">
            <Breadcrumb />

            <router-view v-slot="{ Component }">
                <component :is="Component"></component>
            </router-view>
        </div>

        <div class="loading-container" v-else>
            <div class="loader__circle">
                <div class="loader__border">
                    <div class="loader__core"></div>
                </div>
                <div class="loader__text">Loading</div>
            </div>
        </div>
    </main>
</template>

<script setup>
    import { onBeforeMount, ref } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';
    import { useDataStore } from '@/stores/DataStore';

    import TheHeader from '@/components/common/TheHeader.vue';
    import Breadcrumb from '@/components/Breadcrumb.vue';

    const dataStore = useDataStore();
    const authStore = useAuthStore();
    let isFetching = ref(false);

    onBeforeMount(async () => {
        isFetching.value = true;
        authStore.fetchUser();

        await dataStore.fetchData();
        isFetching.value = false;
    });
</script>

<style lang="scss" scoped>
main {
    display: flex;
    flex: 1;
    padding: 0px 20px;
}

.container {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.loading-container {
    position: fixed;
    left: 0;
    top: 45px;
    width: 100vw;
    height: calc(100vh - 45px);
    display: flex;
    justify-content: center;
    align-items: center;
    background: var(--color-background);
}

.loader {
    @keyframes spin {
        from { transform: rotate(0); }
        to { transform: rotate(360deg); }
    }

    @keyframes bounce {
        0%, 15% {transform: translate(0,0); }
        50% { transform: translate(0,-5px); }
        85%, 100% { transform: translate(0,0); }
    }

    &__border {
      width: 150px;
      height: 150px;
      padding: 3px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      background: rgb(63,249,220);
      background: linear-gradient(0deg, rgba(187, 134, 252, 0.1) 33%, rgba(187, 134, 252, 1) 100%);
      animation: spin .8s linear 0s infinite;
    }

    &__core {
        width: 100%;
        height: 100%;
        background-color: var(--color-background);
        border-radius: 50%;
    }

    &__text {
        margin-top: 15px;
        color: var(--color-primary);
        font-size: 2.4rem;
        letter-spacing: 2px;
        font-weight: bold;
        text-align: center;
    }
}
</style>