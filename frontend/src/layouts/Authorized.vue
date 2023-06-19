<template>
    <TheHeader />

    <main>
        <div class="wrapper" v-if="!isFetching && authStore.isAuthenticated">
            <TheBreadcrumb />

            <router-view v-slot="{ Component }">
                <component :is="Component"></component>
            </router-view>
        </div>

        <div class="loader__wrapper" v-else>
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
    import { ref, onBeforeMount } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';
    import { useDataStore } from '@/stores/DataStore';

    import TheHeader from '@/components/common/TheHeader.vue';
    import TheBreadcrumb from '@/components/common/TheBreadcrumb.vue';

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
    @import '@/assets/styles/animations.scss';

    .loader {
        &__wrapper {
            align-items: center;
            display: flex;
            justify-content: center;
            height: 100%;
            position: fixed;
            width: 100%;
        }

        &__border {
            animation: rotate .8s linear 0s infinite;
            background: linear-gradient(0deg, rgba(187, 134, 252, 0.1) 33%, rgba(187, 134, 252, 1) 100%);
            border-radius: 50%;
            height: 150px;
            padding: 3px;
            width: 150px;
        }

        &__core {
            background-color: $--color-background;
            border-radius: 50%;
            height: 100%;
            width: 100%;
        }

        &__text {
            color: $--color-primary;
            font-size: 2.4rem;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 15px;
            text-align: center;
        }
    }
</style>