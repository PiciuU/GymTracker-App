<template>
	<div class="container">
		<div class="container__error">Error 404</div>
        <div class="container__description">Oops, it seems that the requested page does not exist!</div>
        <button class="container__button" type="primary" @click="handleReturnButton">Go back to the previous page</button>
	</div>
</template>

<script>
export default {
	name: 'Error',
	setup() {
		function handleReturnButton() {
			history.back();
		}

		return { handleReturnButton };
	}
}
</script>

<style lang="scss" scoped>
	.container {
		align-items: center;
		display: flex;
        flex: 1;
		flex-direction: column;
		height: 100%;
		justify-content: center;
        text-align: center;
		width: 100%;

        &__error {
            font-size: 5rem;
            font-weight: bold;
        }

		&__description {
			font-size: 2rem;
			font-weight: bold;
			margin-top: 30px;
			padding: 0px 10px;
		}

		&__button {
            background-color: $--color-primary;
            border: none;
            cursor: pointer;
            font-size: 1.6rem;
            font-weight: bold;
			margin-top: 30px;
            padding: 10px 20px;
            transition: all .25s ease-in-out;
		}
	}

</style>