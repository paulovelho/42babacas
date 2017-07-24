<?php

	include(getSitePath()."Controls/KibeController.php");

	class TestKibe extends UnitTestCase {

		function setUp(){
		}
		function tearDown(){
		}

		function testIfRejectsTweetsWithMentions() {
			$tweet = "estou legitimamente preocupado com a @AndreiaSadi. acho que ela não parou de trabalhar desde quarta.";
			$analyze = KibeController::CheckForMentions($tweet);
			$this->assertTrue($analyze);
		}

		function testIfAcceptsTweetsWithoutMentions() {
			$tweet = "para qual celebridade ainda não perguntamos sobre as intenções de assumir a presidência?";
			$analyze = KibeController::CheckForMentions($tweet);
			$this->assertFalse($analyze);
			$tweet = "manda um vale-vamo pra @...";
			$analyze = KibeController::CheckForMentions($tweet);
			$this->assertFalse($analyze);
		}


	}

?>
