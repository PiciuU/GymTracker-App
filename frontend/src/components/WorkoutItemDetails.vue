<template>
    <div class="modal">
        <div class="modal__close" @click="$emit('close')">
            <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
        </div>
        <div class="modal__header">
            <div class="modal__image">
                <div class="modal__title">
                    {{ item.name }}
                </div>
                <div class="modal__subtitle">
                    {{ item.sets }} series, {{ item.reps }} reps
                </div>
            </div>
        </div>
        <div class="modal__content">
            <div class="modal__group">
                <div class="modal__label">
                    Muscles Targeted:
                </div>
                <div class="modal__text">
                    {{ item.muscle_targeted }}
                </div>
            </div>
            <div class="modal__group">
                <div class="modal__label">
                    Demonstration:
                </div>
                <div class="modal__text">
                    <video class="modal__video" controls>
                        <source src="@/assets/videos/exercises/biceps/uginanie.mp4" type="video/mp4" loading="lazy"/>
                        Your browser does not support mp4 videos.
                    </video>
                </div>
            </div>
            <div class="modal__group">
                <div class="modal__label">
                    Personal Record:
                </div>
                <div class="modal__text">
                    {{ item.max_weight }} kg | {{ item.max_weight_date }}
                </div>
                <div class="modal__more">
                    View personal history
                </div>
            </div>
        </div>

        <div class="modal__footer">
            <div class="modal__button">
                View details
            </div>
        </div>
    </div>
</template>

<script setup>
    defineProps({
        item: { type: Object, required: true, default: '{}' }
    });
</script>

<style lang="scss" scoped>
.modal {
    display: flex;
    flex-direction: column;
    position: absolute;
    width: 100%;
    min-height: 100%;
    box-shadow: 2px 2px 1px var(--color-primary);
    background: var(--color-overlay);
    border-radius: 5px;
    padding: 2rem;

    &__close {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        background: var(--color-primary);
        border-radius: 50%;
        box-shadow: 0 0 15px rgb(0 0 0 / 30%);
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 1;

        svg {
            color: black;
        }
    }

    &__image {
        align-items: center;
        background-blend-mode: multiply;
        background-image: url("@/assets/images/exercises/biceps/lat-pulldown.jpg");
        background-color: rgb(95, 95, 95);
        background-position: center;
        background-size: 100%;
        display: flex;
        flex-direction: column;
        height: 100px;
        justify-content: center;
        transition: all .25s ease-in-out;
        border-radius: 15px;
        margin-bottom: 1rem;
    }

    &__subtitle {
        color: var(--color-text-muted-1);
    }

    &__group {
        margin-bottom: 10px;
    }

    &__title {
        font-size: 2.5rem;
        font-weight: bold;
        letter-spacing: 0.2rem;
    }

    &__text {
        font-size: 1.4rem;
        color: var(--color-text-muted-2);
    }

    &__more {
        margin-top: 5px;
        font-size: 1.2rem;
        color:var(--color-secondary);
        opacity: .75;

        &:hover {
            opacity: 1;
        }
    }

    &__video {
        margin-top: 5px;
        width: 100%;
        border-radius: 10px;
    }

    &__footer {
        margin-top: auto;
    }

    &__button {
        padding: 10px 20px;
        margin: 0rem 20px;
        background: var(--color-primary);
        color: var(--color-on-primary);
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
        box-shadow: 1px 1px 5px var(--color-background);
    }
}
</style>