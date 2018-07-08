<template>
	<div>
        <div>
            <h3 class="text-center">{{ title }}</h3>
            <br>
            <h2>Current Player : {{ currentPlayer }}</h2>
            <!--
            <p>Set current player name <input v-model.trim="currentPlayer"></p>
            <p><em>Player name replaces authentication! Use different names on different browsers, and don't change it frequently.</em></p>
            -->
            <hr>
            <h3 class="text-center">Lobby</h3>
            <div v-if="this.$parent.isAuthenticated">
            <p>
                Max number of Players:
                <select v-model.trim="maxjoins">
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                </select>
                <button class="btn btn-xs btn-success" v-on:click.prevent="createGame">Create a New Game</button></p>
            </div>
            <hr>
            <h4>Pending games (<a @click.prevent="loadLobby">Refresh</a>)</h4>
            <lobby :games="lobbyGames" @join-click="join"></lobby>
            <template v-for="game in activeGames">
                <game :game="game"></game>
            </template>
        </div>
    </div>
</template>

<script type="text/javascript">
    import Lobby from './lobby.vue';
    import GameBlackjack from './game-blackjack.vue';

	export default {
        data: function(){
			return {
                title: 'Multiplayer Blackjack',
                currentPlayer: '',
                lobbyGames: [],
                activeGames: [],
                maxjoins: 2,
                socketId: "",
            }
        },
        sockets:{
            connect(){
                console.log('socket connected');
                this.socketId = this.$socket.id;
            },
            discconnect(){
                console.log('socket disconnected');
                this.socketId = "";
            },
            lobby_changed(){
                this.loadLobby();
            },
            my_active_games_changed(){
                this.loadActiveGames();
            },
            my_active_games(games){
                this.activeGames = games;
            },
            my_lobby_games(games){
                this.lobbyGames = games;
            },
            invalid_play(errorObject){
                if (errorObject.type == 'Invalid_Game') {
                    alert("Error: Game does not exist on the server");
                } else if (errorObject.type == 'Invalid_Player') {
                    alert("Error: Player not valid for this game");
                } else if (errorObject.type == 'Invalid_Play') {
                    alert("Error: Move is not valid or it's not your turn");
                } else {
                    alert("Error: " + errorObject.type);
                }
            },
            game_changed(game){
                for (var lobbyGame of this.lobbyGames) {
                        if (game.gameID == lobbyGame.gameID) {
                            Object.assign(lobbyGame, game);
                            break;
                        }
                }
                for (var activeGame of this. activeGames) {
                        if (game.gameID == activeGame.gameID) {
                            Object.assign(activeGame, game);
                            break;
                        }
                }
            },
        },        
        methods: {
            loadLobby(){
                this.$socket.emit('get_my_lobby_games');
            },
            loadActiveGames(){
                this.$socket.emit('get_my_activegames');
            },
            createGame(){
                if (this.currentPlayer == "") {
                    alert('Current Player is Empty - Cannot Create a Game');
                    return;
                }
                else {
                    this.$socket.emit('create_game', { playerName: this.currentPlayer, maxplayers: this.maxjoins });   
                }
            },
            replay(game) {
                this.$socket.emit('replay_game', {gameID: game.gameID });   
            },
            join(game){
                var i;
                for(i=0;i<game.maxPlayers;i++) {
                    if (game.playersList[i] == this.currentPlayer) {
                        alert('Failed. Cannot join a game because your name is the same as player in game!');
                        return;
                    }
                }
                this.$socket.emit('join_game', {gameID: game.gameID, playerName: this.currentPlayer });   
            },
            addbot(game){
                this.$socket.emit('addbot', {gameID: game.gameID });   
            },
            request(game){
                this.$socket.emit('request', {gameID: game.gameID });   
            },
            update(game){
                this.$socket.emit('update', {gameID: game.gameID });   
            },
            close(game){
                this.$socket.emit('remove_game', {gameID: game.gameID });   
            }
        },
        components: {
            'lobby': Lobby,
            'game': GameBlackjack,
        },
        mounted() {
            this.loadLobby();
            this.currentPlayer = this.$root.$data['own']['nickname'];
        }

    }
</script>

<style>	
    
</style>