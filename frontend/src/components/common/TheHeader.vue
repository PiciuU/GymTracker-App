<template>
    <header>
        <nav>
            GymTracker
            <font-awesome-icon icon="fa-solid fa-bars" size="1x"/>
        </nav>
    </header>
</template>

<script setup>
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();
</script>

<style lang="scss" scoped>
header {
    background: var(--color-overlay);
    padding: 10px 20px;
    height: var(--header-height);

    nav {
        align-items: center;
        display: flex;
        justify-content: space-between;
    }
}
</style>