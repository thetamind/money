<?php

namespace Tests\Money\Parser;

use Money\Parser\IntlMoneyParser;

final class IntlMoneyParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFormattedMoney
     */
    public function testIntlParser($string, $units)
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $parser = new IntlMoneyParser($formatter);
        $this->assertEquals($units, $parser->parse($string, 'USD')->getAmount());
    }

    public static function provideFormattedMoney()
    {
        return [
            ['$1000.00', 100000],
            ['$1000.0', 100000],
            ['$1000.00', 100000],
            ['$0.01', 1],
            ['$1', 100],
            ['-$1000', -100000],
            ['-$1000.0', -100000],
            ['-$1000.00', -100000],
            ['-$0.01', -1],
            ['-$1', -100],
            ['$1000', 100000],
            ['$1000.0', 100000],
            ['$1000.00', 100000],
            ['$0.01', 1],
            ['$1', 100],
            ['$.99', 99],
            ['-$.99', -99],
            ['$75.50', 7550],
            ['-$75.50', -7550],
        ];
    }

    /**
     * @expectedException \Money\Exception\ParserException
     */
    public function testCannotConvertStringToUnits()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $parser = new IntlMoneyParser($formatter);
        $parser->parse('THIS_IS_NOT_CONVERTABLE_TO_UNIT', 'USD');
    }

    public function testDifferentLocale()
    {
        $formatter = new \NumberFormatter('en_CA', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $parser = new IntlMoneyParser($formatter);
        $money = $parser->parse('$1000.00');

        $this->assertEquals('100000', $money->getAmount());
        $this->assertEquals('CAD', $money->getCurrency()->getCode());
    }

    public function testForceCurrency()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $parser = new IntlMoneyParser($formatter);
        $money = $parser->parse('$1000.00', 'CAD');

        $this->assertEquals('100000', $money->getAmount());
        $this->assertEquals('CAD', $money->getCurrency()->getCode());
    }

    public function testFractionDigits()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlMoneyParser($formatter);
        $money = $parser->parse('$1000.005');

        $this->assertEquals('1000005', $money->getAmount());
    }

    public function testFractionDigitsTrailingZero()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlMoneyParser($formatter);
        $money = $parser->parse('$1000.500');

        $this->assertEquals('1000500', $money->getAmount());
    }

    public function testDifferentStyleWithPattern()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlMoneyParser($formatter);
        $money = $parser->parse('$1000.005');

        $this->assertEquals('1000005', $money->getAmount());
    }
}
