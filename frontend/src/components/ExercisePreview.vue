<template>
    <div class="exercise">
       <div class="exercise__thumbnail">
           <img :src="setImage()" @error="setAltImage"/>
       </div>
       <div class="exercise__content">
           <div class="exercise__title">
                <p>
                    <slot name="title">Unknown</slot>
                </p>
            </div>
           <div class="exercise__subtitle">
                <p>
                    <slot name="subtitle">No data</slot>
                </p>
            </div>
       </div>
       <div class="exercise__arrowhead">
           <font-awesome-icon icon="angle-up" rotation="90"/>
       </div>
       <div class="exercise__delete" v-if="props.editMode">
           <button class="exercise__delete" :disabled="props.isLoading">
                Remove
           </button>
       </div>
    </div>
</template>

<script setup>
    import { useDataStore } from '@/stores/DataStore';

    const dataStore = useDataStore();

    const props = defineProps({
        exercise: { type: Object, required: true, default: {} },
        editMode: { type: Boolean, required: false, default: false },
        isLoading: { type: Boolean, required: false, default: false }
    });

    const setImage = () => {
        if (props.exercise.thumbnailUrl) return `${dataStore.getPath}/${props.exercise.id}/${props.exercise.thumbnailUrl}`;
        else return `${dataStore.getPath}/missing.jpg`;
    };

    const setAltImage = (event) => {
        event.target.src = `${dataStore.getPath}/missing.jpg`;
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/animations.scss';
    .exercise {
        background: $--color-overlay;
        border-radius: 10px;
        box-shadow: 1px 1px 0px $--color-secondary;
        cursor: pointer;
        display: flex;
        margin: 5px 0px;

        &__thumbnail {
            height: 65px;

            img {
                border-radius: 10px 0px 0px 10px;
                height: 100%;
                width: 75px;
            }
        }

        &__content {
            padding: 1rem 2rem;
            flex: 1;
            overflow: hidden;
        }

        &__title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        &__subtitle {
            color: $--color-text-muted-2;
            font-size: 1.2rem;
        }

        &__arrowhead {
            align-items: center;
            display: flex;
            justify-content: center;
            margin-left: auto;
            text-align: center;
            width: 50px;
        }

        &__delete {
            height: 100%;
            position: absolute;
            width: 100%;

            button {
                background: rgba(0, 0, 0, 0.75);
                border: none;
                border-radius: 5px 10px 10px 5px;
                color: $--color-text;
                cursor: pointer;
                font-weight: bold;
                font-size: 1.6rem;
                outline: none;
                text-transform: uppercase;
                transition: all .25s ease-in-out;

                &:disabled {
                    color: rgba(255 ,255, 255, 0.5);
                    cursor: wait;
                    letter-spacing: 3px;

                    &:after {
                        animation: spin 1s linear infinite;
                        border: 2px solid white;
                        border-radius: 50%;
                        border-top-color: transparent;
                        content: '';
                        height: 20px;
                        left: 50%;
                        position: absolute;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        width: 20px;
                    }
                }
            }
        }
    }
</style>