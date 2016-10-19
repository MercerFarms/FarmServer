<!DOCTYPE html>
<?php
    include 'lib/MobileDetect.php';
    $detect = new Mobile_Detect();
?>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Mexican Train Scorecard</title>
    <script type="text/javascript">
        var cplayers = 0;
        function buildtable() {
            var txt = document.getElementById('txtNumber');
            cplayers = parseInt(txt.value);
			
			var spncount = document.getElementById('playercount');
			if (cplayers < 5) spncount.innerHTML="each player gets 15 dominoes";
			else if (cplayers == 5 || cplayers == 6) spncount.innerHTML="each player gets 12 dominoes";
			else if (cplayers == 7 || cplayers == 8) spncount.innerHTML="each player gets 10 dominoes";
			else if (cplayers == 9 || cplayers == 10) spncount.innerHTML="each player gets 8 dominoes";

            var card = '';
            var col = '';
            for (var j = 0; j <= cplayers; j++) {
                col = '<div style="float:left;padding:10px">';
                for (var i = 13; i >= -1; i--) {
                    var tindex = ((14 - i) * 10) + j;
                    if (j != 0) {
                        if (i == 13)
                            col = col + '<input id="name' + j + '" type="text" size="10" value="Player ' + j + '" tabindex="' + tindex + '"/>';
                        else if (i == -1)
                            col = col + '<input id="total' + j + '" type="text" size="3" readonly="readonly" tabindex="' + tindex + '"/>';
                        else
                            col = col + '<input id="score' + i + j + '" type="text" size="2" onchange="updatescores()" tabindex="' + tindex + '"/>';
                    }
                    else {
                        if (i == 13)
                            col = col + '<button onclick="commitnames()"  style="width:70px">commit</button>';
                        else if (i == -1)
                            col = col + '<div style="height:6px"><span style="color:white; font-size:larger;font-family:Verdana,Arial">total:</span></div>';
                        else
                            col = col + '<button onclick="commitscores(' + i + ')" style="width:70px">' + i + '</button>';
                    }
                    col = col + '<br/>';
                }
                col = col + '</div>';
                card = card + col;
            }
            card = card + '<div style="clear:both">';
            var scorecard = document.getElementById('scorecard');
            scorecard.innerHTML = card;
        }

        function updatescores() {
            var txt = document.getElementById('txtNumber');
            var lowestscore = 9999999;

            for (var j = 1; j <= cplayers; j++) {
                sum = 0;
                for (var i = 12; i >= 0; i--) {
                    var input = document.getElementById('score' + i + j);
                    if (input.value.length != 0)
                        sum = sum + parseInt(input.value);
                }
                var tot = document.getElementById('total' + j);
                tot.value = sum;
                if (sum < lowestscore)
                    lowestscore = sum;
            }

            for (var j = 1; j <= cplayers; j++) {
                var tot = document.getElementById('total' + j);
                if (parseInt(tot.value) == lowestscore)
                    tot.style.color = 'red';
                else
                    tot.style.color = 'black';
            }
        }

        function commitnames() {
            for (var j = 1; j <= cplayers; j++) {
                var input = document.getElementById('name' + j);
                if (input != null)
                    input.readOnly = 'readonly';
            }
        }

        function commitscores(i) {
            for (var j = 1; j <= cplayers; j++) {
                var input = document.getElementById('score' + i + j);
                if (input != null)
                    input.readOnly = 'readonly';
            }
        }
    </script>
</head>
<body style="background-color:#009245">
    <div style="text-align:center">
        <h1 style="text-align:center;color:white;font-family:Verdana,Arial">Scorecard for Mexican Train</h1>
    </div>
    <div style="text-align:center">
		<?php if (!$detect->isMobile()) { ?>
        <div style="float:left; padding:40px">
            <img src="http://www.learnplaywin.net/dominoes/dominoes-images/mexican-train.jpg" onclick="window.location='http://www.pagat.com/tile/wdom/mextrain.html'" style="cursor:pointer"/<br />
            <a href="http://www.pagat.com/tile/wdom/mextrain.html">rules</a>
        </div>
		<?php } ?>
        <div style="float:left;padding:40px">
            <p style="color:white; font-size:larger;font-family:Verdana,Arial">Number of players: <input id="txtNumber" style="font-family:Verdana,Arial" type="text" size="3" /> <button type="button" style="font-family:Verdana,Arial" onclick="buildtable()">begin</button></p>
			<div id="playercount" style="color:white; font-size:larger;font-family:Verdana,Arial;margin=10,0,10,0"></div>
            <div id="scorecard"></div>
        </div>
    </div>
</body>
</html>