<template>
    <div class="container card__container">
        <VerticalCard path="workout" image="overlay/workout">
            Workout
        </VerticalCard>

        <VerticalCard path="exercises" image="overlay/exercises">
            Exercises
        </VerticalCard>

        <VerticalCard path="progress" image="overlay/progress">
            Progress
        </VerticalCard>
    </div>
</template>

<script setup>
    import VerticalCard from "@/components/VerticalCard.vue";
</script>