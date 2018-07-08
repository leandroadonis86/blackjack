<template>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>ID</th>
	            <th>Creator</th>
				<th>Time Created</th>
				<th>Joined Players</th>
				<th>Actions</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr v-for="game in games"  :key="game.gameID">
	            <td>{{ game.gameID }}</td>
				<td>{{ game.playersList[0] }}</td>
				<td>{{ game.gameDate }}</td>
	            <td>{{ game.joinedPlayers }}</td>
	            <td>
	                <a class="btn btn-xs btn-primary" v-on:click.prevent="join(game)" v-if="isAuthenticated">Join</a>
	            </td>
	        </tr>
	    </tbody>
	</table>
</template>

<script type="text/javascript">
	// Component code (not registered)
	module.exports={
		props: ['games'],
        data: function(){
			return {
                isAuthenticated: this.autenticated()
            }
        },
        methods: {
            join(game) {
            	this.$emit('join-click', game);
			},
			autenticated() {
				if(this.$root.isAuthenticated==null || this.$root.isAuthenticated==undefined) {
					return false;
				} else {
					return this.$root.isAuthenticated;
				}
			},
        },
	}
</script>

<style scoped>

</style>