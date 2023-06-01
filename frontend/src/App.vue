<script setup>
import { RouterView } from 'vue-router';
import Breadcrumb from './components/Breadcrumb.vue';
</script>

<template>
    <header>
        <nav>
            GymTracker
            <font-awesome-icon icon="fa-solid fa-bars" size="1x" />
        </nav>
    </header>

    <main>
        <Breadcrumb />

        <RouterView />
    </main>

</template>

<style scoped lang="scss">
header {
    background: var(--color-overlay);
    padding: 1rem 2rem;

    nav {
        align-items: center;
        display: flex;
        justify-content: space-between;
    }
}

main {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    padding: 0rem 2rem;
}

.container.card__container {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    justify-content: space-around;
    margin: 1rem 0rem 2rem 0rem;
    gap: 15px;
}
</style>
