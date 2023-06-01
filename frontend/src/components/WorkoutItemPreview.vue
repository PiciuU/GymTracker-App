<template>
    <div class="exercise-item">
       <div class="exercise-item__thumbnail">
           <img :src="loadImage(data)"/>
       </div>
       <div class="exercise-item__description">
           <div class="exercise-item__title">
                <slot name="title">Unknown</slot>
            </div>
           <div class="exercise-item__subtitle">
                <slot name="subtitle">No data</slot>
            </div>
       </div>
       <div class="exercise-item__arrowhead">
           <font-awesome-icon icon="fa-solid fa-angle-up" rotation="90"/>
       </div>
    </div>
</template>

<script setup>
    defineProps({
        data: { type: Array, required: true, default: [] }
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
            console.log(e);
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
.exercise-item {
    background: var(--color-overlay);
    border-radius: 10px;
    box-shadow: 1px 1px 0px var(--color-secondary);
    cursor: pointer;
    display: flex;
    margin: 5px 0px;

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
</style>