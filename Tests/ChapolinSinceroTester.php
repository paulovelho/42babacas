<?php

  include(getSitePath()."Services/ChapolinSinceroService.php");

  class TestChapolin extends UnitTestCase {

    function setUp(){
    }
    function tearDown(){
    }

    function testIsAllCaps() {
      $tweet1 = "GENTE..É VERDADE QUE NA LINGUAGEM DA INTERNETE QDO A GENTE ESCREVE EM MAISCULO É PORQUE TA BRAVA?..EU SÓ ESCREVO EM MAISCULA.POQUE SOU MIOPE";
      $analyze = ChapolinSinceroService::IsAllCaps($tweet1);
      $this->assertTrue($analyze);
      $tweet2 = "nosso torcedor fazendo figa em escanteio cobrado contra a gente MEU FLAMENGO ESTÁ MORTO EXISTE APENAS MEDO E SOLIDÃO";
      $analyze = ChapolinSinceroService::IsAllCaps($tweet2);
      $this->assertFalse($analyze);
    }

    function testNormalize() {
      $normalTweet = "Sua mãe é tão porca que o peido de suvaco que ela dava pra alegrar a criançada fedia mais que o normal";
      $tweet = ChapolinSinceroService::NormalizeTweet($normalTweet);
      $this->assertEqual($tweet, $normalTweet);
      $sosTweet = "SUA A MÃE É DAQUELAS QUE VOCE TÁ JOGANDO STREET FIGHTER COM O RYU ELA PASSA E FALA `ÓI QUE JAPONEISÃO BONITO´.";
      $sosNormalTweet = "Sua a mãe é daquelas que voce tá jogando street fighter com o ryu ela passa e fala `ói que japoneisão bonito´.";
      $tweet = ChapolinSinceroService::NormalizeTweet($sosTweet);
      $this->assertEqual($tweet, $sosNormalTweet);
    }

    function testRemovePlurals() {
      $tweet = "Dona Florinda conquistou o Girafales com café e n nudes";
      $finalTweet = "Dona Florinda conquistou o Girafale com café e n nude";
      $this->assertEqual(
        ChapolinSinceroService::RemovePlurals($tweet),
        $finalTweet
      );
    }

    function testRemovePunctuation() {
      $tweet = "o abacate/avocado é uma fruta muito chata pq vc compra e quase nunca tem maduro, daí tem que esperar pra comer, vc passa um dia e não tá bom, no outro também não, no outro, no outro, no outro aí vc já fica ah velho vsf tb daí vc se distrai fica maduro de vez e vc perde fruta bbk";
      $cleanTweet = "o abacateavocado é uma fruta muito chata pq vc compra e quase nunca tem maduro daí tem que esperar pra comer vc passa um dia e não tá bom no outro também não no outro no outro no outro aí vc já fica ah velho vsf tb daí vc se distrai fica maduro de vez e vc perde fruta bbk";
      $this->assertEqual(
        ChapolinSinceroService::RemovePunctuation($tweet),
        $cleanTweet
      );
    }

    function testRemoveAccents() {
      $tweet = "o pau mole é o não tirar o chapéu do grande programa do raul gil que é a sensualidade";
      $cleanTweet = "o pau mole e o nao tirar o chapeu do grande programa do raul gil que e a sensualidade";
      $this->assertEqual(
        ChapolinSinceroService::RemoveAccents($tweet),
        $cleanTweet
      );
    }

    function testGetWords() {
      $tweet = "FINALMENTE SEXTA FEIRA: BEBEDEIRA X BEICON PROSTITUTAS FOGUETES CIGARRO ARMAS DE FOGO E RINHA DE GALO. SÓ NÃO VALE CONTRAIR AIDS. VÁLIDO APÓS O HORÁRIO COMERCIAL. BOM DIA.";
      $twWords = ChapolinSinceroService::GetWords($tweet);
      $words = array(
        "finalmente", "sexta", "feira", "bebedeira", "beicon", "prostituta",
        "foguete", "cigarro", "arma", "fogo", "rinha", "galo", "nao", "vale",
        "contrair", "aid", "valido", "apo", "horario", "comercial", "bom", "dia"
      );
      $this->assertEqual($twWords, $words);
    }

  }

?>
