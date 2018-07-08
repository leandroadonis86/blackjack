<template>
    <div>
        <div class="jumbotron">
            <h2>General Statistics</h2>
            <br>
            Total players in platform: {{ generalStats.usercount }} <br>
            Total games played in platform: {{ generalStats.totalplayedgames }}
            <br>
            <table class="gstattab">
                <tr><td class="gstattd">
                    Top 5 players with more games:
                    <table class="gstabin">
                        <tr v-for="datamg in generalStats.top5maxgames">
                            <td>{{ datamg.nickname }}</td><td>{{ datamg.total_games_played }}</td>
                        </tr>
                    </table></td>
                    <td class="gstattd">
                    Top 5 players with more points:
                    <table class="gstabin">
                        <tr v-for="datamp in generalStats.top5maxpoints">
                            <td>{{ datamp.nickname }}</td><td>{{ datamp.total_points }}</td>
                        </tr>
                    </table></td>
                    <td class="gstattd">
                    Top 5 players with good avarage:
                    <table class="gstabin">
                        <tr v-for="dataavg in generalStats.top5besavg">
                            <td>{{ dataavg.nickname }}</td><td>{{ dataavg.total_points }}</td>
                        </tr>
                    </table></td>
                </tr>
            </table>
        </div>
        <div class="jumbotron" v-if="autenticated">
            <h2>User Statistics</h2>
            <br>
            <!-- { "totalgamesplayed": [ { "total_games_played": 12 } ], "totalpoints": [ { "total_points": 450 } ], "totalwins": 0, "totalLoseorDie": 0, "totalpointsavg": 0 } -->
            Total games played by the user: {{ userStats.totalgamesplayed }} <br>
            Total games with victory: {{ userStats.totalwins }} <br>
            Total games die or lose: {{ userStats.totalLoseorDie }} <br>
            Total current points: {{ userStats.totalpoints }} <br>
            Total avarage points: {{ userStats.totalpointsavg }} <br>
        </div>
    </div>
</template>

<script>
    export default {
		data: function(){
			return {
                showFailure: false,
                failureMessage: '',
                showSuccess: false,
                successMessage: '',
                autenticated: this.$root.isAuthenticated,
                current: this.$root.own,
                generalStats: {},
                userStats: { 
                    totalgamesplayed: 0, 
                    totalwins: 0, 
                    totalLoseorDie: 0,
                    totalpoints: 0,
                    totalpointsavg: 0,
                }
			}
        },
        methods: {
            generalStat() {
                axios.get('api/statistics')
                    .then(response=> {
                        this.generalStats = response.data;
                        //console.log(response);
                    });
            },
            userStat() {
                axios.get('api/statistics/'+this.current.id)
                    .then(response=> {
                        this.userStats = response.data;
                    });
            }
        },
        mounted() {
            this.generalStat();
            if(this.$root.isAuthenticated)
                this.userStat();
        }
    }
</script>

<style>
.gstattab {
    width: 950px;
}

.gstattd {
    vertical-align: initial;
}

.gstabin {
    width: 145px;
}
</style>
