<?php

class mw_helper {

  static function wfBaseConvert($input, $sourceBase, $destBase, $pad = 1, $lowercase = true, $engine = 'auto'
  ) {
    $input = (string) $input;
    if (
      $sourceBase < 2 ||
      $sourceBase > 36 ||
      $destBase < 2 ||
      $destBase > 36 ||
      $sourceBase != (int) $sourceBase ||
      $destBase != (int) $destBase ||
      $pad != (int) $pad ||
      !preg_match(
        "/^[" . substr('0123456789abcdefghijklmnopqrstuvwxyz', 0, $sourceBase) . "]+$/i", $input
      )
    ) {
      return false;
    }

    static $baseChars = array(
      10 => 'a', 11 => 'b', 12 => 'c', 13 => 'd', 14 => 'e', 15 => 'f',
      16 => 'g', 17 => 'h', 18 => 'i', 19 => 'j', 20 => 'k', 21 => 'l',
      22 => 'm', 23 => 'n', 24 => 'o', 25 => 'p', 26 => 'q', 27 => 'r',
      28 => 's', 29 => 't', 30 => 'u', 31 => 'v', 32 => 'w', 33 => 'x',
      34 => 'y', 35 => 'z',
      '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5,
      '6' => 6, '7' => 7, '8' => 8, '9' => 9, 'a' => 10, 'b' => 11,
      'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17,
      'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23,
      'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29,
      'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35
    );

    if (extension_loaded('gmp') && ( $engine == 'auto' || $engine == 'gmp' )) {
      // Removing leading zeros works around broken base detection code in
      // some PHP versions (see <https://bugs.php.net/bug.php?id=50175> and
      // <https://bugs.php.net/bug.php?id=55398>).
      $result = gmp_strval(gmp_init(ltrim($input, '0') ? : '0', $sourceBase), $destBase);
    } elseif (extension_loaded('bcmath') && ( $engine == 'auto' || $engine == 'bcmath' )) {
      $decimal = '0';
      foreach (str_split(strtolower($input)) as $char) {
        $decimal = bcmul($decimal, $sourceBase);
        $decimal = bcadd($decimal, $baseChars[$char]);
      }

      // @codingStandardsIgnoreStart Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed
      for ($result = ''; bccomp($decimal, 0); $decimal = bcdiv($decimal, $destBase, 0)) {
        $result .= $baseChars[bcmod($decimal, $destBase)];
      }
      // @codingStandardsIgnoreEnd

      $result = strrev($result);
    } else {
      $inDigits = array();
      foreach (str_split(strtolower($input)) as $char) {
        $inDigits[] = $baseChars[$char];
      }

      // Iterate over the input, modulo-ing out an output digit
      // at a time until input is gone.
      $result = '';
      while ($inDigits) {
        $work = 0;
        $workDigits = array();

        // Long division...
        foreach ($inDigits as $digit) {
          $work *= $sourceBase;
          $work += $digit;

          if ($workDigits || $work >= $destBase) {
            $workDigits[] = (int) ( $work / $destBase );
          }
          $work %= $destBase;
        }

        // All that division leaves us with a remainder,
        // which is conveniently our next output digit.
        $result .= $baseChars[$work];

        // And we continue!
        $inDigits = $workDigits;
      }

      $result = strrev($result);
    }

    if (!$lowercase) {
      $result = strtoupper($result);
    }

    return str_pad($result, $pad, '0', STR_PAD_LEFT);
  }
  
  public static function wfRandom() {
      # The maximum random value is "only" 2^31-1, so get two random
      # values to reduce the chance of dupes
      $max = mt_getrandmax() + 1;
      $rand = number_format( ( mt_rand() * $max + mt_rand() ) / $max / $max, 12, '.', '' );

	return $rand;
}

}
