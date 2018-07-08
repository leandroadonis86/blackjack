<template>
    <div>
        <div class="jumbotron">
            <h2>{{ title }}</h2>

            <div class="alert alert-success" v-if="showSuccess">
                    <button type="button" class="close-btn" v-on:click="showSuccess=false">&times;</button>
                    <strong>{{ successMessage }}</strong>
            </div>
            <div class="alert alert-danger" v-if="showFailure">
                <button type="button" class="close-btn" v-on:click="showFailure=false">&times;</button>
                <strong>{{ failureMessage }}</strong>
            </div>
            
            <div v-if="autenticated">

                <div class="form-group" v-if="isAdmin">
                        <label for="inputPlatformEmail">Platform email (leave empty if do not want to change)</label>
                        <input
                            type="text" class="form-control"
                            name="platform" id="inputPlatformEmail"
                            placeholder="Platform email" v-model="platform" />
                </div>
                <div class="form-group" v-if="isAdmin">
                    <a class="btn btn-default" v-on:click.prevent="updatePlatform()">Update Email Platform</a>
                    &nbsp;&nbsp;&nbsp;This will update only platform email. 
                </div>

                <div class="form-group">
                    <label for="inputEmail">Email</label>
                    <input
                        type="text" class="form-control"
                        name="email" id="inputEmail" 
                        placeholder="Email" v-model="form.email"/>
                </div>
                <div class="form-group">
                        <label for="inputoldPassword">Old Password (leave empty if do not want to change)</label>
                        <input
                            type="password" class="form-control"
                            name="password" id="inputoldPassword"
                            placeholder="Old Password" v-model="form.oldpassword" />
                </div>
                <div class="form-group" v-if="isAdmin">
                    <a class="btn btn-default" v-on:click.prevent="resetPassword()">Reset Password</a>
                    &nbsp;&nbsp;&nbsp;This reset admin password to default: 'secret' 
                </div>
                <div class="form-group" v-if="form.oldpassword!=''">
                    <label for="inputPassword">New Password</label>
                    <input
                        type="password" class="form-control"
                        name="password" id="inputPassword"
                        placeholder="Password" v-model="form.password" />
                </div>
                <div class="form-group" v-if="form.oldpassword!=''">
                    <label for="inputConfPassword">Repeat New Password</label>
                    <input
                        type="password" class="form-control"
                        name="cpassword" id="inputConfPassword" 
                        placeholder="Confirm Password" v-model="form.confirmpass"/>
                </div>
                <div class="form-group">
                        <label for="inputPassword">User FullName</label>
                        <input
                            type="text" class="form-control"
                            name="name" id="inputFullName"
                            placeholder="User FullName" v-model="form.name"/>
                </div>
                <div class="form-group">
                        <label for="inputPassword">Game Nickname</label>
                        <input
                            type="text" class="form-control"
                            name="nickname" id="inputNickname"
                            placeholder="User nickname" v-model="form.nickname"/>
                </div>
                <div class="form-group">
                    <a class="btn btn-default" v-on:click.prevent="saveProfile()">Save</a>
                    <a class="btn btn-default"  v-on:click.prevent="deleteProfile()">Delete Account</a>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
	export default {
		data: function(){
			return { 
		        title: 'Account Managment',
                showFailure: false,
                failureMessage: '',
                showSuccess: false,
                successMessage: '',
                signup: false,
                platform: '',
                current: this.$root.own,
                autenticated: this.$root.isAuthenticated,
                isAdmin: this.$root.isAdmin,
                form: { name: this.$root.own.name, 
                        email: this.$root.own.email, 
                        oldpassword: '', 
                        confirmpass: '', 
                        password: '', 
                        nickname: this.$root.own.nickname
                }
			}
		},
	    methods: {
            deleteProfile: function() {
                axios.delete('api/users/'+this.current.id)
	                .then(response => {
                        this.showSuccess = true;
                        this.successMessage = 'User with nickname '+this.current.nickname+' deleted';
                        setTimeout(function() {
                            this.$root.isAuthenticated = false;
                            return redirect('/login');
                        },3000)
	                });
            },
            saveProfile: function() {
                this.showSuccess = false;
                this.showFailure = false;

                if(this.form.name!='') {
                    if(this.form.name.length<6) {
                        this.showFailure = true;
                        this.failureMessage = "The name is to short. min:6 chars";
                        return;
                    }
                } else {
                    this.form.name = this.current.name;
                }

                if(this.form.email!='') {
                    if(this.form.email!=this.current.email) {
                            axios.get('api/users/emailavailable', {id: this.current.id, email: this.form.email})
                            .then(response=> {
                                if(response.data) {
                                    this.showFailure = true;
                                    this.failureMessage = "The email specified already exists.";
                                    return;                                    
                                }
                            });
                    }
                } else {
                    this.form.email = this.current.email;
                }

                if(this.form.nickname!='') {
                    if(this.form.nickname!=this.current.nickname) {
                        axios.get('api/users/nicknameavailable', {id: this.current.id, nickname: this.form.nickname})
                        .then(response=> {
                            if(response.data) {
                                this.showFailure = true;
                                this.failureMessage = "The nickname specified already exists. Try another.";
                                return;                                    
                            }
                        });
                    }
                } else {
                    this.form.nickname = this.current.nickname;
                }

                if(this.form.oldpassword!='') {
                    if(this.form.oldpassword==this.current.password) {
                        if(this.form.confirmpass!=this.form.password) {
                            this.showFailure = true;
                            this.failureMessage = "The new Password and confirmed New Password doesn't match. Try again";
                            return;
                        } else {
                            if(this.form.confirmpass!="" || this.form.password!="") {
                                    if(this.form.confirmpass.length<6) {
                                        this.showFailure = true;
                                        this.failureMessage = "The password should have more then 6 characters.";
                                        return;
                                    }
                                } else {
                                    this.showFailure = true;
                                    this.failureMessage = "The new Password or confirmed New Password are empty. Try again";
                                    return;
                                }
                        }
                    } else {
                        this.showFailure = true;
                        this.failureMessage = "Old password does not match to your current password. Try again";
                        return;                      
                    }
                } else {
                    this.form.password = this.current.password;
                }

                //update
                console.log(this.form.name+" "+this.form.password+" "+this.form.email+" "+this.form.nickname);
	            axios.put('api/user/mng/'+this.current.id, { 
                    name: this.form.name,
                    password: this.form.password, 
                    email: this.form.email,
                    nickname: this.form.nickname
                    })
	                .then(response=> {
                        this.showSuccess = true;
                        this.successMessage = "User profile Updated.";
                        this.current.password = this.form.password;
	                });
            },
            resetPassword: function() {
                this.showSuccess = false;
                this.showFailure = false;
	            axios.put('api/user/adm/rst/pass/'+this.current.id, { 
                    name: this.current.name,
                    password: "secret",
                    email: this.current.email,
                    nickname: this.current.nickname
                    })
	                .then(response=> {
                        this.showSuccess = true;
                        this.successMessage = "Password Reset successfully.";
                        this.current.password = "secret";
	                });
            },
            updatePlatform: function() {
                this.showSuccess = false;
                this.showFailure = false;
                if(this.platform!='') {
                        axios.put('api/platform/mail/'+this.platform)
                        .then(response=> {
                            this.showSuccess = true;
                            this.successMessage = "Platform email successfully updated.";
                        });
                } else {
                    return;
                }


            }

        }
	}
</script>
