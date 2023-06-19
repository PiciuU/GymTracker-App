<template>
    <div class="card">
        <router-link :to="'/' + path">
            <div class="card__figure" :style="backgroundImage()">
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
        image: { type: String, required: true, default: 'missing' }
    });

    const backgroundImage = () => {
        return {
            'background-image': `url(${import.meta.env.BASE_URL}images/${props.image}.jpg)`
        }
    };
</script>

<style lang="scss" scoped>
.card {
    border: 5px solid $--color-overlay;
    max-width: 400px;
    overflow: hidden;
    width: 100%;

    &__figure {
        align-items: center;
        background-blend-mode: multiply;
        background-color: grey;
        background-position: center;
        background-size: cover;
        display: flex;
        justify-content: center;
        height: 100%;
        min-height: 150px;
        transition: all 0.25s ease-in-out;
    }


    &__title {
        font-size: 2.5rem;
        font-weight: bold;
        letter-spacing: 10px;
        margin-right: -10px; /* Avoid letter spacing gap for last character */
        text-align: center;
        text-transform: uppercase;
        transition: all .25s ease-in-out;
    }

    &:hover &__figure {
        transform: scale(1.25);
    }

    &:hover &__title {
        font-size: 2rem;
        letter-spacing: 16px;
        margin-right: -16px; /* Avoid letter spacing gap for last character */
    }
}

@media screen and (min-width: $--breakpoint-large-devices) {
    .card {
        height: 250px;
        flex-basis: 33.33%;
    }
}
</style>