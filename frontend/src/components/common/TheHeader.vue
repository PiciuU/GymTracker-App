<template>
    <header>
        <nav>
            <router-link to="/" @click="handleAction">GymTracker</router-link>
            <font-awesome-icon icon="bars" @click="handleAction"/>

            <div class="menu" :class="{ 'menu--expanded': isExpanded }">
                <div class="menu__links">
                    <router-link to="/workout" @click="handleAction">
                        Workout
                    </router-link>
                    <router-link to="/exercises" @click="handleAction">
                        Exercises
                    </router-link>
                    <router-link to="/progress" @click="handleAction">
                        Progress
                    </router-link>
                </div>
                <div class="menu__user">
                    <div class="menu__username">
                        Hello <span>{{ authStore.user.login }}</span>
                    </div>
                    <div class="menu__options">
                        <router-link to="/settings" @click="handleAction">
                            Settings
                        </router-link>
                        <router-link to="#" @click="authStore.logout()">
                            Logout
                        </router-link>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
    import { ref } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    let isExpanded = ref(false);

    const handleAction = () => {
        isExpanded.value = !isExpanded.value;
        document.body.classList.toggle('disableScroll');
    };
</script>

<style lang="scss" scoped>
    header {
        background: $--color-overlay;
        height: $--header-height;
        padding: 10px 20px;

        nav {
            align-items: center;
            display: flex;
            justify-content: space-between;

            svg {
                cursor: pointer;
                transition: color .25s ease-in-out;

                &:hover {
                    color: $--color-primary;
                }
            }
        }
    }

    .menu {
        align-items: center;
        background-color: $--color-background;
        border-top: 1px solid $--color-primary;
        display: flex;
        flex-direction: column;
        height: calc(100% - $--header-height);
        left: -100%;
        overflow-y: auto;
        padding: 20px 10px;
        position: fixed;
        top: $--header-height;
        transition: left .3s ease-in-out;
        width: 100%;
        z-index: 999;

        &--expanded {
            left: 0%;
        }

        &__links {
            display: flex;
            flex-direction: column;
            margin-top: 20px;

            a {
                font-size: 3.2rem;
                margin: 10px 0px;
                text-align: center;
                transition: color .25s ease-in-out;

                &:hover, &:active {
                    color: $--color-primary;
                }
            }
        }

        &__user {
            margin-top: auto;
            padding: 20px 10px;
        }

        &__username {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;

            span {
                color: $--color-secondary;
            }

            &:after {
                background: $--color-text-muted-2;
                bottom: -10px;
                content: '';
                height: 1px;
                left: 0;
                position: absolute;
                width: 100%;
            }
        }

        &__options {
            a {
                transition: color .25s ease-in-out;

                &:hover, &:active {
                    color: $--color-primary;
                }

                &:first-of-type {
                    margin-right: 40px;
                }
            }
        }
    }
</style>