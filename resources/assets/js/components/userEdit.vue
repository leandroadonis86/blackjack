<template>
	<div class="jumbotron">
	    <h2>Edit User</h2>
	    <div class="form-group">
	        <label for="inputName">Name</label>
	        <input
	            type="text" class="form-control" v-model="user.name"
	            name="name" id="inputName" 
	            placeholder="Fullname"/>
	    </div>
	    <div class="form-group">
	        <label for="inputEmail">Email</label>
	        <input
	            type="text" class="form-control" v-model="user.email"
	            name="email" id="inputEmail"
	            placeholder="Email address"/>
		</div>
	    <div class="form-group">
	        <label for="inputNickname">Nickname</label>
	        <input
	            type="text" class="form-control" v-model="user.nickname"
	            name="nickname" id="inputNickname"
	            placeholder="Nickname"/>
		</div>
	    <div class="form-group" v-if="user.blocked">
				<label for="blockReason">Block Reason (if needed):</label>
				<input
					type="text" class="form-control" v-model="user.reason_blocked"
					name="blockr" id="blockReason"
					placeholder="Type reason"/>
		</div>
		<div class="form-group" v-if="!user.blocked">
				<label for="unblockReason">Unblock Reason (if needed):</label>
				<input
					type="text" class="form-control" v-model="user.reason_reactivated"
					name="unblockr" id="unblockReason"
					placeholder="Type reason"/>
		</div>
	    <div class="form-group">
	        <a class="btn btn-default" v-on:click.prevent="saveUser()">Save</a>
	        <a class="btn btn-default" v-on:click.prevent="cancelEdit()">Cancel</a>
	    </div>
	</div>
</template>

<script type="text/javascript">
	module.exports={
		props: ['user'],
	    methods: {
	        saveUser: function(){
				axios.put('api/users/'+this.user.id, this.user)
	                .then(response=> {
	                	// Copy object properties from response.data.data to this.user
	                	// without creating a new reference
	                	Object.assign(this.user, response.data.data);
	                	this.$emit('user-saved', this.user);
	                });
	        },
	        cancelEdit: function(){
	        	axios.get('api/users/'+this.user.id)
	                .then(response=>{
	                	// Copy object properties from response.data.data to this.user
	                	// without creating a new reference
	                	Object.assign(this.user, response.data.data);
	                	this.$emit('user-canceled', this.user);
	                });
	        }
		}
	}
</script>

<style scoped>	

</style>