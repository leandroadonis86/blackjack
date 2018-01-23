<template>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>ID</th>
	            <th>Creator</th>
				<th>Time Created</th>
				<th>Joined Players</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr v-for="game in games"  :key="game.gameID">
	            <td>{{ game.gameID }}</td>
				<td>{{ game.playersList[0] }}</td>
				<td>{{ game.gameDate }}</td>
	            <td>{{ game.joinedPlayers }}</td>
	            <td>
	                <a class="btn btn-xs btn-primary" v-on:click.prevent="join(game)">Join</a>
	            </td>
	        </tr>
	    </tbody>
	</table>
</template>

<script type="text/javascript">
	// Component code (not registered)
	module.exports={
		props: ['games'],
        methods: {
            join(game) {
            	this.$emit('join-click', game);
            },
        },
	}
</script>

<style scoped>

</style>