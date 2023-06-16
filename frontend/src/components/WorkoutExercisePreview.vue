<template>
    <div class="exercise">
       <div class="exercise__thumbnail">
           <img :src="loadImage(data)"/>
       </div>
       <div class="exercise__description">
           <div class="exercise__title">
                <slot name="title">Unknown</slot>
            </div>
           <div class="exercise__subtitle">
                <slot name="subtitle">No data</slot>
            </div>
       </div>
       <div class="exercise__arrowhead">
           <font-awesome-icon icon="fa-solid fa-angle-up" rotation="90"/>
       </div>
       <button class="exercise__delete" v-if="props.editMode" :disabled="props.isLoading">
            Remove
       </button>
    </div>
</template>

<script setup>
    const props = defineProps({
        data: { type: Array, required: true, default: [] },
        editMode: { type: Boolean, required: false, default: false },
        isLoading: { type: Boolean, required: false, default: false }
    });

    function loadImage(data) {
        let imageName;
        try {
            imageName = `${data[0]}/${data[1].replace(/ /g, "-")}`.toLowerCase();

            if (!imageExists(imageName)) {
                throw "Image not found";
            }
        }
        catch(e) {
            // console.log(e);
            imageName = 'missing';
        }
        return new URL("../assets/images/exercises/" + imageName + ".jpg", import.meta.url).href;
    }

    function imageExists(image_url){
        let http = new XMLHttpRequest();
        http.open('HEAD', new URL("../assets/images/exercises/" + image_url + ".jpg", import.meta.url).href, false);
        http.send();
        return http.status != 404;
    }
</script>

<style lang="scss" scoped>
.exercise {
    background: var(--color-overlay);
    border-radius: 10px;
    box-shadow: 1px 1px 0px var(--color-secondary);
    cursor: pointer;
    display: flex;
    margin: 5px 0px;

    &__delete {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(0,0,0, 0.8);
        color: var(--color-text);
        outline: none;
        border: none;
        border-radius: 5px 10px 10px 5px;
        font-weight: bold;
        font-size: 1.6rem;
        transition: all 0.25s ease-in-out;
        text-transform: uppercase;
        cursor: pointer;

        &:disabled {
            color: rgba(255,255,255,0.5);
            letter-spacing: 3px;
            cursor: wait;

            &:after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 20px;
                height: 20px;
                border-radius: 50%;
                border: 2px solid white;
                border-top-color: transparent;
                animation: spin 1s linear infinite;
            }
        }
    }

    &__thumbnail {
        height: 65px;

        img {
            border-bottom-left-radius: 10px;
            border-top-left-radius: 10px;
            height: 100%;
            width: 75px;
        }
    }

    &__description {
        padding: 1rem 2rem;
        flex: 1;
        overflow: hidden;
    }

    &__title {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    &__subtitle {
        color: var(--color-text-muted-2);
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
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}
</style>