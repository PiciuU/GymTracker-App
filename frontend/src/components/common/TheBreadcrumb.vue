<template>
    <aside class="breadcrumb" v-if="route.meta.breadcrumb">
        <div class="breadcrumb__list" v-for="(part, index) in parts" :key="index">
            <span class="breadcrumb__link--current" v-if="isLast(index)">{{ part }}</span>
            <router-link class="breadcrumb__link" v-else :to="{name: part}">{{ part }}</router-link>
            <span class="breadcrumb__divider" v-if="!isLast(index)"><font-awesome-icon icon="angle-up" rotation="90"/></span>
        </div>
    </aside>
</template>

<script setup>
    import { ref, watchEffect } from 'vue';
    import { useRoute } from 'vue-router';

    const route = useRoute();
    const parts = ref([]);

    watchEffect(() => {
        refreshBreadcrumb(route.meta.breadcrumb);
    });

    function refreshBreadcrumb(data) {
        parts.value = data ? data.split('/') : [];
    }

    const isLast = (index) => index === parts.value.length - 1;
</script>

<style lang="scss" scoped>
    .breadcrumb {
        display: flex;
        font-size: 1.2rem;
        margin-bottom: auto;

        &__divider {
            margin: 0 5px
        }

        &__link {
            transition: color .25s ease-in-out;

            &--current {
                color: $--color-primary;
                font-weight: bold;
            }

            &:hover {
                color: $--color-secondary;
            }
        }
    }
</style>