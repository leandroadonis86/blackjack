<template>
    <div>
		<div class="jumbotron">
            
            <h1>{{ title }}</h1>
            
            <div class="alert alert-success" v-if="showSuccess">
                    <button type="button" class="close-btn" v-on:click="showSuccess=false">&times;</button>
                    <strong>{{ successMessage }}</strong>
            </div>
            <div class="alert alert-danger" v-if="showFailure">
                <button type="button" class="close-btn" v-on:click="showFailure=false">&times;</button>
                <strong>{{ failureMessage }}</strong>
            </div>

            <h2 v-if="autenticated">User Autenticated. Welcome {{ current.nickname }}</h2>
            <div v-if="!autenticated">
                <h2 v-if="!signup">Login User</h2><h2 v-if="signup">Sign User</h2>
                <div class="form-group">
                    <label for="inputEmail">Email \ Username</label>
                    <input
                        type="text" class="form-control"
                        name="email" id="inputEmail" 
                        placeholder="Email" v-model="form.email"/>
                </div>
                <div class="form-group">
                    <label for="inputPassword">Password</label>
                    <input
                        type="password" class="form-control"
                        name="password" id="inputPassword"
                        placeholder="Password" v-model="form.password" />
                </div>
                <div v-if="signup">
                    <div class="form-group">
                        <label for="inputConfPassword">Repeat Password</label>
                        <input
                            type="password" class="form-control"
                            name="cpassword" id="inputConfPassword" 
                            placeholder="Confirm Password" v-model="confirmpass"/>
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
                </div>
                <div class="form-group">
                    <a class="btn btn-default" v-if="!signup" v-on:click.prevent="loginUser()">Login</a>
                    <a class="btn btn-default" v-if="signup" v-on:click.prevent="signinLogin()">Sign Up</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span v-if="!signup">For new users please sign up by clicking </span>
                    <span v-if="signup">Return to login by clicking </span>
                    <a v-on:click.prevent="changein()">here</a>.
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
	export default {
		data: function(){
			return { 
		        title: 'Welcome to Blackjack',
                showFailure: false,
                failureMessage: '',
                showSuccess: false,
                successMessage: '',
                signup: false,
                confirmpass: '',
                form: { name: null, email: null, password: null, nickname: null },
                current: this.$root.own,
                autenticated: this.$root.isAuthenticated
			}
		},
	    methods: {
            changein: function () {
                this.showSuccess = false;
                this.showFailure = false;
                this.signup = (this.signup) ? false : true;
            },
			loginUser: function(){
                this.showSuccess = false;
                this.showFailure = false;

                axios.post('api/user/login', this.form)
                    .then(response=> {
                        this.current = response.data;
                        this.checkLogin();                                                
                    });
            },
            checkLogin: function() {

                switch(this.current) {
                    case -1:
                        this.showFailure = true;
                        this.failureMessage = "Email or Username is not correct. New user? Try sign Up";
                        break;
                    case 0:
                        this.showFailure = true;
                        this.failureMessage = "Incorrect Password. Please check again";
                        break;
                    default:
                        this.current=this.current[0];
                        this.autenticated = true;
                        this.$root.isAuthenticated= true;
                        if(this.current.admin==1) {
                            this.$root.isAdmin = true;
                        }
                        this.$root.own= this.current;
                }
            },
            signinLogin: function() {
                this.showSuccess = false;
                this.showFailure = false;

                if(this.confirmpass!=this.form.password) {
                    this.showFailure = true;
                    this.failureMessage = "Password and Confirmed Password doesn't match. Try again";
                    return;                  
                }

	            axios.post('api/user/add', this.form)
	                .then(response=> {
                        this.current = response.data;
                        this.checkSign(); 
	                });
            },
            checkSign: function() {
                switch(this.current) {
                    case -1:
                        this.showFailure = true;
                        this.failureMessage = "Email is already in use.";
                        break;
                    case 1:
                        this.showFailure = true;
                        this.failureMessage = "Nickname is already in use";
                        break;
                    default:
                        this.showSuccess = true;
                        this.successMessage = "User has successfully registered. Try to Login.";
                        this.signup = false;
                }
            }
	    }

	}
</script>
