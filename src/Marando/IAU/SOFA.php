<?php

namespace Marando\IAU;

class SOFA {

  const D2PI   = 6.283185307179586476925287;
  const DAYSEC = 86400;

  /**
   *  - - - - - - - -
   *   i a u A 2 a f
   *  - - - - - - - -
   *
   *  Decompose radians into degrees, arcminutes, arcseconds, fraction.
   *
   *  This function is part of the International Astronomical Union's
   *  SOFA (Standards Of Fundamental Astronomy) software collection.
   *
   *  Status:  vector/matrix support function.
   *
   *  Given:
   *     ndp     int     resolution (Note 1)
   *     angle   double  angle in radians
   *
   *  Returned:
   *     sign    char    '+' or '-'
   *     idmsf   int[4]  degrees, arcminutes, arcseconds, fraction
   *
   *  Called:
   *     iauD2tf      decompose days to hms
   *
   *  Notes:
   *
   *  1) The argument ndp is interpreted as follows:
   *
   *     ndp         resolution
   *      :      ...0000 00 00
   *     -7         1000 00 00
   *     -6          100 00 00
   *     -5           10 00 00
   *     -4            1 00 00
   *     -3            0 10 00
   *     -2            0 01 00
   *     -1            0 00 10
   *      0            0 00 01
   *      1            0 00 00.1
   *      2            0 00 00.01
   *      3            0 00 00.001
   *      :            0 00 00.000...
   *
   *  2) The largest positive useful value for ndp is determined by the
   *     size of angle, the format of doubles on the target platform, and
   *     the risk of overflowing idmsf[3].  On a typical platform, for
   *     angle up to 2pi, the available floating-point precision might
   *     correspond to ndp=12.  However, the practical limit is typically
   *     ndp=9, set by the capacity of a 32-bit int, or ndp=4 if int is
   *     only 16 bits.
   *
   *  3) The absolute value of angle may exceed 2pi.  In cases where it
   *     does not, it is up to the caller to test for and handle the
   *     case where angle is very nearly 2pi and rounds up to 360 degrees,
   *     by testing for idmsf[0]=360 and setting idmsf[0-3] to zero.
   *
   *  This revision:  2013 June 18
   *
   *  SOFA release 2015-02-09
   *
   *  Copyright (C) 2015 IAU SOFA Board.  See notes at end.
   */
  public static function iauA2af($ndp, $angle, &$sign, array &$idmsf) {
    /* Hours to degrees * radians to turns */
    $F = 15.0 / static::D2PI;

    /* Scale then use days to h,m,s function. */
    static::iauD2tf($ndp, $angle * $F, $sign, $idmsf);

    return;
  }

  /**
   *  - - - - - - - -
   *   i a u D 2 t f
   *  - - - - - - - -
   *
   *  Decompose days to hours, minutes, seconds, fraction.
   *
   *  This function is part of the International Astronomical Union's
   *  SOFA (Standards Of Fundamental Astronomy) software collection.
   *
   *  Status:  vector/matrix support function.
   *
   *  Given:
   *     ndp     int     resolution (Note 1)
   *     days    double  interval in days
   *
   *  Returned:
   *     sign    char    '+' or '-'
   *     ihmsf   int[4]  hours, minutes, seconds, fraction
   *
   *  Notes:
   *
   *  1) The argument ndp is interpreted as follows:
   *
   *     ndp         resolution
   *      :      ...0000 00 00
   *     -7         1000 00 00
   *     -6          100 00 00
   *     -5           10 00 00
   *     -4            1 00 00
   *     -3            0 10 00
   *     -2            0 01 00
   *     -1            0 00 10
   *      0            0 00 01
   *      1            0 00 00.1
   *      2            0 00 00.01
   *      3            0 00 00.001
   *      :            0 00 00.000...
   *
   *  2) The largest positive useful value for ndp is determined by the
   *     size of days, the format of double on the target platform, and
   *     the risk of overflowing ihmsf[3].  On a typical platform, for
   *     days up to 1.0, the available floating-point precision might
   *     correspond to ndp=12.  However, the practical limit is typically
   *     ndp=9, set by the capacity of a 32-bit int, or ndp=4 if int is
   *     only 16 bits.
   *
   *  3) The absolute value of days may exceed 1.0.  In cases where it
   *     does not, it is up to the caller to test for and handle the
   *     case where days is very nearly 1.0 and rounds up to 24 hours,
   *     by testing for ihmsf[0]=24 and setting ihmsf[0-3] to zero.
   *
   *  This revision:  2013 June 18
   *
   *  SOFA release 2015-02-09
   *
   *  Copyright (C) 2015 IAU SOFA Board.  See notes at end.
   */
  public static function iauD2tf($ndp, $days, &$sign, array &$ihmsf) {
    $nrs;
    $n;
    $rs;
    $rm;
    $rh;
    $a;
    $w;
    $ah;
    $am;
    $as;
    $af;

    /* Handle sign. */
    $sign = $days >= 0.0 ? '+' : '-';

    /* Interval in seconds. */
    $a = static::DAYSEC * abs($days);

    /* Pre-round if resolution coarser than 1s (then pretend ndp=1). */
    if ($ndp < 0) {
      $nrs = 1;
      for ($n = 1; $n <= -$ndp; $n++) {
        $nrs *= ($n == 2 || $n == 4) ? 6 : 10;
      }
      $rs = (double)$nrs;
      $w  = $a / $rs;
      $a  = $rs * intval($w);
    }

    /* Express the unit of each field in resolution units. */
    $nrs = 1;
    for ($n = 1; $n <= $ndp; $n++) {
      $nrs *= 10;
    }
    $rs = (double)$nrs;
    $rm = $rs * 60.0;
    $rh = $rm * 60.0;

    /* Round the interval and express in resolution units. */
    $a = intval($rs * $a);

    /* Break into fields. */
    $ah = $a / $rh;
    $ah = intval($ah);
    $a -= $ah * $rh;
    $am = $a / $rm;
    $am = intval($am);
    $a -= $am * $rm;
    $as = $a / $rs;
    $as = intval($as);
    $af = $a - $as * $rs;

    /* Return results. */
    $ihmsf[0] = (int)$ah;
    $ihmsf[1] = (int)$am;
    $ihmsf[2] = (int)$as;
    $ihmsf[3] = (int)$af;

    return;
  }

}
