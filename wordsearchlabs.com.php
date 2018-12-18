<?php
/**
 * Solves a word search from https://wordsearchlabs.com/
 * Run script by providing word search id, e.g.: php wordsearchlabs.com.php 85694
 */

if (empty($argv[1])) {
	die('Please provide a work search number.  It is the number in the URL, e.g. 84790 for https://wordsearchlabs.com/view/84790');
}

$url = 'https://wordsearchlabs.com/view/' . $argv[1];
$urlContent = file_get_contents($url);

// get words to find
preg_match_all('/<td class=\'wordsearch-word-block\'>(.*)<\/td>/', $urlContent, $wordList);

// get letters in puzzle
preg_match_all('/<td id="g-(.*)"><span >(.*)<\/span><\/td>/', $urlContent, $letters);

// build word search multidimensional array
$wordsearch = [];
foreach ($letters[1] as $i => $letter) {
	list($row, $col) = explode('-', $letter);
	$wordsearch[$row][$col] = $letters[2][$i];
}

// print word search along with word list
echo PHP_EOL . 'Word Search Puzzle:' . PHP_EOL . PHP_EOL;
foreach ($wordsearch as $ws) {
	echo implode($ws) . PHP_EOL;
}
echo PHP_EOL . 'Word List:' . PHP_EOL . PHP_EOL;
foreach ($wordList[1] as $word) {
	echo $word . ' ';
}
echo PHP_EOL;

// find words
foreach ($wordList[1] as $word) {
	$word = strtoupper($word);
	$wordLength = strlen($word);
	$wordFound = false;
	
	// check horizontal
	for ($i = 0; $i < count($wordsearch); $i++) {
		for ($k = 0; $k < count($wordsearch[$i]) - $wordLength; $k++) {
			$section = implode(array_slice($wordsearch[$i], $k, $wordLength));
			
			if ($section === $word) {
				$wordFound = true;
				
				found($word, 'horizontal', $i + 1, $k + 1, $i + 1, $k + $wordLength);
			} elseif (strrev($section) === $word) {
				$wordFound = true;
				
				found($word, 'horizontal (reverse)', $i + 1, $k + $wordLength, $i + 1, $k + 1);
			}
		}
	}
	
	if ($wordFound) {
		continue;
	}
	
	// check vertical
	for ($i = 0; $i < count($wordsearch[0]); $i++) {
		for ($k = 0; $k < count($wordsearch) - $wordLength; $k++) {
			$letters = [];
			
			for ($n = 0; $n < $wordLength; $n++) {
				$letters[] = $wordsearch[$k + $n][$i];
			}
			
			$section = implode($letters);
			
			if ($section === $word) {
				$wordFound = true;
				
				found($word, 'vertical', $k + 1, $i + 1, $k + $wordLength, $i);
			} elseif (strrev($section) === $word) {
				$wordFound = true;
				
				found($word, 'vertical (reverse)', $k + $wordLength, $i + 1, $k + 1, $i + 1);
			}
		}
	}
	
	if ($wordFound) {
		continue;
	}
	
	// check diagonal
	for ($i = 0; $i < count($wordsearch); $i++) {
		for ($k = 0; $k < count($wordsearch[0]); $k++) {
			$letters = [];

			for ($n = 0; $n < $wordLength; $n++) {
				if (isset($wordsearch[$i+ $n][$k + $n])) {
					$letters[] = $wordsearch[$i + $n][$k + $n];
				}
			}

			if (count($letters) !== $wordLength) {
				break;
			}

			$section = implode($letters);

			if ($section === $word) {
				$wordFound = true;

				found($word, 'diagonal (down-right)', $i + 1, $k + 1, $i + $wordLength, $k + $wordLength);
			} elseif (strrev($section) === $word) {
				$wordFound = true;

				found($word, 'diagonal (up-left)', $i + $wordLength, $k + $wordLength, $i + 1, $k + 1);
			}
		}
	}
}

/**
 * Nicely prints out found word information
 *
 * @param string $word
 * @param string $direction
 * @param int $rowStart
 * @param int $colStart
 * @param int $rowEnd
 * @param int $colEnd
 */
function found(string $word, string $direction, int $rowStart, int $colStart, int $rowEnd, int $colEnd)
{
	echo PHP_EOL .
		'WORD: ' . strtoupper($word) . PHP_EOL .
		'DIRECTION: ' . strtoupper($direction) . PHP_EOL .
		'ROW START: ' . $rowStart . PHP_EOL .
		'COL START: ' . $colStart . PHP_EOL .
		'ROW END: ' . $rowEnd . PHP_EOL .
		'COL END: ' . $colEnd . PHP_EOL;
}
