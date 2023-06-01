<template>
    <div class="card">
        <router-link :to="'/' + path">
            <div class="card__figure" :style="backgroundStyles(image)">
                <div class="card__title">
                    <slot></slot>
                </div>
            </div>
        </router-link>
    </div>
</template>

<script setup>
    const props = defineProps({
        path: { type: String, required: true, default: '/' },
        image: { type: String, required: true, default: '' }
    });

    function backgroundStyles(imageName) {
        return {
            'background-image': `url(${new URL("../assets/images/" + imageName + ".jpg", import.meta.url).href})`,
        }
    }
</script>

<style lang="scss" scoped>
.card {
    background: var(--color-overlay);
    padding: .5rem;

    &__figure {
        align-items: center;
        background-blend-mode: multiply;
        background-color: grey;
        background-position: center;
        background-size: 100%;
        display: flex;
        height: 150px;
        justify-content: center;
        transition: all .25s ease-in-out;
    }


    &__title {
        color: var(--color-text);
        font-size: 2.5rem;
        font-weight: bold;
        letter-spacing: 1rem;
        margin-right: -1rem; /* Avoid letter spacing gap for last character */
        text-align: center;
        text-transform: uppercase;
        transition: all .25s ease-in-out;
    }

    &:hover &__figure {
        background-size: 125%;
    }

    &:hover &__title {
        font-size: 2rem;
        letter-spacing: 2rem;
        margin-right: -2rem; /* Avoid letter spacing gap for last character */
    }
}
</style>