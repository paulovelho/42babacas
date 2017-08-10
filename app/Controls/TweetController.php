<?php

class TweetsController extends MagratheaController {

	public function Index() {
		$tweets = TweetsControl::GetLast(20);
		$this->assign("tweets", $tweets);
		$this->display("layouts/tweets.html");
	}

}

?>
