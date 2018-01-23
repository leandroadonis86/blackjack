/*jshint esversion: 6 */

class BlackjackGame {
    constructor(ID, creatorName, maxNumPlayers) {
        this.gameID = ID;
        this.gameEnded = false;
        this.gameStarted = false;
        this.gameReplay = false;
        this.gameDate = function() {
            var now = new Date(); 
            return now.toDateString() + " " +
                now.toLocaleTimeString(); }();
        this.joinedPlayers = 1;
        this.maxPlayers = maxNumPlayers;
        this.playersList = [];
        this.playersList[0] = creatorName;
        this.playersPoints = [];
        this.playersHand = [[],[],[],[]];
        this.playersStatus = []; // W-Winner, P-Push, B-Bust, R-Request, ''-Pending , L-Lose, S-Waiting
        this.lastPlayer='';
        this.bots = 0;
        this.winner = 0;
        this.countdown = 20;
        this.stage = 2;
        this.requests = 0;
        this.deck = [];
        this.deckIndex = 0;
        this.initGame();
    }

    initGame() { //ok
        var i=this.maxPlayers;
        while(i--) {
            this.playersPoints.push(0);
            this.playersStatus.push('S');
        }
        this.createDeck();
    }

    join(playerName){ //ok
        this.playersList.push(playerName);
        this.joinedPlayers++;
        this.gameStarted = this.checkStart();
        if(this.gameStarted) {
            //começa a contar o tempo
            this.tik();
            this.redistribute();
            var i=this.maxPlayers;
            while(i--) {
                this.playersStatus[i]='R';
            }
            this.redistribute();
            this.botplay();
        }
    }

    createDeck() { //ok
        var types = ['c','e','o','p'];
        var t=types.length, n;
        while(t--) {
            n=13;
            while(n--) {
                this.deck.push(types[t] + (n+1));
            }
        }

        this.suffleDeck();
    }

    suffleDeck() { //ok
        var min=1, max=51, i=51;
        var rand, card;

        while(i--) {
            rand = Math.floor(Math.random() * (max - min + 1)) + min;
            card = this.deck[i];
            this.deck[i] = this.deck[rand];
            this.deck[rand] = card;
        }
    }

    hasHandBust(value) { //ok
        return (value>21) ? true : false;
    }

    hasPlayerBust(id) { //ok
        return this.hasHandBust(this.playersPoints[id]);
    }

    hasPlayerPush(ida,idb) { //ok
        return (this.playersPoints[ida] == this.playersPoints[idb]);
    }

    checkStart() { //ok
        return (this.maxPlayers==this.joinedPlayers);
    }

    updatePoints() { //ok
        var i;
        for(i=0; i<this.maxPlayers; i++) {
                this.playersPoints[i]=this.checkHandPoints(this.playersHand[i]);
        }
    }

    checkHandPoints(hand) { //ok
        var i, total = 0;
        for(i=0;i<hand.length;i++) {
            total = total+this.checkCardPoints(hand[i]);
        }
        return total;
    }

    checkCardPoints(card) { //ok
        var id = card.substr(1, 2);
        if (id==1)
            return 11;
        if (id>1 && id <=10)
            return parseInt(id);
        if (id>10)
            return 10;
    }

    closeDeal() { //ok
        var i;
        for(i=0;i<this.maxPlayers;i++) {
            if(this.playersStatus[i]=='')
                this.playersStatus[i] = 'D';
        }
    }

    getMaxPoints() { //ok
        var i, max=0;
        for(i=0;i<this.maxPlayers;i++) {
            if(this.playersPoints[i]<=21) {
                if(max<this.playersPoints[i])
                    max=this.playersPoints[i];
            }
        }
        return max;
    }

    getPlayerMaxPoints() { //ok
        return this.playersPoints.indexOf(this.getMaxPoints());
    }

    checkGameEnded(){
        this.updatePoints();
        // verificar o maior numero de pontos no jogo
        var max = this.getMaxPoints();
        // se todos excederem os pontos max=0
        if(!max) {
            // se max=0 então todos foram bust
            var i;
            for(i=0;i<this.maxPlayers;i++) {
                this.playersStatus[i] = 'B';
            }
            this.winner = -1;
            this.gameEnded = true;
            return true;
        }
        // verifica os que excederam os pontos
        var i, c=0;
        for(i=0;i<this.maxPlayers;i++) {
            if(this.playersStatus[i]!="B") {
                if(this.hasPlayerBust(i)) {
                    this.playersStatus[i]="B";
                    c++;
                }
            }
        }
        //se todos tiverem bust e houver um jogador disponivel
        if (c==this.maxPlayers-1) {
            this.stage=0;
        }        
        // se ja tiverem fechado a jogada e houver jogadores com bust
        c=0;
        for(i=0;i<this.maxPlayers;i++) {
            if(this.playersStatus[i]!="") {
                c++;
            }
        }
        if (c==this.maxPlayers) {
            this.stage=0;
        }
        if(!this.stage) {
            // verifica jogador com o mesmo numero maximo de pontos
            var i, j, val=0;
            for(i=0;i<this.maxPlayers;i++) {
                for(j=i+1;j<this.maxPlayers;j++) {
                    if(this.hasPlayerPush(i,j)) {
                        if(this.playersPoints[i]==max) {
                            this.playersStatus[i] = 'P';
                            this.playersStatus[j] = 'P';
                            this.winner = 0;
                            this.gameEnded = true;
                            // os que não fizeram Push perdem
                            for(i=0;i<this.maxPlayers;i++) {
                                if(this.playersStatus[i]!="P") {
                                    this.playersStatus[i] = 'L';
                                }
                            }
                            return true;
                        }
                    }
                }
            }
            //verifica o jogador unico com o maior numero de pontos
            this.winner = this.getPlayerMaxPoints()+1;
            this.playersStatus[this.winner-1]="W";
            for(i=0;i<this.maxPlayers;i++) {
                if(this.playersStatus[i]!="W" && this.playersStatus[i]!="B") {
                    this.playersStatus[i]="L";
                }
            }
            this.gameEnded = true;
            return true;
        }
        return false
    }

