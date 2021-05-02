

<?php

//Fetching data from Marriam
$page = file_get_contents('https://www.merriam-webster.com/word-of-the-day');
preg_match('#<h1>(.*?)</h1>#', $page, $wordoftheday); // Capture the Word of the Day
$wod = strtoupper(strip_tags($wordoftheday[0])); // Remove html tags

include_once 'dictionary.class.php';

$dictionary = new Dictionary("xxxxx", "xxxxxxxxxx", "en-gb");
$dictionary->newDictionaryRequest($wod );


//Build slack block code - Refer https://app.slack.com/block-kit-builder/

$url = 'https://hooks.slack.com/services/your-webhook-url';
$data = array (
'text' => "Word of ".date("d/m/Y"),
  'blocks' => 
  array (
    0 => 
    array (
      'type' => 'header',
      'text' => 
      array (
        'type' => 'plain_text',
        'text' => 'Word of the day - '.date("d/m/Y"),
        'emoji' => true,
      ),
    ),
    1 => 
    array (
      'type' => 'section',
      'text' => 
      array (
        'type' => 'mrkdwn',
        'text' => '*'.$wod.'*',
      ),
    ),
    2 => 
    array (
      'type' => 'section',
      'text' => 
      array (
        'type' => 'mrkdwn',
        'text' => '*Definition:* '.$dictionary->getDefinition(),
      ),
    ),
    3 => 
    array (
      'type' => 'section',
      'text' => 
      array (
        'type' => 'mrkdwn',
        'text' => '*Example:* '.$dictionary->getExample(0),
      ),
    ),
    
	4 => 
    array (
      'type' => 'divider',
    ),
  ),
);
$data = json_encode($data);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
echo var_dump($result);
if($result === false)
{
    echo 'Curl error: ' . curl_error($ch);
}
 
curl_close($ch);


//print_r($dictionary);
//$dictionary->setResult(0);

/*
echo "<h1>Dictionary Class Results - ".$dictionary->word."</h1> - status: ".$dictionary->errors['status'];
echo "Word: ".$dictionary->word;
echo "Definition: ".$dictionary->getDefinition();
echo "Short Definition: ".$dictionary->getShortDefinition();
echo "<br><b>Example:</b> ".$dictionary->getExample(0);
echo "Example 2 ".$dictionary->getExample(1);
echo "Lexical: ".$dictionary->getLexical();
echo "Phonetic: ".$dictionary->getPhonetic();
echo "Origin: ".$dictionary->getOrigin();
echo "Language: ".$dictionary->API_LANG;
//echo "Audio:</b> <audio controls><source src='".$dictionary->getAudio()."' type='audio/mpeg'>Your browser does not support HTML audio</audio><br>";

//echo "<br></br>Using result set: <b>".$dictionary->selected_result."</b>";
//echo "<br></br>Total result sets available from request: <b>".$dictionary->num_returned_results."</b>";
*/

?>
