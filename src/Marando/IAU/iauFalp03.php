<?php

namespace Marando\IAU;

trait iauFalp03 {

  /**
   *  - - - - - - - - - -
   *   i a u F a l p 0 3
   *  - - - - - - - - - -
   *
   *  Fundamental argument, IERS Conventions (2003):
   *  mean anomaly of the Sun.
   *
   *  This function is part of the International Astronomical Union's
   *  SOFA (Standards Of Fundamental Astronomy) software collection.
   *
   *  Status:  canonical model.
   *
   *  Given:
   *     t     double    TDB, Julian centuries since J2000.0 (Note 1)
   *
   *  Returned (function value):
   *           double    l', radians (Note 2)
   *
   *  Notes:
   *
   *  1) Though t is strictly TDB, it is usually more convenient to use
   *     TT, which makes no significant difference.
   *
   *  2) The expression used is as adopted in IERS Conventions (2003) and
   *     is from Simon et al. (1994).
   *
   *  References:
   *
   *     McCarthy, D. D., Petit, G. (eds.), IERS Conventions (2003),
   *     IERS Technical Note No. 32, BKG (2004)
   *
   *     Simon, J.-L., Bretagnon, P., Chapront, J., Chapront-Touze, M.,
   *     Francou, G., Laskar, J. 1994, Astron.Astrophys. 282, 663-683
   *
   *  This revision:  2013 June 18
   *
   *  SOFA release 2015-02-09
   *
   *  Copyright (C) 2015 IAU SOFA Board.  See notes at end.
   */
  public static function Falp03($t) {
    $a;

    /* Mean anomaly of the Sun (IERS Conventions 2003). */
    $a = fmod(1287104.793048 +
                    $t * ( 129596581.0481 +
                    $t * ( - 0.5532 +
                    $t * ( 0.000136 +
                    $t * ( - 0.00001149 ) ) ) ), TURNAS) * DAS2R;

    return $a;
  }

}