    getCard() { //ok
        return this.deck[this.deckIndex++];
    }

    request(playerNumber) { //ok
        if (!this.gameStarted || this.gameEnded) {
            return false;
        }

        // verifica se o jogador é valido par apedir carta extra
        if(this.playersStatus[playerNumber-1]=='') {
            this.playersStatus[playerNumber-1]='R';
            this.lastPlayer=this.playersList[playerNumber-1];
        }

        // verificar se já não existe ninguem pendente
        var i, count=0;
        for(i=0;i<this.maxPlayers;i++) {
            if(this.playersStatus[i]!='')
                count++;
        }
        if(count==this.maxPlayers) {
            this.redistribute();
            this.stage--;
            this.countdown=20;
            this.checkGameEnded();         
        }
        

        return true;        
    }

    redistribute() { //ok..
        var i;
        for(i=this.maxPlayers-1;i>=0;i--) {
            if(this.playersStatus[i]=='R' || this.playersStatus[i]=='S') {
                this.playersHand[i].push(this.getCard());
                this.playersStatus[i]='';
            }
        }
        console.log(this.playersStatus);
        console.log(this.playersHand);
    }

    tik() {
        var intv;
        intv = setInterval(()=> {
            if(this.gameStarted) {
                if(this.stage>0) {
                    if(!this.countdown) {
                        this.stage--;
                        this.botplay();
                        this.closeDeal();
                        this.redistribute();
                        this.checkGameEnded();
                    }
                    (this.countdown)? this.countdown-- : this.countdown=20;
                } else {
                    this.gameEnded = true;
                    clearInterval(this.intv);
                }
            }
        },1000);
    }

    addbot() { //ok
        this.bots++;
        this.join("Bot-"+this.bots);
    }

    botplay() { //ok
        var i;
        for(i=0; i<this.maxPlayers;i++) {
            if(this.playersList[i].split("-")[0]=="Bot") {
                if( this.ia(i) * 100 >= 100) {
                    this.request(i+1); //+1 porque é player number
                }
            }
        }
    }

    ia(id) { //ok
        // quantos pontos precisa na mao
        var resto = (21-this.checkHandPoints(this.playersHand[id]));
        // vars para probalidade
        var ases = 4, faces = 16, valst = 27, tdeck = 52;
        var vals_dois = 4, vals_tres = 4, vals_quatro = 4, vals_cinco = 4,
            vals_seis = 4, vals_sete = 4, vals_oito = 4, vals_nove = 4, vals_dez = 4;
        // retirar a probalidade das que estão visiveis e ja sairam
        var a, b, id, card;
        for(a=0; a<this.maxPlayers; a++) {
            for(b=0; b<this.playersHand[a].length; b++) {
                card = this.playersHand[a][b];
                id = card.substr(1, 2);
                if (id==1) ases--;
                if (id==2) vals_dois--;
                if (id==3) vals_tres--;
                if (id==4) vals_quatro--;
                if (id==5) vals_cinco--;
                if (id==6) vals_seis--;
                if (id==7) vals_sete--;
                if (id==8) vals_oito--;
                if (id==9) vals_nove--;
                if (id==10) vals_dez--;
                if (id>10) faces--;
                tdeck--;
                if (a!=id) { break; } //só ve 1a carta do adversario
            }
        }
        
        valst= vals_dois+vals_tres+vals_quatro+vals_cinco+vals_seis+
                vals_sete+vals_oito+vals_nove+vals_dez;

        // calc probalidades percentagem
        var cas = ases / tdeck; // =11
        var cface = faces / tdeck; // =10
        var cval = valst / tdeck; // 2>= <=10

        // >=2 <=10
        var r;
        if(resto>=2 && resto<=10) {
            cval=0;
            for(r=2;r<=resto;r++) {
                switch(r) {
                    case 2:
                        cval += vals_dois / tdeck; 
                        break;
                    case 3:
                        cval += vals_dois / tdeck; 
                        break;
                    case 4:
                        cval += vals_dois / tdeck; 
                        break;
                    case 5:
                        cval += vals_dois / tdeck; 
                        break;
                    case 6:
                        cval += vals_dois / tdeck; 
                        break;
                    case 7:
                        cval += vals_dois / tdeck; 
                        break;
                    case 8:
                        cval += vals_dois / tdeck; 
                        break;
                    case 9:
                        cval += vals_dois / tdeck; 
                        break;
                    case 10:
                        cval += vals_dois / tdeck; 
                        break;
                    default:
                        cval = 0;
                }
            }
        }

        if(resto>=11) {
            return (ases+faces+valst)/tdeck;
        } else if(resto==10) {
            return (faces+valst)/tdeck;
        } else {
            return (cval)/tdeck;
        }

        return 0;
    }


}

module.exports = BlackjackGame;
