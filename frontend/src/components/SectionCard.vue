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
    max-width: 400px;
    width: 100%;
    border: 5px solid $--color-overlay;
    overflow: hidden;

    &__figure {
        display: flex;
        align-items: center;
        justify-content: center;
        background-blend-mode: multiply;
        background-color: grey;
        background-position: center;
        background-size: cover;
        min-height: 150px;
        height: 100%;
        transition: all 0.25s ease-in-out;
    }


    &__title {
        font-size: 2.5rem;
        font-weight: bold;
        letter-spacing: 1rem;
        margin-right: -1rem; /* Avoid letter spacing gap for last character */
        text-align: center;
        text-transform: uppercase;
        transition: all .25s ease-in-out;
    }

    &:hover &__figure {
        transform: scale(1.25);
    }

    &:hover &__title {
        font-size: 2rem;
        letter-spacing: 1.5rem;
        margin-right: -1.5rem; /* Avoid letter spacing gap for last character */
    }
}

@media screen and (min-width: $--breakpoint-large-devices) {
    .card {
        height: 250px;
        flex-basis: 33.33%;
    }
}
</style>