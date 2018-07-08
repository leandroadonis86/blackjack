<template>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>Name</th>
				<th>Email</th>
				<th>Nickname</th>
				<th>Blocked</th>
				<th>Points</th>
	            <th>Games</th>
	            <th>Actions</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr v-for="user in users"  :key="user.id" :class="{activerow: editingUser === user}" v-if="(user.nickname != 'admin')">
	            <td>{{ user.name }}</td>
				<td>{{ user.email }}</td>
				<td>{{ user.nickname }}</td>
				<td>{{ user.blocked }}</td>
				<td>{{ user.total_points }}</td>
				<td>{{ user.total_games_played }}</td>
	            <td>
					<a class="btn btn-xs btn-success" v-on:click.prevent="definePlayer(user,1)">P1</a>
					<a class="btn btn-xs btn-primary" v-on:click.prevent="editUser(user)">Edit</a>
					<a class="btn btn-xs btn-primary" v-on:click.prevent="blockUser(user)">Block</a>
					<a class="btn btn-xs btn-primary" v-on:click.prevent="unblockUser(user)">Unblock</a>
	                <a class="btn btn-xs btn-danger" v-on:click.prevent="deleteUser(user)">Delete</a>
	            </td>
	        </tr>
	    </tbody>
	</table>
</template>

<script type="text/javascript">
	// Component code (not registered)
	module.exports={
		props: ['users'],
		data: function(){
			return { 
				editingUser: null
			}
		},
        methods: {
            editUser: function(user){
                this.editingUser = user;
                this.$emit('edit-click', user);
			},
            blockUser: function(user){
				this.$emit('blk-click', user);
			},	
            unblockUser: function(user){
				this.$emit('unblk-click', user);
            },			
            deleteUser: function(user){
                this.editingUser = null;
				this.$emit('delete-click', user);
			},
			definePlayer: function(user,player){
				this.$root.$data['own'] = user;
				this.$root.$data['playersList['+(player-1)+']'] = user.nickname;
				this.$emit('message', user.name+' selected as Player'+player);
			}
        },		
	}
</script>

<style scoped>
	tr.activerow {
  		background: #123456  !important;
  		color: #fff          !important;
}

</style>