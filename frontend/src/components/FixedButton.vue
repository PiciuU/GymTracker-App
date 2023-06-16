<template>
    <button class="action-button">
        <font-awesome-icon icon="fa-solid fa-plus" size="xl" fixed-width />
    </button>
</template>

<style lang="scss" scoped>
.action-button {
    position: fixed;
    right: 20px;
    bottom: 20px;
    width: 50px;
    height: 50px;
    background: var(--color-primary);
    border-radius: 50%;
    box-shadow: 0 0 15px rgb(0 0 0 / 30%);
    display: flex;
    outline: none;
    border: none;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: all 0.25s ease-in-out;

    svg {
        color: black;
        transition: all 0.25s ease-in-out;
    }

    &:hover {
        transform: scale(0.9);

        svg {
            transform: scale(1.25);
        }
    }
}
</style>