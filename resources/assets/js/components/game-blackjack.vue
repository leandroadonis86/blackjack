<template>
	<div class="gameseparator">
        <div>
            <h2 class="text-center">Game {{ game.gameID }}</h2>
            <br>
        </div>
        <div class="game-zone-content">       
            <div class="alert" :class="alerttype">
                <strong>{{ message }} &nbsp;&nbsp;&nbsp;&nbsp;
                    <a v-show="!game.gameStarted" v-on:click.prevent="addBot">Add Bot</a>
                    <a v-show="game.gameEnded" v-on:click.prevent="replayGame" v-if="!game.gameReplay">Replay</a>
                    <a v-show="game.gameEnded" v-on:click.prevent="closeGame">Close Game</a></strong>
            </div>
            <h4>Time left: {{ game.countdown }}    
                Players: <span v-for="player of game.playersList"> {{ player }} </span>
            </h4>
            <div class="board">
                <table class="table">
                <tr v-for="(playerName, p) of game.playersList">
                        <td>{{ playerName }} <br> {{ status(p) }}</td>
                        <td>
                            <div v-for="(card, c) of game.playersHand[p]" >
                                    <img class="card" v-bind:src="cardImageURL(card)" v-if="(playerName == current) && !game.gameEnded">
                                    <img class="card" v-bind:src="cardImageURL(card)" v-if="(playerName != current) && (c==0) && !game.gameEnded">
                                    <img class="card" v-bind:src="cardImageURL('semFace')" v-if="(playerName !=  current) && (c>0) && !game.gameEnded">
                                    <img class="card" v-bind:src="cardImageURL(card)" v-if="game.gameEnded">                                   
                            </div>
                            <strong><a v-show="game.gameStarted" v-on:click="wantCard" v-if="!game.gameEnded && (playerName == current) && (game.playersStatus[p] == '')">Request</a></strong>
                        </td>
                </tr>
                </table>
            </div>
            <hr>
        </div>  
    </div>			
</template>

<script type="text/javascript">
	export default {
        props: ['game'],
        data: function(){
			return {
                replaying: false,
                scoreSubmited: false,
                current: this.$parent.currentPlayer,
                data: {
                    id: this.$root.own.id, 
                    nickname: this.$root.own.nickname, 
                    total_points: this.$root.own.total_points, 
                    total_games_played: this.$root.own.total_games_played
                    }
            }
        },
        computed: {
            ownPlayerNumber(){
                if (this.current == this.game.playersList[0]) {
                    return 1;
                } else if (this.current == this.game.playersList[1]) {
                    return 2;
                } else if (this.current == this.game.playersList[2]) {
                    return 3;
                } else if (this.current == this.game.playersList[3]) {
                    return 4;
                }
                return 0;
            },
            ownPlayerName(){
                var ownNumber = this.ownPlayerNumber;
                var i;
                for(i=0;i<this.game.maxPlayers;i++) {
                    if (ownNumber == (i+1))
                        return this.game.playersList[i];
                }
                return "Unknown";
            },
            adversaryPlayerName(){
                var ownNumber = this.ownPlayerNumber;
                var i;
                for(i=0;i<this.game.maxPlayers;i++) {
                    if (ownNumber == (i+1))
                        return this.game.lastPlayer;
                }
                return "Unknown";
            },
            message(){
                if (!this.game.gameStarted) {
                    return "Game has not started yet";
                } else if (this.game.gameEnded) {
                    if (this.game.winner == this.ownPlayerNumber) {
                        return "Game has ended. You Win.";
                    } else if (this.game.winner == -1) {
                        return "Game has ended with a bust.";
                    }  else if (this.game.winner == 0) {
                        return "Game has ended with a push.";
                    } 
                    return "Game has ended and " + this.game.playersList[this.game.winner-1] + " has won. You lost.";
                } else {
                    if(this.game.lastPlayer != '') {
                        return this.game.lastPlayer +" had request another card";
                    } else {
                        return "Game has started";
                    }
                }
                return "Game is inconsistent";
            },
            alerttype(){
                if (!this.game.gameStarted) {
                    return "alert-warning";
                } else if (this.game.gameEnded) {
                    if (this.game.winner == this.ownPlayerNumber) {
                        return "alert-success";
                    } else if (this.game.winner == -1) {
                        return "alert-info";
                    } else if (this.game.winner == 0) {
                        return "alert-info";
                    } 
                    return "alert-danger";
                } else {
                    return "alert-info";
                }
                return "alert-danger";
            }
        },
        methods: {
            cardImageURL (cardid) {
                var imgSrc = String(cardid);
                return 'img/' + imgSrc + '.png';
            },
            addBot (){
                this.$parent.addbot(this.game);
            },
            closeGame () {
                this.$parent.close(this.game);
            },
            replayGame () {
                this.$parent.replay(this.game);
                this.replaying=true;
                this.refresh();
            },
            wantCard() {
                this.$parent.request(this.game);
            },
            status(id) {
                switch(this.game.playersStatus[id]) {
                    case 'P':
                     return "Push";
                    case 'W':
                     return "Winner";
                    case 'D':
                     return "Done";
                    case 'L':
                     return "Lose";
                    case 'R':
                     return "Requested";
                    case 'B':
                     return "Bust";
                    case '':
                     return "Pending...";
                    case 'S':
                     return "Waiting..."
                    default:
                     return "Unknown";
                }
                return "Unknown";
            },
            updateScore() {
                if(!this.scoreSubmited) {
                    //console.dir(this.game.finalscore);
                    axios.put('api/user/score/'+this.data.id, {
                            nickname: this.data.nickname,
                            total_points: (this.data.total_points+this.game.finalscore[this.ownPlayerNumber-1]),
                            total_games_played: (this.data.total_games_played+1)
                        })
                        .then(response=> {
                            this.$root.own.total_points = (this.data.total_points+this.game.finalscore[this.ownPlayerNumber-1]);
                            this.$root.own.total_games_played = (this.data.total_games_played+1);
                        });
                    this.scoreSubmited = true;
                }
            },
            refresh() {
                if(!this.game.gameEnded || this.replaying) {
                    if(!this.game.gameReplay) {
                            this.replaying=false;
                    }
                    setTimeout(()=> {
                        this.$parent.update(this.game);
                        this.refresh();
                    }, 1000);
                } else {
                    this.updateScore();
                }
            }
        },
        mounted() {
            this.refresh();
        }

    }
</script>

<style scoped>	
.gameseparator{
    border-style: solid;
    border-width: 2px 0 0 0;
    border-color: black;
}
.card {
    width: 60px;
    height: 87px;
}
.board {
    max-width: 450px;
}
.board div {
    border-style: none;
}
.table {
    width: 400px;
}
</style>