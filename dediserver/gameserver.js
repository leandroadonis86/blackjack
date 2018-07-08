/*jshint esversion: 6 */

var app = require('http').createServer();
var io = require('socket.io')(app);

var BlackjackGame = require('./gamemodel.js');
var GameList = require('./gamelist.js');

app.listen(8080, function() {
	console.log('listening on *:8080');
});

// ------------------------
// Estrutura dados - server
// ------------------------

let games = new GameList();

io.on('connection', function (socket) {
    console.log('client has connected');

    socket.on('create_game', function (data){
    	let game = games.createGame(data.playerName, data.maxplayers, socket.id);
		socket.join(game.gameID);
		// Notifications to the client
		socket.emit('my_active_games_changed');
		io.emit('lobby_changed');
	});
	
    socket.on('replay_game', function (data){
		let game = games.gameByID(data.gameID);
		if (game === null) {
			socket.emit('invalid_play', {'type': 'Invalid_Game', 'game': null});
			return;
		}
		console.log("replaying game");
		game.replay();
		io.to(game.gameID).emit('game_changed', game);
    });

    socket.on('join_game', function (data){
    	let game = games.joinGame(data.gameID, data.playerName, socket.id);
		socket.join(game.gameID);
		io.to(game.gameID).emit('my_active_games_changed');
		io.emit('lobby_changed');
    });

    socket.on('remove_game', function (data){
    	let game = games.removeGame(data.gameID, socket.id);
    	socket.emit('my_active_games_changed');
    });

    socket.on('request', function (data){
		let game = games.gameByID(data.gameID);
		if (game === null) {
			socket.emit('invalid_play', {'type': 'Invalid_Game', 'game': null});
			return;
		}
		var numPlayer = 0;
		if (game.player1SocketID == socket.id) {
			numPlayer = 1;
		} else if (game.player2SocketID == socket.id) {
			numPlayer = 2;
		} else if (game.player3SocketID == socket.id) {
			numPlayer = 3;
		} else if (game.player4SocketID == socket.id) {
			numPlayer = 4;
		}
		if (numPlayer === 0) {
			socket.emit('invalid_play', {'type': 'Invalid_Player', 'game': game});
			return;
		}
		if (game.request(numPlayer)) {
			io.to(game.gameID).emit('game_changed', game);
		} else {
			socket.emit('invalid_play', {'type': 'Invalid_Play', 'game': game});
			return;
		}
	});
	
    socket.on('update', function (data){
		let game = games.gameByID(data.gameID);
		if (game === null) {
			socket.emit('invalid_play', {'type': 'Invalid_Game', 'game': null});
			return;
		}
		io.to(game.gameID).emit('game_changed', game);
	});
	
    socket.on('addbot', function (data){
		let game = games.gameByID(data.gameID);
		var numPlayer = 0;
		if (game.player1SocketID == socket.id) {
			numPlayer = 1;
		}
		if (!game.gameStarted) {
			game.addbot();
		}
		io.to(game.gameID).emit('game_changed', game);		
		console.log("Bot added");
	});

    socket.on('get_game', function (data){
		let game = games.gameByID(data.gameID);
		socket.emit('game_changed', game);
    });

    socket.on('get_my_activegames', function (data){
    	var my_games= games.getConnectedGamesOf(socket.id);
    	socket.emit('my_active_games', my_games);
    });

    socket.on('get_my_lobby_games', function (){
    	var my_games= games.getLobbyGamesOf(socket.id);
    	socket.emit('my_lobby_games', my_games);
    });

});
