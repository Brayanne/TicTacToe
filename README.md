# TicTacToe
Slack Coding Exercise Tic Tac Toe Game
For this to work, the slash command token from the specific team has to be added on the code to ensure that request to server is coming from a trusted team. 
To start playing the game, the slash command token is added. Entering "/tic_tac_toe username" will ensure that player 1 will
be playing against "username". 
Player 1 will start the game and will enter the row letter followed by a column number. 
Anyone in the channel can enter "/tic_tac_toe info" to know who's turn is next and and to get the current board displayed.
If user enters a value that was already entered, the users will be notified and input will not be counted. 
When a user makes a winning move, the users will be notified that the last move was the winner and and that the game is over. If all possible moves are taken, users will be notified that there are no more possible moves and that the game has ended. Game will not allow any addtional moves once the game is over and will indicate the users to start over. 
There will only be one game per channel. 
It is important that when starting the game, the user starting the game enters a valid username for the opponent in order for the opponent to be able to play. 
Entering a "/tic_tac_toe username" where username is any username will restart the game at any point.
