Emarsys test feladat
====

Kód futtatása:
```
php ./src/DueDateCalculator.php
```

Test futtatása:
```
phpunit tests/DueDateCalculatorTest.php --colors
```

Ha simán csak a kód van futattva akkor a DueDateCalculator class-ban a fájl alján benne van a megfelelő kód részlet amivel a végkimenetet meg lehet tekinteni.
Mind ezek mellet van egy info method amivel meg lehet tekinteni a Calculator beállításait ha szabad így fogalmaznom.

Ha PHPUnit-al lesz futattva, akkor a kód végén lévő kódot érdemes kikommentezni mert igen csak csúnya tud lenni :)

Stack ami futattam:
 * Ubuntu 18.04
 * PHP 8.0.2
 * PHPUnit 9.5.2
 * Composer 1.10.15
