<?php

	SimpleTest::prefer(new TextReporter());

	class TestKibe extends UnitTestCase {

		function setUp(){
		}
		function tearDown(){
		}

		function testIfRejectsTweetsWithMentions() {
			$tweet = "estou legitimamente preocupado com a @AndreiaSadi. acho que ela não parou de trabalhar desde quarta.";
			$analyze = KibeController::CheckForMentions($tweet);
			$this->assertFalse($analyze);
		}


	}

?>
