@extends('master')

@section('title', 'Blackjack DAD')

@section('content')
    <router-link to="/login">Login</router-link> -
    <router-link to="/multiblackjack">Multiplayer Blackjack</router-link> - 
    <router-link to="/statistics">Statistics</router-link> -
    <router-link to="/account" v-if="isAuthenticated">User Account</router-link>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <div v-if="isAdmin">
        <span v-if="isAdmin">for Admins:</span>
        <router-link to="/account">Admin Account</router-link> -
        <router-link to="/users">User Management</router-link>
    </div>

    <router-view></router-view>
@endsection

@section('pagescript')
<script src="js/vueapp.js"></script>

@stop  
