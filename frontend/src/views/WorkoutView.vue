<template>
    <div class="container card__container">
        <VerticalCard path="workout/monday" image="overlay/workout/monday">
            Monday
        </VerticalCard>

        <VerticalCard path="workout/tuesday" image="overlay/workout/tuesday">
            Tuesday
        </VerticalCard>

        <VerticalCard path="workout/wednesday" image="overlay/workout/wednesday">
            Wednesday
        </VerticalCard>

        <VerticalCard path="workout/thursday" image="overlay/workout/thursday">
            Thursday
        </VerticalCard>

        <VerticalCard path="workout/friday" image="overlay/workout/friday">
            Friday
        </VerticalCard>
    </div>
</template>

<script setup>
    import VerticalCard from "@/components/VerticalCard.vue";
</script>


<style lang="scss" scoped>
:deep(.card__figure) {
    height: 100px;
}
</style>
